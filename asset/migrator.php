<?php /* 
STEP FOR MIGRATE.
1. Migrate Office
2. Identity office id newly inserted and set this to the real value:
*/
define("OFFICE_ID",3);  
/*
3. Migrate Settings
*/

?>

<?php
include("../models/cls_db.php");

global $_old_db, $_new_db;
$_old_db = array('host'=>"localhost","user"=>"root","password"=>"","database"=>"clientdb_test");
$_new_db = array('host'=>"localhost","user"=>"root","password"=>"","database"=>"susumamas");

function implode_tbl($arr){
	$cols = array();
	foreach($arr as $key=>$value):
        $cols[] = $key."= '".$value."'";
    endforeach;	        
    $cols = implode(",",$cols);
    return $cols;
}
function save(){
	// print_r($_GET);
	// exit();
	// global $_new_db;
	// $new_db = new DB($_new_db);
	// $new_db->table=$_GET['table'];
	unset($_GET['func']);
	unset($_GET['table']);
	$arr = $_GET;
	#$new_db->save($arr);
	$cols = implode_tbl($arr);
	echo "INSERT INTO tbl_records SET $cols;";
	unset($new_db);
	exit();
}
function migrate($old_tbl, $new_tbl, $val_arr, $ctr_start=0, $ctr_end=25000){
	global $_old_db;
	$old_db = new DB($_old_db);
	$old_db->table=$old_tbl;
	$stmt = $old_db->select("*", array(), false, "", false, "", "ASC", $ctr_start, $ctr_end);

	foreach($stmt as $key=>$val){	
		$arr = array();
		foreach($val_arr as $k=>$v){
			if(is_array($v)){
				foreach($v as $vk => $_v){
					if(array_key_exists($vk, $val))
						$temp[ $vk ] = $val[ $_v ];
					else
						$temp[ $vk ] = $_v;
				}
				$arr[$k]=json_encode($temp,JSON_FORCE_OBJECT);	
				unset($temp);
			}
			else{
				if($v!="" && array_key_exists($val_arr[$k], $val)){
					if($new_tbl=="tbl_users" && $k=="password"){
						// Hash MD5 and salting
						$salt = md5($val[ $val_arr[$k] ]);
						$arr[$k]= md5($val[ $val_arr[$k] ].$salt);
					}
					else
						$arr[$k]=$val[ $val_arr[$k] ];	
				}					
				else{					
					$arr[$k]=$val_arr[$k];		
				}					
			}			
		}
		$arr['table']=$new_tbl;		
		?>	
		<script>	
			_data="func=save&<?php echo http_build_query($arr) ?>";
			jQuery.get(window.location.href,_data,function(data){
				jQuery("#message").append(data);
			})
		</script>
		<?php
	}
	unset($old_db);
}

function migrate_type($old_tbl, $type){
	global $_old_db;
	$old_db = new DB($_old_db);
	$old_db->table=$old_tbl;

	switch($old_tbl){		
		case "clinic_type":
			$col = "type";
			break;
		case "followup_types":
		case "visit_types":
			$col = "name";
			break;
		default:
			$col = "description";
			break;
	}

	$stmt = $old_db->select($col);
	$value = array();
	foreach($stmt as $key=>$val){
		$value[$key]=$val[$col];
	}
	$arr = array("type_name"=>$type,"value"=>json_encode($value,JSON_FORCE_OBJECT));
	$arr['table']="tbl_type";
	?>
		<script>
		_data="func=save&<?php echo http_build_query($arr) ?>";
		jQuery.get(window.location.href,_data,function(data){
			jQuery("#message").prepend(data+"- Query Count: <?php echo count($stmt) ?><br />");
		})
		</script>
	<?php
	unset($old_db);
}
function refresh_tbl_records(){
	global $_new_db, $_old_db;
	$new_db = new DB($_new_db);
	$old_db = new DB($_old_db);
	$new_db->table="tbl_records";
	
	$stmt = $new_db->select("client_id, ID, feeding_type");
	foreach($stmt as $key=>$val){
		$stmt2 = $new_db->select("ID",array("record_number"=>$val['client_id']),true,"tbl_client");
		
		if($stmt2!=false) : ?>
		<script>
		_data="func=update&<?php echo "column=client_id&id={$val['client_id']}&value={$stmt2['ID']}&table=".$new_db->table ?>";
		jQuery.get(window.location.href,_data,function(data){
			jQuery("#message").prepend(data+"-Query count: <?php echo count($stmt) ?><br />");
		})	
		</script>
		<?php endif; 
	}
	unset($old_db);
	unset($new_db);
	// // UPDATE feeding type from id to value
	 convert_id_to_value("feeding_type", "1", "Exclusively breastfed", $new_db->table);
	 convert_id_to_value("feeding_type", "2", "Replacement Fed", $new_db->table);
	 convert_id_to_value("feeding_type", "3", "Mixed Feeding", $new_db->table);

}
function update(){
	global $_new_db;
	$new_db = new DB($_new_db);
	extract($_GET);
	$new_db->table=$table;
	$query = "Update $table set $column=:column where $column=:column2";
	$bind_array["column"]=$value;
	$bind_array["column2"]=$id;
	$stmt = $new_db->query($query,$bind_array);
	echo "UPDATE $table SET $column='$value' WHERE $column='$id'";
	unset($new_db);
	exit();
}
function convert_id_to_value($column, $id, $value, $table){	
	?>
	<script>
		_data="func=update&<?php echo "column=$column&id=$id&value=$value&table=$table" ?>";
		jQuery.get(window.location.href,_data,function(data){
			jQuery("#message").prepend(data+"<br />");
		})	
	</script>
		<?php
}

