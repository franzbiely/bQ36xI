<?php
include("../config.php");
include("../models/cls_db.php");
if(MAINTENANCE_MODE){
	echo "Sorry, you are not allowed beyond this point. Maintenance is going on. Thank you!"; exit();
}
global $con, $db, $stmt;

$db = new DB();

define("LOG_FILE","ssm_upgrader_log.txt");


function get_queries(){

	// first:
	/*add columns to tbl_users 
	* 03-17-14
	*/
	/*$query[0]['query'] = "ALTER TABLE tbl_users ADD fullname VARCHAR( 50 ) AFTER password";	
	$query[0]['log'] = 'Add column fullname to tbl_users';	
	$query[1]['query'] = "ALTER TABLE tbl_users ADD email VARCHAR( 50 ) AFTER fullname";	
	$query[1]['log'] = 'Add column email to tbl_users';
	$query[2]['query'] = "ALTER TABLE tbl_users ADD phone VARCHAR( 25 ) AFTER email";	
	$query[2]['log'] = 'Add column phone to tbl_users';
	$query[3]['query'] = "ALTER TABLE tbl_users ADD address VARCHAR( 500 ) AFTER phone";	
	$query[3]['log'] = 'Add column address to tbl_users';	
	$query[4]['query'] = "ALTER TABLE tbl_users ADD district VARCHAR( 100 ) AFTER address";	
	$query[4]['log'] = 'Add column district to tbl_users';	*/

	// second:
	/* Add superadmin user to tbl_users */
	/*$query[0]['query'] = "INSERT INTO tbl_users (username, password, fullname, type)
							VALUES( 'superadmin', '0bf7f86d42d12bb8bbf0cd8be8d651de', 'superadmin', 'superadmin')";
	$query[0]['log'] = 'insert superadmin user n in tbl_users table';*/

	//third:
	/* add table for permission schema 
	* 03-17-14
	*/	
	/*$query[0]['query'] = "CREATE TABLE tbl_permissions(
						ID INT NOT NULL AUTO_INCREMENT, 
						PRIMARY KEY(ID),
						user_id INT (10),
						client_section VARCHAR(150),
						records VARCHAR(100),
						search_client VARCHAR(150),
						user VARCHAR (150),
						district VARCHAR (100),
						province VARCHAR (150),
						clinic VARCHAR (150),
						client_reports VARCHAR (150),
						consultation_reports VARCHAR (150),
						feeding_reports VARCHAR (150),
						hc_access VARCHAR (50),
						add_hc VARCHAR (50)
						)";
	$query[0]['log'] = 'add table tbl_permissions for permission schema function also columns';*/

	// fourth:
	/* insert permission access to user gkaadmin in tbl_permissions table */
	/*$query[0]['query'] = "INSERT INTO tbl_permissions (user_id, client_section, records,
										search_client, user, district, province, clinic,
										client_reports, consultation_reports, feeding_reports, 
										hc_access, add_hc)
							VALUES( '10',
								'[\"view_current_hc\",\"add\",\"delete\"]', 
								'[\"view_con_records\",\"add_con_records\",\"delete_con_records\"]', 
								'[\"quick_search\",\"advanced_search\"]',
								'[\"view_other_profile\",\"add_new_user\",\"edit_personal_record\",\"edit_other_record\",\"delete_other_user\",\"delete_personal_record\"]',
								'[\"view\",\"add\",\"edit\",\"delete\"]',
								'[\"view\",\"add\",\"edit\",\"delete\"]',
								'[\"view\",\"add\",\"edit\",\"delete\"]',
								'[\"generate_current_hc\",\"generate_other_hc\",\"generate_all_hc\",\"export_csv\",\"export_excel\"]',
								'[\"generate_current_hc\",\"generate_other_hc\",\"generate_all_hc\",\"export_csv\",\"export_excel\"]',
								'[\"generate_current_hc\",\"generate_other_hc\",\"generate_all_hc\",\"export_csv\",\"export_excel\"]',
								'1',
								'0')";
	$query[0]['log'] = 'insert permission access to user gkaadmin in tbl_permissions table';*/

	// fifth:
	/* update llg_id reference to district in tbl_clinic by llg_id */
	/*$ids = array(7, 8, 19, 20, 21, 22, 23, 24, 42, 43, 44, 45, 46, 47, 48, 49, 59, 73, 74, 75, 76, 77, 18, 19, 20, 33, 39, 41, 42, 43, 46, 47, 48, 50, 53, 59, 65);
	$parents = array(5, 6, 13, 14, 15 ,16 ,17, 18, 34, 34, 34, 37, 39, 39, 39, 37, 56, 68, 69, 72, 70, 71, 15, 15, 14, 14, 72, 86, 86, 86, 86, 86, 86, 86, 86, 36, 72);
	for ($i=0; $i < count($parents); $i++) { 
		$query[$i]['query'] = "UPDATE tbl_clinic SET llg_id = {$parents[$i]} WHERE llg_id = {$ids[$i]}";
		$query[$i]['log'] 	= "Update llg_id from {$ids[$i]} to {$parents[$i]}";
	}	*/

	// fifth - 2:
	/* update llg_id reference to district in tbl_clinic by ID */
	/*$ids = array(18, 19, 20, 33, 39, 41, 42, 43, 46, 47, 48, 50, 53, 59, 65);
	$parents = array(15, 15, 14, 14, 72, 86, 86, 86, 86, 86, 86, 86, 86, 36, 72);
	for ($i=0; $i < count($parents); $i++) { 
		$query[$i]['query'] = "UPDATE tbl_clinic SET llg_id = {$parents[$i]} WHERE ID = {$ids[$i]}";
		$query[$i]['log'] 	= "Update llg_id from {$ids[$i]} to {$parents[$i]}";
	}	*/

	//sixth:
	/* add column province to tbl_clinic */
	/*$query[0]['query'] = "ALTER TABLE tbl_clinic ADD province VARCHAR( 100 ) AFTER clinic_type";	
	$query[0]['log'] = 'Add column "province" to tbl_clinic';*/

	// sixth-2:
	/* update clinic type to tbl_clinic */
	/*$query[0]['query'] = "UPDATE tbl_clinic SET clinic_type = 'ANC\/PPTCT Clinic' WHERE clinic_type = 1 OR clinic_type = ''";
	$query[0]['log'] 	= "Update clinic type to ANC\/PPTCT Clinic";
	$query[1]['query'] = "UPDATE tbl_clinic SET clinic_type = 'Outreach Clinic' WHERE clinic_type = 3";
	$query[1]['log'] 	= "Update clinic type to Outreach Clinic";*/

	// sixth-3
	/* updated llg_id of clinic MHGH to 15 */
	/*$query[0]['query'] = "UPDATE tbl_clinic SET llg_id = 15 WHERE ID = 15";
	$query[0]['log'] 	= "updated llg_id of clinic MHGH to 15";*/

	// sixth-3
	/* updated llg_id of Kompiam Hospital clinic MHGH to 13 */
	/*$query[0]['query'] = "UPDATE tbl_clinic SET llg_id = 13 WHERE ID = 8";
	$query[0]['log'] 	= "updated llg_id of Kompiam Hospital clinic MHGH to 13";*/

	//seventh:
	/* add district to tbl_client */
	/*$query[0]['query'] = "UPDATE tbl_client SET district = 6 WHERE office_id = 1 AND district = 0";
	$query[0]['log'] 	= "Add district Goroka to Goroka HC";
	$query[1]['query'] = "UPDATE tbl_client SET district = 18 WHERE office_id = 9 AND district = 0";
	$query[1]['log'] 	= "Add district Hagen to Hagen HC";
	$query[2]['query'] = "UPDATE tbl_client SET district = 37 WHERE office_id = 31 AND district = 0";
	$query[2]['log'] 	= "Add district Lae District to Morobe HC";
	$query[3]['query'] = "UPDATE tbl_client SET district = 72 WHERE office_id = 64 AND district = 0";
	$query[3]['log'] 	= "Add district to Moresby HC";*/

	//eighth:
	/* update HC parent_ids */
/*	$query[0]['query'] = "UPDATE tbl_area SET parent_ids = 6 WHERE office_id = 1 AND entry_type='office'";
	$query[0]['log'] 	= "Add parent_ids to Goroka HC";
	$query[1]['query'] = "UPDATE tbl_area SET parent_ids = 18 WHERE office_id = 9 AND entry_type='office'";
	$query[1]['log'] 	= "Add parent_ids to Hagen HC";
	$query[2]['query'] = "UPDATE tbl_area SET parent_ids = 37 WHERE office_id = 31 AND entry_type='office'";
	$query[2]['log'] 	= "Add parent_ids to Morobe HC";
	$query[3]['query'] = "UPDATE tbl_area SET parent_ids = 72 WHERE office_id = 64 AND entry_type='office'";
	$query[3]['log'] 	= "Add parent_ids to Moresby HC";
	$query[4]['query'] = "UPDATE tbl_area SET parent_ids = 18 WHERE office_id = 65";
	$query[4]['log'] 	= "Add parent_ids to Kagamuga HC";*/

	// ninth
	/* delete un usefull province in tbl_area*/
	// $ids = array(61, 63, 83, 60, 51, 28, 29, 50, 62, 52);
	// for ($i=0; $i < count($ids); $i++) { 
	// 	$query[$i]['query'] = "DELETE FROM tbl_area WHERE ID = {$ids[$i]} AND entry_type = 'province'";
	// 	$query[$i]['log'] 	= "DELETE province from tbl_area where ID is {$ids[$i]}";
	// }	

	/* add user types to tbl_type */
	/*$query[0]['query'] = "INSERT INTO tbl_type (type_name, value)
							VALUES( 'user',
								'{\"0\":\"superadmin\",\"1\":\"admin\",\"2\":\"dataentry\",\"3\":\"enquiry\",\"4\":\"reporting\",\"5\":\"superreporting\"}'
								)";
	$query[0]['log'] = 'insert user types to tbl_type.';*/

	/* additional 04-28-4 */

	/* update clinic llg_ig on tbl_clinic table */
	/*$ids = array(25, 26, 30);
	for ($i=0; $i < count($ids); $i++) { 
		$query[$i]['query'] = "DELETE FROM tbl_area WHERE ID = {$ids[$i]} AND entry_type = 'district'";
		$query[$i]['log'] 	= "DELETE province from tbl_area where ID is {$ids[$i]}";
	}	*/

	/* update llg_id of clicnic in tbl_clinic table */
	/*$llgs = array(39, 34);
	$ids = array(32, 63);
	for ($i=0; $i < count($llgs); $i++) { 
		$query[$i]['query'] = "UPDATE tbl_clinic SET llg_id = {$llgs[$i]} WHERE ID = {$ids[$i]}";
		$query[$i]['log'] 	= "Update llg_id from {$ids[$i]} to {$llgs[$i]}";
	}	*/
	return $query;
}
function log_header(){
	date_default_timezone_set('Pacific/Port_Moresby');
	$str = "===[ ".date('m/d/Y h:i:s a', time())." ]=========".PHP_EOL;
	return $str;
}
function begin_transaction(){
	global $db, $con;
	$con = $db->connect();
	$con->beginTransaction();
}
function execute_query($query){	
	global $stmt, $con;
    $stmt = $con->prepare($query);
    $stmt->execute();
    return $stmt;
}

