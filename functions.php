<?php


if(isset($_POST['func']) && isset($_POST['class'])){
    $class = new $_POST['class']();
    $class->$_POST['func']();    
}
else if(isset($_POST['func']) && function_exists($_POST['func'])) {
	$_POST['func']();
}
if(isset($_GET['f']) && isset($_GET['c'])){
    $class = new $_GET['c']();
    $class->$_GET['f']();    
}

// GENERAL FUNCTIONS
function has_error($err_code){
	if(isset($_GET['err']) && $_GET['err']==$err_code)
		return true;
	else
		return false;
}

function body_class(){
	$string = 'class="';

	// DETECT PAGE
	if(isset($_GET['page']))
		$string.=$_GET['page']." ";

	// DETECT BROWSER
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10')!==false) $browser = "ie10";
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9')!==false) $browser = "ie9";
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8')!==false) $browser = "ie8";
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7')!==false) $browser = "ie7";	
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')!==false) $browser = "firefox";
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')!==false) $browser = "safari";
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')!==false) $browser = "chrome";

	$string.=$browser." ";	
	
	$string = substr($string, 0,-1);
	$string .='"';

	echo $string;
}
function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}
function export_client_records(){
   require_once dirname(__FILE__) . '/models/Classes/PHPExcel.php';
      global $client;
      global $record;
      global $reports;
      extract($_POST);
      $client_info = $client->get_personal_info($_GET['cid']);
      $client_record_header = array("ID", "Feeding Type", "Consultation Date", "Clinic Attended", "Visit Reasons");
      $data = $record->get_consultation_records();
       if ($param1 == "excel") {
           $reports->generate_client_records($client_info['fname']. " " .$client_info['lname'],  $client_info['date_birth'], 
                                      $client_info['date_death'], $client_info['client_type'], $client_info['phone'],
                                        $client_record_header,  $data, 'Excel2007','Client Records.xlsx');
      }else{
           $reports->generate_client_records($client_info['fname']. " " .$client_info['lname'],  $client_info['date_birth'], 
                                      $client_info['date_death'], $client_info['client_type'], $client_info['phone'],
                                        $client_record_header,  $data, 'CSV', 'Client Records.csv');
      }
}
function export_client(){
      require_once dirname(__FILE__) . '/models/Classes/PHPExcel.php';
      global $reports;
      extract($_POST);

      if($visit_type != ""){
        $data = $reports->get_client_record($sDate,$eDate,$client_type,$visit_type,$clinic);
        $data2 = $reports->search_by_visit_reason($data, $visit_type);  

        $client_details = $reports->get_client_record_details($sDate,$eDate,$client_type,$visit_type,$clinic);
        $client_details2 = $reports->search_visit_reason_details($client_details, $visit_type);  
      }else{
         $data2 = $reports->get_client_record($sDate,$eDate,$client_type,$visit_type,$clinic);
         $client_details2 = $reports->get_client_record_details($sDate,$eDate,$client_type,$visit_type,$clinic);
      }
     
      $visit_type_report = $reports->visit_type_reports_excel($data2);

      switch($_POST['client_type']) {
        case "Male" : 
          $visit_type_reports_header =  array("Type", "Overall", " ", "Male", " "); break;
        case "Female" :
          $visit_type_reports_header =  array("Type", "Overall", " ", "Female", " "); break;
        case "Child" : 
          $visit_type_reports_header =  array("Type", "Overall", " ", "Childs", " "); break;
        default : {
          $visit_type_reports_header =  array("Type", "Overall", " ", "Male", " ", "Female", " ", "Unknown");  
        }
      }
      
      $client_record_header = array("Record Number", "Client Type", "Full Name", "Province", "District", "Health Facility", "Clinic", "Date","Visit Reasons", "Consultation", "Review Date", "Current Age");
      

      if ($param1 == "excel") {
           $reports->generate_report_client($sDate, $eDate,  $data2, $visit_type_report, $visit_type_reports_header, 
                                        $client_record_header,  $client_details2, 'Excel2007','Client Report.xlsx');
      }else{
           $reports->generate_report_client($sDate, $eDate,  $data2, $visit_type_report, $visit_type_reports_header, 
                                        $client_record_header,  $client_details2, 'CSV', 'Client Report.csv');
      }
  }
