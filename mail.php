<?php

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

require 'library/plugins/vendor/autoload.php';

//use Mailgun\Mailgun;

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

function send_mail($from, $to, $replyTo, $subject, $htmlBody, $plainBody) {
   $mail = new PHPMailer;

   $mail->isSMTP();
   $mail->Host = 'smtp.mailgun.org';
   $mail->SMTPAuth = true; 
   $mail->Username = 'postmaster@sandboxe8b212114bb642a98b1fd4bca453aaf5.mailgun.org';         
   $mail->Password = 'f31fcf6bff82cb10feac44ed37bd7cc9';
   $mail->SMTPSecure = 'tls'; 
   $mail->Port = 587;

   $mail->setFrom($from['email'], $from['name']);
   $mail->addAddress($to['email'], $to['name']); 
   $mail->addReplyTo($replyTo['email'], $replyTo['name']);

   $mail->isHTML(true);  

   $mail->Subject = $subject;
   $mail->Body    = $htmlBody;
   $mail->AltBody = $plainBody;

   if(!$mail->send()) {
      return false;
       //echo 'Mailer Error: ' . $mail->ErrorInfo;
   } else {
      return true;
      //echo 'Message has been sent';
   }
}

function disp_record($arr, $filter) {
	$no_data = true;
   ?>
   <div>
      <table style="width:100%;">
   <?php
         $no_record = false;
         foreach($arr as $a):
                     $tmp = "";

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
                              //$tmp .= date('jS M Y', strtotime($c_record['date'])) . ' : HB ' . $c_record['hb_level'];
                              if($c_record['hb_level'] == '10-' || $c_record['hb_level'] == '9-'){
                                 $tmp .= date('jS M Y', strtotime($c_record['date'])) ." : Hb8-10g%";
                              }else if($c_record['hb_level'] == '8-'){
                                 $tmp .= date('jS M Y', strtotime($c_record['date'])) ." : Hb<8g%";
                              }

                              $tmp .= ' ' .$c_record['clinic_name'];

                              if($c_record['clinic_type'] != '')
                              {
                                 $tmp .= '-'.$c_record['clinic_type'];
                              }
                              
                              if($c_record['area_name'] != '')
                              {
                                 $tmp .= '-'.$c_record['area_name'];
                              }


                              $tmp .= '<br>';
                              $no_data = false;
                     }
          
            if($tmp != ""){         
         ?>
            <tr>
               <td style="width:30%;vertical-align:top;"><?= $a['client']['lname']?>, <?= $a['client']['fname']?> - <?= $a['client']['record_number']?></td>
               <td style="width:70%;vertical-align:top;"><?= $tmp?></td>
            </tr>
         <?php 
            }
           

         endforeach; 
          
          if($no_data == true){
	         ?>
            <tr>
               <td style="width:70%;vertical-align:top;">There is currently no people in this range.</td>
               <td style="width:30%;vertical-align:top;"></td>
            </tr>
         <?php     
	            
            }
         ?>
         
         
      </table>
   </div>
   <?php
}

function body($data) {
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
      <div class="content" style="background:#ffffff;border:1px solid #d5d5d5; box-shadow: 1px 2px 2px #d3d3d3;width:700px; margin:0 auto;">
         <div class="header" style="background:#1abc9c; color: #fff; padding:7px 15px; font-size:22px;">
            Susu mamas | ANC
         </div>
         <div class="body" style="padding:10px 15px;">
            
            <div class="hb-heading" style="background: #f5f5f2;padding:10px 5px;">HB Level: 8-10</div>
            <div>
               <?= disp_record($b1, 1); ?>
            </div>
            <div class="hb-heading" style="background: #f5f5f2;padding:10px 5px;">HB Level: 8 below</div>
            <div>
               <?= disp_record($b1, 2); ?>
            </div>
         </div>
      </div>
   </p>

   <?php
   $output = ob_get_contents();
   ob_end_clean();
   return $output;
}

$file = 'anc.json';
$json = json_decode(file_get_contents($file), true);

$emails = $json['email'];
$emails = str_replace(' ', '', $emails);
   $emails = explode(',', $emails);
   

$reports = new Reports();


if($json['schedule'] == 'daily') {
   $date = date('Y-m-d', strtotime('-1 days'));
   $data = $reports->get_hb_level_all();
   $body = body($data);
} else if ($json['schedule'] == 'weekly') {
   if(strtolower(date('l', strtotime('now'))) != $json['every']) {
      exit;
   }
   $start_date = date('Y-m-d', strtotime('-7 days'));
   $end_date = date('Y-m-d', strtotime('-1 days'));
   $data = $reports->get_hb_level_all();
   $body = body($body);
} else if($json['schedule'] == 'monthly') {
   if(date('d', strtotime('now')) != "01") {
      exit;
   }
   $start_date = date('Y-m-01', strtotime('-1 months'));
   $this_year = date('Y', strtotime('-1 months'));
   $this_month = date('m', strtotime('-1 months'));
   $last_date = cal_days_in_month(CAL_GREGORIAN,$this_month,$this_year);
   $end_date = $this_year . '-' . $this_month . '-' . $last_date;
   $data = $reports->get_hb_level_all();
   $body = body($body);
}

echo $body;

foreach($emails as $email) {
   $mail = send_mail(
      array('email' => 'admin@susumamas.org.pg', 'name' => 'Susumamas'), 
      array('email' => $email, 'name' => ''), 
      array('email' => 'admin@susumamas.org.pg', 'name' => ''), 
      'Susumamas | ANC Report', 
      $body, 
      htmlentities($body)
   );
}

?>