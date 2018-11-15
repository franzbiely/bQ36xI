<?php
/* show all errors */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//Note:  Do not include the opening and closing PHP tags when you copy this code

session_start();
set_time_limit(99999);

include("config.php");


include("models/cls_db.php");
include("models/main.php");
include("models/cls_users.php");
include("models/cls_office.php"); 
include("models/cls_clients.php"); 
include("models/cls_clinics.php");
include("models/cls_reports.php");
include("models/cls_province.php");
include("models/cls_district.php");
include("models/cls_llg.php");
include("models/cls_type.php");
include("models/cls_records.php");
include("models/cls_permission_shema.php");
include("models/cls_relationship.php");


// DECLARING THE OBJECTS
global $users, $office, $client, $clinic, $province, $district, $llg, $type, $record, $reports, $main, $permission, $relationship;

$user = new User(); 
$office = new Office();
$client = new Client();
$clinic = new Clinic();
$province = new Province();
$district = new District();
$llg = new Llg(); 
$type = new Type(); 
$record = new Records(); 
$reports = new Reports(); 
$main = new Client();
$permission = new Permission();
$relationship = new Relationship();
$current_page = isset($_GET['page']) ? $_GET['page'] : FRONT_PAGE;

include("functions.php");

Class Ajax extends DB {
	function count_anc_visit($id) {
		$query = "SELECT count(*) as count
	              FROM tbl_records 
	              WHERE client_id = :client_id AND visit_reasons LIKE '%ANC%'";
	    $bind_array['client_id'] = $id;   
	    $stmt = $this->query($query,$bind_array);
	    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    echo  json_encode($array);
	}
	function capture_data() {
		$query = "SELECT *
				  FROM tbl_fingerprint";
		 $stmt = $this->query($query,['']);
		 $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
		 echo  json_encode($array);
	}
}


if(isset($_GET['func'])) {
	$func = $_GET['func'];
	$ajax = new Ajax();
	if($func == 'count_anc_visit') {
		$ajax->count_anc_visit($_GET['client_id']);
	}
}
if(isset($_GET['capture'])) {
		$ajax = new Ajax();
		$ajax->capture_data();
}

?>