function export_consultation(){
	require_once dirname(__FILE__) . '/models/Classes/PHPExcel.php';
	global $reports;
	extract($_POST);


	 $data = $reports->get_consultation_record($sDate, $eDate, $select_by , $select_id);
	 $total_no_client = $reports->count_client($data);
	 $total_no_consul = $reports->count_report($data,array("record_type"=>"consultation"));
   $total_no_referrals = $reports->count_no_referrals($data);
	  if($total_no_consul != 0) $ave_no_consul = $total_no_consul/$total_no_client;
	 $overview_row = array("total_no_client"=>$total_no_client, 
	                        "total_no_consul"=> $total_no_consul,
                          "total_no_referrals" => $total_no_referrals,
	                        "ave_no_consul"=>round($ave_no_consul,1,PHP_ROUND_HALF_DOWN));

	 $client_record_header = array("Record Number", "Full Name", "Clinic", "Consultation");
	 $client_details= $reports->get_client_record_details($sDate, $eDate);

	  if ($param1 == "excel") {
	       $reports->generate_report_consultation($sDate, $eDate,   $overview_row, 
	                  $client_record_header , $client_details, 'Excel2007','Consultation Report.xlsx');
	  }else{
	      $reports->generate_report_consultation($sDate, $eDate,   $overview_row, 
	                  $client_record_header , $client_details, 'CSV', 'Client Report.csv');
	}
     
}
function export_catchment(){
  require_once dirname(__FILE__) . '/models/Classes/PHPExcel.php';
  global $reports;
  extract($_POST);

   $data = $reports->get_consultation_record($sDate, $eDate, $select_by , $select_id);
   $total_no_client = $reports->count_client($data);
   $total_no_consul = $reports->count_no_consultation($data);
   $total_no_referrals = $reports->count_no_referrals($data);
   if($total_no_consul != 0) $ave_no_consul = $total_no_consul/$total_no_client;
   $overview_row = array( "total_no_client"=>$total_no_client, 
                          "total_no_consul"=> $total_no_consul,
                          "total_no_referrals" => $total_no_referrals,
                          "ave_no_consul"=>round($ave_no_consul,1,PHP_ROUND_HALF_DOWN));

   $client_record_header = array("Record Number", "Full Name", "Clinic", "Consultation", "Catchment Area", "NHFC");
   $client_details= $reports->get_client_and_catchment_record_details($sDate,$eDate,$client_type,$visit_type,$select_id);
    if ($param1 == "excel") {
         $reports->generate_report_catchment($sDate, $eDate,   $overview_row, 
                    $client_record_header , $client_details, 'Excel2007','Catchment Report.xlsx');
    }else{
        $reports->generate_report_catchment($sDate, $eDate,   $overview_row, 
                    $client_record_header , $client_details, 'CSV', 'Catchment Report.csv');
  }
     
}
function export_feeding(){
	require_once dirname(__FILE__) . '/models/Classes/PHPExcel.php';
	global $reports;
	extract($_POST);
            
    $data_overview = array();
    $client_details = array();
    $client_record_header = array("Record Number", "Full Name", "Province", "District", "LLG", "Health Facility", "Clinic", "Feeding");
    if($visit_type != ""){
         $data = $reports->get_feeding_record($sDate, $eDate,  $_SESSION['office_id'], $visit_type,$clinic);
          $data2 = $reports->filter_feeding_by_visit_reason($data, $visit_type);   
    }else{
          $data2 = $reports->get_feeding_record($sDate, $eDate,  $_SESSION['office_id'], $visit_type, $clinic);
    }
    
    $under_6_exc_fed=$Under_6_rep_fed=$under_6_mixed_fed=0;
    $exc_fed=$mix_fed=$rep_fed=0;
    $under_6_exc_fed_final;$Under_6_rep_fed_final;$under_6_mixed_fed_final;

      if($data2!=false): foreach($data2 as $value ): 
        // populate client details 
      $client_details[] = array($value['record_number'],  $value['province'], $value['district'], $value['llg'],
                      $value['office'], $value['clinic_name'], $value['feeding_type']);

      if ($value['feeding_type'] == "Replacement Fed"){
        $rep_fed++; 
         $total_month = $reports->calc_feeding_type($value['date_birth'], $value['date']);
         if($total_month <= 6 AND $total_month >= 0) $Under_6_rep_fed++;
      } 
      if ($value['feeding_type'] == "Mixed Feeding"){
         $mix_fed++; 
         $total_month = $reports->calc_feeding_type($value['date_birth'], $value['date']);
         if($total_month <= 6 AND $total_month >= 0) $under_6_mixed_fed++;
      } 
      if ($value['feeding_type'] == "Exclusively breastfed"){
          $exc_fed++;
            $total_month = $reports->calc_feeding_type($value['date_birth'], $value['date']);
            if($total_month <= 6 AND $total_month >= 0)  $under_6_exc_fed++;
      } 
      //echo $data['date_birth'];
      endforeach; endif;
      if(count($data2) !=0){
          $total_exc_fed = round($exc_fed/count($data2) * 100, 2,PHP_ROUND_HALF_DOWN);
          $total_rep_fed = round($rep_fed/count($data2) * 100, 2,PHP_ROUND_HALF_DOWN);
          $total_mix_fed = round($mix_fed/count($data2) * 100, 2,PHP_ROUND_HALF_DOWN);
          $total_6_months = $Under_6_rep_fed + $under_6_mixed_fed +  $under_6_exc_fed;
          $under_6_exc_fed_final=$Under_6_rep_fed_final=$under_6_mixed_fed_final=0;
      }
     
    
    if($exc_fed !=0) if($under_6_exc_fed_final != 0) $under_6_exc_fed_final =  round($under_6_exc_fed /$total_6_months * 100 ,2,PHP_ROUND_HALF_DOWN);
    if($rep_fed !=0) if($Under_6_rep_fed_final != 0) $Under_6_rep_fed_final =  round($Under_6_rep_fed/$total_6_months * 100, 2,PHP_ROUND_HALF_DOWN);
    if($mix_fed !=0) if($under_6_mixed_fed_final) $under_6_mixed_fed_final = round($under_6_mixed_fed/$total_6_months * 100, 2,PHP_ROUND_HALF_DOWN);
     
   $data_overview = array("exc_fed"=>$total_exc_fed ,"rep_fed"=>$total_rep_fed, "mix_fed"=>$total_mix_fed,
                         "under_6_exc_fed"=>$under_6_exc_fed_final ."%" ,
                         "under_6_rep_fed"=>$Under_6_rep_fed_final. "%",
                          "under_6_mixed_fed"=>$under_6_mixed_fed_final. "%",
                       );
   if ($param1 == "excel") {
    $reports->generate_report_fedding($sDate, $eDate, $data_overview, $client_record_header , $client_details ,'Excel2007','Feeding Report.xlsx');
   }else{
    $reports->generate_report_fedding($sDate, $eDate, $data_overview, $client_record_header , $client_details ,'CSV', 'Feeding Report.csv');
      
   }
}

