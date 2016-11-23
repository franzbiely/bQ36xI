<?php

class Province extends DB{
	private $entry_type;
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_area";
		$this->entry_type = "province";
	}
	function get_all(){
		//if ($_SESSION['type'] == 'superadmin') {
			$query = "SELECT DISTINCT ID, area_name, description, parent_ids
						FROM tbl_area
						WHERE entry_type = :en_type
						GROUP BY area_name ASC";
			$bind_array= array("en_type"=>"province");
			$stmt = $this->query($query,$bind_array);
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $data;
		/*}else{
			$data = $this->select("ID, area_name, description, parent_ids",array("entry_type"=>$this->entry_type, "office_id"=>$_SESSION['office_id']));
		}*/
		/*if($_SESSION['office_id']!=0)
			if ($_SESSION['office_id'] == 65 OR $_SESSION['office_id'] == 9) {
				$query = "SELECT ID, area_name, description, parent_ids
					FROM tbl_area
					WHERE entry_type = :en_type
					AND (office_id = :kagamuga OR office_id = :hagen)";
					$bind_array= array("kagamuga"=>65, 'hagen'=>9, "en_type"=>$this->entry_type);
					$stmt = $this->query($query,$bind_array);
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}else{
				
			}
		else*/
			
		return $data;
	}
	
	function get_office_name($parent_ids){

		if($parent_ids != 0){
			$province = $this->select("area_name", array("ID"=>$parent_ids), true);
			foreach ($province as $key => $value) {
				return $province['area_name'];
			}
		}else{
			return"NOT SET";
		}
	}
	function filter_display(&$data){
		foreach($data as $key=>$val){
			$parent_id = json_decode($val['parent_ids'], true);
			if($parent_id['office']!=$_SESSION['office_id'])
				unset($data[$key]);
		}
	}
	function add(){
		$_data = $_POST;
		unset($_data['class']);
		unset($_data['func']);			
		//$_data['parent_ids'] = $_data['office_id'];			
		
		$p_name =  $this->select("area_name", array("area_name"=>$_data['area_name'], "entry_type"=>"province"),true, "tbl_area" );
		if($p_name !=false){
			echo "double_record";
			exit();
		}else{
			$data = $this->save($_data);
			echo "success";
			exit();
		}	
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
	function check_province_districts(){
        $_data = $_POST;
        $data = $this->select("*",array("parent_ids"=>$_data['id']), false, 'tbl_area');
        if($data != false) { echo "has_clients"; exit(); }
        else{
            echo "no_clients"; exit(); 
        }
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
					if($.trim(data)=="success"){
						$("#newClientModal").modal('hide');						
						$('.btn-success').prop('disabled', false);
						$('.btn-success').prop('disabled', false);
						if($(_this).find("input[name='func']").val()=="add")
							show_alert_info("New Record Successfully Added!",$);
						else
							show_alert_info("Record Modified Successfully!",$);
						$("table").load(window.location.href+" table");console.log(data);
					}
					else if($.trim(data)=="double_record"){
						$("#errormessage").fadeIn().html("Oops! Province name already exists!");
						$("#area_name").addClass('error').focus();
						$('.btn-success').prop('disabled', false);
						console.log(data);
						
						//location.reload();
					}else{
						$("#newClientModal").modal('hide');
						console.log(data);
					}	
					close_loader($);
				})
				return false;
			})
			
			modal_close($);
			add_button($);
			delete_button($,this);	
			edit_button($,this);
		});	
		function show_alert_info(string, $){
			$(".alert-info").fadeIn().find('strong').html(string);
			setTimeout(function(){
				$(".alert-info").fadeOut();
			},3500);
		}
		function show_alert_info_unseccessful(string, $){
            $(".alert-warning").fadeIn().find('strong').html(string);
            setTimeout(function(){
                $(".alert-warning").fadeOut();
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
				$("#errormessage").fadeOut();
				$("#newClientModal input[name='id']").remove();
				$('.edit_or_add').html('Add New');
				$("#newClientModal input[name='func']").val('add');
				$("#area_name, #description").val('');
			});
		}
		function delete_province(_id){
       		_data = "class=province&func=remove&id="+_id;
            $.post(window.location.href,_data, function(data){
                if($.trim(data)!="success"){
                }
                else{
                    $("tr.focus").remove();
                }   
                $("#alert-sure-delete").fadeOut();  
            });
        }
        function check_province_districts(_id){
            /* this function will check if Health facility has clients attacthed.
            * this is needed to not to delete the HF when it has clients attached. 
            */
            $("#alert-sure-delete").fadeOut();
           _data = "class=province&func=check_province_districts&id="+_id;
            $.post(window.location.href,_data, function(data){
                if($.trim(data)=="has_clients"){
                	console.log(data);
                	close_loader($);
                    show_alert_info_unseccessful("Sorry you can't delete this Province because there are Districts attached.",$);
                }
                else{
                	console.log(data);
                	show_loader($);
                   	delete_province(_id);
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
				id = $("tr.focus .id").data("id");
				show_loader($);
				/* delete function should be placed here */
                check_province_districts(id);
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

				$("#newClientModal input[name='id']").remove();
				$('.edit_or_add').html('Edit');
				_this = $(this).parent().parent().parent();
				$("table tr").removeClass('focus');
				_this.addClass('focus');
				$("#newClientModal").each(function(){
					$("#errormessage").fadeOut();
					$(this).find('form').prepend('<input type="hidden" name="id" value="'+$(_this).find('.id').data('id')+'" />')
					$(this).find("input[name='func']").val('edit');
					$(this).find("#area_name").val( $(_this).find('.name').html() );
					$(this).find("#description").val( $(_this).find('.description').html() );
				});
			});
		}
		</script>
		<?php
	}
}