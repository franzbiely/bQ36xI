<?php
/*error_reporting(E_ALL); 
ini_set( 'display_errors','1');*/
class Clinic extends DB{
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_clinic";

	}
	function pagination(){
		$paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
		$query = "SELECT COUNT(*) as count FROM tbl_clinic";
		$stmt = $this->query($query,array());
		$count = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $count[0]['count'];
		$pages_count = ceil($count/ITEM_DISPLAY_COUNT);
		if($paged>1){
			echo '<a href="?page=clinics&paged='.($paged-1).'" class="prev btn btn-default">Previous</a>';
		}
		if($paged<($pages_count-1)){
			echo '<a href="?page=clinics&paged='.($paged+1).'" class="next  btn btn-default">Next</a>';
		}
		?>
		
		<?php
	}
	// function get_name($id){
	// 	$data = $this->select("clinic_name",array("ID"=>$id), true);
	// 	print_r($data);
	// 	return $data['clinic_name'];
	// }
	function get_all(){
		if ($_SESSION['type'] == 'superreporting') {
			$data = $this->select("*",array(), false,"",false,"ID","DESC");
		}else{
			//$data = $this->select("*",array("office_id"=>$_SESSION['office_id']), false,"",false,"ID","DESC");
			/*$query = "SELECT * 
					FROM tbl_clinic
					WHERE office_id = :office_id
					OR llg_id = :district
					ORDER BY clinic_name ASC";
					
					$bind_array= array("office_id"=>$_SESSION['office_id'], 'district'=>$_SESSION['district_id']);
					$stmt = $this->query($query,$bind_array);
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);*/
			if ($_SESSION['office_id'] == 65 OR $_SESSION['office_id'] == 9) {
				/* Health Care is Kagamuga. All Kagamuga records will be shown also in Hagen HC */
				$query = "SELECT * 
					FROM tbl_clinic
					WHERE office_id = :kagamuga
					OR office_id = :hagen
					ORDER BY ID DESC";
					$bind_array= array("kagamuga"=>65, 'hagen'=>9);
					$stmt = $this->query($query,$bind_array);
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}else{
				$data = $this->select("*",array("office_id"=>$_SESSION['office_id']), false,"",false,"ID","DESC");
			}		
		}

		return $data;
		//$data = $this->select("*",array("office_id"=>$_SESSION['office_id']), false,"",false,"ID","DESC",$start,ITEM_DISPLAY_COUNT);
	}
	function display_contacts($contacts){
		 //json_decode( stripslashes( $post_data ) );
	     $temp = json_decode($contacts, true);
	     if ($temp != false) {
	        $temp = implode(",",  $temp); 
	        if($temp==",") return "";
	         else return $temp;
	     }else{
	      return $contacts;
	     }
	}
	function add(){
		$_data = $_POST;
		unset($_data['class']);
		unset($_data['func']);
		$contact =array();
		$contact =  (explode("," ,$_data['contact']));
		$arr = array("office_id"=>$_SESSION['office_id']);
		$_data = array_merge($_data, $arr);
	
		if (count($contact) > 1) {
			$_data['contact']=json_encode(array("0"=>$contact[0],"1"=>$contact[1]));
		}else{
			$_data['contact']=json_encode(array("0"=>$contact[0]));
		}
		
		//unset($_data['contact']);
		$data = $this->save($_data);	
		//header("Location:?page=clinics");	
		if($data==false)
			echo "error";
		else
			echo "success";


		exit();
	}
	function edit(){
		/*$_data = $_POST;
		$id = $_data['id'];
		unset($_data['id']);
		unset($_data['class']);
		unset($_data['func']);*/
		$_data = $_POST;
		$id = $_data['id'];
		unset($_data['id']);
		unset($_data['class']);
		unset($_data['func']);
		$contact =array();
		$contact =  (explode("," ,$_data['contact']));
		if (count($contact) > 1) {
			$_data['contact']=json_encode(array("0"=>$contact[0],"1"=>$contact[1]));
		}else{
			$_data['contact']=json_encode(array("0"=>$contact[0]));
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
		$data = $this->select("ID, area_name, description, contact, office_address, parent_ids",array( "entry_type"=>"district", "parent_ids"=>$_data['province_id']), false, "tbl_area");
		 echo json_encode($data);
		exit();
	}
	function get_areaname($id){
		$data = $this->select("area_name",array("ID"=>$id), true, "tbl_area");
		return $data['area_name'];
	}
	function modal(){
		global $llg, $type, $district, $province;
		ob_start();
		?>
		<form role="form" action="" method="post">
          <input type="hidden" name="class" value="clinic" />
          <input type="hidden" name="func" value="add" />
           <span class="required_field">* <span class="required_label">required fields.</span></span>
          <div class="form-group">
            <label for="clinicname">Name</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="clinic_name" name="clinic_name" placeholder="Enter Clinic Name" required>
          </div>                      
          <div class="form-group">
            <label for="clinic_type">Type</label><span class="required_field">*</span>
            <select class="form-control" name="clinic_type" id="clinic_type" required>
              <option value="">Select Clinic Type</option>
              	<?php 
	          	$_data = $type->get_all('clinic');
	      		if($_data!=false): foreach($_data['value'] as $data ): ?>
	      			<option value="<?php echo $data ?>"><?php echo $data ?></option>	
	      		<?php endforeach; endif; ?>
            </select>
          </div>
           <div class="form-group">
            <label for="area_name">Province</label><span class="required_field">*</span>
            <select class="form-control" name="province" id="province" onchange="javascript: populate_districts(this)" required>
              <option value="">Select Clinic Province</option>
              <?php                 
              foreach($province->get_all() as $data ){ 
                ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
              }
              ?>  
            </select>
          </div> 
           <div class="div-district">
          	<div class="alert alert-warning no-distirct"><strong></strong></div> 
          </div> 
          <div class="form-group">
            <label for="officer_in_charge">Person In Charge</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="officer_in_charge" name="officer_in_charge" placeholder="Enter Clinic Person In Charge" required>
          </div> 
          <div class="form-group">
            <label for="clinictype">Contact Number</label>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="contact" name="contact" placeholder="Enter Clinic Contact Number">
          </div>                                                              
          <input style="margin-top: 20px;" type="submit" name="btn-add-clinic" id="btn-add-clinic" class="btn btn-success btn-default">
        </form>
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("Clinic",$output);
	}		
	function scripts(){
		?>
		<script>
		$(document).ready(function(){  
			$(".col-md-9 form").on('submit',function(){
				show_loader($);
				$(this).find('.btn-success').prop('disabled', true);
				$(this).find('.btn-success').html('Saving...');
				console.log("adding");
				_data = $(this).serialize();
				_this = $(this);
				$.post(window.location.href,_data, function(data){
					$("#newClientModal").modal('hide');
					if($.trim(data)!="success"){
						console.log(data);
					}
					else{
						$('.btn-success').prop('disabled', false);
						console.log(data);
						$('.btn-success').prop('disabled', false);
						if($(_this).find("input[name='func']").val()=="add")
							show_alert_info("New Record Successfully Added!",$);
						else
							show_alert_info("Record Modified Successfully!",$);
						//$("table").load(window.location.href+" table");
						$(".container table").load(window.location.href+" table");
						
					}
					close_loader($);										
				})
				return false;
			})
			
			modal_close($);
			add_button($);
			delete_button($,this);	
			edit_button($,this);
		    //populate_districts();
		});	
		function show_alert_info(string, $){
			$(".alert-info").fadeIn().find('strong').html(string);
			setTimeout(function(){
				$(".alert-info").fadeOut();
			},5000);
		}
		function modal_close($){
			$("#newClientModal, #alert-sure-delete").find(".close, .btn-default").on('click',function(){
				$("table tr").removeClass('focus');
				//location.reload();
				//$(".district-form-1").remove(); 
				$("#district-form").removeClass("hide"); 
			});
			$(".alert .btn-default, .alert .close").on('click',function(){
				$(this).parent().fadeOut();
			});
		}
		function add_button($){
			$("#addClient").on('click',function(){
				$('.edit_or_add').html('Add New');
				$("#newClientModal input[name='func']").val('add');
				$("#newClientModal input[name='id']").remove();
				resetForm();
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
				_data = "class=clinic&func=remove&id="+id;
				$.post(window.location.href,_data, function(data){
					if($.trim(data)!="success"){
						console.log(data);
					}
					else{
						$("tr.focus").remove();
					}	
					$("#alert-sure-delete").fadeOut();	
					close_loader($);	
				});
				return false;
			});
		}
		function edit_button($,_this){
			$(_this).data('original-title','Edit Records').on('click', "a.edit",function(){
				$('.edit_or_add').html('Edit');
				//$(".no-distirct")fadeOut();
				$("#newClientModal input[name='id']").remove();
				_this = $(this).parent().parent().parent();
				$("table tr").removeClass('focus');
				_this.addClass('focus');
				_this = $(this).parent().parent().parent();
				$("#newClientModal").each(function(){
					$(this).find('form').prepend('<input type="hidden" id=id" name="id" value="'+$(_this).find('.id').data('id')+'" />')
					$(this).find("input[name='func']").val('edit');
					$(this).find("#clinic_name").val( $(_this).find('.name').html() );										
					$(this).find("#clinic_type option").each(function(){
						if($(this).attr('value')==$(_this).find('.clinic_type').html())
							$(this).attr('selected','selected');						
					});
					$(this).find("#province option").each(function(){
						if($(this).html()==$(_this).find('.province').html())
							$(this).attr('selected','selected');						
					});
					$(this).find("#llg_id option").each(function(){
						if($(this).html()==$(_this).find('.district').html())
							$(this).attr('selected','selected');						
					});
					$(this).find("#location").val( $(_this).find('.location').html() );

					$(this).find("#officer_in_charge").val( $(_this).find('.officer_in_charge').html() );
					$(this).find("#contact").val( $(_this).find('.contact').html() );
				}); 
			});
		}
		function populate_districts(_this){
			show_loader($,"#newClientModal");
			$(".no-distirct").fadeOut();
				var _hc = "<?php echo $_SESSION['area_name']; ?> Health Facility";
				$('.district-form1').remove();  
				$("#district-form").addClass("hide"); 
			 	var _province_id = $("select#province option").filter(":selected").val();
			 	_data = "class=clinic&func=get_districts&province_id="+_province_id;
                console.log()
			 	$.post(window.location.href,_data, function(data){
					//console.log(data);	
					var _district = $.parseJSON(data);
					   element = '<div class="form-group district-form1" id="district-form1">';
		            	element += '<label for="area_name">Clinic District</label><span class="required_field">*</span>';
			            element += '<select class="form-control" name="llg_id" id="llg_id" required>';
			            if ( _district.length > 0) {
			            	element += '<option value="">Select District</option>';
			            	for (var i = _district.length - 1; i >= 0; i--) {
				            	element += '<option value='+_district[i]['ID']+'>'+_district[i]['area_name']+'</option>';
							}; 
							element += '</select></div>';   
							$('.div-district').append(element);
			            }else{
			            	//element += '<option value=none>No District Found</option>';
			            	$(".no-distirct").fadeIn().html('No district under province selected, now showing all districts');
			            	$("#district-form").removeClass("hide"); 
			            }
			            close_loader($,"#newClientModal");
			           
					    
				});
		}
		</script>
		<?php
	}
	
}