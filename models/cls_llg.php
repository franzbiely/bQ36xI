<?php

class Llg extends DB{
	private $entry_type;
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_area";
		$this->entry_type = "llg";
	}
	function get_all(){
		if($_SESSION['office_id']!=0)
			if ($_SESSION['office_id'] == 65 OR $_SESSION['office_id'] == 9) {
				$query = "SELECT ID, area_name, description, contact, office_address, parent_ids
					FROM tbl_area
					WHERE entry_type = :en_type
					AND (office_id = :kagamuga OR office_id = :hagen)";
					$bind_array= array("kagamuga"=>65, 'hagen'=>9, "en_type"=>$this->entry_type);
					$stmt = $this->query($query,$bind_array);
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}else{
				$data = $this->select("ID, area_name, description, contact, office_address, parent_ids",array("entry_type"=>$this->entry_type, "office_id"=>$_SESSION['office_id']));
			}
		else
			$data = $this->select("ID, area_name, description, contact, office_address, parent_ids",array("entry_type"=>$this->entry_type));
		return $data;		
	}
	function get_district_id($parent_ids){
		
		if($parent_ids != 0){
			$district = $this->select("area_name", array("ID"=>$parent_ids), true);
			foreach ($district as $key => $value) {
				return $district['area_name'];
			}
		}else{
			return"NOT SET";
		}
	}
	function get_office_name($parent_ids){
		$office_id = json_decode($parent_ids, true);
		$office_id = $office_id['office'];
		$data = $this->select("area_name", array("ID"=>$office_id),true);
		return $data['area_name'];
	}
	function get_councillor_name($contact){
		$data = json_decode($contact);
		return $data->name;
	}
	function get_councillor_contact($contact){
		$data = json_decode($contact);
		if (isset($data->contact)) {
			return $data->contact;
		}else{
			return "NOT SET";
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
		
		$_data['parent_ids'] =  $_data['district_id'];
		unset($_data['district_id']);
		$_data['contact']=json_encode(array("name"=>$_data['councillor_name'],"contact"=>$_data['councillor_contact']));
		unset($_data['councillor_name']);
		unset($_data['councillor_contact']);

	   	$data = $this->save($_data);
		if($data==false) echo "error";
		else echo "success";
	  	exit();
	 }
	function edit(){
		$_data = $_POST;
		$id = $_data['id'];
		unset($_data['id']);
		unset($_data['class']);
		unset($_data['func']);
		
		$_data['parent_ids'] =  $_POST['district_id'];
		$_data['contact']=json_encode(array("name"=>$_data['councillor_name'],"contact"=>$_data['councillor_contact']));

		unset($_data['district_id']);
		unset($_data['councillor_contact']);
		unset($_data['councillor_name']);

		$data = $this->save($_data,array("ID"=>$id));
		if($data==false) echo "error";
		else echo "success";			
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
	function scripts(){
		?>
		<script>
		$(document).ready(function(){  
			$(".col-md-9 form").on('submit',function(){
				console.log("adding llg");
				_data = $(this).serialize();
				_this = $(this);
				$.post(window.location.href,_data, function(data){
					$("#newClientModal").modal('hide');
					if($.trim(data)!="success"){
						console.log(data);
					}
					else{
						if($(_this).find("input[name='func']").val()=="add")
							show_alert_info("New Record Successfully Added!",$);
						else
							show_alert_info("Record Modified Successfully!",$);
						$("table").load(window.location.href+" table");
						location.reload();
					}										
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
				$("#newClientModal input[name='func']").val('add');
				$("#area_name, #description").val('');
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
				_data = "class=llg&func=remove&id="+id;
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
		function edit_button($,_this){
			$(_this).data('original-title','Edit Records').on('click', "a.edit",function(){
				_this = $(this).parent().parent().parent();
				$("table tr").removeClass('focus');
				_this.addClass('focus');
				_this = $(this).parent().parent().parent();
				$("#newClientModal").each(function(){
					$(this).find('form').prepend('<input type="hidden" name="id" value="'+$(_this).find('.id').data('id')+'" />')
					$(this).find("input[name='func']").val('edit');
					$(this).find("#area_name").val( $(_this).find('.name').html() );
					$(this).find("#description").val( $(_this).find('.description').html() );

					$(this).find("#district_id option").each(function(){
						if($(this).attr('value')==$(_this).find('.district').data('district-id'))
							$(this).attr('selected','selected');						
					});

					$(this).find("#councillor_name").val( $(_this).find('.councillor_name').html() );
					$(this).find("#councillor_contact").val( $(_this).find('.councillor_contact').html() );

				}); 
			});
		}
		</script>
		<?php
	}
}