function export_search(){
	require_once dirname(__FILE__) . '/models/Classes/PHPExcel.php';
	global $reports;
	extract($_POST);
     $client_record_header = array("Record Number", "First Name", "Last Name","Last Consulted Clinic",
                                   "Last Consulted Date", "Client Type");    
     $client_details = json_decode($result);                     
       if ($param1 == "excel") {
           $reports-> generate_report_search($client_record_header, $client_details, 'Excel2007','Search Report.xlsx');
      		
      }else{
           $reports-> generate_report_search($client_record_header, $client_details, 'CSV', 'Search Report.csv');
      }
}
function header_nav_bar($icon, $page, $parent_page=""){

	//to display banner
	if (preg_match('/testclients.susumamas.org.pg$/', $_SERVER['HTTP_HOST'])) {
	      ?><div style="background:#EC7A7A;text-align:center ;width:30%;padding:5px;position:fixed;right:0%;top:20px;z-index:10000;overflow:hidden">This is the TEST SITE</div><?php
	  }
	//end banner here
	
	?>
	<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
	  <div class="container">
	    <div class="row">
	      <div class="navbar-header">
	        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </button>
	        <a class="navbar-brand" 
          <?php 
          if ($_SESSION['type'] != 'reporting' && 
              $_SESSION['type'] != 'superreporting' && 
              $_SESSION['type'] != 'dataentry' && 
              $_SESSION['type'] != 'enquiry') : ?>
            href="<?php echo SITE_URL ?>/?page=dashboard" 

          <?php elseif ($_SESSION['type'] == 'reporting' || 
                        $_SESSION['type'] == 'superreporting') : ?>
            href="<?php echo SITE_URL ?>/?page=reports" 

          <?php elseif ($_SESSION['type'] == 'dataentry' || 
                        $_SESSION['type'] == 'enquiry') : ?>
            href="<?php echo SITE_URL ?>/?page=clients"
          <?php endif ?>
           title="Susu Mamas | Dashboard">Susu Mamas</a>
	      </div>
	      <div class="collapse navbar-collapse">
	        <ul class="nav navbar-nav">
	        	<?php if($parent_page=="reports") : ?>
	        		<li><a href="<?php echo SITE_URL ?>/?page=reports"><span class="glyphicon glyphicon-list-alt" style="margin-right: 10px;"></span>Reports</a></li>
	        	<?php endif; ?>
	        	<?php if($parent_page=="settings") : ?>
		        	<li><a href="<?php echo SITE_URL ?>/?page=settings"><span class="glyphicon glyphicon-cog" style="margin-right: 10px;"></span>Settings</a></li>
		        <?php else: ?>
            <li class="active"><a href="#"><span class="glyphicon glyphicon-<?php echo $icon ?>" style="margin-right: 10px;"></span><?php echo $page ?></a></li>
            <?php endif; ?>
	          	<?php //echo SITE_URL ?><!-- /?page=dashboard -->
	        </ul>
	      </div><!-- /.nav-collapse -->
	    </div><!-- /.row -->
	  </div><!-- /.container -->
	</div><!-- /.navbar -->
	<?php
}
function are_you_sure_delete(){
	?>
	<div id="alert-sure-delete" class="alert alert-danger">
      <h4>Are you sure you want to delete this record?</h4>
      <a class="btn btn-danger yes" href="#">Yes, delete this one please</a> <a class="btn btn-default no" href="#">Cancel</a>
    </div>
    <?php
}
function success_message($page="", $p){
  ?>
  <div id="success_message" class="alert alert-info">
      <?php if ($page == 'records' && ($p =='update' || $p =='view') ): ?>
        <h4>Saving Changes...</h4>
         <p>Please wait...</p>
        <?php else: ?>
        <h4>Deleting record...</h4>
       <p>Redirecting you back to your previous search page.</p>
      <?php endif ?>
    </div>
    <?php
}

