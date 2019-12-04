<?php
// error_reporting(1);
require 'library/plugins/vendor/autoload.php';
class Malnutrition extends DB{
	
	// Notes : To rename Discharged to Cured in db record. We just need to trigger this query : 
	// UPDATE tbl_records SET outcome_review='Cured' WHERE outcome_review = 'Discharged' 


	public function __construct() {
		parent::__construct();
	}
	private function send_mail($from, $to, $replyTo, $subject, $htmlBody, $plainBody) {
	   $mail = new PHPMailer;

	   $mail->isSMTP();
	   $mail->Host = 'mail.smtp2go.com';
	   $mail->SMTPAuth = true; 
	   $mail->Username = 'admin@susumamas.org.pg';         
	   $mail->Password = 'pF2$OsVQozz4';
	   $mail->SMTPSecure = 'tls'; 
	   $mail->Port = 2525;

	   $mail->setFrom($from['email'], $from['name']);
	   $mail->addAddress($to['email'], $to['name']); 
	   $mail->addReplyTo($replyTo['email'], $replyTo['name']);

	   $mail->isHTML(true);  

	   $mail->Subject = $subject;
	   $mail->Body    = $htmlBody;
	   $mail->AltBody = $plainBody;
	   
	   if(!$mail->send()) {
	      return false;
	       echo 'Mailer Error: ' . $mail->ErrorInfo;
	   } else {
	      return true;
	      echo 'Message has been sent';
	   }
	}
	private function fetchReportData() {
		$stmt = $this->query("
			SELECT a.record_number, CONCAT(a.lname, ', ', a.fname) as fullname, 
					a.client_type as gender,
					FLOOR(MOD(DATEDIFF(NOW(), a.date_birth)/365.25 * 12, 12)) as age_months, 
					FLOOR(DATEDIFF(NOW(), a.date_birth)/365.25) as age_year,
				   b.date, b.rutf, b.review_date_future, b.ref_hospital, b.outcome_review,
				   c.series, c.tb_diagnosed, c.hiv_status, c.muac, c.oedema, c.wfh,
				   c.reason,
				   d.area_name as province
            FROM tbl_client a,
            	 tbl_records b,
            	 tbl_client_malnutrition c,
            	 tbl_area d, tbl_clinic e
            WHERE b.client_id = a.ID
            AND b.client_malnutrition_id = c.id
            AND d.entry_type='province'
			AND b.clinic_id=e.ID AND e.province=d.ID
            AND ((b.review_date_future >= b.date AND c.isPrevious=0) OR MONTH(b.date) = MONTH(CURRENT_DATE()))
            ORDER BY b.date ASC
			", array());
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	private function fetchNotEnrolledWithMalnutReason() {
		$query = "SELECT b.record_number,  CONCAT(b.lname,', ',b.fname) AS fullname, 
					b.client_type as gender,
					FLOOR(MOD(DATEDIFF(NOW(), b.date_birth)/365.25 * 12, 12)) as age_months, 
					FLOOR(DATEDIFF(NOW(), b.date_birth)/365.25) as age_year,
				   a.date, a.rutf, 'n/a' as review_date_future, a.ref_hospital, a.outcome_review,
				   '' as series, '' as tb_diagnosed, '' as hiv_status, '' as muac, '' as oedema, '' as wfh, 'Unknown' as reason,
				   province.area_name as province
                FROM tbl_records as a
                JOIN tbl_client as b ON b.ID = a.client_id
                JOIN tbl_clinic AS c ON a.clinic_id = c.ID
                JOIN tbl_area AS district ON c.llg_id = district.ID
                JOIN tbl_area AS province ON district.parent_ids = province.ID 
                JOIN tbl_area AS office ON office.ID = a.office_id
                WHERE a.date >= :start_date AND a.date <= :end_date
                AND a.visit_reasons LIKE '%Malnutrition%'
                AND client_malnutrition_id='0'
                ORDER BY a.date ASC";       
        $bind_array = array("start_date"=>'2019-01-01', "end_date"=>'CURDATE()');
        $stmt = $this->query($query,$bind_array);
    	return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	private function in_array_2d($id, $array) {
		foreach ($array as $key => $val) {
			if ($val['province'] === $id) {
				return true;
			}
		}
		return false;
	}
	private function formatArraybyProvince($datas = null) {
		$tmp = array();
		
		foreach($datas as $data) {
			$province = $data['province'];
			unset($data['province']);
		    $tmp[trim($province)][] = $data;
		}
		$output = array();
		foreach($tmp as $key => $val) {
		    $output[] = array(
		        'province' => $key,
		        'datas' => $val
		    );
		}
		$fetchNotif = $this->fetchNotificationSettingsForMalnutrition();
		$provinces = array_splice($fetchNotif, 0, -3);
		foreach($provinces as $prov) {
			$foo = $this->in_array_2d( str_replace('_',' ',$prov['label']) , $output );
			if( !$foo ) {
				array_push($output, array(
					'province' => $prov['label'],
					'datas' => array()
				)); 
			}
		}
		return $output;
	}
	private function renderEmailBody($datas=null) {
		$visit_counter = 0;
		$th_style='background: #f5f5f2;padding:10px 5px;border-right:1px solid #999;border-top:1px solid #999;border-bottom:3px double #999;';
		$td_style='border-right:1px solid #aaa;border-bottom: 1px solid #aaa; vertical-align: top;';
		if(count($datas)>0) {
			// if specific province
			if(isset($datas['province'])) {
				$province = $datas['province'];
				$datas = array($datas); // the purpose is so that it will follow the same format as with sending for all provinces case. the purpose is for the foreach loop below.
			}
			else {
				$province = $datas[0]['province'];
			}
			
		}
		ob_start(); ?>

		<p style="background:#f5f5f2;font-family:Arial;">
			<?php 
			if(count($datas) < 1) : ?>
				<div class="content" style="background:#ffffff;border:1px solid #d5d5d5; box-shadow: 1px 2px 2px #d3d3d3;width:700px; margin:10px auto 20px;">
				     <div class="header" style="background:#1abc9c; color: #fff; padding:7px 15px; font-size:22px;">
				        Susu mamas | Malnutrition
				     </div>
				     <div class="body" style="padding:10px 15px;">
				     	<table border="0" cellpadding="10" cellspacing="0" width="100%" style="font-size:11; font-family:monospace;border-left:1px solid #aaa;">
				        	<thead>
				        		<tr>
				        			<th style="<?php echo $th_style ?>" width="80">Date</th>
				        			<th style="<?php echo $th_style ?>" width="180">Patient</th>
				        			<th style="<?php echo $th_style ?>" width="100">Enrollment Status</th>
				        			<th style="<?php echo $th_style ?>">Reason of Visit</th>
				        			<th style="<?php echo $th_style ?>" width="80">Review Date</th>
				        			<th style="<?php echo $th_style ?>">Outcome of Consultation</th>
				        		</tr>
				        	</thead>
				        	<tbody>
				        		<tr>
			        				<td colspan="6" style="<?php echo $td_style ?>">No data available.</td>
			        			</tr>
				        	</tbody>
				        </table>
				     </div>
			  </div> <?php
			else :
				foreach($datas as $data) : ?>	
				  <div class="content" style="background:#ffffff;border:1px solid #d5d5d5; box-shadow: 1px 2px 2px #d3d3d3;width:700px; margin:10px auto 20px;">
				     <div class="header" style="background:#1abc9c; color: #fff; padding:7px 15px; font-size:22px;">
				        Susu mamas | Malnutrition
				     </div>
				     <div class="body" style="padding:10px 15px;">
				     	
				        <strong>Province: </strong><span><?php echo $data['province'] ?></span><br /><br />
				        <table border="0" cellpadding="10" cellspacing="0" width="100%" style="font-size:11; font-family:monospace;border-left:1px solid #aaa;">
				        	<thead>
				        		<tr>
				        			<th style="<?php echo $th_style ?>" width="80">Date</th>
				        			<th style="<?php echo $th_style ?>" width="180">Patient</th>
				        			<th style="<?php echo $th_style ?>" width="100">Enrollment Status</th>
				        			<th style="<?php echo $th_style ?>">Reason of Visit</th>
				        			<th style="<?php echo $th_style ?>" width="80">Review Date</th>
				        			<th style="<?php echo $th_style ?>">Outcome of Consultation</th>
				        		</tr>
				        	</thead>
				        	<tbody>
								<?php 
			        			foreach($data['datas'] as $_data) : 
				        			$visit_counter++;
				        			?>
					        		<tr>
					        			<td style="<?php echo $td_style ?>"><?php echo $_data['date'] ?></td>
					        			<td style="<?php echo $td_style ?>">
					        				<strong><?php echo $_data['fullname'] ?></strong><br />
					        				<em>(<?php echo $_data['record_number'] ?>)</em><br />
											<strong>Age</strong> : <?php echo $_data['age_year'] . ' year(s) ' . $_data['age_months'] . ' month(s)'; ?><br />
					        				<strong>Gender</strong> : <?php echo $_data['gender']; ?><br />
					        				<hr />
					        				<strong>HIV Status</strong> : <?php echo $_data['hiv_status']; ?><br />
					        				<strong>TB Diagnosed</strong> : <?php echo $_data['tb_diagnosed']; ?><br />
											<strong>Muac</strong> : <?php echo $_data['muac']; ?><br />
					        				<strong>Oedema</strong> : <?php echo $_data['oedema']; ?><br />
					        				<strong>WFH</strong> : <?php echo $_data['wfh']; ?><br />											
											<strong>No of RUTF Given</strong> : <?php echo $_data['rutf']; ?><br />
					        			</td>
					        			<td style="<?php echo $td_style ?>">
					        				
					        				<?php
					        					if($_data['series'] === '') {
					        						echo '';
					        					}  
					        					else {
					        						echo '<strong>Enrollment #</strong>'. $_data['series'];
					        					}
					        				?><br />
					        				<!-- <strong>Visit #</strong>{under construction} -->
					        			</td>
					        			<td style="<?php echo $td_style ?>"><?php echo $_data['reason'] ?></td>
					        			<td style="<?php echo $td_style ?>"><?php echo $_data['review_date_future'] ?></td>
					        			<td style="<?php echo $td_style ?>"><?php echo $_data['outcome_review'] ?></td>
					        		</tr>
				        		<?php 
				        		endforeach; 
				        		if(count($data['datas'])===0) : ?>
				        			<tr>
				        				<td colspan="6" style="<?php echo $td_style ?>">No consultation done with Malnutrition.</td>
				        			</tr>

				        		<?php endif; ?>
				        	</tbody>
				        </table>
				     </div>
				  </div>

				<?php endforeach; 
			endif; ?>
		</p>

		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	private function getEmailsByProvince($province) {
		$stmt = $this->query("
			SELECT * 
            FROM tbl_notifications
			WHERE label = :province
			", array('province' => $province));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function mail_mal() {
		
		$data = $this->fetchNotificationSettingsForMalnutrition();
		
		$schedule = array_values(array_filter($data, function($_d) {
			return ($_d['label']==='malnutrition_schedule');
		}))[0]['value'];
		$every = array_values(array_filter($data, function($_d) {
			return ($_d['label']==='malnutrition_weekly');
		}))[0]['value'];
		
		$datas = $this->compileDataForReports();
		switch($schedule) {
			case "daily": 
				break;
			case "weekly": 
				if(strtolower(date('l', strtotime('now'))) != $every) {
					exit;
				}
			break;
			case "monthly": 
				if(date('d', strtotime('now')) != "01") {
					exit;
				}		
			break;
		}
		$body_all_prov = $this->renderEmailBody($datas); //body for all provinces
		
		// Loop through provinces and send mail
		array_push($datas, array('province'=>'All Provinces'));
		foreach($datas as $key=>$prov) {
			
			$emails = $this->getEmailsByProvince( str_replace(' ','_',trim($prov['province'])) );
			if(isset($emails[0]['value']) && $emails[0]['value']) {
				$emails = str_replace(' ', '', $emails[0]['value']);
				$emails = explode(',', $emails);
				$body = $this->renderEmailBody($prov); // body for specific province
				if($key==(count($datas)-1)) {
					$body = $body_all_prov;
				}

				// Loop each email addresses
				foreach($emails as $email) {
					$to = $_GET['to'] ?? $email;
					$subject = $_GET['subject'] ?? 'Susumamas | ' . $prov['province'] . ' | Malnutrition Report ';
				
					$mail = $this->send_mail(
						array('email' => 'admin@susumamas.org.pg', 'name' => 'Susumamas'), 
						array('email' => $to, 'name' => ''), 
						array('email' => 'admin@susumamas.org.pg', 'name' => ''), 
						$subject, 
						$body,
						htmlentities($body)
					 );
					 if($mail) {
						echo "<pre>Mail Sent to : {$to} for {$subject}.</pre>";
					 }
					 else {
						echo "<pre>Error Mail (to : {$to} for {$subject}).</pre>";
					 }
				}
			}
			else {
				echo "<pre>No email address for ".str_replace(' ','_',trim($prov['province'])) . "</pre>";
			}
		}
		exit();
	}
	private function compileDataForReports() {
		// FETCH NOT Enrolled consultation with malnutrition
		$unenrolled = $this->fetchNotEnrolledWithMalnutReason();

		// FETCH Enrolled Malnutrition consultation datas
		$datas = $this->fetchReportData();

		$datas = array_merge($unenrolled, $datas);

		// GROUP BY PROVINCE Name
		return $this->formatArraybyProvince($datas);
	}
	public function reportEmailSend() {
		echo $this->renderEmailBody( $this->compileDataForReports() );
		exit();
	}

	/* ============================= Superadmin Settings > Notification Malnutrition =============*/
	public function fetchNotificationSettingsForMalnutrition() {
		return $this->select('*', array(), false, 'tbl_notifications');
	}
	public function storeNotificationSettingsForMalnutrition() {
		unset($_POST['class']);
		unset($_POST['func']);
		foreach($_POST as $key => $val) {


			$data = $this->select('ID', array('label'=>$key), false, 'tbl_notifications');
			if( $data ) {
				echo $this->save(array(
					'value'=>$val
				), array(
					'label'=>$key
				), 'tbl_notifications') ? 'success' : 'error';
			}
			else {
				echo $this->save(array(
					'value'=>$val,
					'label'=>$key
				), array(), 'tbl_notifications') ? 'success' : 'error';

			}
		}
		
		exit();
	}
}

?>