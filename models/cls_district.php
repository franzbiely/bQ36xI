<?php

class District extends DB{
	private $entry_type;
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_area";
		$this->entry_type = "district";
	}
	function get_all(){
		$_data = $_POST;

		if ($_SESSION['type'] == 'superadmin') {
			$data = $this->select("ID, area_name, description, contact, office_address, parent_ids",array("entry_type"=>$this->entry_type), 
							false, 'tbl_area', false, 'area_name', 'ASC');
		}else{

			$query = "SELECT ID, area_name, description, contact, office_address, parent_ids 
					FROM tbl_area
					WHERE entry_type = :entry_type
					AND (parent_ids = :parent_ids OR office_id = :office_id)
					ORDER BY area_name ASC";
					
					$bind_array= array("entry_type"=>$this->entry_type,"office_id"=>$_SESSION['office_id'], 'parent_ids'=>$_SESSION['province_id']);
					$stmt = $this->query($query,$bind_array);
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			/*$data = $this->select("ID, area_name, description, contact, office_address, parent_ids",
					array("entry_type"=>$this->entry_type, "parent_ids"=>$_SESSION['province_id']));*/
		}
		return $data;
			
	}
	function combine_district_province_to_district(){
		$data = $this->get_all();
		$_data = array();
		foreach ($data as $key => $data) {
			$_data[] = array("ID"=>$data['ID'], "area_name"=>$data['area_name'], "description"=>$data['description'],
							"contact"=>$data['contact'], "office_address"=>$data['office_address'], 
							"province"=>$this->get_province_name($data['parent_ids']), "parent_ids"=>$data['parent_ids']);
		}
		return $_data;
	}
	function get_contact_name($contact){
		$temp = json_decode($contact,true);
		return $temp['name'];
	}
	function get_province_name($parent_ids){
		if($parent_ids != 0){
			$province = $this->select("area_name", array("ID"=>$parent_ids), true);
			foreach ($province as $key => $value) {
				return $province['area_name'];
			}
		}else{
			return"NOT SET";
		}
	}
	function get_province_id($parent_ids){
		$office_id = json_decode($parent_ids, true);
		$office_id = $office_id['province'];
		$data = $this->select("ID", array("ID"=>$office_id),true);
		return $data['ID'];
	}
	function get_parent_ids($office_id){
		$data = $this->select("parent_ids", array("ID"=>$office_id),true);
		return $data['parent_ids'];	
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
	  $_data['parent_ids'] = $_data['province_id'];
	  $_data['contact']=json_encode(array("name"=>$_data['contact']));
	  unset($_data['province_id']);
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
		$_data['contact']=json_encode(array("name"=>$_data['contact']));
 		$_data['parent_ids'] = $_data['province_id'];
		unset($_data['province_id']);

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
	function check_district_hc(){
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
						$("table").load(window.location.href+" table");
						//location.reload();
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
				$('.edit_or_add').html('Add New');
				console.log("test now");
				$("#district_id").remove();
				$("#newClientModal input[name='func']").val('add');
				resetForm();
			});
		}
		function delete_district(_id){
        _data = "class=district&func=remove&id="+_id;
            $.post(window.location.href,_data, function(data){
                if($.trim(data)!="success"){
                }
                else{
                    $("tr.focus").remove();
                }   
                $("#alert-sure-delete").fadeOut();  
            });
        }
        function check_district_hc(_id){
            /* this function will check if Health facility has clients attacthed.
            * this is needed to not to delete the HF when it has clients attached. 
            */
            show_loader($);
            $("#alert-sure-delete").fadeOut();
           _data = "class=district&func=check_district_hc&id="+_id;
            $.post(window.location.href,_data, function(data){
                if($.trim(data)=="has_clients"){
                	close_loader($);
                    show_alert_info_unseccessful("Sorry you can't delete this District because there are Health Facilities attached.",$);
                }
                else{
                   delete_district(_id);
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
				/* delete function should be placed here */
                check_district_hc(id);
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
				$("#district_id").remove();
				_this = $(this).parent().parent().parent();
				$("table tr").removeClass('focus');
				_this.addClass('focus');
				_this = $(this).parent().parent().parent();
				$("#newClientModal").each(function(){
					$(this).find('form').prepend('<input  type="hidden" id="district_id" name="id" value="'+$(_this).find('.id').data('id')+'" />')
					$(this).find("input[name='func']").val('edit');
					$(this).find("#area_name").val( $(_this).find('.name').html() );

					$(this).find("#province_id option").each(function(){
						if($(this).html()==$(_this).find('.province').html())
						$(this).attr('selected','selected');				
					});

					/*$(this).find("#description").val( $(_this).find('.description').html() );*/
					$(this).find("#contact").val( $(_this).find('.contact').html() );
					$(this).find("#office_address").val( $(_this).find('.office_address').html() );
				});
			});
		}
		</script>
		<?php
	}
}