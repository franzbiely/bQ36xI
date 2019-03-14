<?php
class Client extends DB{
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_client";
		
	}

	function get_age($date_birth){
		// GETTING THE AGE from birth date
        if($date_birth!=null || $date_birth!=""){
	        $tmp = explode('-',$date_birth);
	        $age = date("Y")-$tmp[0];
	        return $age; 
        }
        else
        return "undefined";
	}
	function get_personal_info($id){
		$data = $this->select("*",array("id"=>$id),true);
		return $data;
	}
	function pagination(){
		$paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
		$r = (isset($_GET['r'])) ? '&r='.$_GET['r'] : '';
		$query = "SELECT COUNT(*) as count FROM tbl_client";
		$stmt = $this->query($query,array());
		$count = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $count[0]['count'];
		$pages_count = ceil($count/ITEM_DISPLAY_COUNT);
		$pages_count = (isset($_GET['r'])) ? ceil($this->get_record_count()/ITEM_DISPLAY_COUNT)+1 : $pages_count;
		if($paged>1){
			echo '<a href="?page=clients&paged='.($paged-1).$r.'" class="prev btn btn-default">Previous</a>';
		}
		if($paged<($pages_count-1)){
			echo '<a href="?page=clients&paged='.($paged+1).$r.'" class="next btn btn-default">Next</a>';
		}
	}
	function get_all($paged=1){
		$start =  ($paged > 1) ? ($paged-1)*ITEM_DISPLAY_COUNT : 0;
		if ($_SESSION['type'] == 'superadmin' || $_SESSION['type'] == 'superreporting') {
			$data = $this->select("*"); 
		}else{
			$end = ITEM_DISPLAY_COUNT;
				
			$query = "SELECT c.* FROM tbl_client c WHERE c.office_id = :office_id
				ORDER BY c.ID DESC
				LIMIT $start, $end;";	
				$bind_array= array("office_id"=>$_SESSION['office_id']);
				$stmt = $this->query($query,$bind_array);
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

			/*$d1 = $this->select("*",array("office_id"=>$_SESSION['office_id']), 
					false,"",false,"ID","DESC",$start,ITEM_DISPLAY_COUNT);

			$d2 = $this->select("*",array("district"=>$_SESSION['district_id']), 
					false,"",false,"ID","DESC",$start,ITEM_DISPLAY_COUNT);
			$data = array_merge($d1, $d2);*/
		}
		if($data==false) return array();
		else return $data;
		
	}
	function get_all_unknown($paged=1){
		$start =  ($paged > 1) ? ($paged-1)*ITEM_DISPLAY_COUNT : 0;
		if ($_SESSION['type'] == 'superadmin' || $_SESSION['type'] == 'superreporting') {
			$data = $this->select("*"); 
		}else{
			$end = ITEM_DISPLAY_COUNT;
			
			$query = "SELECT DISTINCT a.*, b.*  
								FROM tbl_records as a
								INNER JOIN tbl_client as b ON b.ID = a.client_id
								INNER JOIN tbl_area AS office ON office.ID = a.office_id
								
								WHERE a.date >= :start_date AND a.date <= CURDATE()
								AND a.office_id = :office_id
								AND (b.client_type='Child' 
								OR b.date_birth ='0000-00-00')
								
								GROUP BY a.ID
								ORDER BY a.date DESC
								LIMIT $start, $end;";      
			// Only get records starting for 2019 consultations.
			$bind_array = array("start_date"=>'2019-01-01', "office_id"=>$_SESSION['office_id']);
			$stmt = $this->query($query,$bind_array);
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		if($data==false) return array();
		else return $data;
	}
	// get the count of all unknown clients
	function get_record_count($paged=1){
		$query = "SELECT COUNT(*) as count  
								FROM tbl_records as a
								INNER JOIN tbl_client as b ON b.ID = a.client_id
								INNER JOIN tbl_area AS office ON office.ID = a.office_id
								WHERE a.date >= :start_date AND a.date <= CURDATE()
								AND a.office_id = :office_id
								AND (b.client_type='Child' 
								OR b.date_birth ='0000-00-00')
								GROUP BY a.ID";   
			$bind_array = array("start_date"=>'2019-01-01', "office_id"=>$_SESSION['office_id']);
			$stmt = $this->query($query,$bind_array);
			$count = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $count[0]['count'];
	}
	function get_mother(){
		$start = ($paged==1) ? 0 : $paged*ITEM_DISPLAY_COUNT;
		if ($_SESSION['type'] == 'superadmin' || $_SESSION['type'] == 'superreporting') {
			$query = "SELECT * 
					FROM tbl_client
					WHERE client_type = 'Female'
					ORDER BY ID DESC";
										
					$stmt = $this->query($query);
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$query = "SELECT * 
					FROM tbl_client
					WHERE office_id = :office_id AND client_type = 'Female'
					ORDER BY ID DESC";
					
					$bind_array= array("office_id"=>$_SESSION['office_id']);
					$stmt = $this->query($query,$bind_array);
					$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		echo json_encode($data);
		exit();
	}
	function add(){
		$this->table = "tbl_client";
		$_data = $_POST;
		unset($_data['class']);
		unset($_data['func']);		
		$arr2 = array("office_id"=>$_SESSION['office_id']);		
		$_data = array_merge($_data, $arr2);
		if(isset($_data['is_archived'])){
			$_data['is_archived']=($_data['is_archived']=="on") ? 1 : 0;	
			$_data['date_archived']=date("m.d.y");
		}
		else{
			$_data['is_archived'] = 0;
		}

		$check_record_nuber =  $this->select("record_number", array("record_number"=>$_data['record_number']),true, "tbl_client" );
		
		if ($check_record_nuber!=false){
			echo "double-record";
			exit();
		}
		else{	
			//Code added by Joe [to fix ajax error when adding a new client record "this error will show as undefined in the network tab by pressing F12 goto network tab then response" using ajax call]
			/*if (empty($_data['review_date'])) {
				unset($_data['review_date']);
			} else {
				$d = explode('T', $_data['review_date']);
				$review_date=new DateTime($d[0] . ' ' . $d[1]);
				$_data['review_date'] = $review_date->getTimestamp();
			}*/
			$mother = '';
			//End added code here
			if($_data['client_type']=='Child'){
				$mother = $_data['relation_to'];								
			}
			unset($_data['relation_to']);	
			if($_data['date_death']=='') {
				unset($_data['date_death']);
			}		

			$data = $this->save($_data, array(), "tbl_client", "lastInsertId");
			
			if($data==false){
				echo "error";
			}else{
				if($mother!=''){
					if($_data['client_type']=='Child'){
						//$_last_id = mysql_insert_id();
						echo "last id " . $data;
						$this->table = "tbl_relationship";
						$_mother_data = array(
											'base_client' => $data,
											'relation_to' => $mother,
											'type' => 1
										);
						$data = $this->save($_mother_data);
					}
				}
				echo "success";
			}
			exit();
		}		
	}
	function edit(){
		ob_start();
		$_data = $_POST;
		$id = $_data['id'];
		unset($_data['id']);
		unset($_data['class']);
		unset($_data['func']);
		if(isset($_data['is_archived'])){
			$_data['is_archived']=($_data['is_archived']=="on") ? 1 : 0;	
			$_data['date_archived']=date("m.d.y");
		}
		else{
			$_data['is_archived'] = 0;
		}
		//Code added by Joe [to validate the value from datetimelocal value and to fixed existing but in not saving the edited client records]
		/*if (empty($_data['review_date'])) {
			unset($_data['review_date']);
		} else {
			$d = explode('T', $_data['review_date']);
			$review_date=new DateTime($d[0] . ' ' . $d[1]);
			$_data['review_date'] = $review_date->getTimestamp();
		}*/
		if(isset($_data['relation_to'])) {
			if ($_data['relation_to'] !== "undefined"){
				$relation_to = $_data['relation_to'];
			}
		}
		
		$query = "DELETE FROM tbl_relationship WHERE base_client = '$id'";
		$stmt = $this->connect();
		$data = $stmt->exec($query);
		// if($data==false){
		// 	echo ""; //"error on delete";
		// }
		// else
		// 	echo "";//"success on delete";
		if(isset($_data['relation_to'])) {
			if ($_data['relation_to'] !== "undefined"){
				unset($_data['relation_to']);
			}		
		}
		$this->table = 'tbl_client';
		$data = $this->save($_data,array("ID"=>$id));

		if($data==false){
			echo "error";
		}
		else{
			echo "success";
			// echo'<script type="text/javascript">alert();</script>';
		}

		if($_data['client_type']=='Child') {
			$_mother_data = array(
								'base_client' => $id,
								'relation_to' => $relation_to,
								'type' => 1
							);
			$this->table = "tbl_relationship";
			$data = $this->save($_mother_data);
			if($data==false){
				echo "";//"error on adding relationship";
			}
			else
				echo "";//"success on adding relationship";
		}
		//End added code here
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
	private function quick_search($data="", $key){
		$_data = $data;
		if($_SESSION['type'] == 'superreporting' || check_permission("search_other_hc", $_SESSION['search_client'])){
			$query = "SELECT DISTINCT a.$data, c.clinic_name as last_clinic, b.date as last_date 
						FROM $this->table as a, tbl_records as b, tbl_clinic as c
						WHERE (a.fname LIKE :key OR a.lname LIKE :key OR a.record_number LIKE :key) 
						AND b.clinic_id = c.ID
						AND b.client_id = a.ID";
						$bind_array= array("key"=>"%$key%");
		}
		else{
			if (check_permission("quick_search", $_SESSION['search_client'])) {
				$query = "SELECT DISTINCT b.ID, b.record_number, b.fname, b.lname, b.client_type, a.date AS last_date, c.clinic_name AS last_clinic
					FROM tbl_records AS a
					JOIN tbl_client AS b ON a.client_id = b.ID
					JOIN tbl_clinic AS c ON a.clinic_id = c.ID
					WHERE (b.fname LIKE :key OR b.lname LIKE :key OR b.record_number LIKE :key) AND c.llg_id = :district
					GROUP BY b.record_number
					ORDER BY b.record_number";
					$bind_array= array("district"=>$_SESSION['district_id'], "key"=>"$key%");
			}	
		}	
		$stmt = $this->query($query,$bind_array);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$data2 = $this->quick_search2($_data,$key);
		
		// combine $data and $data2;
			foreach($data2 as $key=>$val){
				if(!isset($data[$key]['ID'])){
					$data[$key]=$val;
					$data[$key]['last_date']="n/a";
					$data[$key]['last_clinic']="n/a";
				}
		}
		if($data==false){
			return array();			

		}
		else {
			return $data;	
		}
	}
	function search(){
		$temp = $_POST;
		if (isset($temp['quick_search'])) { 
			$_SESSION['quick'] = "yes";
			unset($_SESSION['advanced']);
			unset($_SESSION['quick_search']);
			$_SESSION['quick_search'] = $temp['quick_search'];
		}

		if(isset($temp['search'])){
			unset($advanced_data);
			unset($_SESSION['fname']);
			unset($_SESSION['lname']);
			unset($_SESSION['record_number']);
			unset($_SESSION['clinicloc']);
			unset($_SESSION['district']);
			unset($_SESSION['client_type']);
			$_SESSION['fname'] = $temp['fname'];
			$_SESSION['lname'] = $temp['lname'];
			$_SESSION['record_number'] = $temp['record_number'];
			$_SESSION['clinicloc'] = $temp['clinicloc'];
			$_SESSION['district'] = $temp['district'];
			$_SESSION['client_type'] = $temp['client_type'];
			$advanced_data = array("fname"=>$_SESSION['fname'], "lname"=>$_SESSION['lname'], "record_number"=>$_SESSION['record_number'], 
									"clinicloc"=>$_SESSION['clinicloc'], "district"=>$_SESSION['district'], "client_type"=>$_SESSION['client_type']);
			$_SESSION['advanced'] = "yes";
			unset($_SESSION['quick']);
		}
		
		if(isset($_SESSION['advanced'])){
			
			$_data = array_filter($advanced_data);
			
			unset($_data['search']);

			$where = "";
			$bind_query = array();

			if(array_key_exists("record_number", $_data)){
				$where .= "b.record_number LIKE :record_number AND ";
				$bind_query['record_number']= $_data['record_number'] ."%";
			}
			if(array_key_exists("fname", $_data)){
				$where .= "b.fname LIKE :fname AND ";
				$bind_query['fname']=$_data['fname']."%";
			}
			if(array_key_exists("lname", $_data)){
				$where .= "b.lname LIKE :lname AND ";
				$bind_query['lname']=$_data['lname']."%";
			}
			
			if(array_key_exists("clinicloc", $_data)){
				$where .= "c.ID=:clinicloc AND ";
				$bind_query['clinicloc']=$_data['clinicloc'];
			}
			if(array_key_exists("district", $_data)){
				$where .= "d.ID=:district AND ";
				$bind_query['district']=$_data['district'];
			}
			if(array_key_exists("client_type", $_data)){
				$where .= "b.client_type=:client_type AND ";
				$bind_query['client_type']=$_data['client_type'];
			}


			if($_SESSION['type'] == 'superadmin' || check_permission("search_other_hc", $_SESSION['search_client'])){
				$query = "SELECT DISTINCT b.ID, b.record_number, b.fname, b.lname, b.client_type, a.date AS last_date, c.clinic_name AS last_clinic, is_archived, date_archived
					FROM tbl_records AS a
					JOIN tbl_client AS b ON a.client_id = b.ID
					JOIN tbl_clinic AS c ON a.clinic_id = c.ID
					JOIN tbl_area AS d ON a.clinic_id = c.ID

					WHERE $where
					GROUP BY b.record_number
					ORDER BY b.record_number";

			}
			else{
				if (check_permission("advanced_search", $_SESSION['search_client'])) {
					$query = "SELECT DISTINCT b.ID, b.record_number, b.fname, b.lname, b.client_type, a.date AS last_date, c.clinic_name AS last_clinic, is_archived, date_archived
						FROM tbl_records AS a
						JOIN tbl_client AS b ON a.client_id = b.ID
						JOIN tbl_clinic AS c ON a.clinic_id = c.ID
						JOIN tbl_area AS d ON a.clinic_id = c.ID

						WHERE $where b.office_id = :office_id
						GROUP BY b.record_number
						ORDER BY b.record_number";
						$bind_query['office_id']=$_SESSION['office_id'];
				} 
		}	
			
		$stmt = $this->query($query,$bind_query);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $data;
		}
	
		elseif(isset($_SESSION['quick'])){
			$data = $this->quick_search("*", $_SESSION['quick_search']);
			return $data;
		}
		else{
			return false;
		}	

	}

	/* ========= search alternatve if search with tbl_records returns empty ======= */

	private function quick_search2($data="", $key){
		$bind_array= array();
			if ($_SESSION['type'] == 'superadmin' || check_permission("search_other_hc", $_SESSION['search_client'])) {
				/* Health Care is Kagamuga. All Kagamuga records will be shown also in Hagen HC */
				$query = "SELECT DISTINCT b.ID, b.record_number, b.fname, b.lname, b.client_type
					FROM tbl_client AS b 
					WHERE (b.fname LIKE :key OR b.lname LIKE :key OR b.record_number LIKE :key) 
					GROUP BY b.record_number
					ORDER BY b.record_number";
					$bind_array["key"]= "$key%";
			}else{
				if (check_permission("quick_search", $_SESSION['search_client'])) {
					$query = "SELECT DISTINCT b.ID, b.record_number, b.fname, b.lname, b.client_type
					FROM tbl_client AS b 
					WHERE (b.fname LIKE :key OR b.lname LIKE :key OR b.record_number LIKE :key) AND b.office_id = :office_id
					GROUP BY b.record_number
					ORDER BY b.record_number";
				$bind_array["office_id"]=$_SESSION['office_id'];
				$bind_array["key"]= "$key%";
				}
			}	
		$stmt = $this->query($query,$bind_array);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//print_r($data);
		if($data==false){
			return array();				
		}
		else {
			return $data;	
		}
	}
	function search2(){

		$temp = $_POST;
		if (isset($temp['quick_search'])) { 
			$_SESSION['quick_search'] = $temp['quick_search'];
			$_SESSION['quick'] = "yes";
			unset($_SESSION['advanced']);
		}
		if(isset($temp['search'])){
			unset($advanced_data);
			$_SESSION['fname'] = $temp['fname'];
			$_SESSION['lname'] = $temp['lname'];
			$_SESSION['record_number'] = $temp['record_number'];
			$_SESSION['clinicloc'] = $temp['clinicloc'];
			$_SESSION['district'] = $temp['district'];
			$_SESSION['client_type'] = $temp['client_type'];
			$advanced_data = array("fname"=>$_SESSION['fname'], "lname"=>$_SESSION['lname'], "record_number"=>$_SESSION['record_number'], 
									"clinicloc"=>$_SESSION['clinicloc'], "district"=>$_SESSION['district'], "client_type"=>$_SESSION['client_type']);
			$_SESSION['advanced'] = "yes";
			unset($_SESSION['quick']);
		}
		if(isset($_SESSION['advanced'])){
			
			$_data = array_filter($advanced_data);
			unset($_data['search']);

			$where = "";
			$bind_query = array();

			if(array_key_exists("fname", $_data)){
				$where .= "b.fname LIKE :fname AND ";
				$bind_query['fname']=$_data['fname']."%";
			}
			if(array_key_exists("lname", $_data)){
				$where .= "b.lname LIKE :lname AND ";
				$bind_query['lname']=$_data['lname']."%";
			}
			if(array_key_exists("record_number", $_data)){
				$where .= "b.record_number LIKE :record_number AND ";
				$bind_query['record_number']=$_data['record_number'] ."%";
				}
			if(array_key_exists("clinicloc", $_data)){
				$where .= "c.ID=:clinicloc AND ";
				$bind_query['clinicloc']=$_data['clinicloc'];
			}
			if(array_key_exists("district", $_data)){
				$where .= "d.ID=:district AND ";
				$bind_query['district']=$_data['district'];
			}
			if(array_key_exists("client_type", $_data)){
				$where .= "b.client_type=:client_type AND ";
				$bind_query['client_type']=$_data['client_type'];
			}

			if($_SESSION['office_id']!=0){
				if ($_SESSION['office_id'] == 65 OR $_SESSION['office_id'] == 9) {
					$query = "SELECT DISTINCT b.ID, b.record_number, b.fname, b.lname, b.client_type
						FROM tbl_client AS b
						WHERE $where (b.office_id = :kagamuga OR b.office_id = :hagen)
						GROUP BY b.record_number
						ORDER BY b.record_number";
						$bind_query['kagamuga']=65;	
						$bind_query['hagen']=9;				
						$stmt = $this->query($query,$bind_query);
						$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
						return $data;
				}else{
						
						$query = "SELECT DISTINCT b.ID, b.record_number, b.fname, b.lname, b.client_type
						FROM tbl_client AS b
						WHERE $where b.office_id = :office_id
						GROUP BY b.record_number
						ORDER BY b.record_number";
						$bind_query['office_id']=$_SESSION['office_id'];				
						$stmt = $this->query($query,$bind_query);
						$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
						return $data;
				}
			}
		}
	
		elseif(isset($_SESSION['quick'])){
			$data = $this->quick_search2("*",$_SESSION['quick_search']);
			return $data;
		}
		else{
			return false;
		}	

	}

	function modal() {
		global $type;
		global $district;
		global $province;
		ob_start();
		?>
		<!-- <div id="modal-body"> -->
		<form role="form" action="" method="post">
		
          <input type="hidden" name="class" value="client" />
          <input type="hidden" name="func" value="add" />
          <span class="required_field">* <span class="required_label">required fields.</span></span>
          <div class="form-group">
            <label for="recordnumber">Record Number</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="record_number" name="record_number" placeholder="Enter Client Record Number" required>
          </div>                      
          <div class="form-group">
            <label for="firstname">First Name</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="fname" placeholder="Enter Client First Name" name="fname" required>
          </div>
          <div class="form-group">
            <label for="lastname">Last Name</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="lname" placeholder="Enter Client Last Name" name="lname" required>
          </div>
          <div class="form-group">
            <label for="birthdate">Birth Date</label><span class="required_field">*</span>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="date_birth" name="date_birth" placeholder="Enter Client Birth Date" required>
          	<!-- Code added by Eric -->
          	<div class="alert alert-warning birthdate-warning"><strong></strong></div> 
          	<!-- End added code here -->
          </div>  
          <div class="form-group">
            <label for="deathdate">Date of Death</label>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control datepicker bdeath" id="date_death" name="date_death" placeholder="Enter Client Date of Death">
          	<!-- Code added by Eric -->
          	<div class="alert alert-warning deathdate-warning"><strong></strong></div> 
          	<!-- End added code here -->
          </div> 
          <div class="form-group">
            <label for="clinictype">Gender</label><span class="required_field">*</span>
            <select class="form-control" name="client_type" id="client_type" required>
              <option value="">Select Client Gender</option>
              	<?php 
              	$_data = $type->get_all('client');
          		if($_data!=false): foreach($_data['value'] as $data ): 
          			if($data != 'Child') : ?>
          			<option value="<?php echo $data ?>"><?php echo $data ?></option>	
          		<?php endif; endforeach; endif; ?>
            </select>
            <span style="font-size:11px"></span>
          </div>
         <div class="form-group showonchildtype" style="display:none">
            <label for="relation_to">Mother Client ID</label><span class="required_field">*</span>
            <input type="text" class="form-control" name="relation_to" id="relation_to" placeholder="Enter Mother Client ID" />
          </div>  
         <div class="form-group">
            <label for="clinictype">Phone Number</label>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="phone" name="phone" placeholder="Enter Client Phone Number">
          </div>
          <div class="form-group">
            <label for="place_of_birth">Place of birth</label>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="place_of_birth" placeholder="Enter Client Place of birth" name="place_of_birth">
          </div>
           <div class="form-group">
            <label for="area_name">Client Province (where client currently resides)</label> <span class="required_field">*</span>
            <select class="form-control" name="province" id="province" onchange="javascript: populate_districts(this)" required>
              <option value="">Select Client Province</option>
              <?php                 
              foreach($province->get_all() as $data ){ 
                ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
              }
              ?>  
            </select>
          </div> 

          <div class="div-district">
          	<div class="alert alert-warning no-distirct"><strong></strong></div> 
          </div> 
           <div class="form-group">
            <label for="current_address">Client Address (where client currently resides)</label>
            <input type="text" autocapitalize="off" autocorrect="off" autocomplete="off" class="form-control" id="current_address" placeholder="Enter Client current address" name="current_address">
          </div>

					<?php if (isset($_GET['modal']) && $_GET['modal']  === "add" ){ ?> 
						<div class="form-group edit_only hide">  
							<input type="checkbox" name="is_archived" id="is_archived" style="top: 2px;position: relative;">
							<label for="is_archived" style="font-weight:normal;">Check this if you want to archive this client record. </label>
						</div>
					<?php } else {?>
						<div class="form-group edit_only">  
							<input type="checkbox" name="is_archived" id="is_archived" style="top: 2px;position: relative;">
							<label for="is_archived" style="font-weight:normal;">Check this if you want to archive this client record. </label>
						</div>
					<?php }?>
          <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
        </form>
		<!-- </div>  --><!-- ===== id: modal-body ===== -->
		
		
		<?php
	    $output = ob_get_contents();
	    ob_end_clean();
	    modal_container("Client",$output);
	}	
	function scripts(){
		?>
		<script>
		$(document).ready(function(){
			//Code added by Eric
			var hold_dob = ""; //this will hold the previous birthdate value
			$("#date_birth").click(function(){
				if($("#date_birth").val() != ""){
					hold_dob = $("#date_birth").val();
				}
			});
			//End added code here

			$("#date_birth").on( 'change', function(){
				var dob = new Date($(this).val());

				//Code added by Eric
		    	if(dob > Date.now()){
		    		$("#date_birth").val('');
		    		alert("Please set a birthdate that is not in the future.");
		    		$(".birthdate-warning").fadeIn().html('Please set a birthdate that is not in the future.');
		    		$("#date_birth").val(hold_dob);
		    		return false;
		    	} else {
		    		$(".birthdate-warning").fadeOut();
		    	}
		    	// End added code Here

				var ageDifMs = Date.now() - dob.getTime();
				var ageDate = new Date(ageDifMs); // miliseconds from epoch
				var age = Math.abs(ageDate.getUTCFullYear() - 1970);

				//if(age >= 3){
					//$("#client_type option[value='Child']").remove();
				//}else{

					//if( $("#client_type option[value='Child']").length <= 0 ){
					//	$("#client_type").append('<option value="Child">Child</option>');
					//}
				//}
			});

			//Code added by Eric
		      var hold_dod = ""; //this will hold the previous death date value [date of death]
		      $("#date_death").click(function(){
		        if($("#date_death").val() != ""){
		          hold_dod = $("#date_death").val();
		        }
		      });

		      $("#date_death").on( 'change', function(){
		        var dod = new Date($(this).val());
		        //Code added by Eric
		          if(dod > Date.now()){
		            $("#date_death").val('');
		            alert("Please set date of death that is not in the future.");
		            $(".deathdate-warning").fadeIn().html('Please set date of death that is not in the future.');
		            $("#date_death").val(hold_dod);
		            return false;
		          } else {
		            $(".deathdate-warning").fadeOut();
		          }
		      });
		      //End added code here
		    $('#date_birth').on('change', function() {
		    	var dob = new Date($("#date_birth").val());
				var ageDifMs = Date.now() - dob.getTime();
				var ageDate = new Date(ageDifMs); // miliseconds from epoch
				var age = Math.abs(ageDate.getUTCFullYear() - 1970);
				if(age <= 15) { // for child only
					$(".showonchildtype").css("display", "block");
				}
				else {
					$(".showonchildtype").css("display", "none");
					$("#relationship").removeAttr("required");
				}
		    });

			$(document).on('submit',".col-md-9 form",function(){
				show_loader($);
				$(this).find('.btn-success').prop('disabled', true);
				$(this).find('.btn-success').html('Saving...');
			    disbled_submit_button(this); // make submit button disabled when user submits a form. solved the porblem of doubled data when submit. refer to js/global_script.js
				_data = $(this).serialize();
				_this = $(this);
				$.post(window.location.href,_data, function(data){
					
					if($.trim(data)=="double-record"){
						$('.btn-success').prop('disabled', false);
						$("#errormessage").fadeIn().html("Oops! Record number already exists!");
						$("#record_number").addClass('error').focus();
						console.log(data);
					}
					else if($.trim(data)=="success"){
						$("#newClientModal").modal('hide');
						console.log(data);						
						if($(_this).find("input[name='func']").val()=="add"){
							show_alert_info("New Record Successfully Added!",$);
						}
						else{
							show_alert_info("Record Modified Successfully!",$);
						}						
					}
					else { $("#newClientModal").modal('hide'); console.log(data); }	
					$(".container table").load(window.location.href+" table",function(){
						close_loader($);
					});
					$('.btn-success').prop('disabled', false);	
													
				})
				return false;
			});

			$(".check_records").on('click',function(){
				show_loader($);
			});
			form($);
			modal_close($);
			add_button($);
			delete_button($,this);	
			edit_button($,this);
			quick_search($);
			empty_bdeath_field();

			//edit_toggle($);
		});	
		function empty_bdeath_field(){
			$('#date_death').on('keypress', function() {
				   //code to be executed
				 }).on('keydown', function(e) {
				   if (e.keyCode==8)
				      $("#newClientModal input[name='date_death']").val('');
				  	  $('.ui-datepicker').hide();
					  return false;
				 });
		}
		function form($){
			$("#record_number").on('keydown',function(){
				$(this).removeClass('error');
				$("#errormessage").fadeOut();
			})
		}
		function show_alert_info(string, $){
			$(".alert-info").fadeIn().find('strong').html(string);
			setTimeout(function(){
				$(".alert-info").fadeOut();
			},5000);
		}
		
		$('#newClientModal').on('hidden.bs.modal', function () {
			// do something…
			$("#client_type").next().html("");
		});
		
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
				$("#newClientModal input[name='func']").val('add');
				$("#newClientModal input[name='id']").remove();
				$("#is_archived").removeAttr('checked');
				resetForm();
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
				show_loader($);
				id = $("tr.focus .id").data("id");
				window.location = "?page=records&cid="+id+"&p=delete&f=clients";
				_data = "class=client&func=remove&id="+id;
				//window.location = "?page=records&cid="+id+"&p=delete";
				/*$.post(window.location.href,_data, function(data){
					if($.trim(data)!="success"){
						console.log(data);
					}
					else{
						 $("tr.focus").remove();
						$("table").load(window.location.href+" table");
					}	
					$("#alert-sure-delete").fadeOut();
					close_loader($);		
				});*/
				return false;
			});
		}
		/*Code added by Joe [ to clear the input element before the modal is showed]
			resetForm(), 
			$(this).find('form').prepend('<input type="hidden" id="client_id" name="id" value="'+ $(_this).find('.id').data('id') +'" />')]*/
		function edit_button($,_this){
			$(_this).data('original-title','Edit Records').on('click', "a.edit",function(){		
				$('.edit_or_add').html('Edit');
				_this = $(this).parent().parent().parent();
				$("table tr").removeClass('focus');
				_this.addClass('focus');
				_this = $(this).parent().parent().parent();
				
				resetForm();
				$("#newClientModal").each(function(){
					$("#client_id").remove();
					
					$(this).find('form').prepend('<input type="hidden" id="client_id" name="id" value="'+ $(_this).find('.id').data('id') +'" />');
					$(this).find("input[name='func']").val('edit');
					$(this).find("#record_number").val( $(_this).find('.record').html() );
					$(this).find("#fname").val( $(_this).find('.fname').html() );
					$(this).find("#lname").val( $(_this).find('.lname').html() );
					
					//Replace datefield to null if has value 0000-00-00 for validation
					if($(_this).find('.date_birth').html() !== '0000-00-00') {
						$(this).find("#date_birth").val( $(_this).find('.date_birth').html() );
					}
					$(this).find("#date_death").val( $(_this).find('.date_birth').data("date-death") );

					//Code added by Joe [to set the datetimelocal format value when "dataEntry user" clicks the edit button for client records]
					/*var timestamp = parseInt($(_this).find('.review_date').html());
					$(this).find("#referral_id").val( $(_this).find('.referral_id').html() );
					$(this).find("#review_date").val( timestampToDateTimeLocal(timestamp) );*/
					//End added code here

					var dob = new Date($(_this).find('.date_birth').html());
					var ageDifMs = Date.now() - dob.getTime();
					var ageDate = new Date(ageDifMs); // miliseconds from epoch
					var age = Math.abs(ageDate.getUTCFullYear() - 1970);
	
					if(age >= 2){
						//$("#client_type option[value='Child']").remove();
						$(this).find("#client_type").next().html("<span class='required_field'>*</span> Please update the client type. Type no longer appropriate for age.");
					}else{
						$(this).find("#client_type").next().html("");
						//if( $("#client_type option[value='Child']").length <= 0 ){
						//	$("#client_type").append('<option value="Child">Child</option>');
						//}
					}
					
					$(this).find("#client_type option").each(function(){
						if($(this).attr('value')==$(_this).find('.type').html()){
							$(this).attr('selected','selected');
							$(this).prop('selected', true);
							
							if(age >= 2 && $(_this).find('.type').html()=='Child'){								
								$("#client_type").next().html("<span class='required_field'>*</span> Please update the client type. Type no longer appropriate for age.");
							}else{
								$("#client_type").next().html("");
							}
						}
					});

					//Code added by Joe [to set the value if the client is a child type and to display the relationship value]
					if ($("#client_type").val() == 'Child') {
						$("#client_type").change();
						$('#relation_to').val( $(_this).find('.relationship').html());
					}	
					//End added code here
					
					
					$(this).find("#phone").val( $(_this).find('.phone').html() );
					$(this).find("#place_of_birth").val( $(_this).find('.place_of_birth').html() );
					$(this).find("#district option").each(function(){
						if($(this).attr('value')==$(_this).find('.district').html())
							$(this).attr('selected','selected');						
					});

					//Code added by Joe [to fix the bug that not showing the province value from database for every client record and populate the listed districts for particular area]
					$(this).find("#province option").each(function(){
						if($(this).attr('value')==$(_this).find('.province').html())
							$(this).attr('selected','selected');						
					});
					
					populate_districts($("#province")[0], function () {
						$("#district").val($(_this).find('.district').html());
					});
					//End added code here

					$(this).find("#current_address").val( $(_this).find('.current_address').html() );

					// remove the archive date at first
					$("#is_archived").parent().find('em').remove();
					if($(_this).hasClass('is_archived')){
						$("#is_archived").prop("checked",true);
						$("#is_archived").next().append(' <em style="font-size: 12px;font-weight: bold;">(Archived date : '+ $(_this).data('archived-date') +')</em>');            						
					}
					else{
						$("#is_archived").prop("checked",false).parent().find('em').remove();
					}
				}); 
			});
		}
		//Code added by Joe [to set _callback variable if needed a list of province from database]
		function populate_districts(_this, _callback){
			show_loader($,"#newClientModal");
			$(".no-distirct").fadeOut();
				var _hc = "<?php echo $_SESSION['area_name']; ?> Health Facility";
				$('.district-form1').remove();  
				$("#district-form").remove(); 
			 	var _province_id = $("select#province option").filter(":selected").val();
			 	_data = "class=clinic&func=get_districts&province_id="+_province_id;
               
			 	$.post(window.location.href,_data, function(data){
					//console.log(data);	
					var _district = $.parseJSON(data);
					   element = '<div class="form-group district-form1" id="district-form1">';
		            	element += '<label for="area_name">Client District</label><span class="required_field">*</span>';
			            element += '<select class="form-control" name="district" id="district" required>';
			            if ( _district.length > 0) {
			            	element += '<option value="">Select Client District</option>';
			            	for (var i = _district.length - 1; i >= 0; i--) {
				            	element += '<option value='+_district[i]['ID']+'>'+_district[i]['area_name']+'</option>';
							}; 
							element += '</select></div>';   
							$('.div-district').append(element);
			            }else{
			            	//element += '<option value=none>No District Found</option>';
			            	$(".no-distirct").fadeIn().html('No district under province selected.');
			            	$("#district-form").removeClass("hide"); 
			            }
			           	close_loader($,"#newClientModal");
			           	if (_callback !== undefined) {
			           		_callback();	
			           	}
				});
		}
		function quick_search($){
			$(".btnsubmit").on('click',function(){
				$(this).parent().parent().submit();
			})
		}
		</script>
		<?php
	}

}