function enablea_and_disable_ele($user_type, $ele, $arr){
  if ($user_type != 'superadmin') {
    /* enabled all since user is superadmin */
     if (empty($arr)) {
      return false;
     }else{  
        if (in_array($ele, $arr)) {
          return true;
        }else{  return false; }
     }
  }else{
    return true;
  }
}
function check_add_user($data){
  /* function will check if user can add new HC*/
  if (is_null($data)) {
    return false;
  }else{
    if($data == 1){
      return false;
    }else{
      return true;
    }
  }
}
function check_user($user_type){
  if ($user_type == 'superadmin' || $user_type == 'reporting_user') {
      return true;
  }else { return false; }
}
function check_permission($ele, $arr){
  /* check if user has the specefic permission */
  if (in_array($ele, $arr)) {
   return true;
  }else{ return false; }
}

function personal_record($user_id1, $user_id2, $action, $user_type, $ele, $arr){
  /* used to check if usr has access to update and delete personal account */
  if ($user_id1 == $user_id2) {
      if (nablea_and_disable_ele($user_type, $ele, $arr) == false) {
          return false;
      }else{ return true; }
  }
}
function check_report($report_type1, $report_type2, $report_type3){
  /*php this will make the reports type disabled(client, consultation and feeding) based on the permission access of the logged in user */
  if ($report_type1 == false && $report_type2 == false && $report_type3 == false) {
    return false;
  }
}

function get_clinic_id(){
  global $clinic;
  $clinic_id = $clinic->select("ID", array("llg_id"=>$_SESSION['district_id']),true, "tbl_clinic" );
  return $clinic_id['ID'];
}
function get_area_name($id){
  global $office;
  $area_name = $office->select("area_name", array("ID"=>$id),true, "tbl_area" );
  return $area_name['area_name'];
}
function get_user_hc($id){
  global $user;
  $office_id = $user->select("office_id", array("ID"=>$id),true, "tbl_users" );
  return $office_id['office_id'];
}
function get_parent_ids($ids){
  global $office;
  $id = $office->select("parent_ids", array("ID"=>$ids),true, "tbl_area" );
  return $id['parent_ids'];
}

function get_datas(){
  global $clinic;
  $data = $clinic->select("llg_id",array(),false, "tbl_clinic");
  return print_r($data);
}
function array_sort($array, $on, $order=SORT_ASC){
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function loader_modal(){
  ?>
  <div class="modal fade" id="loader_modal"> 
    <div class="modal-dialog">
      <img src="<?php echo SITE_URL ?>/images/loader.gif" />
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <?php
}