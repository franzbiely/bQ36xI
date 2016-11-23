<?php
include("cls_db.php");

class Area extends DB{
	function add(){
		$arr[0] = array(
			"name"=>,
			"description"=>"",
			"parent_id"=>"",
			"contact"=>$con,
			"entry_type"=>"llg"
		)
		foreach($arr as $key=>$val){
			$this->save($arr[$key], null, "tbl_area");	
		}
		
	}
}

$x = new Area();
$x->add();