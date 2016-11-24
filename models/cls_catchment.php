<?php
/*error_reporting(E_ALL); 
ini_set( 'display_errors','1');*/
class Catchment extends DB{
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_catchment";

	}
	function pagination(){
		$paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
		$query = "SELECT COUNT(*) as count FROM tbl_catchment";
		$stmt = $this->query($query,array());
		$count = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $count[0]['count'];
		$pages_count = ceil($count/ITEM_DISPLAY_COUNT);
		if($paged>1){
			echo '<a href="?page=catchment&paged='.($paged-1).'" class="prev btn btn-default">Previous</a>';
		}
		if($paged<($pages_count-1)){
			echo '<a href="?page=catchment&paged='.($paged+1).'" class="next  btn btn-default">Next</a>';
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
		
		$data = $this->select("*",array("office_id"=>$_SESSION['office_id']), false,"",false,"id","DESC");
		
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
		
		$arr = array("office_id"=>$_SESSION['office_id']);
		$_data = array_merge($_data, $arr);
	
	
		$data = $this->save($_data);	
		
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
	function get_clinic_name($id){
		$data = $this->select("clinic_name",array("ID"=>$id), true, "tbl_clinic");
		return $data['clinic_name'];
	}
	function get_office_options() { 
		global $office;
		$return = '';
        if (enablea_and_disable_ele($_SESSION['type'], "generate_all_hc", $_SESSION['consultation_reports']) == true || $_SESSION['type'] == 'superreporting') {
            $return .= "<option value='0'>--[All HC]--</option>"; 
            if (enablea_and_disable_ele($_SESSION['type'], "generate_other_hc", $_SESSION['consultation_reports'])){
                $data = $office->get_all(); // access all office/HC
            }
            else { 
            	$data = $office->get_all_wsession(); 
            } // access offices/HC in current district only }
          	if($data!=false) {
              	foreach($data as $data ) {
	                if ($data['ID'] == $_SESSION['office_id']) {
	                	if (enablea_and_disable_ele($_SESSION['type'], "generate_current_hc", $_SESSION['consultation_reports'])) {
	                  		$return .= "<option value='". $data['ID'] ."'>". $data['area_name'] ."</option>\n";
	                	}
	                }else{
	                	$return .= "<option value='". $data['ID'] ."'>". $data['area_name'] ."</option>\n";
	               }
	            }
            }
        }
        echo $return;
	}
	function modal(){
		global $llg, $type, $district, $province,$clinic;
		ob_start();
		?>
		<form role="form" action="" method="post">
          <input type="hidden" name="class" value="catchment" />
          <input type="hidden" name="func" value="add" />
           <span class="required_field">* <span class="required_label">required fields.</span></span>
          <div class="form-group">
            <label for="catchment_area">Catchment Area</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control catchment_area" id="catchment_area" name="catchment_area" placeholder="Enter Catchment Area" required>
          </div>  
		<!-- <div class="form-group">
            <label for="internal_record_no">Internal Record No</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="internal_record_no" name="internal_record_no" placeholder="Enter Internal Record No">
          </div> -->
          <div class="form-group">
            <label for="national_health_facility_code">National Health Facility Code</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="national_health_facility_code" name="national_health_facility_code" placeholder="Enter National Health Facility Code" required>
          </div>  
		  
          <div class="form-group">
            <label for="clinic_id">Clinic</label><span class="required_field">*</span>
            <select class="form-control" name="clinic_id" id="clinic_id" required>
              <option value="">Select Clinic</option>
              	<?php 
	          	$_data = $clinic->get_all();
	      		if($_data!=false): foreach($_data as $data ): ?>
	      			<option value="<?php echo $data['ID'] ?>"><?php echo $data['clinic_name'] ?></option>	
	      		<?php endforeach; endif; ?>
            </select>
          </div>
          
           <div class="div-district">
          	<div class="alert alert-warning no-distirct"><strong></strong></div> 
          </div> 
                                                                     
          <input style="margin-top: 20px;" type="submit" name="btn-add-clinic" id="btn-add-clinic" class="btn btn-success btn-default">
        </form>
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("Catchment",$output);
	}	
	function get_clinic_lists() {
		$data = $this->select("*",array("office_id"=>$_POST['health_facility']), false, "tbl_clinic");
		echo json_encode($data);
		exit();
	}	
	function scripts_report() {
		?>
		<script>
		$(document).ready(function(){ 
			get_clinics();
		});
		// =============== Catchment Page > get clinic names by health facility
		function get_clinics() {
			$('#healthFacility').on('change',function(){
				var that = $(this);
				// request data sa backend based sa this.value
				_data = 'class=catchment&func=get_clinic_lists&health_facility='+$(this).find('option:selected').val();
				$.post( window.location.href, _data, function( data ) {
					element = "<div class='form-group' id='by_detail'><select class='form-control' name='id' id='id' required>";
					element += "<option value=''>--[Choose clinic]--</option>";
              		if(data!='false') {
              			JSON.parse(data).forEach(function( elem ) {
              				element += "<option value='"+ elem.ID +"'>"+ elem.clinic_name +"</option>";
              			});
              		}
              		element += "</select></div>";
              		$(that).parent().after(element);
				});
			});
		}
		</script>
		<?php
	}
	function scripts(){
		?>
		<script>
		$(document).ready(function(){ 
			$(".col-md-9 form").on('submit',function(){
				
				show_loader($);
				
				$(this).find('.btn-success').prop('disabled', true);
				$(this).find('.btn-success').html('Saving...');
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
				_data = "class=catchment&func=remove&id="+id;
				
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
					$(this).find("#catchment_area").val( $(_this).find('.name').html() );										
					$(this).find("#clinic_id option").each(function(){
						if($(this).attr('value')==$(_this).find('.clinic_id').attr("clinic_id"))
							$(this).attr('selected','selected');						
					});
				
					$(this).find("#internal_record_no").val( $(_this).find('.internal_record_no').html() );

					$(this).find("#national_health_facility_code").val( $(_this).find('.national_health_facility_code').html() );
					
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
			 	$.post(window.location.href,_data, function(data){
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