function rollBack(){
	global $con;
	$con->rollBack();
}

if(file_exists(LOG_FILE)){
	$str_log = file_get_contents(LOG_FILE);	
}
else{
	file_put_contents(LOG_FILE, "");
}
$new_str = log_header();
$steps = get_queries();
// LOOP START
try{
	begin_transaction();
	$new_str .= "Begin...".PHP_EOL;
	$ctr=0;
	foreach($steps as $step){
		$new_str .= $step["log"];	
		$result = execute_query($step['query']);
		$new_str .= " | Rows affected = ".$result->rowCount().PHP_EOL;
		if(isset($step['type']) && $step['type']=="select"){
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$new_str .= "Returned Data affected (".$step['return'].") : ";
			foreach($rows as $row){
				$new_str .= $row[$step['return']].",";
			}
			$new_str = substr($new_str, 0,-1);
			$new_str .= PHP_EOL;
		}			
		$ctr++;
	}
	$new_str .= "Success".PHP_EOL;
	
	$con->commit();
}
catch(exception $ex){
	$new_str .= "Failed... (rolling back changes)".PHP_EOL;	
	rollBack();
}
$new_str .= "---- end ---------".PHP_EOL.PHP_EOL;		
var_dump($new_str);

file_put_contents(LOG_FILE,$new_str, FILE_APPEND | LOCK_EX);
