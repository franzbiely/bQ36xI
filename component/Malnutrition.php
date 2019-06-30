<?php

class Malnutrition extends DB{
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
					FLOOR(MOD(DATEDIFF(NOW(), a.date_birth)/365.25 * 12, 12)) as age_months, 
					FLOOR(DATEDIFF(NOW(), a.date_birth)/365.25) as age_year,
				   b.date, b.rutf, b.review_date_future, b.ref_hospital, b.outcome_review,
				   c.series, c.tb_diagnosed, c.hiv_status, c.muac, c.oedema, c.wfh,
				   d.area_name as province
            FROM tbl_client a,
            	 tbl_records b,
            	 tbl_client_malnutrition c,
            	 tbl_area d, tbl_clinic e
            WHERE b.client_id = a.ID
            AND b.client_malnutrition_id = c.id
            AND d.entry_type='province'
            AND b.clinic_id=e.ID AND e.province=d.ID
            AND ((b.review_date_future >= CURDATE() AND c.isPrevious=0) OR MONTH(b.date) = MONTH(CURRENT_DATE()))
            ORDER BY b.date ASC
			", array());

		// print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	private function fetchNotEnrolledWithMalnutReason() {
		$query = "SELECT b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, 
					FLOOR(MOD(DATEDIFF(NOW(), b.date_birth)/365.25 * 12, 12)) as age_months, 
					FLOOR(DATEDIFF(NOW(), b.date_birth)/365.25) as age_year,
				   a.date, a.rutf, 'n/a' as review_date_future, a.ref_hospital, a.outcome_review,
				   '' as series, '' as tb_diagnosed, '' as hiv_status, '' as muac, '' as oedema, '' as wfh,
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
	private function formatArraybyProvince($datas = null) {
		$tmp = array();

		foreach($datas as $data) {
			$province = $data['province'];
			unset($data['province']);
		    $tmp[$province][] = $data;
		}

		$output = array();
		foreach($tmp as $key => $val) {
		    $output[] = array(
		        'province' => $key,
		        'datas' => $val
		    );
		}
		return $output;
	}
	private function renderEmailBody($datas=null) {
		$visit_counter = 0;
		$th_style='background: #f5f5f2;padding:10px 5px;border-right:1px solid #999;border-top:1px solid #999;border-bottom:3px double #999;';
		$td_style='border-right:1px solid #aaa;border-bottom: 1px solid #aaa; vertical-align: top;';
		if(count($datas)>0) {
			$province = $datas[0]['province'];
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
				        			<th style="<?php echo $th_style ?>" width="100">Visit Status</th>
				        			<th style="<?php echo $th_style ?>">No of RUTF Given</th>
				        			<th style="<?php echo $th_style ?>" width="80">Review Date</th>
				        			<th style="<?php echo $th_style ?>">Outcome of Consultation</th>
				        		</tr>
				        	</thead>
				        	<tbody>
				        		<tr>
			        				<td colspan="6" style="<?php echo $td_style ?>">No consultation done with Malnutrition this month.</td>
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
				        <em><strong>Note :</strong> Please notice that the following datas <br />may include UNENROLLED consultations from January 1, 2019.</em><br /><br /><br />
				        <table border="0" cellpadding="10" cellspacing="0" width="100%" style="font-size:11; font-family:monospace;border-left:1px solid #aaa;">
				        	<thead>
				        		<tr>
				        			<th style="<?php echo $th_style ?>" width="80">Date</th>
				        			<th style="<?php echo $th_style ?>" width="180">Patient</th>
				        			<th style="<?php echo $th_style ?>" width="100">Visit Status</th>
				        			<th style="<?php echo $th_style ?>">No of RUTF Given</th>
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
					        				<hr />
					        				<strong>TB Diagnosed</strong> : <?php echo $_data['tb_diagnosed']; ?><br />
					        				<strong>HIV Status</strong> : <?php echo $_data['hiv_status']; ?><br />
					        				<strong>Muac</strong> : <?php echo $_data['muac']; ?><br />
					        				<strong>Oedema</strong> : <?php echo $_data['oedema']; ?><br />
					        				<strong>WFH</strong> : <?php echo $_data['wfh']; ?><br />
					        			</td>
					        			<td style="<?php echo $td_style ?>">
					        				
					        				<?php
					        					if($_data['series'] === '') {
					        						echo 'Not enrolled';
					        					}  
					        					else {
					        						echo '<strong>Enrollment #</strong>'. $_data['series'];
					        					}
					        				?><br />
					        				<!-- <strong>Visit #</strong>{under construction} -->
					        			</td>
					        			<td style="<?php echo $td_style ?>"><?php echo $_data['rutf'] ?></td>
					        			<td style="<?php echo $td_style ?>"><?php echo $_data['review_date_future'] ?></td>
					        			<td style="<?php echo $td_style ?>"><?php echo $_data['outcome_review'] ?></td>
					        		</tr>
				        		<?php 
				        		endforeach; 
				        		if(count($datas)==0) : ?>
				        			<tr>
				        				<td colspan="6" style="<?php echo $td_style ?>">No consultation done with Malnutrition this month.</td>
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
	public function reportEmailSend() {

		// FETCH NOT Enrolled consultation with malnutrition
		$unenrolled = $this->fetchNotEnrolledWithMalnutReason();

		// FETCH Enrolled Malnutrition consultation datas
		$datas = $this->fetchReportData();

		$datas = array_merge($unenrolled, $datas);

		// GROUP BY PROVINCE Name
		$datas = $this->formatArraybyProvince($datas);
		echo $this->renderEmailBody( $datas );
		exit();
		// $file = 'anc.json';
		// $json = json_decode(file_get_contents($file), true);

		// $emails = $json['email'];
		// $data = $reports->get_hb_level_from_today();
		// $body = body($data);
		// $emails = 'admin@test.com, mrthemetribe@gmail.com';
		// $emails = str_replace(' ', '', $emails);
		//    $emails = explode(',', $emails);

		// foreach($emails as $email) {
		//    $mail = $this->send_mail(
		//       array('email' => 'admin@susumamas.org.pg', 'name' => 'Susumamas'), 
		//       array('email' => $email, 'name' => ''), 
		//       array('email' => 'admin@susumamas.org.pg', 'name' => ''), 
		//       'Susumamas | Malnutrition Report', 
		//       $body,
		//       htmlentities($body)
		//    );
		// }
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