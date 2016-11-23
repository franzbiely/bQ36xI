<?php
class Office extends DB{
	private $entry_type;
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_area";
		$this->entry_type = "office";
	}
	function get_all(){
		$data = $this->select("ID, office_id, area_name, office_address, contact",array("entry_type"=>$this->entry_type));
		//$this->filter($data);
		return $data;
	}
	function get_all_wsession(){
		if($_SESSION['type'] == 'superadmin' || $_SESSION['type'] == 'reporting_user'){
			$data = $this->select("ID, area_name, office_address, contact, parent_ids",array("entry_type"=>$this->entry_type));
		}else{
			$data = $this->select("ID, area_name, office_address, contact, parent_ids",array("entry_type"=>$this->entry_type, "office_id"=>$_SESSION["office_id"]));
		}

		return $data;
	}
	function add(){
		$_data = $_POST;
		unset($_data['class']);
		unset($_data['func']);
		unset($_data['province']);		
		$_data['parent_ids'] = 	$_data['parent_ids'];
		$_data['entry_type'] = 	'office';
		unset($_data['province']);
		$arr2 = array("office_id"=>$this->get_max("tbl_area", "ID") + 1);		
		$_data = array_merge($_data, $arr2);
		$data = $this->save($_data);
		if($data==false)
			echo "error";
		else
			echo "success";
			exit();
		
	}
	function edit(){
		$_data = $_POST;
		$id = $_data['id'];
		unset($_data['id']);
		unset($_data['class']);
		unset($_data['func']);
		unset($_data['province']);
		$data = $this->save($_data,array("ID"=>$id));
		if($data==false){
			echo "error";
			exit();
		}else{
			$_SESSION['area_name'] = $_data['area_name'];
			echo "success";
			exit();
		}
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
	function get_districts(){
		$_data = $_POST;
		$data = $this->select("ID, area_name",array( "entry_type"=>"district", "parent_ids"=>$_data['province_id']), false, "tbl_area");
		echo json_encode($data);
		exit();
	}
	function get_province(){
		$query = "SELECT DISTINCT ID, area_name
				FROM tbl_area
				WHERE entry_type = :en_type
				GROUP BY area_name ASC";
		$bind_array= array("en_type"=>"province");
		$stmt = $this->query($query,$bind_array);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $data;
		exit();
	}
    function check_hc_clients(){
        $_data = $_POST;
        $data = $this->select("*",array("office_id"=>$_data['id']), false, 'tbl_client');
        if($data != false) { echo "has_clients"; exit(); }
        else{
            echo "no_clients"; exit(); 
        }
    }
	function modal(){
		global $district, $province;
		ob_start();
		?>
		<form role="form" id="form_client" action="" method="post">
          <input type="hidden" name="class" value="office" />
          <input type="hidden" name="func" value="add" />
          <span class="required_field">* <span class="required_label">required fields.</span></span>
          <div class="form-group">
            <label for="area_name">Name</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="area_name" name="area_name" placeholder="Enter Health Faciltiiy Name" required>
          </div>               
          <div class="form-group">
            <label for="province_name">Address</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="address1" name="office_address" placeholder="Enter Health Faciltiiy Address">
          </div>       
          <?php /*                       
          <div class="form-group">
            <label for="office_description">Office Secondary Address</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="address2" placeholder="Enter Office Secondary Address" name="address2">
          </div> 
          */ ?>
          <div class="form-group">
            <label for="office">Phone Number</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="phone1" name="contact" placeholder="Enter Health Faciltiiy Phone Number">
          </div>
          <div class="form-group">
            <label for="area_name">Province</label><span class="required_field">*</span>
            <select class="form-control" name="province" id="province" onchange="javascript: populate_districts(this)" required>
              <option value="">Select Health Facility Province</option>
              <?php                 
              foreach($this->get_province() as $data ){ 
                ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
              }
              ?>  
            </select>
          </div> 
           <div class="div-district">
          	<div class="form-group district-form" id="district-form">
            <label for="area_name">District</label><span class="required_field">*</span>
            <select class="form-control district_select" name="parent_ids" id="parent_ids" disabled required>
             
              <div class="district_option">
              	 <option>Select Health Facility District</option>
              	<?php                 
	              foreach($district->get_all() as $data ){ 
	                ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
	              }
	              ?> 
              </div>
            </select>
            <div class="alert alert-warning no-distirct"><strong></strong></div>	
          </div> 
          </div> 
          <?php /*  
          <div class="form-group">
            <label for="office">Office Secondary Phone Number</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="phone1" name="phone2" placeholder="Enter Office Secondary Phone Number">
          </div>  
          */ ?>                                                                                  
          <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
        </form>
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("Health Facility",$output);
	}
	function scripts(){
		?>
		<script>
		$(document).ready(function(){ 
			$(".col-md-9 form").on('submit',function(){
				show_loader($);
				//$(this).find('.btn-success').prop('disabled', true);
				$(this).find('.btn-success').html('Saving...');
				console.log("test form office");
				_data = $(this).serialize();
				_this = $(this);
				$.post(window.location.href,_data, function(data){
					$("#newClientModal").modal('hide');
					if($.trim(data)=="success"){
						 console.log(data);
						$('.btn-success').prop('disabled', false);
						if($(_this).find("input[name='func']").val()=="add")
							show_alert_info("New Record Successfully Added!",$);
						else
							show_alert_info("Record Modified Successfully!",$);
							$("table").load(window.location.href+" table");
							//location.reload();
							console.log(data);
					}
					else{
                       console.log(data);
					}		
					close_loader($);								
				})
				return false;
			})
			edit_button($,this);
			delete_button($,this);	
			add_button($);
		});	
		function show_alert_info(string, $){
			$(".alert-info").fadeIn().find('strong').html(string);
			setTimeout(function(){
				$(".alert-info").fadeOut();
			},5000);
		}
        function show_alert_info_unseccessful(string, $){
            $(".alert-warning").fadeIn().find('strong').html(string);
            setTimeout(function(){
                $(".alert-warning").fadeOut();
            },5000);
        }
        function add_button($){
            $("#addClient").on('click',function(){
                $('.edit_or_add').html('Add New');
                resetForm();
            });
        }
        function delete_hc(_id){
        _data = "class=office&func=remove&id="+_id;
            $.post(window.location.href,_data, function(data){
                if($.trim(data)!="success"){
                    console.log(data);
                }
                else{
                    $("tr.focus").remove();
                }   
                $("#alert-sure-delete").fadeOut();  
            });
        }
        function check_hc_clients(_id){
            /* this function will check if Health facility has clients attacthed.
            * this is needed to not to delete the HF when it has clients attached. 
            */
            $("#alert-sure-delete").fadeOut();
            _data = "class=office&func=check_hc_clients&id="+_id;
            $.post(window.location.href,_data, function(data){
                if($.trim(data)=="has_clients"){
                    console.log(data);
                    close_loader($);
                    show_alert_info_unseccessful("Sorry you can't delete this Health Facility because there are clients attached.",$);
                }
                else{
                   console.log(data);
                   show_loader($);
                   delete_hc(_id);
                   close_loader($);
                }     
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

				/* delete function should be placed here */
                check_hc_clients(id);
				return false;
			});
            $("#alert-sure-delete .no").on('click',function(){
                id = $("tr.focus .id").data("id");
                 $("#alert-sure-delete").fadeOut();
                return false;
            });
		}
		function edit_button($,_this){
			$(_this).data('original-title','Edit Records').on('click', "a.edit",function(){
				$('.edit_or_add').html('Edit');
				 $("#newClientModal input[name='id']").remove();
				_this = $(this).parent().parent().parent();
				$("table tr").removeClass('focus');
				_this.addClass('focus');
				_this = $(this).parent().parent().parent();
				$("#newClientModal").each(function(){	
					//if ( $("input[name='id']").length<1 )
						$(this).find('form').prepend('<input type="hidden" name="id" value="'+$(_this).find('.id').data('id')+'" />');
					$(this).find("input[name='func']").val('edit');

					$(this).find("#area_name").val( $(_this).find('.name').html() );
					$(this).find("#address1").val( $(_this).find('.address').html() );
					$(this).find("#phone1").val( $(_this).find('.phone').html() );
					$(this).find("#province option").each(function(){
						if($(this).html()==$(_this).find('.province').html())
							$(this).attr('selected','selected');						
					});
					$(this).find("#parent_ids option").each(function(){
						if($(this).html()==$(_this).find('.district').html())
							$(this).attr('selected','selected');						
					});
				}); 
			});
		}
		function populate_districts(_this){
				show_loader($,"#newClientModal");
				$('.district_select').prop('disabled', false);
				$(".no-distirct").fadeOut();
				var _hc = $("select#province option").filter(":selected").html();
				$(".district_select option").remove();
			 	var _province_id = $("select#province option").filter(":selected").val();
			 	_data = "class=office&func=get_districts&province_id="+_province_id;
                console.log()
			 	$.post(window.location.href,_data, function(data){
					//console.log(data);	
					var _district = $.parseJSON(data);
					   element = ' <option value="">Select District</option>';
			            if ( _district.length > 0) {
			            	for (var i = _district.length - 1; i >= 0; i--) {
				            	element += '<option value='+_district[i]['ID']+'>'+_district[i]['area_name']+'</option>';
							};   
							$('.district_select').append(element);
			            }else{
			            	//element += '<option value=none>No District Found</option>';
			            	$(".no-distirct").fadeIn().html('No district under province selected');
			            	$("#district-form").removeClass("hide"); 
			            }
			        close_loader($,"#newClientModal");					    
				});
		}
		</script>
		<?php
	}
}