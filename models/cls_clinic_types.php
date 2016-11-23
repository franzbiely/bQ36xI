<?php

class ClinicType extends DB{
	function get_all(){
		$this->table = "tbl_clinic_type";
		$data = $this->select("*"); 
		return $data;
	}
}