<?php
/* show all errors */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//Note:  Do not include the opening and closing PHP tags when you copy this code

session_start();

$now = time();
if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['discard_after'] = $now + 28800; // destroy session after 8 hours


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
include("models/cls_catchment.php");

// DECLARING THE OBJECTS
global $users,$catchment, $office, $client, $clinic, $province, $district, $llg, $type, $record, $reports, $main, $permission, $relationship;

$user = new User(); 
$office = new Office();
$client = new Client();
$catchment = new Catchment();
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

include("parts/header.php");
include("pages/{$current_page}.php");
include("parts/footer.php");

?>