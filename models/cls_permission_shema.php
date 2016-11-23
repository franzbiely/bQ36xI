<?php

class Permission extends DB{
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_permissions";

	}
	function convert_to_json($data){
		$json = json_encode($data,JSON_FORCE_OBJECT);
		return $json;
	}
	function get_office_id(){
		$office_id =  $this->select("office_id", array("ID"=>$_GET['ID']),true, "tbl_users" );
		return $office_id['office_id'];
	}
	function add_permission(){
		$_data = $_POST;
		unset($_data['class']);
		unset($_data['func']);	
		$_data2['type'] = $_data['user_type'];
		if ($_data2['type'] == 'superadmin') {
			$check_type =  $this->select("*", array("type"=>'superadmin'),true, "tbl_users" );
			if ($check_type!=false) { echo "duplicate_superadmin"; exit(); } 
		}
		if ($_data2['type'] == 'superreporting') {
			$check_type =  $this->select("*", array("type"=>'superreporting'),true, "tbl_users" );
			if ($check_type!=false) { echo "duplicate_superreporting"; exit(); }
		}
		$this->save($_data2, $arr_where=array("ID"=>$_data['user_id']), "tbl_users");
		$check =  $this->select("ID", array("user_id"=>$_data['user_id']),true, "tbl_permissions" );
		unset($_data['user_type']);
		if ($check!=false){
			$_data3 = array("client_section"=>'', "records"=>'', "search_client"=>'', "user"=>'',
							"district"=>'', "province"=>'', "clinic"=>'', "client_reports"=>'', 
							"client_reports"=>'', "feeding_reports"=>'', "hc_access"=>'', "add_hc"=>'');
			$data = $this->save($_data3, $arr_where=array("user_id"=>$_data['user_id']), "tbl_permissions");
			$data = $this->save($_data, $arr_where=array("user_id"=>$_data['user_id']), "tbl_permissions");
			if($data==false)
				echo "error";
			else
				echo "success";			
			exit();
		}
		else{
			$data = $this->save($_data, $arr_where=array(), "tbl_permissions");
			if($data==false)
				echo "error";
			else
				echo "success";			
			exit();
		}	
	}
	function script(){
		?>
		<script>
			$(document).ready(function(){  
				 var _type;
				modal_close($);
				populate_checkbox();
				add_permission();
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
				});
				$(".alert .btn-default, .alert .close").on('click',function(){
					$(this).parent().fadeOut();
				});
			}
			function clear_field(_this){
				$(this).parents(".row").find(':checkbox').each(function() {
	               	$(this).prp('checked', false);
		        });
			}
			/*function disable_field(){
				$(".permission_schema").find(':checkbox').each(function() {
	                $(this).prop('readonly', true);
		        });
		        $(".permission_schema").find(':radio').each(function() {
	                $(this).prop('readonly', true);
		        });
			}*/
			function add_permission(){
				$(".permission_schema form").on('submit',function(){
					show_loader($);
					//show_loader($); - not applicable here, for some reason it will ruin the system
					$(this).find('.btn-success').prop('disabled', true);
					$(this).find('.btn-success').html('Saving...');
					_data = $(this).serialize();
					_this = $(this);
					$.post(window.location.href,_data, function(data){
						if($.trim(data)=="success"){
							console.log(data);
							show_alert_info("New Record Successfully Added!",$);
							window.location.href = "?page=permission_schema";
							
						}else if($.trim(data)=="duplicate_superadmin"){
							show_alert_info("Error! useradmin already found!",$);
						}else if($.trim(data)=="duplicate_superreporting"){
							show_alert_info("Error! superreporting already found!",$);
						}else{ console.log(data); }		
						//close_loader($);							
					})
					return false;
				});
			}
			function populate_checkbox(){
				$('.user_type_select').on('change',function() {
					_type = this.value;
					switch (_type){
			        	case 'superadmin': 
					        $(this).parents(".row").find(':checkbox').each(function() {
				                $(this).prop('checked', true);
				            });
				            $(".can_access_hc_0").prop('checked', true);
				            $(".hc_access_option").prop('checked', true);
					        break; //end Tetris Hotel 
						case 'superreporting': 
					        user_superreporting(); 
					        break;
				        case 'admin': 
			      			user_admin(); 
			        		break;
 						case 'dataentry':
 							user_dataentry();
 							break;
						case 'enquiry': 
					        user_enquiry();
					        break;
					    case 'reporting': 
					        user_reporting();
					        break;    
						default: 
						      $(this).parents(".row").find(':checkbox').each(function() {
				                  $(this).removeAttr('checked');
				               });   
			                  break;
			        };  
			    });      
			}
			function user_admin(){
				$(".permission_form").parents(".row").find(':checkbox').each(function() {
	                $(this).prop('checked', false);
		        });
		        $(".hc_access_all").prop('checked', false);
		        $(".can_access_hc_0").prop('checked', false);
				/*client_section */
				_check(".view_current_hc"); //_disable(".view_current_hc");
				_check(".client_section_add"); //_disable(".client_section_add");
				_check(".client_section_edit");//_disable(".client_section_edit");
				_check(".client_section_delete");//_disable(".client_section_delete");

				/* records section */
				_check(".view_con_records");//_disable(".view_con_records");
				_check(".add_con_records");//_disable(".add_con_records");
				_check(".delete_con_records");//_disable(".delete_con_records");

				/* search client section */
				_check(".quick_search");//_disable(".quick_search");
				_check(".advanced_search");//_disable(".advanced_search");

				/* user section */
				_check(".view_other_profile");//_disable(".view_other_profile");
				_check(".add_new_user");//_disable(".add_new_user");
				_check(".edit_personal_record");//_disable(".edit_personal_record");
				_check(".edit_other_record");//_disable(".edit_other_record");
				_check(".delete_other_user");//_disable(".delete_other_user");
				_check(".delete_personal_record");//_disable(".delete_personal_record");

				/* district section */
				_check(".district_view");//_disable(".district_view");
				_check(".district_add");//_disable(".district_add");
				_check(".district_edit");//_disable(".district_edit");
				_check(".district_delete");//_disable(".district_delete");

				/* province section */
				_check(".province_view");
				_check(".province_add");
				_check(".province_edit");
				_check(".province_delete");

				/* clinic section */
				_check(".clinic_view");
				_check(".clinic_add");//_disable(".clinic_add");
				_check(".clinic_edit");//_disable(".clinic_edit");
				_check(".clinic_delete");//_disable(".clinic_delete");

				/* client reports section */
				_check(".client_reports_generate_current_hc");//_disable(".client_reports_generate_current_hc");
				_check(".client_reports_export_csv");//_disable(".client_reports_export_csv");
				_check(".client_reports_export_excel");//_disable(".client_reports_export_excel");

				/* consultation reports section */
				_check(".consultation_generate_current_hc");//_disable(".consultation_generate_current_hc");
				_check(".consultation_export_csv");//_disable(".consultation_export_csv");
				_check(".consultation_export_excel");//_disable(".consultation_export_excel");
				/* consultation reports section */
				_check(".feeding_generate_current_hc");//_disable(".consultation_generate_current_hc");
				_check(".feeding_export_csv");//_disable(".consultation_export_csv");
				_check(".feeding_export_excel");//_disable(".consultation_export_excel");

				/* hc access */
				_check(".can_access_hc_1");
			}
			function user_superreporting(){
				$(".permission_form").parents(".row").find(':checkbox').each(function() {
	                //$(this).removeAttr('checked');
	               	$(this).removeAttr('checked');
		        });
		       $(".permission_form").parents(".row").find(':radio').each(function() {
	                //$(this).removeAttr('checked');
	               	$(this).removeAttr('checked');
		        });
		        $(".hc_access_all").attr("checked", "checked");
			
				var _ele_check = ['.client_report_option', '.consultion_report_option', '.feeding_report_option', '.can_access_hc_1', '.hc_access_all'];			
				for (var i = _ele_check.length - 1; i >= 0; i--) {
					_check(_ele_check[i]);
				};
			}
			function user_dataentry(){
				$(".permission_form").parents(".row").find(':checkbox').each(function() {
	                $(this).prop('checked', false);
		        });
		        $(".hc_access_all").prop('checked', false);
		        $(".can_access_hc_0").prop('checked', false);
				/*client_section */
				_check(".view_current_hc"); //_disable(".view_current_hc");
				_check(".client_section_add"); //_disable(".client_section_add");
				_check(".client_section_edit");//_disable(".client_section_edit");
				_check(".client_section_delete");

				/* search client section */
				_check(".quick_search");//_disable(".quick_search");
				_check(".advanced_search");//_disable(".advanced_search");

				/* client records */
				_check(".records_option");
				/* hc access */
				_check(".can_access_hc_1");
			}
			function user_enquiry(){
				$(".permission_form").parents(".row").find(':checkbox').each(function() {
	                $(this).prop('checked', false);
		        });
		        $(".hc_access_all").prop('checked', false);
		        $(".can_access_hc_0").prop('checked', false);
				/*client_section */
				_check(".view_current_hc"); //_disable(".view_current_hc");
				/* search client section */
				_check(".quick_search");//_disable(".quick_search");
				_check(".advanced_search");//_disable(".advanced_search");

				/* client records */
				_check(".view_con_records");
				_check(".view_con_records");
				_check(".view_con_records");
				/* hc access */
				_check(".can_access_hc_1");
			}
			function user_reporting(){
				$(".permission_form").parents(".row").find(':checkbox').each(function() {
	                $(this).prop('checked', false);
		        });
		        $(".hc_access_all").prop('checked', false);
		        $(".can_access_hc_0").prop('checked', false);

				var _ele_check = ['.client_reports_generate_current_hc', '.client_reports_export_excel', '.client_reports_export_csv', 
								'.consultation_generate_current_hc','.consultation_export_csv', '.consultation_export_excel',
								'.feeding_generate_current_hc', '.feeding_export_excel',  '.feeding_export_csv',
								'.can_access_hc_1'];			
				for (var i = _ele_check.length - 1; i >= 0; i--) {
					_check(_ele_check[i]);
				};
			}
			function _check(_ele){ $(_ele).prop('checked', true); }
			function getUrlVars() {
			    var vars = {};
			    var parts =window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
			        vars[ decodeURIComponent(key)] = decodeURIComponent(value);
			    });
			    return vars;
			}
		</script>
		<?php
	}
}