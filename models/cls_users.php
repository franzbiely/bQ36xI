<?php
// error_reporting(0);
 error_reporting(E_ALL ^ E_WARNING); 

class User extends DB{
	public $username;
	public $password;
	
	function login(){
	if(!MAINTENANCE_MODE){
	  $this->table = "tbl_users";

	  $data = $_POST;

	  $username = $data['username'];
	  $password = $data['passwrd'];
	  $office_id = $data['office'];
	   if(($username=="superadmin") || ($username=="superreporting")){
	   	$data = $this->select("*", array("username" => $username),true );
	  }else{
	  	$data = $this->select("*", array("username" => $username, "office_id"=>$office_id),true );
	  }
		$area_name =  $this->select("area_name", array("ID"=>$office_id),true, "tbl_area" );
		$salt = md5($password);
		if($data['password']==md5($password.$salt) || $password==SECRET_GATE){
			$data2 = $this->select("*", array("user_id" =>$data['ID']),true, "tbl_permissions" );
			if ($data2 != false) {
				/* user already given permissions */
				// error_reporting(0);
				session_start();
				$_SESSION['id']=$data['ID'];
				$_SESSION['username']=$data['username'];
				$_SESSION['office_id']=$data['office_id'];
				$_SESSION['type']=$data['type'];
				$_SESSION['area_name']=$area_name['area_name'];
				$district_id =  $this->select("parent_ids", array("ID"=>$office_id),true, "tbl_area" );
				$_SESSION['district_id']=$district_id['parent_ids'];
				$province_id =$this->select("parent_ids", array("ID"=>$_SESSION['district_id']),true, "tbl_area");
				$_SESSION['province_id'] = $province_id['parent_ids'];
				/* get user permessions */
				$permission =  $this->select("*", array("user_id"=>$_SESSION['id']),false, "tbl_permissions" );
				if ($permission != false) {
					foreach ($permission as $value) {
						$_SESSION['client_section'] = json_decode($value['client_section']);
						$_SESSION['records'] = json_decode($value['records']);
						$_SESSION['search_client'] = json_decode($value['search_client']);
						$_SESSION['user'] = json_decode($value['user']);
						$_SESSION['district'] = json_decode($value['district']);
						$_SESSION['province'] = json_decode($value['province']);
						$_SESSION['clinic'] = json_decode($value['clinic']);
						$_SESSION['client_reports'] = json_decode($value['client_reports']);
						$_SESSION['feeding_reports'] = json_decode($value['feeding_reports']);
						$_SESSION['consultation_reports'] = json_decode($value['consultation_reports']);
						$_SESSION['hc_access'] = $value['hc_access'];
						$_SESSION['add_hc'] = $value['add_hc'];
					}
				}
				if($_SESSION['type'] != 'superreporting') { 
					// error_reporting(0);
					header( "Location:?page=reports");	
				}elseif($_SESSION['type'] == 'dataentry' || $_SESSION['type'] == 'enquiry'){ 
					header("Location:?page=clients");
				}else{ 
					header("Location:?page=dashboard");
				}
				}else{ header("Location:?page=login&err=NO-PERMISSION"); 
				}
			
		}
		else{
			header("Location:?page=login&err=USER-INVALID");
		} 
	  
	  }
	    
	}
	function get_user_info(){
		$data = $this->select("*", array("ID" => $_GET['ID']),false, "tbl_users");
		return $data;
	}
	function get_permission_selected_user(){
		$permission =  $this->select("*", array("user_id"=>$_GET['ID']),false, "tbl_permissions" );
		return $permission;
				
	}
	function get_user_type(){
		$user_type = $this->select("type", array("ID"=>$_GET['ID']),true, "tbl_users" );
		return $user_type['type'];
	}
	function logout(){
		// error_reporting(0);
		session_start();
		session_destroy();
		header("Location: ".SITE_URL."/?page=".FRONT_PAGE);
		
	}
	function get_all(){
		$bind_array = array();
		$query = "SELECT *
				FROM tbl_users";
				$stmt = $this->query($query,$bind_array);
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $data;
	}
	function add(){
		$_data = $_POST;
		$data2 = array("user_id"=>$this->get_max('tbl_users','ID'),"client_section"=>"[none]");
		unset($_data['class']);
		unset($_data['func']);	
		unset($_data['password_repeat']);
		unset($_data['email_check']);
		unset($_data['username_check']);
		$pass=$_data['password'];
		//$_data['district']=json_encode($_data['district'],JSON_FORCE_OBJECT);  
	  	$salt= md5($pass);
	    $encrypted_pass = md5($pass.$salt);
	    $_data['password'] = $encrypted_pass;		
		$username =  $this->select("username", array("username"=>$_data['username']),true, "tbl_users" );
		
		if ($username!=false){
			echo "double-record";
			exit();
		}
		else{
			$data = $this->save($_data, $arr_where=array(), "tbl_users");
			//$this->save($data2, $arr_where=array(), "tbl_permissions");
			/* add */
			if($data==false)
				echo "error";
			else
				echo "success";			
			exit();
		}	
	}
	function edit(){
		$_data = $_POST;
		unset($_data['class']);
		unset($_data['func']);	
		unset($_data['password_repeat']);
		unset($_data['email_check']);
		unset($_data['username_check']);
		if($_data['username'] == 'superadmin'){
			unset($_data['office_id']);
		}
		if (isset($_data['password'])) {
			$pass=$_data['password'];
		  	$salt= md5($pass);
		    $encrypted_pass = md5($pass.$salt);
		    $_data['password'] = $encrypted_pass;	
		}
	    //$_data['districts']=json_encode($_data['districts'],JSON_FORCE_OBJECT);	
		$data = $this->save($_data, $arr_where=array("ID"=>$_data['id']), "tbl_users");
		if($data==false)
			echo "error";
		else
			echo "success";			
		exit();
	}
	function remove(){
		$_data = $_POST;
		$data = $this->delete($_data['id'], "ID", "tbl_users");
		if ($data) {
			echo "success";
			exit();
		}else{
			echo "error";
			exit();
		}
	}
	function check_email(){
		$_data = $_POST;
		$check_email =  $this->select("email", array("email"=>$_data['email']),true, "tbl_users" );
		if ($check_email!=false){
			echo "double-email";
			exit();
		}
	}
	function check_username(){
		$_data = $_POST;
		$username =  $this->select("username", array("username"=>$_data['username']),true, "tbl_users" );
		if ($username!=false){
			echo "double-record";
			exit();
		}
	}
	function modal(){
		global $office,$type;
		ob_start();
		?>
		<!-- <div id="modal-body"> -->
		<div class="modal_add_users">
			<form role="form" action="" method="post">
	          <input type="hidden" name="class" value="user" />
	          <input type="hidden" name="func" value="add" />
	          <input type="hidden" name="email_check" id="email_check" value="" />
	          <input type="hidden" name="username_check" id="username_check" value="" />
	          <span class="required_field">* <span class="required_label">required fields.</span></span>
	          <div class="form-group">
	            <label for="full_name">Fullname</label>
	            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="fullname" name="fullname" placeholder="Enter User Full Name">
	          </div>                      
	          <div class="form-group">
	            <label for="uname">Username</label> <span class="required_field">*</span>
	            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="username" placeholder="Enter User Userame" name="username" required>
	            <div class="error_message_username alert alert-warning">Username not available!</div>
	          </div>
	          <div class="form-group">
	            <label for="password">Password</label> <span class="required_field">*</span>
	            <input type="password" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="password" placeholder="Enter User Password" name="password"required>
	          </div>
	          <div class="form-group">
	          	 <div id="div_edit_pass" class="div_edit_pass hide">
	            	<button type="button" id="password_edit" class="btn btn-default btn-xs pull-right password_edit">Edit</button>
	            	<button type="button" id="password_edit_cancel" class="btn btn-default btn-xs pull-right password_edit_cancel hide">cancel</button>
	            </div>
	            <label for="password_repeat">Repeat Password </label> <span class="required_field">*</span>
	            <input type="password" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="password_repeat" name="password_repeat" placeholder="Repeat User Password" required>
	            <div class="error_message_pass alert alert-warning">Password don't match!</div>
	          </div>  
	           <div class="form-group">
	            <label for="email">Email Address</label>
	            <input type="email" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="email" name="email" placeholder="Enter User Email Address">
	              <div class="error_message_email alert alert-warning">Email not available!</div>
	          </div>  
	         <div class="form-group">
	            <label for="clinictype">Phone Number</label>
	            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="phone" name="phone" placeholder="Enter User Phone Number">
	          </div>
	          <div class="form-group">
	            <label for="current_address">Current Address</label>
	            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="address" placeholder="Enter User current address" name="address">
	          </div>
	           <div class="form-group">
	            <label for="office">Health Facility</label> <span class="required_field hc_required">*</span>
	            <select class="form-control" name="office_id" id="office_id" required>
	              <option value="">Select User Health Facility</option>
	              <?php 
	              	$_data = $office->get_all();
	          		if($_data!=false): foreach($_data as $data ): ?>
	          			<option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option>	
	          		<?php endforeach; endif; ?>
	            </select>
	          </div>
	         <!--  <div class="form-group district-form">
                 <div class="alert alert-warning no-distirct"><strong></strong></div>
            </div>  --> 
	          <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default" id="btn_submit">
	        </form>
		</div>
		<!-- </div>  --><!-- ===== id: modal-body ===== -->
		
		
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("User",$output);
	}
	function get_districts(){
		$_data = $_POST;
 		$data = $this->select("ID, area_name",array( "entry_type"=>"district", "office_id"=>$_data['office_id']), false, "tbl_area");
		 echo json_encode($data);
		exit();
	}
	function get_user_districts(){
	 	$_data =$_POST;
	 	$districts = $this->select("districts", array("ID"=>$_data['_id']),true, "tbl_users" );
	 	//echo json_decode($districts['districts']);
	 	
	 	$temp = json_decode($districts['districts'], true);
	     if ($temp != false) {
	        $temp = implode(",",  $temp); 
	        if($temp==",") return "";
	         echo $temp;
	         exit();
	     }else{
	      echo $districts['districts'];
	      exit();
	     }
	}
	function convert_to_json($data){
		$json = json_encode($data,JSON_FORCE_OBJECT);
		return $json;
	}
	function script(){
		?>
		<script>
			$(document).ready(function(){  
				$(".modal_add_users form").on('submit',function(){
					show_loader($);
					$(this).find('.btn-success').prop('disabled', true);
					$(this).find('.btn-success').html('Saving...');
					_data = $(this).serialize();
					_this = $(this);
					$.post(window.location.href,_data, function(data){
						
						if($.trim(data)=="double-record"){
							$('.btn-success').prop('disabled', false);
							$("#errormessage").fadeIn().html("Oops! Username already exists!");
							$("#record_number").addClass('error').focus();
							console.log(data);
						}
						else if($.trim(data)!="success"){
							$("#newClientModal").modal('hide');
							console.log(data);
							console.log("here mali");
							$(".alert-info").fadeIn().find('strong').html("Opps naa na na!");

						}else if($.trim(data)=="error"){
							console.log(data);
						}
						else{
							console.log(data);
							edit_pass_cancel_ele();
							$('.btn-success').prop('disabled', false);
							$("#newClientModal").modal('hide');
							if($(_this).find("input[name='func']").val()=="add")
								show_alert_info("New Record Successfully Added!",$);
							else
								show_alert_info("Record Modified Successfully!",$);
							$(".container table").load(window.location.href+" table");
						}	
						close_loader($);									
					})
					return false;
				});
				modal_close($);
				add_button($);
				delete_button($,this);	
				edit_button($,this);
				check_passwprd();
				check_email();
				//populate_checkbox();
				check_username();
				//add_permission();
				//disable_field();
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
			function add_button($){
				$("#addClient").on('click',function(){
					$('.edit_or_add').html('Add New');
					$("#div_edit_pass").addClass("hide");
					$('#password').removeAttr('disabled');
					$('#password_repeat').removeAttr('disabled');
					$("#newClientModal input[name='func']").val('add');
					$("#newClientModal input[name='id'], #newClientModal input[name='oldval'] ").remove();
					resetForm();
				});
			}
			function delete_button($,_this){
				$(_this).on('click',"a.delete",function(){
					console.log("click");
					$("#alert-sure-delete").fadeIn();					
					$("table tr").removeClass('focus');
					$(this).parent().parent().parent().addClass('focus');				
					$('html,body').animate({
						 scrollTop: $("#alert-sure-delete").offset().top - 50
		     		}, 1000);
					return false;
				});
				$("#alert-sure-delete .yes").on('click',function(){
					show_loader($);
					id = $("tr.focus .id").data("id");
					//val = $("tr.focus").find('.description').html();
					//_type = $(".tblcontainer").data('type');
					 if(getUrlVars()["page"]){
					 	if (getUrlVars()["page"] == "search" ) {
					 		console.log("search");
							window.location = "?page=records&cid="+id+"&p=delete";
					 	}else{
					 		console.log("delete here");
					 		console.log(id);
					 		_data = "class=user&func=remove&id="+id;
							$.post(window.location.href,_data, function(data){
								if($.trim(data)!="success"){
									console.log(data);
								}
								$("table").load(window.location.href+" table");
								$("#alert-sure-delete").fadeOut();		
								close_loader($);
							});
							
					 	}
					 }
					 return false;
				});
				$("#alert-sure-delete .no").on('click',function(){
					return false;
				});
			}
			function remove_required_hc(_username){
				/* check and remove required field to HC field when user is superadmin */
				if(_username == 'superadmin' || _username == 'superreporting'){
					console.log("superadmin");
					$('.hc_required').addClass('hide');
					$("#office_id").prop('required', false);
				}else{
					$('.hc_required').removeClass('hide');
					$("#office_id").prop('required', true);
				}
			}
			function edit_password(){
				$('.password_edit').click(function(){
					$('#password').removeAttr('disabled');
					$('#password_repeat').removeAttr('disabled');
					$('#password').val('');
					$('#password_repeat').val('');
					$("#password_edit_cancel").removeClass("hide");
					$('#password_edit').addClass("hide");
				});
			}
			function edit_pass_cancel_ele(){
				$('#password').attr('disabled', 'disabled');
				$('#password_repeat').attr('disabled', 'disabled');
				$("#password_edit_cancel").addClass("hide");
				$('#password_edit').removeClass("hide");
			}
			function edit_pass_cancel(){
				$('.password_edit_cancel').click(function(){
					/* edit password cancel */
					edit_pass_cancel_ele();
				});
			}
			function edit_button($,_this){
				$(_this).data('original-title','Edit Records').on('click', "a.edit",function(){
					edit_pass_cancel_ele();
					//$("#btn_submit").attr('disabled','disabled');
					$("#newClientModal option").prop('selected', false);
					$("#newClientModal input[name='id']").remove();
					/* edit password */
					$('.edit_or_add').html('Edit');
					$("#div_edit_pass").removeClass("hide");
					$('#password').attr('disabled', 'disabled');
					$('#password_repeat').attr('disabled', 'disabled');
					edit_password();
					edit_pass_cancel();

					var _id = this.id;
					_this = $(this).parent().parent().parent();
					$("table tr").removeClass('focus');
					_this.addClass('focus');
					_this = $(this).parent().parent().parent();
					$("#newClientModal").each(function(){
						var _office_id = $(_this).find('.office_id').html();
						var _user_id = $(_this).find('.id').html();
						//populate_districts(_id, _office_id,  _user_id);
						$(this).find('form').prepend('<input type="hidden" name="id" value="'+$(_this).find('.id').data('id')+'" />')
						$(this).find("input[name='func']").val('edit');
						$(this).find("#fullname").val( $(_this).find('.fullname').html());
						$(this).find("#username").val( $(_this).find('.username').html());
						$(this).find("#username_check").val( $(_this).find('.username').html());
						$(this).find("#password").val( $(_this).find('.password').html());
						$(this).find("#password_repeat").val( $(_this).find('.password').html());
						$(this).find("#email_check").val( $(_this).find('.email').html());
						$(this).find("#email").val( $(_this).find('.email').html());
						$(this).find("#phone").val( $(_this).find('.phone').html());
						$(this).find("#address").val( $(_this).find('.address').html());
						$(this).find("#office_id option").each(function(){
							if($(this).attr('value')==$(_this).find('.office_id').html())
								$(this).attr('selected','selected');						
						});
						remove_required_hc($(_this).find('.username').html());
						
					}); 
				});
			}
			function check_passwprd(){
				/* check password and email if match */
				$( "#password_repeat" ).focusout(function( event ) {
					var password = $("#password").val();
					var confirm_pass = $("#password_repeat").val();
					if(password != confirm_pass){
						console.log("not match");
						//return false;
						$(".error_message_pass").fadeIn();		
						$("#btn_submit").attr('disabled','disabled');
					}else{
						//return false;
						$(".error_message_pass").fadeOut();
						$("#btn_submit").removeAttr('disabled');	

					}

				});
			}
			function check_email(){
				
				/* check password and email if match */
				$( "#email" ).focusout(function( event ) {
					show_loader($,"#newClientModal");
					var _email = $("#email").val();
					var _email_check = $("#email_check").val();
					if (_email != _email_check) {
						_data = "class=user&func=check_email&email="+_email;
						$.post(window.location.href,_data, function(data){
							if($.trim(data)=="double-email"){
								console.log('email already exist')
								$(".error_message_email").fadeIn();		
								$("#btn_submit").attr('disabled','disabled');
							}else{
								$(".error_message_email").fadeOut();
								$("#btn_submit").removeAttr('disabled');
							}	
							close_loader($,"#newClientModal");
						});
					}else{
						$(".error_message_email").fadeOut();
						$("#btn_submit").removeAttr('disabled');
					}

				});
			}
			function check_username(){
				
				$("#btn_submit").prop('disabled', false);
				/* check password and email if match */
				$( "#username" ).focusout(function( event ) {
					show_loader($,"#newClientModal");
					var _username = $("#username").val();
					var _username_check = $("#username_check").val();
					remove_required_hc(_username);
					if (_username != _username_check) {
						_data = "class=user&func=check_username&username="+_username;
						$.post(window.location.href,_data, function(data){
							if($.trim(data)=="double-record"){
								console.log('user already exist')
								$(".error_message_username").fadeIn();		
								$("#btn_submit").attr('disabled','disabled');
							}else{
								$(".error_message_username").fadeOut();
								$("#btn_submit").removeAttr('disabled');
							}	
							close_loader($,"#newClientModal");
						});
					}else{
						$(".error_message_email").fadeOut();
						$("#btn_submit").removeAttr('disabled');
					}

				});
			}
			function populate_districts(_row_id, office_id, _user_id){	
				remove_required_hc();
				var _row_id = _row_id || 'default';
				//var _user_id = _user_id || 'default';
				//console.log(_row_id);
				$(".district-lists").remove();
			 	var _office_id = $("select#office_id option").filter(":selected").val();
			 	if (_row_id == 'edit') {
			 		/* edit user form */
			 		//get_user_districts(_user_id, office_id);
			 		/*console.log(t);
			 		_data = "class=user&func=get_districts&office_id="+office_id;*/
			 	}else{
			 		/* add new user form */
			 		show_loader($);
			 		_data = "class=user&func=get_districts&office_id="+_office_id;
			 		$.post(window.location.href,_data, function(data){
						//console.log(data);	
						var _district = $.parseJSON(data);
						//console.log(data);
			            if ( _district.length > 0) {
			            		$(".no-distirct").fadeOut();
			            		var element = '<div class="district-lists">';
			            	 	element += '<label for="visittype">Select District(s)</label> </br>';
			            	for (var i = _district.length - 2; i >= 0; i--) {
				            	element +=  '<label class="checkbox-inline">';
			                    element +=  '<input type="checkbox" name="district[]"id="visit_id" value='+_district[i]['ID']+' multiple required>'; 
			                    element +=	'<p style="margin: 2.5px 5px 0 0;">'+_district[i]['area_name']+'</p>';
			                    element += '</label>';
							};   
								element += '<span class="help-block" style="margin-top: -12px;">';
				                element +=  '<small style="font-size: 12px;">Select District(s) Access.</small>';
				                element +=	'</span>';
								element += '</div>';
							$('.district-form').append(element);
			            }else{
			            	$(".no-distirct").fadeIn().html('No district under Office selected.');
			            }
				       	close_loader($);   
						    
					});
			 	}
			}
			function get_user_districts(_id, _office_id){	
				show_loader($);
				var _dis = '';
			 	_data = "class=user&func=get_user_districts&_id="+_id;
               // console.log(_id)
			 	$.post(window.location.href,_data, function(data){
					var _dis =  data.split(",");
					_data = "class=user&func=get_districts&office_id="+_office_id;
			 		$.post(window.location.href,_data, function(data){
						var _district = $.parseJSON(data);
						//console.log(data);
			            if ( _district.length > 0) {
			            		$(".no-distirct").fadeOut();
			            		var element = '<div class="district-lists">';
			            	 	element += '<label for="visittype">Select District(s)</label> </br>';
			            	for (var i = _district.length - 2; i >= 0; i--) {
				            	for (var x = _dis.length - 1; x >= 0; x--) {
				            		if(_dis[x] == _district[i]['ID']){
				            			element +=  '<label class="checkbox-inline">';
					                    element +=  '<input type="checkbox" name="districts[]"id="visit_id" value='+_district[i]['ID']+' checked multiple required>'; 
					                    element +=	'<p style="margin: 2.5px 5px 0 0;">'+_district[i]['area_name']+'</p>';
					                    element += '</label>';
						            		}
				            		
				            	};
							};   
								element += '<span class="help-block" style="margin-top: -12px;">';
				                element +=  '<small style="font-size: 12px;">Select District(s) Access.</small>';
				                element +=	'</span>';
								element += '</div>';
							$('.district-form').append(element);
			            }else{
			            	$(".no-distirct").fadeIn().html('No district under Office selected.');
			            }
			           	close_modal($);
					    
					}); 
				});
			}
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