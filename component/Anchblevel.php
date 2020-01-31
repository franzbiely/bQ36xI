<?php
error_reporting(1);
require 'library/plugins/vendor/autoload.php';
class Anchblevel extends DB{
	public function __construct() {
		parent::__construct();
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
	private function disp_record($arr, $filter) {
		$no_data = true; ?>
		<div>
    		<table style="width:100%;"><?php
				$no_record = false;
			 	foreach($arr as $a) : $tmp = "";
					foreach($a['records'] as $c_record) {
						if($c_record['hb_level'] == '10+' ) {
							continue;
						}
						if($filter == 1 && $c_record['hb_level'] == '8-' ) {
							continue;
						}
						if($filter == 2 && ($c_record['hb_level'] == '9-' || $c_record['hb_level'] == '10-')) {
							continue;
						}
						if($c_record['hb_level'] == '10-' || $c_record['hb_level'] == '9-'){
							$tmp .= date('jS M Y', strtotime($c_record['review_date'])) ." : Hb8-10g%";
						} 
						else if($c_record['hb_level'] == '8-'){
							$tmp .= date('jS M Y', strtotime($c_record['review_date'])) ." : Hb<8g%";
						}
						$tmp .= ' ' .$c_record['clinic_name'];
						if($c_record['clinic_type'] != '') {
							$tmp .= '-'.$c_record['clinic_type'];
						}
						if($c_record['area_name'] != '') {
							$tmp .= '-'.$c_record['area_name'];
						}
						$tmp .= '<br>';
						$no_data = false;
					}
			  		if($tmp != ""){ ?>
						<tr>
							<td style="width:30%;vertical-align:top;"><?php echo $a['client']['lname']?>, <?php echo $a['client']['fname']?> -
								<?php echo $a['client']['record_number']?></td>
							<td style="width:70%;vertical-align:top;"><?php echo $tmp?></td>
						</tr> <?php 
					}
			 	endforeach; 
			  	if($no_data == true){ ?>
					<tr>
						<td style="width:70%;vertical-align:top;">There is currently no people in this range.</td>
						<td style="width:30%;vertical-align:top;"></td>
					</tr> <?php     
				} ?>
			</table>
		</div>
		<?php
	}
	private function body($data) {
		$a1 = array(); //10+
		$b1 = array(); //10-
		$c1 = array(); //9-
		$d1 = array(); //8-
	 
		foreach($data as $a) {
		   if(count($a['records'] > 0)) {
			  if($a['records'][0]['hb_level'] == '10+') {
				 $a1[] = $a;
			  } else if($a['records'][0]['hb_level'] == '10-') {
				 $b1[] = $a;
			  } else if($a['records'][0]['hb_level'] == '9-') {
				 $b1[] = $a;
			  } else if($a['records'][0]['hb_level'] == '8-') {
				 $b1[] = $a;
			  }
		   }
		}
		ob_start(); ?>
		<p style="background:#f5f5f2;font-family:Arial;">
			<div class="content"
				style="background:#ffffff;border:1px solid #d5d5d5; box-shadow: 1px 2px 2px #d3d3d3;width:700px; margin:0 auto;">
				<div class="header" style="background:#1abc9c; color: #fff; padding:7px 15px; font-size:22px;">
				eCIS | ANC
				</div>
				<div class="body" style="padding:10px 15px;">

					<div class="hb-heading" style="background: #f5f5f2;padding:10px 5px;">HB Level: 8-10</div>
					<div>
						<?php echo $this->disp_record($b1, 1); ?>
					</div>
					<div class="hb-heading" style="background: #f5f5f2;padding:10px 5px;">HB Level: 8 below</div>
					<div>
						<?php echo $this->disp_record($b1, 2); ?>
					</div>
				</div>
			</div>
		</p>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	public function render_mail_report() {
		$reports = new Reports(); ?>
		<style>table *{font-size:12px;}</style>
		<?php 
		echo $this->body( $reports->get_hb_level_from_today() );
		exit();
	}
	public function mail_anc() {
		$file = 'anc.json';
		$json = json_decode(file_get_contents($file), true);
		
		$emails = $json['email'];
		$emails = str_replace(' ', '', $emails);
		   $emails = explode(',', $emails);
		   
		
		$reports = new Reports();
		
		if($json['schedule'] == 'daily') {
		   $date = date('Y-m-d', strtotime('-1 days'));
		   $data = $reports->get_hb_level_from_today();
		   $body = $this->body($data);
		} else if ($json['schedule'] == 'weekly') {
		   if(strtolower(date('l', strtotime('now'))) != $json['every']) {
			  exit;
		   }
		   $start_date = date('Y-m-d', strtotime('-7 days'));
		   $end_date = date('Y-m-d', strtotime('-1 days'));
		   $data = $reports->get_hb_level_from_today();
		   $body = $this->body($data);
		} else if($json['schedule'] == 'monthly') {
		   if(date('m-t-Y') != date('m-d-Y', strtotime('now'))) {
			  exit;
		   }
		   $start_date = date('Y-m-01', strtotime('-1 months'));
		   $this_year = date('Y', strtotime('-1 months'));
		   $this_month = date('m', strtotime('-1 months'));
		   $last_date = cal_days_in_month(CAL_GREGORIAN,$this_month,$this_year);
		   $end_date = $this_year . '-' . $this_month . '-' . $last_date;
		   $data = $reports->get_hb_level_from_today();
		   $body = $this->body($data);
		}
		
		// echo $body;
		
		foreach($emails as $email) {
			$subject = 'Susumamas | ANC Report';
		   	$mail = $this->send_mail(
				array('email' => 'admin@susumamas.org.pg', 'name' => 'Susumamas'), 
				array('email' => $email, 'name' => ''), 
				array('email' => 'admin@susumamas.org.pg', 'name' => ''), 
				$subject, 
				$body,
				htmlentities($body)
		   	);
		   	if($mail) {
				echo "<pre>Mail Sent to : {$email} for {$subject}.</pre>";
			}
			else {
				echo "<pre>Error Mail (to : {$email} for {$subject}).</pre>";
			}
		}
		exit();
	}
}

?>