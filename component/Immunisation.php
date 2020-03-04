<?php
error_reporting(1);
require 'library/plugins/vendor/autoload.php';
class Immunisation extends DB
{
    
    // Notes : To rename Discharged to Cured in db record. We just need to trigger this query :
    // UPDATE tbl_records SET outcome_review='Cured' WHERE outcome_review = 'Discharged'


    public function __construct()
    {
        parent::__construct();
	}
	public function getTypeById($id) {
		return $this->select('type', array('ID'=>$id), true, 'tbl_client_immunisation')['type'];
	}
    private function send_mail($from, $to, $replyTo, $subject, $htmlBody, $plainBody) {
        $mail = new PHPMailer;
 
        $mail->isSMTP();
        $mail->Host = 'mail.smtp2go.com';
		$mail->SMTPAuth = true; 
		$mail->Username = 'admin@susumamas.org.pg';         
		$mail->Password = 'Y2poLde6Uk%^X@D&K1';
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
		$_days = $this->fetchSendSchedule()[0];
		$_schedule = $this->fetchSendSchedule()[2];

		
		$where = ($_schedule === 'monthly') ? 
			"AND ( DATEDIFF(NOW(),b.date) <= {$_days} && DATEDIFF(NOW(),b.date) > 0 )": 
			"AND ( DATEDIFF(NOW(),b.date) <= {$_days} && DATEDIFF(NOW(),b.date) >= 0 )";
		
		if($_schedule === 'daily') {
			$where = "AND ( DATEDIFF(NOW(),b.date) < {$_days} && DATEDIFF(NOW(),b.date) >= 0 )";
		}

		$stmt = $this->query("
        SELECT DISTINCT a.record_number, CONCAT(a.lname, ', ', a.fname) as fullname, 
            a.client_type as gender,
            FLOOR(MOD(DATEDIFF(NOW(), a.date_birth)/365.25 * 12, 12)) as age_months, 
			FLOOR(DATEDIFF(NOW(), a.date_birth)/365.25) as age_year,
			b.date,
			(SELECT GROUP_CONCAT(type SEPARATOR ', ') FROM tbl_client_immunisation as im WHERE b.ID = im.record_id) type,
			d.area_name as province
        FROM tbl_client a,
            tbl_records b,
            tbl_client_immunisation im,
            tbl_area d, tbl_clinic e
        WHERE b.client_id = a.ID
            AND b.ID = im.record_id
            AND d.entry_type='province'
			AND b.clinic_id=e.ID AND e.province=d.ID
			{$where}
        ORDER BY b.date ASC
		", array());
		
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function mail_mal() {
		$data = $this->fetchNotificationSettingsForImmunisationForEmail();
		
		$schedule = array_values(array_filter($data, function($_d) {
			return ($_d['label']==='immunisation_schedule');
		}))[0]['value'];
		$every = array_values(array_filter($data, function($_d) {
			return ($_d['label']==='immunisation_weekly');
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
				if(date('m-t-Y') != date('m-d-Y', strtotime('now'))) {
					exit;
				}		
				break;
		}
		$body_all_prov = $this->renderEmailBody($datas); //body for all provinces
		// Loop through provinces and send mail
		foreach($datas as $key=>$prov) {
			$emails = $this->getEmailsByProvince( 'im_'.str_replace(' ','_',trim($prov['province'])) );
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
					$subject = $_GET['subject'] ?? 'Susumamas | ' . $prov['province'] . ' | Immunisation Report ';
				
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
		$datas = $this->fetchReportData();
		return $this->formatArraybyProvince($datas);
	}
     public function reportEmailSend() {
		echo $this->renderEmailBody( $this->compileDataForReports() );
		exit();
    }
    private function in_array_2d($id, $array) {
		foreach ($array as $key => $val) {
			if ($val['province'] === $id) {
				return true;
			}
		}
		return false;
	}
    public function formatArraybyProvince($datas = null) {
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
		$fetchNotif = $this->fetchNotificationSettingsForImmunisation();
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
	public function fetchReportSummary($prov){
		$_days = $this->fetchSendSchedule()[0];
		$_schedule = $this->fetchSendSchedule()[2];
		$where = 'a.area_name = "'.$prov.'"
			AND r.client_id = c.id
			AND r.clinic_id = b.ID
			AND b.province = a.id
			AND a.entry_type= "province"
			AND r.ID = im.record_id ';
		$select = 'tbl_records r, tbl_clinic b, tbl_area a,tbl_client_immunisation im,tbl_client c';
		$where .= ($_schedule === 'monthly') ? 
			"AND ( DATEDIFF(NOW(),r.date) <= {$_days} && DATEDIFF(NOW(),r.date) > 0 )": 
			"AND ( DATEDIFF(NOW(),r.date) <= {$_days} && DATEDIFF(NOW(),r.date) >= 0 )";
		if($_schedule === 'daily') {
			$where .= "AND ( DATEDIFF(NOW(),r.date) < {$_days} && DATEDIFF(NOW(),r.date) >= 0 )";
		}
		
		$where_month = '( FLOOR(MOD(DATEDIFF(r.date, c.date_birth)/365.25 * 12, 12)) + ( FLOOR(DATEDIFF(r.date, c.date_birth)/365.25) * 12)';
		
		$stmt = $this->query('
		SELECT 
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "BCG (Birth)"
				AND DATEDIFF(r.date, c.date_birth) <= 1
			) as bcg_within_24_hours,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "BCG (Birth)"
				AND DATEDIFF(r.date, c.date_birth) < 7
			) as bcg_less_than_a_week,
			(SELECT  COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "BCG (Birth)"
				AND DATEDIFF(r.date, c.date_birth) >= 7
			) as bcg_greater_than_a_week,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Hep B (Birth)"
				AND DATEDIFF(r.date, c.date_birth) <= 1
			) as hepb_within_24_hours,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Hep B (Birth)"
				AND DATEDIFF(r.date, c.date_birth) > 1
			) as greater_than_24_hours,
			
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "DTP, Hib, Hep B (Penta) 1st Dose"
				AND '.$where_month.' < 12 )
			) as dtp_first_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "DTP, Hib, Hep B (Penta) 2nd Dose"
				AND '.$where_month.' < 12 )
			) as dtp_second_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "DTP, Hib, Hep B (Penta) 3rd Dose"
				AND '.$where_month.' < 12 )
			) as dtp_third_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "DTP, Hib, Hep B (Penta)"
				AND '.$where_month.' >= 12 )
			) as dtp_over_a_year,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "PCV 1st Dose"
				AND '.$where_month.' < 12 )
			) as pcv_first_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "PCV 2nd Dose"
				AND '.$where_month.' < 12 )
			) as pcv_second_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "PCV 3rd Dose"
				AND '.$where_month.' < 12 )
			) as pcv_third_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "PCV"
				AND '.$where_month.' >= 12 )
			) as pcv_over_a_year,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Sabin 1st Dose"
				AND '.$where_month.' < 12 )
			) as sabin_first_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Sabin 2nd Dose"
				AND '.$where_month.' < 12 )
			) as sabin_second_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Sabin 3rd Dose"
				AND '.$where_month.' < 12 )
			) as sabin_third_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Sabin"
				AND '.$where_month.' >= 12 )
			) as sabin_over_a_year,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "IPV"
				AND '.$where_month.' BETWEEN 3 AND 11 )
			) as ipv,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Measles Rubella (MR)"
				AND '.$where_month.' BETWEEN 6 AND 8 )
			) as mr_6_8_months,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Measles Rubella (MR)"
				AND '.$where_month.' BETWEEN 9 AND 17 )
			) as mr_9_17_months,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Measles Rubella (MR)"
				AND '.$where_month.' BETWEEN 18 AND 23 )
			) as mr_18_23_months,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Measles Rubella (MR)"
				AND '.$where_month.' >= 12 )
			) as mr_above_a_year,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Vitamin A"
				AND '.$where_month.' BETWEEN 6 AND 11 )
			) as vita_6_11_months,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Vitamin A"
				AND '.$where_month.' BETWEEN 12 AND 59 )
			) as vita_12_59_months,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Albendazole"
				AND '.$where_month.' BETWEEN 12 AND 59 )
			) as alb_12_59_months,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Tetanus Toxoid 1st Dose"
			) as tetanus_1_dose,
			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Tetanus Toxoid 2nd Dose"
			) as tetanus_2_dose,

			(SELECT COUNT(im.ID) FROM '.$select.' WHERE '.$where.'
				AND im.type = "Pregnant Women Booster"
			) as prenant_booster
		', array());
		$datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datas[0];
	}
	public function fetchSendSchedule () {
		$notif_data = $this->fetchNotificationSettingsForImmunisation();
		$im_schedule = array_values(array_filter($notif_data, function($_d) {
			return ($_d['label']==='immunisation_schedule');
		}))[0]['value'];
		$im_every = array_values(array_filter($notif_data, function($_d) {
			return ($_d['label']==='immunisation_weekly');
		}))[0]['value'];
		$_schedule = '';
		$_arr = [];
		$_note = '';
		switch($im_schedule) {
			case "daily": 
					$_schedule = 1;
					$_note = date('F', strtotime('now')).' '.date('d', strtotime('now')).', '.date('Y', strtotime('now'));
				break;
			case "weekly": 
				$_schedule = 6;
				$_note = date('F d, Y', strtotime('-6 days')). ' - '.date('F d, Y', strtotime('now'));
			break;
			case "monthly": 
				$_schedule = cal_days_in_month(CAL_GREGORIAN, date('n', strtotime('now')), date('Y', strtotime('now')));
				$_note = date('F 01, Y'). ' - '.date('F d, Y', strtotime('now'));
			break;
		}
		
		array_push($_arr, $_schedule, $_note, $im_schedule);
		return $_arr;
	}
    private function renderEmailBody($datas=null) {
		$immunisation_details = [
			"BCG (Birth) within 24hrs birth",
			"BCG (Birth) < 1 week",
			"BCG (Birth) >= 1 week",
			"Hep B (Birth) within 24hrs birth",
			"Hep B (Birth) > 24 hrs",
			"DTP, Hib, Hep B (Penta) 1st dose < 1 yr",
			"DTP, Hib, Hep B (Penta) 2nd dose < 1 yr",
			"DTP, Hib, Hep B (Penta) 3rd dose < 1 yr",
			"DTP, Hib, Hep B (Penta) >= 1 yr",
			"PCV 1st dose < 1 yr",
			"PCV 2nd dose < 1 yr",
			"PCV 3rd dose < 1 yr",
			"PCV >= 1 yr",
			"Sabin 1st dose < 1 yr",
			"Sabin 2nd dose < 1 yr",
			"Sabin 3rd dose < 1 yr",
			"Sabin >= 1 yr",
			"IPV 1 dose only (3-11mths)",
			"Measles Rubella (MR) 6-8 mths",
			"Measles Rubella (MR) 9-17 mths",
			"Measles Rubella (MR) 18-23 mths",
			"Measles Rubella (MR) >= 24 mths",
			"Vitamin A 6-11 mths",
			"Vitamin A 12-59 mths",
			"Albendazole 12-59 mths",
			"Tetanus Toxoid 1st dose",
			"Tetanus Toxoid 2nd dose",
			"Pregnant Women Booster"
		];
		$visit_counter = 0;
		$th_style='background: #f5f5f2;padding:10px 5px;border-right:1px solid #999;border-top:1px solid #999;border-bottom:3px double #999;';
        $td_style='border-right:1px solid #aaa;border-bottom: 1px solid #aaa; vertical-align: top;';
        $toBeExcludeData = ['schedule','malnutrition_schedule','malnutrition_weekly','immunisation_schedule','immunisation_weekly'];
		foreach($datas as $key => $ex){
            if (array_search($ex['province'], $toBeExcludeData) || substr($ex['province'], 0, 3 ) == 'im_'){
                unset($datas[$key]);
            }
		}
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
		<div style="font-size:12px; font-family:Arial;width:700px; margin:10px auto 20px;">
			<p><strong>Note:</strong> The data within this report contains client consultation records for immunisation :</p>
			<ul>
				<li><strong>Report Duration: </strong>(<?php echo $this->fetchSendSchedule()[1]; ?>)</li>
			</ul> 
		</div>
		<p style="background:#f5f5f2;font-family:Arial;">
			
			<?php 
			if(count($datas) < 1) : ?>
				<div class="content" style="background:#ffffff;border:1px solid #d5d5d5; box-shadow: 1px 2px 2px #d3d3d3;width:700px; margin:10px auto 20px;">
				     <div class="header" style="background:#1abc9c; color: #fff; padding:7px 15px; font-size:22px;">
					 eCIS | Immunisation
				     </div>
				     <div class="body" style="padding:10px 15px;">
				     	<table border="0" cellpadding="10" cellspacing="0" width="100%" style="font-size:11; font-family:monospace;border-left:1px solid #aaa;">
				        	<thead>
				        		<tr>
				        			<th style="<?php echo $th_style ?>" width="80">Date</th>
				        			<th style="<?php echo $th_style ?>" width="180">Patient</th>
				        			<th style="<?php echo $th_style ?>" width="150">Immunisation Details</th>>
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
					eCIS | Immunisation
					</div>
					<div class="body" style="padding:10px 15px;">
						<?php $summary = $this->fetchReportSummary($data['province']); ?>
						<strong><?php echo $data['province'] ?> Immunisation Report Summary</strong><br /><br />
						<div class="body" style="padding-bottom:10px;"  >
							<table border="0" cellpadding="10" cellspacing="0" width="100%" style="font-size:11; font-family:monospace;border-left:1px solid #aaa;">
								<thead>
									<tr>
										<th style="<?php echo $th_style ?> text-align: left; margin-left: 10px;" width="550">Immunisation Details</th>
										<th style="<?php echo $th_style ?>" width="50"># of Children</th>
									</tr>
								</thead>
								<tbody>
								<?php 
								$x = 0;
								foreach($summary as $sum=>$key) { ?>
									<tr>
										<td colspan="1" style="<?php echo $td_style ?>text-align: left"><?php echo $immunisation_details[$x] ?></td>
										<td colspan="1" style="<?php echo $td_style ?>text-align: center"><?php echo $summary[$sum] ?></td>
									</tr>
								<?php 
								$x++;
								} ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="content" style="background:#ffffff;border:1px solid #d5d5d5; box-shadow: 1px 2px 2px #d3d3d3;width:700px; margin:10px auto 20px;">
					<div class="header" style="background:#1abc9c; color: #fff; padding:7px 15px; font-size:22px;">
					 eCIS | Immunisation
					</div>
					<div class="body" style="padding:10px 15px;">
				        <strong>Province: </strong><span><?php echo $data['province'] ?></span><br /><br />
				        <table border="0" cellpadding="10" cellspacing="0" width="100%" style="font-size:11; font-family:monospace;border-left:1px solid #aaa;">
				        	<thead>
				        		<tr>
				        			<th style="<?php echo $th_style ?>" width="80">Date</th>
				        			<th style="<?php echo $th_style ?>" width="180">Patient</th>
				        			<th style="<?php echo $th_style ?>" width="150">Immunisation Details</th>
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
					        			</td>
					        			<td style="<?php echo $td_style ?>">
                                            <?php  echo $_data['type'] ?>
					        			</td>
					        		</tr>
				        		<?php 
				        		endforeach; 
				        		if(count($data['datas'])===0) : ?>
				        			<tr>
				        				<td colspan="6" style="<?php echo $td_style ?>">No consultation done with Immunisation.</td>
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
    private function provinceExtractor($province) {
        $toBeExcludeData = ['schedule','immunisation_schedule','immunisation_weekly'];
        foreach($province as $key => $ex){
            if (array_search($ex['label'], $toBeExcludeData) || substr($ex['label'], 0, 3 ) == 'im_'){
            } else {  unset($province[$key]);}
        }
        return $province;
	} 
	public function fetchNotificationSettingsForImmunisationForEmail() {
		$stmt = $this->query("
			SELECT * FROM tbl_notifications WHERE label LIKE '%im%'
			", array());
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
     public function fetchNotificationSettingsForImmunisation() {
		return $this->select('*', array(), false, 'tbl_notifications');
	}
     public function storeNotificationSettingsForImmunisation() {
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