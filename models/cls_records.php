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
        $temp = implode(", ",  $temp); 
        if($temp==",") return "";
         else return $temp;
     }else{
      if($reasons != 'null')
        return $reasons;
      else
        return '';
     }
   
  }
  function has_ANC_visits($datas) {
    $has_ANC = false;
    foreach($datas as $data) {
      $temp = json_decode( $data['visit_reasons'], true );
      if(in_array(array('ANC 1stvisit', 'ANC 4th visit', 'ANC Other visit'),$temp)) {
        $has_ANC = true;
      }
    }
    return $has_ANC;
  }
  function has_MALNUTRITION_visits($datas) {
    $has_MALNUTRITION = false;
    $datas = json_decode( $datas['visit_reasons'], true );
    if(in_array('Malnutrition (MAM/SAM)',$datas)) {
      $has_MALNUTRITION = true;
    }

    return $has_MALNUTRITION;
  }
	function add(){
    global $Malnutrition_Blade_Popup;

		$_data = $_POST;
    unset($_data['class']);
    unset($_data['func']);
    $arr2 = array("client_id"=>$_GET['cid'],"record_type"=>$_data['type'], "office_id"=>$_SESSION['office_id']);   
    $_data = array_merge($_data, $arr2);
    
    if($_data['type']=="consultation"){
      $_data['visit_reasons']=json_encode($_data['visit_reasons'],JSON_FORCE_OBJECT);  

      // if malnutrition, then store malnut data to tbl_client_malnutrition
      if(isset($_data['hiv_status'])) {

        if(!isset($_data['client_malnutrition_id'])) {
          $_data['client_malnutrition_id'] = $Malnutrition_Blade_Popup->filter_and_save($_data);  
        }
        if(isset($_data['is_final_consultation'])) {
          $Malnutrition_Blade_Popup->markAsPrevious($_data['client_malnutrition_id']);
        }

        unset($_data['hiv_status']);
        unset($_data['tb_diagnosed']);
        unset($_data['muac']);
        unset($_data['uac']);
        unset($_data['oedema']);
        unset($_data['wfh']);
        unset($_data['series']);
        unset($_data['is_final_consultation']);
      }
    }		
    unset($_data['type']);
    $data = $this->save($_data);
	    
		if($data==false){ 
	      echo "error"; //exit();
	    }else{
	       echo "success"; //exit();
	    }
      exit();
	}
	function edit(){
		$_data = $_POST;
		$id = $_data['id'];
		unset($_data['id']);
		unset($_data['class']);
		unset($_data['func']);
    if(isset($_data['is_archived'])){
      $_data['is_archived']=($_data['is_archived']=="on") ? 1 : 0;  
    }
    else{
      $_data['is_archived'] = 0;
    }
		$data = $this->save($_data,array("ID"=>$id));
		if($data==false){
			echo "error";
		}
		else
			echo "success";
		exit();
	}
	function remove(){
    global $Malnutrition_Blade_Popup;
		$_data = $_POST;
    $client_malnutrition_id = $this->check_to_remove_malnut_record($_data['id']);
    if($client_malnutrition_id) {
      $Malnutrition_Blade_Popup->remove($client_malnutrition_id);
    }
		$data = $this->delete($_data['id']);
		if($data==false){
			echo "error";
		}
		else
			echo "success";
		exit();
	}

  function check_client_record($id){
    /* this check if client has record to delete */
    $record =  $this->select("*", array("client_id"=>$id),false, "tbl_records" );
    if ($record) {
      return true;
    }else{
      return false;
    }
  }
  function check_to_remove_malnut_record($id){
    /* this check if client_malnutrition (from table) is still in used OR ready to be deleted. */
    $query = "SELECT a.* FROM tbl_records a, tbl_records b WHERE a.`client_malnutrition_id`= b.`client_malnutrition_id` AND b.`ID` = :record_id";
    $bind_array = array("record_id"=>$id);

    $stmt = $this->query($query,$bind_array);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($data)<=1) {
      return $data[0]['client_malnutrition_id'];
    }else{
      return false;
    }
  }
  function remove_consultation_records(){
    $_data = $_POST;
    $data = $this->delete($_data['id'], "ID", "tbl_client");
    if ($this->check_client_record($_data['id'])) {
      $data = $this->delete($_data['id'], "client_id", "tbl_records");
    }
    if ($data != false) {
      echo "success";
      exit();
    }else{
      echo "error";
      exit();
    }
  }
  function get_consultation_malnutrition_records() {
    $query = "SELECT b.date, b.rutf, b.review_date_future, b.ref_hospital, b.outcome_review,
                     c.series, c.tb_diagnosed, c.hiv_status, c.muac, c.oedema, c.wfh
              FROM tbl_records b,
                   tbl_client_malnutrition c
              WHERE b.client_malnutrition_id=c.id 
              AND b.id=:record_id 
              ORDER BY b.date ASC";
    $bind_array['record_id'] = $_GET['rid'];
    $stmt = $this->query($query,$bind_array);
    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($array)>0)
      echo json_encode($array[0]);
    else 
      echo "error";
    exit();
  }
	function get_consultation_records(){
    $query = "SELECT a.ID, a.hb_level, a.feeding_type, a.date, b.clinic_name, a.visit_reasons 
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
  function relationship_modal($client_info){
    global $type;
    ob_start();
    ?>
    <span class="required_field">* <span class="required_label">required fields.</span></span>
    <div class='relationship_modal_box'>
      <form  role="form" action="" method="post">
        <input type="hidden" name="class" value="records" />
        <input type="hidden" name="func" value="save_client_relationship" />
        <input type="hidden" name="cid" value="<?php echo $_GET['cid']; ?>" />
        <div class="form-group">
          <label for="relationship_type">Type</label><span class="required_field">*</span>
          <select class="form-control" name="relationship_type" id="relationship_type" required>
            <option value="">Select Relationship Type</option>
            <?php                 
            $data = $this->select("type",array("base_client"=>$_GET['cid']),false,"tbl_relationship");
            
            $relationship_type = $type->get_all('relationship');
            if(in_array_r(0,$data)){
              unset($relationship_type['value'][0]);
            }
            if(in_array_r(1,$data)){
              unset($relationship_type['value'][1]);
            }
            if($client_info['client_type'] == "Male" ){
              unset($relationship_type['value'][4]);
            }
            if($client_info['client_type'] == "Female" ){
              unset($relationship_type['value'][5]);
              if(in_array_r(4,$data)){
                unset($relationship_type['value'][4]);
              }
            }
            foreach($relationship_type['value'] as $key=>$data ){ 
              ?><option value="<?php echo $key ?>"><?php echo $data ?></option><?php echo "\n";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label for="related_rec_num">Record Number</label><span class="required_field">*</span>
          <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="related_rec_num" name="related_rec_num" placeholder="Enter Related Record Number" required>
        </div>
       <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default"> 
      </form>
    </div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    modal_container("Relationship",$output,"relationshipModal");
  }

  function relationship_detail_modal(){
    ob_start();
    ?>
    <div data-rid="-" class="relationship_details clearfix" style="margin-top:30px; margin-bottom:30px;">

      <div class="pull-left">
        <input id="r_id" type="hidden" value="-">
        <div class="form-group">
          <label for="related_rec_num">Record Number</label>
          <input id="r_recno" type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" value="-" disabled>
        </div>
        <div class="form-group">
          <label for="related_rec_num">First Name</label>
          <input id="r_fname" type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" value="-" disabled>
        </div>
        <div class="form-group">
          <label for="related_rec_num">Last Name</label>
          <input id="r_lname" type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" value="-" disabled>
        </div>
      </div>
      <div class="pull-right">
        <div class="form-group">
          <label for="related_rec_num">Relationship</label>
          <input id="r_type" type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" value="Mother" disabled>
        </div>
        <div class="form-group">
          <label for="related_rec_num">Birthdate</label>
          <input id="r_bdate" type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" value="-" disabled>
        </div>
        <div class="form-group">
          <label for="related_rec_num">Age</label>
          <input id="r_age" type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" value="-" disabled>
        </div>        
      </div>
      <br style="clear:both;"/>
      <hr />
      <div class="buttons pull-right">
        <a href="#" class="btn btn-default record_link">Go to Profile</a>
        <a href="#" class="btn btn-danger remove_link" data-rid="-">Unlink</a>        
      </div>
    </div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    modal_container("Relationship Details",$output,"relationshipDetailModal");
  } 

	function consultation_modal($client_info){
		global $type, $clinic, $client, $catchment, $Malnutrition_Blade_Popup;
		ob_start();
		?>

    <div class="row">
      <div class="col-md-12">
        
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-warning" id="hb-warning" style="display:none;margin-bottom:0;">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <div class="content">
            <strong>Check HB Level</strong>  
          </div>
        </div>
      </div>
      
    </div>
		<div class='consultation_modal_box'>
      <form id="frmconsultation" role="form" action="" method="post">
          <input type="hidden" name="class" value="records" />
          <input type="hidden" name="func" value="add" />
          <input type="hidden" name="type" value="consultation" />
          <input type="hidden" name="office_id" value="<?php echo $_SESSION['office_id']; ?>" />

          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <div class="">
                <div class="form-group">
                  <label for="clienttype">Clinic Attendance</label><span class="required_field">*</span>
                  <select class="form-control" name="clinic_id" id="clinic_id" required>
                    <option value="">Select Clinic</option>
                    <?php                 
                    foreach($clinic->get_all() as $data ){ 
                      ?><option value="<?php echo $data['ID'] ?>"><?php echo $data['clinic_name'] ?></option><?php echo "\n";
                    }
                    ?>
                  </select>
                </div>
              </div>
                
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="referral_id">Client Referral</label>
                <select class="form-control" name="referral_id" id="referral_id" required>
                    <?php 
                    $_datas = $type->get_all('referral');
                  if($_datas!=false): foreach($_datas['value'] as $index => $data ): ?>
                    <option value="<?php echo $index; ?>"><?php echo $data ?></option>  
                  <?php endforeach; endif; ?>
                </select>
                <span style="font-size:11px"></span>
              </div> 
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="consultationdate">Consultation Date</label><span class="required_field">*</span>
                <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="datepicker3" name="date" placeholder="Enter Consultation Date" required>
              </div>
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="consultation_time">Consultation time</label>
                
                <select class="form-control" name="consultation_time" id="consultation_time" >
                    <?php 
                    $_datas = $type->get_all('consultation_time');
                  if($_datas!=false): foreach($_datas['value'] as $index => $data ): ?>
                    <option value="<?php echo ($index===-1) ? '' : $data; ?>"><?php echo $data ?></option>  
                  <?php endforeach; endif; ?>
                </select>

              </div> 
            </div>
          </div>
          
           <div class="row">
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="review_date">Review Date</label>
                <input  type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="datepicker-review_date" name="review_date" placeholder="Enter Review Date">
              </div>  
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="referral_time">Review time</label>
             
                <select class="form-control" name="referral_time" id="referral_time">
                    <?php 
                    $_data = $type->get_all('referral_time');
                  if($_data!=false): foreach($_data['value'] as $index => $data ): ?>
                    <option value="<?php echo $data; ?>"><?php echo $data ?></option>  
                  <?php endforeach; endif; ?>
                </select>

              </div>  
            </div>
            
            <div class="col-xs-12 col-sm-6" id="catchment_box" style="display:none;">
              <div class="form-group">
                <label for="catchment">Catchment Area</label><span class="required_field">*</span>
             
             
				<select class="form-control" name="catchment" id="catchment" >
					<option value=""></option>
                    <?php 
                    $_data = $catchment->get_all();
                  if($_data!=false): foreach($_data as $data ): ?>
                    <option value="<?php echo $data['id']; ?>" class="clinic_c clinic_c_<?php echo $data['clinic_id']; ?>" style="display:none;"><?php echo $data['catchment_area'] ?></option>  
                  <?php endforeach; endif; ?>
                   <option value="other" >Other</option> 
                </select>

              </div>  
            </div>
            
            
           </div>


            <div class="form-group visitreasonsdiv">
              <label for="visittype">Visit Reason(s)</label><span class="required_field">*</span>
              <br>
              <?php  
              $_data = $type->get_all('visit');
              if($_data!=false): foreach($_data['value'] as $data ):?>
                <label class="checkbox-inline">
                    <input type="checkbox" name="visit_reasons[]"id="<?php echo toSlug($data) ?>" value="<?php echo $data; ?>" multiple>  <?php echo $data ?>
                </label>
              <?php endforeach; endif;
              ?>
              <span class="help-block" style="margin-top: -12px;display:none">
                <small style="font-size: 12px;">Select Visit Reason(s).</small>
              </span>
            </div>

            <div class="row">
              
              <?php if($client->get_age($client_info['date_birth']) <= 14) : ?>
                <?php if($client_info['date_birth']=="0000-00-00" ||
                          $client->get_age($client_info['date_birth']) <= 2) : ?>
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                      <label for="typefeeding">Type of Feeding</label><span class="required_field">*</span>
                      <select class="form-control" name="feeding_type" id="feeding_id" required>
                        <option value="">Select Feeding Type</option>
                        <?php
                        $_data = $type->get_all('feeding');
                        if($_data!=false): foreach($_data['value'] as $data ): ?>
                        <option value="<?php echo $data ?>"><?php echo $data ?></option>
                      <?php endforeach; endif; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <div id="hb-form" style="display:none">
                      <div class="form-group">
                        <label>HB Levels</label><span class="required_field">*</span>
                        <select class="form-control" id="hb_level" name="hb_level">
                          <option value="">
                            Select HB Level
                          </option>
                          <option value="10+">
                            10 above
                          </option>
                          <option value="10-">
                            8-10
                          </option>
                          <option value="8-">
                            8 below
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <?php else: ?>
                <input type="hidden" name="feeding_type" value="N/A" />
                <div class="col-xs-12 col-sm-6">
                  <div id="hb-form" style="display:none">
                    <div class="form-group">
                      <label>HB Levels</label><span class="required_field">*</span>
                      <select class="form-control" id="hb_level" name="hb_level">
                        <option value="">
                          Select HB Level
                        </option>
                        <option value="10+">
                          10 above
                        </option>
                        <option value="10-">
                          8-10
                        </option>
                        <option value="8-">
                          8 below
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-xs-12 col-sm-6">

                </div>            
              <?php endif; ?>
            </div>
            <?php $Malnutrition_Blade_Popup->render() ?>
            <div class="row">
              <div class="col-md-4">
                <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default" >
              </div>
              <div class="col-md-8 text-right" style="padding-top:15px;">
                <span class="required_field">* <span class="required_label">required fields.</span></span>
              </div>
            </div>                                    
          
    </form>
    </div>
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("Consultation",$output);
	}
  function malnutrition_view_modal() {
    global $Malnutrition_Blade_Popup;
    ob_start();
    $Malnutrition_Blade_Popup->render_readonly();
    $output = ob_get_contents();
    ob_end_clean();
    modal_container("Malnutrition", $output, 'viewMalnutritionDetails');
  }
	function followup_modal($modal_id){
		global $type, $clinic;
		ob_start();
		?>
     <span class="required_field">* <span class="required_label">required fields.</span></span>
		<form id="frmfollowup" role="form" action="" method="post">
          <input type="hidden" name="class" value="records" />
          <input type="hidden" name="func" value="add" />
          <input type="hidden" name="type" value="followup" />
          
          <div class="form-group">
            <label for="clienttype">Follow Up Types</label><span class="required_field">*</span>
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
            <label for="deathdate">Recommended Date</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="followup_date" name="date" placeholder="Enter Date of Death">
          </div>   
          <div class="form-group">
            <label for="clienttype">Clinic</label><span class="required_field">*</span>
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
  function get_age($birth_date){
    if($birth_date == 000-00-00)
    {
      echo "";
    }else
   return floor((time() - strtotime($birth_date))/31556926);
   }
  function transfer_record_modal($client_id){
    ob_start();
    ?>
    <!-- <div id="modal-body"> -->
     <span class="required_field">* <span class="required_label">required fields.</span></span>
    <div class="transfer_modal_form">
      <form role="form" action="" method="post">
        <input type="hidden" name="class" value="records" />
        <input type="hidden" name="func" value="transfer_records" />
        <input type="hidden" name="id" value="<?php echo $client_id; ?>" />
        <br>
        <div class="form-group">
          <label for="recordnumber">Record Number</label><span class="required_field">*</span>
          <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="record_number" name="record_number" placeholder="Enter Record Number" required>
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
  function save_client_relationship(){
    $_data = $_POST;
    $_data2['base_client'] = $_data['cid'];
    $_data2['relation_to'] = $_data['related_rec_num'];
    $_data2['type'] = $_data['relationship_type'];
    
    unset($_data['class']);
    unset($_data['func']);
    unset($_data['cid']);
    
    $base_client_rec_no = $this->select("record_number", array("ID"=>$_data2['base_client']),true, "tbl_client");    
    $check_record_number =  $this->select("record_number", array("record_number"=>$_data2['relation_to']),true, "tbl_client");
    $record_used =  $this->select("ID", array("relation_to"=>$_data2['relation_to'],"base_client"=>$_data2['base_client']),true, "tbl_relationship");
    
    if($record_used!=false){
      echo "record_used";
      exit();
    }
    elseif ($check_record_number!=false){
      if ($base_client_rec_no['record_number'] == $_data2['relation_to']) {
          echo "same-account";
          exit();
      }
      else{
        if($_data2['type'] == 0){ // father
          $father =  $this->select("*", array("base_client"=>$_data2['base_client'], "type"=>"Father"),false, "tbl_relationship" );
          if ($father) {
            echo "father_doubled";
            exit();
          }
        }
        if($_data2['type'] == 1){ // mother
          $mother =  $this->select("*", array("base_client"=>$_data2['base_client'], "type"=>"Mother"),false, "tbl_relationship" );
          if ($mother) {
            echo "mother_doubled";
            exit();
          }
        }
        // no need to check for child and sibling. 
        if($_data2['type'] == 4){ // husband
          $husband =  $this->select("*", array("base_client"=>$_data2['base_client'], "type"=>"Husband"),false, "tbl_relationship" );
          if ($husband) {
            echo "husband_doubled";
            exit();
          }
        }
        $this->save($_data2, $arr_where=array(), 'tbl_relationship');
        echo "success";
        exit();
      }
    }
    else{
      echo "not-found";     
      exit();   
    }
  }

  function get_relationship($cid){
    $data = $this->select("*",array("base_client"=>$cid),false,"tbl_relationship",false,"type");
    // convert type
    $child_count  =   0;
    $child_1_key  =   0;
    $sibli_1_key  =   0;
    $sibli_count  =   0;
    
    if($data) :
      foreach($data as $key=>$d){

        switch($d['type']){
          case 0 : $data[$key]['type']="Father"; break;
          case 1 : $data[$key]['type']="Mother";  break;
          case 2 :
            $child_count++; 
            if($child_count==1)
              $child_1_key = $key; 
            $data[$key]['type']="Child #".$child_count; break;
          case 3 : 
            $sibli_count++; 
            if($sibli_count==1)
              $sibli_1_key = $key; 
            $data[$key]['type']="Sibling #".$sibli_count; break;
          case 4 : 
            $data[$key]['type']="Husband"; break;
        }
      }
      if($child_count==1){
        $data[$child_1_key]['type']="Child";
      }
      if($sibli_count==1){
        $data[$sibli_1_key]['type']="Sibling";
      }
    endif;
    return $data;
  }
  function fetch_relationship_details(){
    $client = new Client;
    $data = $client->select("*",array("record_number"=>$_POST['rid']),true);
    $data['age']=$this->get_age($data['date_birth']);
    echo json_encode($data);
    exit();
  }

	function script(){
    ?>
    <script>
    $(document).ready(function(){  

      $('#newClientModal').on('show.bs.modal', function (e) {
        resetForm();
        $('#catchment').attr('required', false);
      });
      
      $('#clinic_id').on('change',function(){
	      var get_id = $(this).val();
		  
        $('.clinic_c').css('display','none');
        $("#catchment").val('');
        if( $('.clinic_c_'+get_id).length )         // use this if you are using class to check
        {
        $('#catchment_box').show();
              $('.clinic_c_'+get_id).css('display','block');			
              $('#catchment').attr('required', true);
        }     
        else{
          $('#catchment').attr('required', false);
          $('#catchment_box').hide();
        }
	      
      });

      $(document).on('change', '#malnutrition-mam-sam', function() {
          $('#malnutgroup').toggle(this.checked)
      })
      
      

      /*onclick="JavaScript:return onValidateEmptyField(['consultation_time']);"*/
      window.onValidateEmptyField = function (requiredField) {
          var doc = document;
          for (var i in requiredField) {
              var elem = doc.getElementById(requiredField[i]);
              if (elem.tagName === 'SELECT') {
                  if (elem.selectedIndex === 0) {
                      elem.value = '';
                  }
              }
          }
      };

      //Code added by Eric
      var hold_dob = ""; //this will hold the previous birthdate value
      $("#date_birth").click(function(){
        if($("#date_birth").val() != ""){
          hold_dob = $("#date_birth").val();
        }
      });

      $("#date_birth").on( 'change', function(){
        var dob = new Date($(this).val());
        //Code added by Eric
          if(dob > Date.now()){
            $("#date_birth").val('');
            alert("Please set a birthdate that is not in the future.");
            $(".birthdate-warning").fadeIn().html('Please set a birthdate that is not in the future.');
            $("#date_birth").val(hold_dob);
            return false;
          } else {
            $(".birthdate-warning").fadeOut();
          }
      });
      //End added code here

      //Code added by Eric
      var hold_dod = ""; //this will hold the previous death date value [date of death]
      $("#date_death").click(function(){
        if($("#date_death").val() != ""){
          hold_dod = $("#date_death").val();
        }
      });

      $("#date_death").on( 'change', function(){
        var dod = new Date($(this).val());
        //Code added by Eric
          if(dod > Date.now()){
            $("#date_death").val('');
            alert("Please set date of death that is not in the future.");
            $(".deathdate-warning").fadeIn().html('Please set date of death that is not in the future.');
            $("#date_death").val(hold_dod);
            return false;
          } else {
            $(".deathdate-warning").fadeOut();
          }
      });
      //End added code here

      /* ================== Save Clients relationship */
      $(document).on('submit',".relationship_modal_box form",function(){
        show_loader($,"#relationshipModal");
        _data = $(this).serialize();
        _this = $(this);
        
        $.post(window.location.href,_data, function(data){
          if($.trim(data)=="success"){
            $("#relationshipModal").modal('hide');
            $(".container fieldset").load(window.location.href+" fieldset");
            $(".relationship_modal_box").load(window.location.href+" .relationship_modal_box");
          }else if($.trim(data)=="same-account"){
            $("#errormessage").fadeIn().html("Oops! Same account found!");
          }
          else if($.trim(data)=="record_used"){
            $("#errormessage").fadeIn().html("Oops! Record # Already Used!");
          }
          else{
           $("#errormessage").fadeIn().html("Oops! Record number not found!");
          }
          close_loader($,"#relationshipModal");                    
        })
        return false;
      });
      
      /* ================== Unlink Clients relationship */
      $(document).on('click',".remove_link",function(){
        show_loader($,"#relationshipDetailModal");
        var that = this;

        //console.log($(this).attr('data-rid'));
        _data = "class=Relationship&func=unlink&id="+$(this).attr('data-rid')+"&cid="+$("#r_id").val()+"&rid="+$("#r_recno").val();

        $.post(window.location.href,_data, function(data){
          if($.trim(data)=="error"){
            alert('An error occured.');
          }
          else{
            //console.log(data);
            $(".container fieldset").load(window.location.href+" fieldset");
            $(".relationship_modal_box").load(window.location.href+" .relationship_modal_box");
          }
          $("#relationshipDetailModal").modal('hide');
          close_loader($,"#relationshipDetailModal"); 
        });
        return false;
      })

      /* ================== GET Clients relationship details */

      $(document).on('click',".relationshipDetailModal",function(){
        var that = this;
        var _type = $(that).parents('.form-group').find('.lblrelationship_type').html();
        show_loader($,"#relationshipDetailModal");
        _data = "class=Records&func=fetch_relationship_details&rid="+$(this).data('rid');
        $.post(window.location.href,_data, function(data){
          if($.trim(data)=="error"){
            console.log(data);
          }
          else{
            var _relation = $.parseJSON(data);
            $(".relationship_details").attr( 'data-rid', $(that).data('relationship-id') );
            
            $("#r_id").val(_relation['ID']);
            $("#r_recno").val( _relation['record_number'] );
            $("#r_type").val( _type );
            $("#r_fname").val( _relation['fname'] );
            $("#r_lname").val(  _relation['lname'] );
            $("#r_bdate").val( _relation['date_birth'] );
            $("#r_age").val( _relation['age'] );
            $(".record_link").attr('href','?page=records&cid='+_relation['ID']+'&p=view');
            $(".remove_link").attr( 'data-rid', $(that).data('relationship-id') );
          }
          close_loader($,"#relationshipDetailModal");  
          $("#newClientModal").modal('hide');                  
        })
        return false;
      });

      /* ================== save consultation records */
      $(".consultation_modal_box form").on('submit',function(){
        if($('.visitreasonsdiv :checkbox:checked').length <= 0) {
          alert('Please select at least one (1) Visit Reason');
          $('.visitreasonsdiv .help-block').show();
          return false;
        }
        $('#hb-warning').hide();
        show_loader($);
        _data = $(this).serialize();
        _this = $(this);
        $.post(window.location.href,_data, function(data){

          // $("#newClientModal").modal('hide');
          if($.trim(data)!="success"){
            console.log(data);
          }
          else{
            
            $('#hb-form').hide()
            $('.btn-success').prop('disabled', false);
            $('.btn-success').prop('disabled', false);
            if($(_this).find("input[name='func']").val()=="add")
              show_alert_info("New Record Successfully Added!",$);
            else
              show_alert_info("Record Modified Successfully!",$);
            $(".container table").load(window.location.href+" table");
          }
          location.reload();
          close_loader($);                    
        })
        return false;
      });
      /* ===== Transfer Client and consultation records */
        $(".transfer_modal_form form").on('submit',function(){
           show_loader($);
          $(".btn_submit_modal").val('Saving...');
          $("#btn_submit_modal").addClass("disabled");
        _data = $(this).serialize();
        _this = $(this);
        $.post(window.location.href,_data, function(data){
         // console.log(data);
          if($.trim(data)=="success"){
            /*$(".btn_submit_modal").val('Record Transfered. Redirecting back to search page...');
              setTimeout(function(){
              window.location = "?page=search";
            },3500);*/
             if (getUrlVars()["f"] == "search" ) {
               window.location = "?page=search";
              }else{
                window.location = "?page=clients";
              }
          }else if($.trim(data)=="same-account"){
            $("#errormessage").fadeIn().html("Oops! Same account found!");
            $(".btn_submit_modal").val('Submit');
            $("#btn_submit_modal").removeClass("disabled");
          }else{
           $("#errormessage").fadeIn().html("Oops! Record number not found!");
            $(".btn_submit_modal").val('Submit');
            $("#btn_submit_modal").removeClass("disabled");
          }  
           close_loader($);               
        })
        return false;
      });
      /* ===== Delete Clients and consultation records */
        $(".delete-options-entry form").on('submit',function(){
           show_loader($);
          //$("#success_message").fadeIn();
          $("#btn_submit").addClass("disabled");
          $("#btn_transfer").addClass("disabled");
          $("#btn_back").addClass("disabled");
          console.log("delete client and records");
        _data = $(this).serialize();
        _this = $(this);
        $.post(window.location.href,_data, function(data){
         // console.log(data);
          if($.trim(data)=="success"){
             if(getUrlVars()["f"]){
                  if (getUrlVars()["f"] == "search" ) {
                   window.location = "?page=search";
                  }else{
                    window.location = "?page=clients";
                  }
              }
              /*setTimeout(function(){
              window.location = "?page=search";
            },3500);*/
          }else{
           console.log(data);
           /* setTimeout(function(){
              $("#message").fadeOut();
            },3500);*/
            
          } 
           close_loader($);                
        })
        return false;
      });
      $("#frm_client_personal_info_update").on('submit',function(e){
        e.preventDefault();
        show_loader($);
        $('.required_field').hide();  
        _data = $(this).serialize();  
        _this = $(this);
        console.log(_data);
        $.post(window.location.href,_data, function(data){
         if($.trim(data)!="success"){
           console.log(data);
         }
         else{
           if($(_this).find("input[name='func']").val()=="add")
             show_alert_info("New Record Successfully Added!",$);
           else {
            show_alert_info("Record Modified Successfully!",$);
            setTimeout(function() {
              close_loader($);
              window.location.href="?page=records&cid=<?php echo $_GET['cid'] ?>&p=view";
            },3500);
           }           
         } 
        })
        return false;
      })
      modal_close($);
      delete_button($,this);
    });  
    function show_alert_info(string, $){
      $(".alert-info").fadeIn();
      setTimeout(function(){
        $(".alert-info").html('<strong>'+string+'</strong>');
        setTimeout(function(){
          $(".alert-info").fadeOut();
        }, 1000);
      },2000);
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
        show_loader($);
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
          close_loader($);  
          location.reload();
        });
        return false;
      });
    }

    </script>
    <?php
	}
}
?>