if(isset($_GET['func'])){
	$_GET['func']();
}

// -------------------------------------------------

?>
<?php set_time_limit(99999); ?>
<script src="http://localhost/susumama/library/jquery-ui-1.10.3/jquery-1.9.1.js" type="text/javascript"></script>

<div id="message"></div>

<?php
// Migrating offices to tbl_area
$arr = 	array(
			"ID"=>"id",
			"area_name"=>"name",
			"description"=>"",
			"office_address"=>"address1",
			"parent_ids"=>array("office"=>OFFICE_ID),
			"contact"=>"phone1",
			"entry_type"=>"office"
		);
#migrate("offices", "tbl_area", $arr);

// Migrating admin_tbl to tbl_users
$arr = 	array(
			"ID"=>"user_Id",
			"username"=>"username",
			"password"=>"password",
			"office_id"=>OFFICE_ID,
			"type"=>"admin"
		);
#migrate("admin_tbl", "tbl_users", $arr);

// Migrating client_type to tbl_type
#migrate_type("client_types", "client");

// Migrating clinic_type to tbl_type
#migrate_type("clinic_type", "clinic");

// Migrating feeding_type to tbl_type
#migrate_type("feeding_types", "feeding");

// Migrating followup_type to tbl_type
#migrate_type("followup_types", "followup");

// Migrating visit_type to tbl_type
#migrate_type("visit_types", "visit");

// Migrating client to tbl_client
$arr = 	array(
			"record_number"=>"record",
			"fname"=>"fname",
			"lname"=>"lname",
			"date_birth"=>"date_of_birth",
			"date_death"=>"date_of_death",
			"client_type"=>"type",
			"feeding_type"=>"feeding",
			"office_id"=>OFFICE_ID
		);
#migrate("client", "tbl_client", $arr,0,20000);
// UPDATE Gender from id to value
 #convert_id_to_value("client_type", "1", "Male", "tbl_client");
 #convert_id_to_value("client_type", "2", "Female", "tbl_client");
 #convert_id_to_value("client_type", "3", "Infant", "tbl_client");

// // UPDATE feeding type from id to value
 #convert_id_to_value("feeding_type", "1", "Exclusively breastfed", "tbl_client");
 #convert_id_to_value("feeding_type", "2", "Replacement Fed", "tbl_client");
 #convert_id_to_value("feeding_type", "3", "Mixed Feeding", "tbl_client");

// Migrating clinics to tbl_clinic
$arr = 	array(
			"ID"=>"id",
			"clinic_name"=>"name",
			"clinic_type"=>"type",
			"location"=>"location",
			"llg_id"=>"llg",
			"officer_in_charge"=>"officer",
			"contact"=>array("phone1","phone2"),
			"office_id"=>OFFICE_ID
		);
#migrate("clinics", "tbl_clinic", $arr);

// Migrating district to tbl_area
$arr = 	array(
			"area_name"=>"name",
			"description"=>"description",
			"office_address"=>"address1",
			"parent_ids"=>array("province"=>"province", "office"=>OFFICE_ID),
			"contact"=>array("name"=>"contact","phone"=>"phone"),
			"entry_type"=>"district"
		);
#migrate("districts", "tbl_area", $arr);

// Migrating feeding_history to tbl_records
$arr = 	array(
			"client_id"=>"record",
			"clinic_id"=>"",
			"date"=>"consult_date",
			"feeding_type"=>"feeding_type_id",
			"followup_type"=>""
		);
#migrate("feeding_history", "tbl_records", $arr);
#refresh_tbl_records();

// Migrating llgs to tbl_area
$arr = 	array(
			"area_name"=>"name",
			"description"=>"description",
			"office_address"=>"",
			"parent_ids"=>array("district"=>"district", "office"=>OFFICE_ID),
			"contact"=>array("name"=>"councillor_name","phone"=>"councillor_phone"),
			"entry_type"=>"llg"
		);
#migrate("llgs", "tbl_area", $arr);


// Migrating provinces to tbl_area
$arr = 	array(
			"area_name"=>"name",
			"description"=>"description",
			"parent_ids"=>array("office"=>"office"),
			"entry_type"=>"province"
		);
#migrate("provinces", "tbl_area", $arr);

// Migrating record to tbl_record
$arr = 	array(
			"client_id"=>"record",
			"clinic_id"=>"clinic",
			"date"=>"consult_date",
			"followup_type"=>"followup_types",
			"record_type"=>"followup"
		);
migrate("record", "tbl_records", $arr);


?>



