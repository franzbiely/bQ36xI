<?php
class Records extends DB{
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_records";
	}

    function display_visit_reasons($reasons){
      //json_decode( stripslashes( $post_data ) );
     $temp = json_decode($reasons, true);
     if ($temp != false) {
        $temp = implode(",",  $temp); 
        if($temp==",") return "";
         else return $temp;
     }else{
      return $reasons;
     }
   
  }
	function add(){
		$_data = $_POST;
    unset($_data['class']);
    unset($_data['func']);
    $arr2 = array("client_id"=>$_GET['cid'],"record_type"=>$_data['type'], "office_id"=>$_SESSION['office_id']);    
    $_data = array_merge($_data, $arr2);
    
    if($_data['type']=="consultation"){
      $_data['visit_reasons']=json_encode($_data['visit_reasons'],JSON_FORCE_OBJECT);  
    }		

    unset($_data['type']);

    $data = $this->save($_data);
		if($data==false)
			echo "error";
	}
	function edit(){
		$_data = $_POST;
		$id = $_data['id'];
		unset($_data['id']);
		unset($_data['class']);
		unset($_data['func']);
		$data = $this->save($_data,array("ID"=>$id));
		if($data==false){
			echo "error";
		}
		else
			echo "success";
		exit();
	}
	function remove(){
		$_data = $_POST;
		$data = $this->delete($_data['id']);
		if($data==false){
			echo "error";
		}
		else
			echo "success";
		exit();
	}
  function remove_consultation_records(){
    $_data = $_POST;
    $data = $this->delete($_data['id'], "ID", "tbl_client");
    $data = $this->delete($_data['id'], "client_id", "tbl_records");
    if ($data != false) {
      echo "success";
      exit();
    }else{
      echo "error";
      exit();
    }
  }
	function get_consultation_records(){
    $query = "SELECT a.ID, a.feeding_type, a.date, b.clinic_name, a.visit_reasons 
              FROM tbl_records as a JOIN tbl_clinic as b ON b.ID = a.clinic_id
              WHERE a.client_id = :client_id AND record_type = :record_type ORDER BY a.date ASC";
    $bind_array['client_id'] = $_GET['cid'];
    $bind_array['record_type'] = "consultation";
    //$records = $this->query($query, $bind_array);   
    $stmt = $this->query($query,$bind_array);
    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $array;
    //$data = $records;
    //return $data;
	}
	function get_followup_records(){
    $records = $this->select("*",array("record_type"=>"followup","client_id"=>$_GET['cid']));
    if($records!=false){
      foreach($records as $key=>$record){
        $data2 = $this->select("*",array("ID"=>$record['clinic_id']),true,"tbl_clinic");   
        $records[$key]['clinic_name']=$data2['clinic_name'];
      }
    }    
    $data = $records;
    return $data;
	}
	function consultation_modal($client_info){
		global $type, $clinic, $client;
		ob_start();
		?>
		<form id="frmconsultation" role="form" action="" method="post">
          <input type="hidden" name="class" value="records" />
          <input type="hidden" name="func" value="add" />
          <input type="hidden" name="type" value="consultation" />
          <input type="hidden" name="office_id" value="<?php echo $_SESSION['office_id']; ?>" />
          
          <?php if($client_info['client_type']=="Child") : ?>
            <?php if($client_info['date_birth']=="0000-00-00" ||
                     $client->get_age($client_info['date_birth']) <= 2) : ?>
              <div class="form-group">
                <label for="typefeeding">Type of Feeding</label>
                <select class="form-control" name="feeding_type" id="feeding_id" required>
                  <option value="">Select Feeding Type</option>
                  <?php
                  $_data = $type->get_all('feeding');
                  if($_data!=false): foreach($_data['value'] as $data ): ?>
                  <option value="<?php echo $data ?>"><?php echo $data ?></option>
                <?php endforeach; endif; ?>
                </select>
              </div>   
            <?php endif; ?>
            <?php else: ?>
            <input type="hidden" name="feeding_type" value="N/A" />              
          <?php endif; ?>   
          <div class="form-group">
              <label for="consultationdate">Consultation Date</label>
              <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="datepicker3" name="date" placeholder="Enter Consultation Date" required>
            </div>           
            <div class="form-group">
              <label for="clienttype">Clinic Attendance</label>
              <select class="form-control" name="clinic_id" id="clinic_id" required>
                <option value="">Select Clinic</option>
                <?php                 
                foreach($clinic->get_all() as $data ){ 
                  ?><option value="<?php echo $data['ID'] ?>"><?php echo $data['clinic_name'] ?></option><?php echo "\n";
                }
                ?>
              </select>
            </div>   
          <div class="form-group">
              <label for="visittype">Visit Reason(s)</label>
              <br>
               <!--  <select name="visit_reasons[]" class="form-control" id="visit_id" multiple required> -->
             <!--   <option value="<?php //echo $data ?>"><?php //echo $data ?></option> -->
                  <?php  
                  $_data = $type->get_all('visit');
                  if($_data!=false): foreach($_data['value'] as $data ):
                    ?>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="visit_reasons[]"id="visit_id" value="<?php echo $data; ?>" multiple>  <?php echo $data ?>
                    </label>
                  
                  <?php endforeach; endif;
                  ?>
               <!--  </select> -->
                <span class="help-block" style="margin-top: -12px;">
                  <small style="font-size: 12px;">Select Visit Reason(s).</small>
                </span>
            </div>                                       
          <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
        </form>
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("Consultation",$output);
	}
	function followup_modal($modal_id){
		global $type, $clinic;
		ob_start();
		?>
		<form id="frmfollowup" role="form" action="" method="post">
          <input type="hidden" name="class" value="records" />
          <input type="hidden" name="func" value="add" />
          <input type="hidden" name="type" value="followup" />
          
          <div class="form-group">
            <label for="clienttype">Follow Up Types</label>
            <select class="form-control" name="followup_type" id="followup_type">
              <?php  
                $_data = $type->get_all('followup');
                if($_data!=false): foreach($_data['value'] as $data ):
                  ?><option value="<?php echo $data ?>"><?php echo $data ?></option><?php echo "\n";
                endforeach; endif;
                ?>
            </select>
          </div>       
          <div class="form-group">
            <label for="deathdate">Recommended Date</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="followup_date" name="date" placeholder="Enter Date of Death">
          </div>   
          <div class="form-group">
            <label for="clienttype">Clinic</label>
            <select class="form-control" name="clinic_id" id="clinic_id">
              <option value="">Select Clinic</option>
              <?php                 
              foreach($clinic->get_all() as $data ){ 
                ?><option value="<?php echo $data['ID'] ?>"><?php echo $data['clinic_name'] ?></option><?php echo "\n";
              }
              ?>
            </select>
          </div>                        
                              
          <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
        </form>
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("Followup",$output,$modal_id);
	}
  function get_client_id($record_number){
    $data = $this->select("ID" ,array("record_number"=>$record_number), true, "tbl_client",false);
    return $data['ID'];
  }
  function get_id_tbl_records($client_id){
    $data = $this->select("ID" ,array("client_id"=>$client_id), false, "tbl_records",false);
    return $data;
  }
  /*function update_client_id_tbl_records($client_id,  $client_id_tbl_records){
    $con = $this->connect();
    $query = "UPDATE tbl_records
              SET client_id = :client_id
              WHERE ID = :id";
              $bind_array['client_id'] = $client_id;
              $bind_array['id'] =  $client_id_tbl_records;
               $records = $this->query($query, $bind_array);   
  } */
  function transfer_records(){
    $_data = $_POST;
    unset($_data['class']);
    unset($_data['func']);  
    $client_id =  $this->get_client_id($_data['record_number']);
    $check_record_number =  $this->select("record_number", array("record_number"=>$_data['record_number']),true, "tbl_client");
    if ($check_record_number!=false){
      if ($client_id == $_data['id']) {
          echo "same-account";
          exit();
      }else{

        /* delete client */
        $data = $this->delete($_data['id'], "ID", "tbl_client");

        /* update client_id in tbl_records */
        $data_ids = $this->get_id_tbl_records($_data['id']);
        $_data2['client_id'] = $client_id; 
        foreach ($data_ids as $key => $data_ids) {
          $this->save($_data2, array("ID"=>$data_ids['ID']));
        }
        echo "success"; exit();
      }
    }else{
      echo "not-found";     
      exit();   
    } 
  }
  function transfer_record_modal($client_id){
    ob_start();
    ?>
    <!-- <div id="modal-body"> -->
    <div class="transfer_modal_form">
      <form role="form" action="" method="post">
        <input type="hidden" name="class" value="records" />
        <input type="hidden" name="func" value="transfer_records" />
        <input type="hidden" name="id" value="<?php echo $client_id; ?>" />
        <br>
        <div class="form-group">
         <!--  <label for="recordnumber">Record Number</label> -->
          <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="record_number" name="record_number" placeholder="Enter Record Number" required>
        </div>                      
        <input style="margin-top: 20px;" type="submit" id="btn_submit_modal" class="btn btn-success btn-default btn_submit_modal" value="Submit">
      </form>
    </div>
    <!-- </div>  --><!-- ===== id: modal-body ===== -->
    
    
    <?php
      $output = ob_get_contents();
      ob_end_clean();
      modal_container("transfer_record",$output);
  } 
	function script(){
    ?>
    <script>
    $(document).ready(function(){  

      /* ===== Delete Clients and consultation records */
        $(".transfer_modal_form form").on('submit',function(){
          console.log("Test!");
          $(".btn_submit_modal").val('Saving...');
          $("#btn_submit_modal").addClass("disabled");
        _data = $(this).serialize();
        _this = $(this);
        
        $.post(window.location.href,_data, function(data){
         // console.log(data);
          if($.trim(data)=="success"){
            $(".btn_submit_modal").val('Record Transfered');
              setTimeout(function(){
              window.location = "?page=clients";
            },3500);
          }else if($.trim(data)=="same-account"){
            $("#errormessage").fadeIn().html("Oops! Same account found!");
            $(".btn_submit_modal").val('Submit');
            $("#btn_submit_modal").removeClass("disabled");
          }else{
           console.log(data);
           $("#errormessage").fadeIn().html("Oops! Record number not found!");
            $(".btn_submit_modal").val('Submit');
            $("#btn_submit_modal").removeClass("disabled");
          }                 
        })
        return false;
      });
      /* ===== Delete Clients and consultation records */
        $(".delete-options-entry form").on('submit',function(){
         $("#success_message").fadeIn();
          $("#btn_submit").addClass("disabled");
          $("#btn_transfer").addClass("disabled");
          $("#btn_back").addClass("disabled");
          console.log("delete client and records");
        _data = $(this).serialize();
        _this = $(this);
        $.post(window.location.href,_data, function(data){
         // console.log(data);
          if($.trim(data)=="success"){
              setTimeout(function(){
              window.location = "?page=search";
            },3500);
          }else{
           console.log("problem");
           /* setTimeout(function(){
              $("#message").fadeOut();
            },3500);*/
            
          }                 
        })
        return false;
      });
      $("#frm_client_personal_info_update").on('submit',function(){
        _data = $(this).serialize();
        _this = $(this);
        $.post(window.location.href,_data, function(data){
         if($.trim(data)!="success"){
           console.log(data);
         }
         else{
           if($(_this).find("input[name='func']").val()=="add")
             show_alert_info("New Record Successfully Added!",$);
           else
             show_alert_info("Record Modified Successfully!",$);
           window.location.href="?page=records&cid=<?php echo $_GET['cid'] ?>&p=view";
         }                   
        })
        return false;
      })
      modal_close($);
      delete_button($,this);  
     
    });  
    function show_alert_info(string, $){
      $(".alert-info").fadeIn().find('strong').html(string);
      setTimeout(function(){
        $(".alert-info").fadeOut();
      },3500);
    } 

    function modal_close($){
      $("#newClientModal, #alert-sure-delete").find(".close, .btn-default").on('click',function(){
        $("table tr").removeClass('focus');
      });
      $(".alert .btn-default, .alert .close").on('click',function(){
        $(this).parent().fadeOut();
      });
    }
    function delete_button($,_this){
      $(_this).on('click',"a.delete",function(){
        $("#alert-sure-delete").fadeIn();
        $("table tr").removeClass('focus');
        $(this).parent().parent().parent().addClass('focus');       
        return false;
      });
      $("#alert-sure-delete .yes").on('click',function(){
        id = $("tr.focus .id").data("id");
        _data = "class=Records&func=remove&id="+id;
        $.post(window.location.href,_data, function(data){
          if($.trim(data)!="success"){
            console.log(data);
          }
          else{
            $("tr.focus").remove();
          } 
          $("#alert-sure-delete").fadeOut();    
        });
        return false;
      });
    } 
    </script>
    <?php
	}
}
?>