<?php
class Type extends DB{
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_type";
	}
	function get_all($type){
		$data = $this->select("value, ID",array("type_name"=>$type),true);
		if($data==false)
			return array();
		else{
			$data['value'] = json_decode($data['value'],true);
			return $data;
		}
			
	}
	function remove(){
		$_data = $_POST;
		$type = $_data['type'];
		$all = $this->get_all($type);

		if(count($all['value'])>1){
			foreach($all['value'] as $key=>$val){
				if(trim($val) == trim($_data['val'])){
					unset($all['value'][$key]);
				}
			}
			$temp = array("value"=>json_encode($all['value'],JSON_FORCE_OBJECT));
			$data = $this->save($temp,array("type_name"=>$type));
		}
		else{
			$data = $this->delete($_data['id']);
		}
		if($data==false){
			echo "error";
		}
		else
			echo "success";
		exit();
	}
	function edit(){
		$_data = $_POST;
		$type = $_data['type'];
		$oldval = $_data['oldval'];
		$all = $this->get_all($type);
		if(isset($all['value']) && count($all['value'])>0){
			foreach($all['value'] as $key=>$val){
				if(trim($val) == trim($oldval)){
					if ($_GET['page'] == 'user_type') {
						$all['value'][$key]=strtolower($_data['value']);
					}else{ $all['value'][$key]=$_data['value']; }			
				}
			}
		}		
		unset($all['ID']);
		$temp = json_encode($all['value'], JSON_FORCE_OBJECT);
		$data = $this->save(array("value"=>$temp),array("type_name"=>$type));
		if($data==false){
			echo "error";
		}
		else
			echo "success";
			
		exit();
	}
	function add(){

		$_data = $_POST;
		unset($_data['class']);
		unset($_data['func']);		
		$type = $_data['type'];
		$all = $this->get_all($type);
		if(isset($all['value']) && count($all['value'])>0){
			$all['value']= (array)$all['value'];
			if ($_GET['page'] == 'user_type') {
				array_push($all['value'],strtolower($_data['value']));
			}else{ array_push($all['value'],$_data['value']); }
			
			$_data = array("type_name"=>$type, "value"=>json_encode($all['value'],JSON_FORCE_OBJECT));
			unset($_data['type_name']);
			$data = $this->save($_data, array("type_name"=>$type));
		}
		else{
			$_data = array("type_name"=>$type,"value"=>json_encode(array($_data['value']), JSON_FORCE_OBJECT));
			$data = $this->save($_data);
		}
		if($data==false)
			echo "error";
		else
			echo "success";
		exit();
	}
	function modal($type){
		ob_start();
		switch($type){
			default:
				?>
				<span class="required_field">* <span class="required_label">required fields.</span></span>
				<form role="form" action="" method="post">
	              <input type="hidden" name="class" value="type" />
	              <input type="hidden" name="func" value="add" />   
				            <input type="hidden" name="type" value="<?php echo $type ?>" />   
	              <div class="form-group">
	                <label for="firstname" class="type_label">Description</label><span class="required_field">*</span>
	                <input type="text" autocapitalize="off" autocorrect="off" class="form-control type_description" id="value" name="value" required>
	              </div>                    
	              <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
	            </form>
				<?php
		}
		?>
		
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container(ucwords($type)." Type",$output);
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
						if($(_this).find("input[name='func']").val()=="add")
							show_alert_info("New Record Successfully Added!",$);
						else
							show_alert_info("Record Modified Successfully!",$);
						
					}
					$(".tblcontainer").load(window.location.href+" table");
					close_loader($);
				})
				
				$("#newClientModal").modal('hide');
				$("table tr").removeClass('focus');
				return false;
			});
			
			modal_close($);
			add_button($);
			delete_button($,this);	
			edit_button($,this);
			add_field_placeholder();
			
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
		function add_field_placeholder(){
			$('.btn_client_type').click(function(){
				$('.type_description').attr('placeholder','Enter Client Gender Description');
			});
			$('.btn_clinic_type').click(function(){
				$('.type_description').attr('placeholder','Enter Clinic Type Description');
			});
			$('.btn_feeding_type').click(function(){
				$('.type_description').attr('placeholder','Enter Feeding Type Description');
			});
			$('.btn_visit_type').click(function(){
				$('.type_description').attr('placeholder','Enter Visit Type Description');
			});
			/*$('.btn_user_type').click(function(){
				$('.type_description').attr('placeholder','Enter User Type Description');
			});*/

		}
		function add_button($){
			$("#addClient").on('click',function(){
				$('.edit_or_add').html('Add New');
				$("#newClientModal input[name='func']").val('add');
				$("#newClientModal input[type='text']").val('');
				$("#newClientModal input[name='id'], #newClientModal input[name='oldval'] ").remove();
			});
		}
		function delete_button($,_this){
			$(_this).on('click',"a.delete",function(){
				console.log("click");
				$("#alert-sure-delete").fadeIn();
				$("table tr").removeClass('focus');
				$(this).parent().parent().parent().addClass('focus');				
				return false;
			});
			$("#alert-sure-delete .yes").on('click',function(){
				//show_loader($);
				id = $("tr.focus .id").data("id");
				val = $("tr.focus").find('.description').html();
				_type = $(".tblcontainer").data('type');
				console.log(_type);
				window.location = "?page=records&cid="+id+"&p=delete&f=search";
				/* if(getUrlVars()["page"]){
				 	if (getUrlVars()["page"] == "search" ) {
				 		console.log("search");
						window.location = "?page=records&cid="+id+"&p=delete";
				 	}else{
				 		_data = "class=type&func=remove&val="+val+"&type="+_type+"&id="+id;
						$.post(window.location.href,_data, function(data){
							if($.trim(data)!="success"){
								console.log(data);
							}
							$("table").load(window.location.href+" table");
							$("#alert-sure-delete").fadeOut();		
							//close_loader($);
						});
						
				 	}
				 }*/
				 return false;
			});
			$("#alert-sure-delete .no").on('click',function(){
				return false;
			});
		}
		function edit_button($,_this){
			$(_this).data('original-title','Edit Records').on('click', "a.edit",function(){
				$('.edit_or_add').html('Edit');
				_this = $(this).parent().parent().parent();
				$("table tr").removeClass('focus');
				_this.addClass('focus');
				$("#newClientModal").each(function(){
					$("input[name='id']").remove();
					$(this).find('form').prepend('<input type="hidden" name="id" value="'+$(_this).find('.id').data('id')+'" />');
					$("input[name='oldval']").remove();
					$(this).find('form').prepend('<input type="hidden" name="oldval" value="'+$(_this).find('.description').html()+'" />');					
					$(this).find("input[name='func']").val('edit');
					$(this).find("#value").val( $.trim($(_this).find('.description').html()) );
				}); 
			});
		}
		</script>
		<?php
	}
}