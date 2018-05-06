<?php

class Reports extends DB{
  function __construct(){
    parent::__construct(); 
    $this->table = "tbl_records";
  }
  function get_all(){
    $data = $this->select("*"); 
    return $data;
  }

  function calc_feeding_type($date_birth, $today){
    if($date_birth != '0000-00-00'){
        $timestamp_start = strtotime($date_birth);
        $timestamp_end = strtotime($today);
        $difference = abs($timestamp_end - $timestamp_start); 
        $months = floor($difference/(60*60*24*30));
        return $months;
    }
      return -1;
  }

  function generate_report_search($client_record_header , $client_details, $file_type, $file_name){

     error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            date_default_timezone_set('Asia/Manila');

          if (PHP_SAPI == 'cli')
           die('This example should only be run from a Web Browser');
        
          /** Create a new PHPExcel object 1.0 */
          $objPHPExcel = new PHPExcel();
          $sheet = $objPHPExcel->getActiveSheet();
                              $sheet->setTitle('Client Report');
                              $sheet->setCellValue('B1','Consultation Reports');
                              $sheet->fromArray($client_record_header, null, 'A5');
                              $sheet->fromArray($client_details, null, 'A6');   
                             /* $sheet ->mergeCells('A3:C3');
                              $sheet ->mergeCells('A4:C4');
                              $sheet ->mergeCells('D9:E9');
                              $sheet ->mergeCells('D8:E8');
                              $sheet ->mergeCells('D7:E7');*/                    

          /** Create Excel 2007 file with writer 1.0 */
          //ob_end_clean();
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename=' .$file_name);
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
          $objWriter->save('php://output');
          exit;
  }
  function generate_report_catchment($sDate, $eDate, $overview_row, $client_record_header , $client_details, $file_type, $file_name){
     error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            date_default_timezone_set('Asia/Manila');

          if (PHP_SAPI == 'cli')
           die('This example should only be run from a Web Browser');
        
          /** Create a new PHPExcel object 1.0 */
          $objPHPExcel = new PHPExcel();
          $sheet = $objPHPExcel->getActiveSheet();
                              $sheet->setTitle('Catchment Report');
                              $sheet ->mergeCells('B1:D1');
                              $sheet ->mergeCells('A3:C3');
                              $sheet ->mergeCells('A4:C4');
                              $sheet->setCellValue('G1','Catchment Reports');
                              $sheet->setCellValue('F3', 'Start Date:'. ' '. $sDate);
                              $sheet->setCellValue('F4', 'End Date:'. ' '. $eDate);
                              $sheet->setCellValue('A6', 'Overview');
                              $sheet->setCellValue('A7', 'Total No. of Clients');
                              $sheet->setCellValue('A8', 'Total No. of Consultations');
                              $sheet->setCellValue('A9', 'Total No. of Referrals');
                              $sheet->setCellValue('A10', 'Average Consultation');
                              $sheet->setCellValue('E7', $overview_row["total_no_client"]);
                              $sheet->setCellValue('E8', $overview_row["total_no_consul"]);
                              $sheet->setCellValue('E9', $overview_row["total_no_referrals"]);
                              $sheet->setCellValue('E10', $overview_row["ave_no_consul"]);
                              $sheet->setCellValue('A15', 'Record Number');
                              $sheet->setCellValue('B15', 'Full Name');
                              $sheet->setCellValue('C15', 'Clinic');  
                              $sheet->setCellValue('D15', 'Consultation');  
                              $sheet->setCellValue('E15', 'Catchment Area');  
                              $sheet->setCellValue('F15', 'NHFC');  
                              //$sheet->fromArray($client_record_header, null, 'A15'); 
                              $sheet->fromArray($client_details, null, 'A16');  
    

          /** Create Excel 2007 file with writer 1.0 */
          //ob_end_clean();
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename=' .$file_name);
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
          $objWriter->save('php://output');
          exit;
  }
  function generate_report_consultation($sDate, $eDate, $overview_row, $client_record_header , $client_details, $file_type, $file_name){
     error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            date_default_timezone_set('Asia/Manila');

          if (PHP_SAPI == 'cli')
           die('This example should only be run from a Web Browser');
        
          /** Create a new PHPExcel object 1.0 */
          $objPHPExcel = new PHPExcel();
          $sheet = $objPHPExcel->getActiveSheet();
                              $sheet->setTitle('Consultation Report');
                              $sheet ->mergeCells('B1:D1');
                              $sheet ->mergeCells('A3:C3');
                              $sheet ->mergeCells('A4:C4');
                              $sheet->setCellValue('G1','consultation Reports');
                              $sheet->setCellValue('F3', 'Start Date:'. ' '. $sDate);
                              $sheet->setCellValue('F4', 'End Date:'. ' '. $eDate);
                              $sheet->setCellValue('A6', 'Overview');
                              $sheet->setCellValue('A7', 'Total No. of Clients');
                              $sheet->setCellValue('A8', 'Total No. of Consultations');
                              $sheet->setCellValue('A9', 'Total No. of Referrals');
                              $sheet->setCellValue('A10', 'Average Consultation');
                              $sheet->setCellValue('E7', $overview_row["total_no_client"]);
                              $sheet->setCellValue('E8', $overview_row["total_no_consul"]);
                              $sheet->setCellValue('E9', $overview_row["total_no_referrals"]);
                              $sheet->setCellValue('E10', $overview_row["ave_no_consul"]);
                              $sheet->setCellValue('A15', 'Record Number');
                              $sheet->setCellValue('B15', 'Gender');
                              $sheet->setCellValue('C15', 'Full Name');
                              $sheet->setCellValue('D15', 'Province');  
                              $sheet->setCellValue('E15', 'District');  
                              $sheet->setCellValue('F15', 'Health Facility');  
                              $sheet->setCellValue('G15', 'Clinic');  
                              $sheet->setCellValue('H15', 'Date'); 
                              $sheet->setCellValue('I15', 'Visit Reasons '); 
                              $sheet->setCellValue('J15', 'Consultation');  
                              $sheet->setCellValue('K15', 'Review Date');  
                              $sheet->setCellValue('L15', 'Current Age');  

                              //$sheet->fromArray($client_record_header, null, 'A15'); 
                              $sheet->fromArray($client_details, null, 'A16');  
    

          /** Create Excel 2007 file with writer 1.0 */
          //ob_end_clean();
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename=' .$file_name);
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
          $objWriter->save('php://output');
          exit;
  }

  function generate_report_fedding($sDate, $eDate,  $data_overview, $client_record_header , $client_details, $file_type, $file_name){
     error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            date_default_timezone_set('Asia/Manila');

          if (PHP_SAPI == 'cli')
           die('This example should only be run from a Web Browser');
        
          /** Create a new PHPExcel object 1.0 */
          $objPHPExcel = new PHPExcel();
          $sheet = $objPHPExcel->getActiveSheet();
                              $sheet->setTitle('Feeding Report');
                              $sheet ->mergeCells('B1:D1');
                              $sheet ->mergeCells('A3:C3');
                              $sheet ->mergeCells('A4:C4');
                              $sheet->setCellValue('G1','Feeding Reports');
                              $sheet->setCellValue('F3', 'Start Date:'. ' '. $sDate);
                              $sheet->setCellValue('F4', 'End Date:'. ' '. $eDate);
                              $sheet->setCellValue('A6', 'Overview');
                              $sheet->setCellValue('A7', 'Percentage Of Exclusively breastfed');
                              $sheet->setCellValue('A8', 'Percentage Of Replacement Fed');
                              $sheet->setCellValue('A9', 'Percentage Of Mixed Feeding');
                              $sheet->setCellValue('A10', 'Percentage Of Under 6mo: Exclusively breastfed');
                              $sheet->setCellValue('A11', 'Percentage Of Under 6mo: Replacement Fed');
                              $sheet->setCellValue('A12', 'Percentage Of Under 6mo: Mixed Feeding');
                              $sheet->setCellValue('E7', $data_overview["exc_fed"]);
                              $sheet->setCellValue('E8', $data_overview["rep_fed"]);
                              $sheet->setCellValue('E9', $data_overview["mix_fed"]);
                              $sheet->setCellValue('E10', $data_overview["under_6_exc_fed"]);
                              $sheet->setCellValue('E11', $data_overview["under_6_rep_fed"]);
                              $sheet->setCellValue('E12', $data_overview["under_6_mixed_fed"]);
                              $sheet->fromArray($client_record_header, null, 'A15'); 
                              $sheet->fromArray($client_details, null, 'A16');         

          /** Create Excel 2007 file with writer 1.0 */
          //ob_end_clean();
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename=' .$file_name);
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
          $objWriter->save('php://output');
          exit;
  }
  function visit_type_reports_excel($rep_data){

     global $type;
      $visit_reasons = array();
      $client_type = array();
      $rows= array();
      $total_consultation=0;
      $total_male = $this->count_report($rep_data,array("client_type"=>"Male"));
      $total_female = $this->count_report($rep_data,array("client_type"=>"Female"));
      $total_Child = $this->count_report($rep_data,array("client_type"=>"Child"));
      $_data = $type->get_all('visit');
       $overall=$male=$female=$Child=0;
         foreach($_data['value'] as $key => $data ){

           // create dynamic variables, used to store number of clients in one specific "visit_reason"
           ${"visit_{$key}"} = 0;
           ${"visit_male_{$key}"} = 0;
           ${"visit_female_{$key}"} = 0;
           ${"visit_Child_{$key}"} = 0;
         }
        foreach($rep_data as $k => $data ){

           $temp = json_decode($data['visit_reasons'], true);
           if ($temp != false) {
                $temp = implode(",",  $temp); 
                if($temp!=","){
                   $visit_reason = explode(",", $temp);
                   for ($i=0; $i < count($visit_reason) ; $i++) { 
                      $total_consultation++;

                      foreach($_data['value'] as $key => $value ){
                          if($visit_reason[$i] == $value) {
                            ${"visit_{$key}"}++;
                          }
                          if($visit_reason[$i] == $value AND  $data['client_type'] == "Male") ${"visit_male_{$key}"}++;
                          if($visit_reason[$i] == $value AND  $data['client_type'] == "Female") ${"visit_female_{$key}"}++;
                          if($visit_reason[$i] == $value AND  $data['client_type'] == "Child") ${"visit_Child_{$key}"}++; 
                                        
                       }
                   }  
                } //if($temp!=",")
             }else{
                $visit_reason = explode(",", $data['visit_reasons']);

                for ($i=0; $i < count($visit_reason) ; $i++) { 
                   $total_consultation++;
                   foreach($_data['value'] as $key => $value ){
                      if($visit_reason[$i] == $value) ${"visit_{$key}"}++;
                     // if($i == 0){
                        if($visit_reason[$i] == $value AND  $data['client_type'] == "Male") ${"visit_male_{$key}"}++;
                        if($visit_reason[$i] == $value AND  $data['client_type'] == "Female") ${"visit_female_{$key}"}++;
                        if($visit_reason[$i] == $value AND  $data['client_type'] == "Child") ${"visit_Child_{$key}"}++; 
                     // } 
                   }
                }  
             } //else

        }
       if($_data!=false): foreach($_data['value'] as $key => $data ): 
        if($data === 'Sick') $data = 'Sick (block)';
        if(!isset(${"visit_{$key}"})) ${"visit_{$key}"}=0;
        if(!isset(${"visit_male_{$key}"})) ${"visit_male_{$key}"}=0;
        if(!isset(${"visit_female_{$key}"})) ${"visit_female_{$key}"}=0;
        if(!isset(${"visit_Child_{$key}"})) ${"visit_Child_{$key}"}=0;
       
        if($total_consultation!=0)  $overall = round(${"visit_{$key}"}/$total_consultation * 100,1,PHP_ROUND_HALF_DOWN); 

        if($total_male!=0) $male = round(${"visit_male_{$key}"}/$total_male * 100, 1,PHP_ROUND_HALF_DOWN); 
        if($total_female!=0) $female =  round(${"visit_female_{$key}"}/$total_female * 100, 1,PHP_ROUND_HALF_DOWN) ;
        if($total_Child!=0) $Child =  round(${"visit_Child_{$key}"}/$total_Child * 100, 1,PHP_ROUND_HALF_DOWN) ;

        switch($_POST['client_type']) {
          case "Male" : 
            $rows[] = array(
                    $data, $overall. "%",${"visit_{$key}"}, 
                    $male ."%", ${"visit_male_{$key}"});                             
            break;
          case "Female" :
            $rows[] = array(
                    $data, $overall. "%",${"visit_{$key}"}, 
                    $female ."%", ${"visit_female_{$key}"});                           
            break;
          case "Child" : 
            $rows[] = array(
                    $data, $overall. "%",${"visit_{$key}"}, 
                    $Child ."%", ${"visit_Child_{$key}"});                             
            break;
          default : {
            $rows[] = array(
                    $data, $overall. "%",${"visit_{$key}"}, 
                    $male ."%", ${"visit_male_{$key}"},
                    $female ."%", ${"visit_female_{$key}"}, 
                    $Child ."%", ${"visit_Child_{$key}"});                                 
          }
        }
        
        endforeach; endif;     

        return $rows;
  }  
  function generate_client_records($name, $b_date, $d_date, $type, $phone,  $client_record_header, $data, $file_type, $file_name){
     error_reporting(E_ALL);
          ini_set('display_errors', TRUE);
          ini_set('display_startup_errors', TRUE);
          date_default_timezone_set('Asia/Manila');

          if (PHP_SAPI == 'cli')
           die('This example should only be run from a Web Browser');
          
          $type = ($type == 'Child') ? 'Unknown' : $type;
          /** Create a new PHPExcel object 1.0 */
          $objPHPExcel = new PHPExcel();
          $sheet = $objPHPExcel->getActiveSheet();
                              $sheet->setTitle('Client Report');
                              $sheet ->mergeCells('B3:E3');
                              $sheet ->mergeCells('B4:E4');
                              $sheet ->mergeCells('B5:E5');
                              $sheet ->mergeCells('B6:E6');
                              $sheet ->mergeCells('B7:E7');
                              $sheet ->mergeCells('B8:E8');
                              $sheet->setCellValue('A3', 'Overview');
                              $sheet->setCellValue('A4', 'Name:');
                              $sheet->setCellValue('A5', 'Birth Date: ');
                              $sheet->setCellValue('A6', 'Date Death: ');
                              $sheet->setCellValue('A7', 'Client Gender: ');
                              $sheet->setCellValue('A8', 'Phone Number: ');
                              //$sheet->setCellValue('A9', 'Total No. of Followups'); // hide for now will be use in the future development
                              $sheet->setCellValue('B4', $name);
                              $sheet->setCellValue('B5', $b_date);
                              $sheet->setCellValue('B6', $d_date);
                              $sheet->setCellValue('B7', $type);
                              $sheet->setCellValue('B8', $phone);
                              $sheet->fromArray($client_record_header, null, 'A12');
                              $sheet->fromArray($data, null, 'A13');

          /** Create Excel 2007 file with writer 1.0 */
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename=' .$file_name);
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
          $objWriter->save('php://output');
          exit;
  }
  function generate_report_client($sDate, $eDate,  $data, $visit_type_report, $visit_type_reports_header, $client_record_header, $client_row, $file_type, $file_name){
          error_reporting(E_ALL);
          ini_set('display_errors', TRUE);
          ini_set('display_startup_errors', TRUE);
          date_default_timezone_set('Asia/Manila');

          if (PHP_SAPI == 'cli')
           die('This example should only be run from a Web Browser');
          
          $data2_by_gender = $this->separate_by_gender($data);

          /** Create a new PHPExcel object 1.0 */
          $objPHPExcel = new PHPExcel();
          $sheet = $objPHPExcel->getActiveSheet();
                              $sheet->setTitle('Client Report');
                              $sheet ->mergeCells('B1:D1');
                              $sheet ->mergeCells('A3:C3');
                              $sheet ->mergeCells('A4:C4');
                              $sheet->setCellValue('G1','Client Reports');
                              $sheet->setCellValue('F3', 'Start Date:'. ' '. $sDate);
                              $sheet->setCellValue('F4', 'End Date:'. ' '. $eDate);
                              $sheet->setCellValue('A6', 'Overview');
                              $sheet->setCellValue('A7', 'Total No. of Clients');
                              $sheet->setCellValue('A8', 'Total No. of Consultations');
                              //$sheet->setCellValue('A9', 'Total No. of Followups'); // hide for now will be use in the future development
                              $sheet->setCellValue('D7', count($data));
                              $sheet->setCellValue('D8', $this->count_no_consultation($data));
                              //$sheet->setCellValue('D9', $this->count_report($data,array("record_type"=>"followup")));
                              $sheet->setCellValue('A11', 'Client Reports');
                              $sheet->setCellValue('A12', 'Client Gender Totals');

                            $ROW = 12;
                            if($_POST['client_type']=="Male" || $_POST['client_type']=="") {
                              $sheet->setCellValue('C'.$ROW, 'Male');
                              $sheet->setCellValue('E'.$ROW,count($data2_by_gender['Male']));
                              $sheet->setCellValue('D'.($ROW+=1), 'Under 1 Year Old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_under_1_year_old($data2_by_gender['Male']));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 1 - 4 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Male'], 1, 4));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 5 - 14 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Male'], 5, 14));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 15 - 19 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Male'], 15, 19));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 20 - 24 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Male'], 20, 24));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 25 - 30 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Male'], 25, 30));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 31 - 39 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Male'], 31, 39));
                              $sheet->setCellValue('D'.($ROW+=1), 'Older than 40');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Male'], 40, 200));
                              $sheet->setCellValue('D'.($ROW+=1), 'Unknown');
                              $sheet->setCellValue('E'.$ROW, $data2_by_gender['male_unknown_counter']);
                            }
                            if($_POST['client_type']=="Female" || $_POST['client_type']=="") {
                              
                              $sheet->setCellValue('C'.$ROW+=1,'Female');
                              $sheet->setCellValue('E'.$ROW,count($data2_by_gender['Female']));
                              $sheet->setCellValue('D'.($ROW+=1), 'Under 1 Year Old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_under_1_year_old($data2_by_gender['Female']));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 1 - 4 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Female'], 1, 4));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 5 - 14 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Female'], 5, 14));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 15 - 19 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Female'], 15, 19));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 20 - 24 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Female'], 20, 24));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 25 - 30 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Female'], 25, 30));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 31 - 39 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Female'], 31, 39));
                              $sheet->setCellValue('D'.($ROW+=1), 'Older than 40');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Female'], 40, 200));
                              $sheet->setCellValue('D'.($ROW+=1), 'Unknown');
                              $sheet->setCellValue('E'.$ROW, $data2_by_gender['female_unknown_counter']);
                            }
                            if($_POST['client_type']=="Child" || $_POST['client_type']=="") {
                              $sheet->setCellValue('C'.$ROW+=1, 'Unknown');
                              $sheet->setCellValue('E'.$ROW, count($data2_by_gender['Unknown']));
                              $sheet->setCellValue('D'.($ROW+=1), 'Under 1 Year Old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_under_1_year_old($data2_by_gender['Unknown']));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 1 - 4 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Unknown'], 1, 4));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 5 - 14 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Unknown'], 5, 14));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 15 - 19 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Unknown'], 15, 19));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 20 - 24 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Unknown'], 20, 24));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 25 - 30 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Unknown'], 25, 30));
                              $sheet->setCellValue('D'.($ROW+=1), 'Between 31 - 39 years old');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Unknown'], 31, 39));
                              $sheet->setCellValue('D'.($ROW+=1), 'Older than 40');
                              $sheet->setCellValue('E'.$ROW, $this->count_age_between($data2_by_gender['Unknown'], 40, 200));
                              $sheet->setCellValue('D'.($ROW+=1), 'Unknown');
                              $sheet->setCellValue('E'.$ROW, $data2_by_gender['unknown_unknown_counter']);
                            }

                              $sheet->setCellValue('A'.($ROW+=1), 'Client Reports');
                              $sheet->setCellValue('A'.($ROW+=1), 'Visit Type Totals');
                              $sheet->fromArray($visit_type_reports_header , null, 'E'.($ROW));
                              $sheet->fromArray($visit_type_report , null, 'E'.($ROW+=2));
                              $sheet->fromArray($client_record_header, null, 'A'.($ROW+=31));
                              $sheet->fromArray($client_row, null, 'A'.($ROW+=1));
                             // $sheet->fromArray($client_row, null, 'A33');
                          

          /** Create Excel 2007 file with writer 1.0 */
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename=' .$file_name);
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
          $objWriter->save('php://output');
          exit;
  }

  function search_visit_reason_details($rep_data, $visit){
    // if user search client reports by visit_reason
      $content = array();
      foreach($rep_data as $k => $data ){

             $temp = json_decode($data['visit_reasons'], true);
             if ($temp != false) {
                  $temp = implode(",",  $temp); 
                  if($temp!=","){
                     $visit_reason = explode(",", $temp);
                     for ($i=0; $i < count($visit_reason) ; $i++) { 
                            if($visit_reason[$i] == $visit){
                                $content[] = array("record_number"=>$data['record_number'], "fullname"=>$data['fullname'], "province"=>$data['province'],
                                              "district"=>$data['district'], "llg"=>$data['llg'], "office"=>$data['office'],
                                              "clinic_name"=>$data['clinic_name'], "ctr_consultation"=>$data['ctr_consultation']);
                            }
                                            
                    }
                 }  // if($temp!=","){
                 
               }else{
                  $visit_reason = explode(",", $data['visit_reasons']);
                  for ($i=0; $i < count($visit_reason) ; $i++) { 
                    if($visit_reason[$i] == $visit){
                         $content[] = array("record_number"=>$data['record_number'], "fullname"=>$data['fullname'], "province"=>$data['province'],
                                              "district"=>$data['district'], "llg"=>$data['llg'], "office"=>$data['office'],
                                              "clinic_name"=>$data['clinic_name'], "ctr_consultation"=>$data['ctr_consultation']);
                    }
                  }  
               } //else

    }
    return $content;
  }
  function array_key_unique($arr, $key) {
    $uniquekeys = array();
    $output     = array();
    foreach ($arr as $item) {
        if (!in_array($item[$key], $uniquekeys)) {
            $uniquekeys[] = $item[$key];
            $output[]     = $item;
        }
        else {
          $_key = array_search($item[$key], $uniquekeys);
          $output[$_key]['ctr_consultation']++;
        }
    }
    return $output;
}
  function search_by_visit_reason($rep_data, $visit){
    // if user search client reports by visit_reason
    // print_r($rep_data);
      $content = array();
      foreach($rep_data as $k => $data ){

             $temp = json_decode($data['visit_reasons'], true);
             if ($temp != false) {
                  $temp = implode(",",  $temp); 
                  if($temp!=","){
                     $visit_reason = explode(",", $temp);
                     for ($i=0; $i < count($visit_reason) ; $i++) { 
                            if($visit_reason[$i] == $visit){
                                $content[] = array(
                                              'record_number' => $data['record_number'],
                                              'date_birth' => $data['date_birth'],
                                              'client_type' => $data['client_type'],
                                              'fullname' => $data['fullname'],
                                              'province' => $data['province'],
                                              'district' => $data['district'],
                                              'office' => $data['office'],
                                              'clinic_name' => $data['clinic_name'],
                                              'date' => $data['date'],
                                              'visit_reasons' => $data['visit_reasons'],
                                              'ctr_consultation' => $data['ctr_consultation'],
                                              'current_age' => $data['current_age'],
                                              'age' => $data['age'],
                                              "ID"=>$data['ID'], "client_id"=>$data['client_id'], "clinic_id"=>$data['clinic_id'],
                                              "date"=>$data['date'], "feeding_type"=>$data['feeding_type'], "visit_reasons"=>$visit,
                                              "followup_type"=>$data['followup_type'], "record_type"=>$data['record_type'], "office_id"=>$data['office_id'],
                                              "record_number"=>$data['record_number'], "fname"=>$data['fname'], "lname"=>$data['lname'], 
                                              "date_birth"=>$data['date_birth'], "date_death"=>$data['date_death'], "client_type"=>$data['client_type'],
                                              "phone"=>$data['phone'], "place_of_birth"=>$data['place_of_birth'], "current_address"=>$data['current_address'],
                                              "ctr_consultation"=>$data['ctr_consultation'], "age"=>$data['age']);
                            }
                                            
                    }
                 }  // if($temp!=","){
                 
               }else{
                  $visit_reason = explode(",", $data['visit_reasons']);
                  for ($i=0; $i < count($visit_reason) ; $i++) { 
                    if($visit_reason[$i] == $visit){
                        $content[] = array(
                                      'record_number' => $data['record_number'],
                                      'date_birth' => $data['date_birth'],
                                      'client_type' => $data['client_type'],
                                      'fullname' => $data['fullname'],
                                      'province' => $data['province'],
                                      'district' => $data['district'],
                                      'office' => $data['office'],
                                      'clinic_name' => $data['clinic_name'],
                                      'date' => $data['date'],
                                      'visit_reasons' => $data['visit_reasons'],
                                      'ctr_consultation' => $data['ctr_consultation'],
                                      'current_age' => $data['current_age'],
                                      'age' => $data['age'],
                                      "ID">$data['ID'], "client_id"=>$data['client_id'], "clinic_id"=>$data['clinic_id'],
                                      "date"=>$data['date'], "feeding_type"=>$data['feeding_type'], "visit_reasons"=>$visit,
                                      "followup_type"=>$data['followup_type'], "record_type"=>$data['record_type'], "office_id"=>$data['office_id'],
                                      "record_number"=>$data['record_number'], "fname"=>$data['fname'], "lname"=>$data['lname'], 
                                      "date_birth"=>$data['date_birth'], "date_death"=>$data['date_death'], "client_type"=>$data['client_type'],
                                      "phone"=>$data['phone'], "place_of_birth"=>$data['place_of_birth'], "current_address"=>$data['current_address'],
                                      "ctr_consultation"=>$data['ctr_consultation'], "age"=>$data['age']);
                    }
                  }  
               } //else

    }
    return $content;
  }
  function get_unique_client_record($sDate,$eDate,$client_type,$visit_type,$clinic){
        $temp = array("start_date"=>$sDate,"end_date"=>$eDate, "client_type"=>$client_type,
                     "visit_type"=>$visit_type,"clinic"=>$clinic);

        $_data = array_filter($temp);
        $where = "";
        $bind_query = array();

        if(array_key_exists("client_type", $_data)){
          $where .= "b.client_type =  :client_type AND ";
          $bind_query['client_type']=$_data['client_type'];
        }

        if(array_key_exists("clinic", $_data)){
          //if ($_data['clinic'] != 'all') {
            $where .= "a.clinic_id =  :clinic AND ";
            $bind_query['clinic']=$_data['clinic'];
         // }
        }
        $bind_query['start_date']= $_data['start_date'];
        $bind_query['end_date']= $_data['end_date'];

       if($_SESSION['type'] == 'superreporting'){
          $query = "SELECT  b.record_number, b.date_birth, b.client_type,
               CONCAT(b.fname,' ',b.lname) AS fullname,
              province.area_name AS province,
              district.area_name AS district,
              office.area_name AS office, 
              c.clinic_name,   
              MAX(a.date) AS date, 
              a.visit_reasons,      
              a.date,        
              COUNT(*) AS ctr_consultation,
              floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) as current_age    
              FROM tbl_records AS a
              JOIN tbl_client AS b ON a.client_id = b.ID
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS district ON c.llg_id = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE $where a.date >= :start_date AND a.date <= :end_date
              AND b.ID = a.client_id
              AND a.record_type='consultation'
              GROUP BY a.client_id";
        }
        else{

             $query = "SELECT  b.record_number, b.date_birth, b.client_type,
                      CONCAT(b.fname,' ',b.lname) AS fullname,
                      province.area_name AS province,
                      district.area_name AS district,
                      office.area_name AS office, 
                      c.clinic_name,
                      MAX(a.date) AS date,  
                      a.visit_reasons, 
                      a.date,               
                      COUNT(*) AS ctr_consultation,
                      floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) as current_age
                      FROM tbl_records AS a
                      JOIN tbl_client AS b ON a.client_id = b.ID
                      JOIN tbl_clinic AS c ON a.clinic_id = c.ID
                      JOIN tbl_area AS district ON c.llg_id = district.ID
                      JOIN tbl_area AS province ON district.parent_ids = province.ID 
                      JOIN tbl_area AS office ON office.ID = a.office_id
                      WHERE $where a.date >= :start_date AND a.date <= :end_date
                      AND b.ID = a.client_id
                      AND a.record_type='consultation'
                      AND a.office_id = :office_id
                      GROUP BY a.client_id";
                      $bind_query['office_id'] = $_SESSION['office_id'];
          }
        // $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date);
        $stmt = $this->query($query,$bind_query);
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($array as $key=>$val){
          // GETTING THE AGE from birth date and visit date
          if($val['date_birth']!=null || $val['date_birth']!=""){
            //$tmp = explode('-',$val['date_birth']);
            //$age = date("Y")-$tmp[0];

            $dob = new DateTime($val['date_birth']);
            $visit_date = new DateTime($val['date']);
            $interval = $dob->diff($visit_date)->y;
            //echo $val['date'].' - '.$val['date_birth'].' = '.$interval;
            //echo "<br/>";

            $array[$key]['age']=$interval; 
          }
          else 
            $array[$key]['age']="undefined";
        }
        return $array;

  }
  function get_client_record($sDate,$eDate,$client_type,$visit_type,$clinic){
        $temp = array("start_date"=>$sDate,"end_date"=>$eDate, "client_type"=>$client_type,
                     "visit_type"=>$visit_type,"clinic"=>$clinic);

        $_data = array_filter($temp);
        $where = "";
        $bind_query = array();

        if(array_key_exists("client_type", $_data)){
          $where .= "b.client_type =  :client_type AND ";
          $bind_query['client_type']=$_data['client_type'];
        }

        if(array_key_exists("clinic", $_data)){
          //if ($_data['clinic'] != 'all') {
            $where .= "a.clinic_id =  :clinic AND ";
            $bind_query['clinic']=$_data['clinic'];
         // }
        }
        $bind_query['start_date']= $_data['start_date'];
        $bind_query['end_date']= $_data['end_date'];

       if($_SESSION['type'] == 'superreporting'){
          $query = "SELECT  b.record_number, b.date_birth, b.client_type,
               CONCAT(b.fname,' ',b.lname) AS fullname,
              province.area_name AS province,
              district.area_name AS district,
              office.area_name AS office, 
              c.clinic_name,   
              MAX(a.date) AS date, 
              a.visit_reasons,      
              a.date,        
              COUNT(*) AS ctr_consultation,
              floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) as current_age    
              FROM tbl_records AS a
              JOIN tbl_client AS b ON a.client_id = b.ID
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS district ON c.llg_id = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE $where a.date >= :start_date AND a.date <= :end_date
              AND b.ID = a.client_id
              AND a.record_type='consultation'
              GROUP BY a.ID
              ORDER BY a.client_id";
        }
        else{

             $query = "SELECT  b.record_number, b.date_birth, b.client_type,
                      CONCAT(b.fname,' ',b.lname) AS fullname,
                      province.area_name AS province,
                      district.area_name AS district,
                      office.area_name AS office, 
                      c.clinic_name,
                      MAX(a.date) AS date,  
                      a.visit_reasons, 
                      a.date,               
                      COUNT(*) AS ctr_consultation,
                      floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) as current_age
                      FROM tbl_records AS a
                      JOIN tbl_client AS b ON a.client_id = b.ID
                      JOIN tbl_clinic AS c ON a.clinic_id = c.ID
                      JOIN tbl_area AS district ON c.llg_id = district.ID
                      JOIN tbl_area AS province ON district.parent_ids = province.ID 
                      JOIN tbl_area AS office ON office.ID = a.office_id
                      WHERE $where a.date >= :start_date AND a.date <= :end_date
                      AND b.ID = a.client_id
                      AND a.record_type='consultation'
                      AND a.office_id = :office_id
                      GROUP BY a.ID
                      ORDER BY a.client_id";
                      $bind_query['office_id'] = $_SESSION['office_id'];
          }
        // $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date);
        $stmt = $this->query($query,$bind_query);
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($array as $key=>$val){
          // GETTING THE AGE from birth date and visit date
          if($val['date_birth']!=null || $val['date_birth']!=""){
            //$tmp = explode('-',$val['date_birth']);
            //$age = date("Y")-$tmp[0];

            $dob = new DateTime($val['date_birth']);
            $visit_date = new DateTime($val['date']);
            $interval = $dob->diff($visit_date)->y;
            //echo $val['date'].' - '.$val['date_birth'].' = '.$interval;
            //echo "<br/>";

            $array[$key]['age']=$interval; 
          }
          else 
            $array[$key]['age']="undefined";
        }
        return $array;

  }
  function get_client_and_catchment_record_details($sDate,$eDate,$client_type="",$visit_type="",$clinic=""){
         $temp = array("start_date"=>$sDate,"end_date"=>$eDate, "client_type"=>$client_type,
                       "visit_type"=>$visit_type,"clinic"=>$clinic);
       
            $_data = array_filter($temp);  
            $where = "";
            $bind_query = array();

            if(array_key_exists("client_type", $_data)){
              $where .= "b.client_type =  :client_type AND ";
              $bind_query['client_type']=$_data['client_type'];
            }
            
            if(array_key_exists("clinic", $_data)){
              if ($_data['clinic'] != 'all') {
                 $where .= "a.clinic_id =  :clinic AND ";
                 $bind_query['clinic']=$_data['clinic'];
              }
              
            }
            $bind_query['start_date']= $_data['start_date'];
            $bind_query['end_date']= $_data['end_date'];

            $query = "SELECT b.record_number, CONCAT(b.fname,' ',b.lname) AS fullname, c.clinic_name as name, COUNT(*) AS ctr_consultation, d.catchment_area, d.national_health_facility_code 
                  FROM tbl_records as a
                  JOIN tbl_client as b ON b.ID = a.client_id
                  JOIN tbl_clinic AS c ON c.ID = a.clinic_id
                  JOIN tbl_catchment AS d ON d.ID = a.catchment
                  WHERE a.date >= :start_date AND a.date <= :end_date 
                  AND d.clinic_id = :clinic
                  GROUP BY a.client_id";
            $stmt = $this->query($query,$bind_query);
            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $array;
    
  }
  function get_client_record_details($sDate,$eDate,$client_type="",$visit_type="",$clinic=""){
         $temp = array("start_date"=>$sDate,"end_date"=>$eDate, "client_type"=>$client_type,
                       "visit_type"=>$visit_type,"clinic"=>$clinic);
       
            $_data = array_filter($temp);  
            $where = "";
            $bind_query = array();

            if(array_key_exists("client_type", $_data)){

              
              if($_data['client_type'] === "Male" || $_data['client_type'] === "Female") {
                $where .= "b.client_type =  :client_type AND ";
                $where .= "floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) > 14 AND ";
                $bind_query['client_type']=$_data['client_type'];
              }
              else if($_data['client_type'] === "Child") {
                $where .= "floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) < 15 AND "; 
              }
              
            }
            
            if(array_key_exists("clinic", $_data)){
              if ($_data['clinic'] != 'all') {
                 $where .= "a.clinic_id =  :clinic AND ";
                 $bind_query['clinic']=$_data['clinic'];
              }
              
            }
            $bind_query['start_date']= $_data['start_date'];
            $bind_query['end_date']= $_data['end_date'];

              if($_SESSION['type'] == 'superreporting'){
                 $query = "SELECT DISTINCT b.record_number, IF(b.client_type <> 'Child', b.client_type, 'Unknown') as client_type,
                  CONCAT(b.fname,' ',b.lname) AS fullname,
                  province.area_name AS province,
                  district.area_name AS district,
                  office.area_name AS office, 
                  c.clinic_name,    
                  a.date,
                  a.visit_reasons,  
                  COUNT(*) AS ctr_consultation,
                  a.review_date,
                  floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) as current_age
                  FROM tbl_records AS a
                  JOIN tbl_client AS b ON a.client_id = b.ID
                  JOIN tbl_clinic AS c ON a.clinic_id = c.ID
                  JOIN tbl_area AS district ON c.llg_id = district.ID
                  JOIN tbl_area AS province ON district.parent_ids = province.ID 
                  JOIN tbl_area AS office ON office.ID = a.office_id
                  WHERE $where a.date >= :start_date AND a.date <= :end_date
                  AND b.ID = a.client_id
                  AND a.record_type='consultation'
                  GROUP BY a.client_id";
              }else{
                   $query = "SELECT  DISTINCT b.record_number, IF(b.client_type <> 'Child', b.client_type, 'Unknown') as client_type,
                      CONCAT(b.fname,' ',b.lname) AS fullname,
                      province.area_name AS province,
                      district.area_name AS district,
                      office.area_name AS office, 
                      c.clinic_name,  
                      a.date,  
                      a.visit_reasons,  
                      COUNT(*) AS ctr_consultation,
                      a.review_date,
                      floor( DATEDIFF(CURDATE(),STR_TO_DATE(b.date_birth, '%Y-%m-%d')) / 365 ) as current_age
                      FROM tbl_records AS a
                      JOIN tbl_client AS b ON a.client_id = b.ID
                      JOIN tbl_clinic AS c ON a.clinic_id = c.ID
                      JOIN tbl_area AS district ON c.llg_id = district.ID
                      JOIN tbl_area AS province ON district.parent_ids = province.ID 
                      JOIN tbl_area AS office ON office.ID = a.office_id
                      WHERE $where a.date >= :start_date AND a.date <= :end_date
                      AND b.ID = a.client_id
                      AND a.record_type='consultation'
                      AND a.office_id = :office_id
                      GROUP BY a.client_id";
                      $bind_query['office_id'] = $_SESSION['office_id'];
                      // AND a.office_id = :office_id
              }
            $stmt = $this->query($query,$bind_query);
            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $array;
    
  }
  function count_client($data){
    $client_array = array();
    foreach($data as $key=>$val){
      if(!in_array($val['client_id'], $client_array)){
        array_push($client_array,$val['client_id']);  
      }     
    }
    return count($client_array);
  }

  function switch_by($start_date, $end_date, $by, $id){
    switch($by){
      case "clinic":
        $query = "SELECT * FROM tbl_records WHERE `date` >= :start_date AND `date` <= :end_date AND `clinic_id` = :clinic_id  ";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "clinic_id"=>$id);
        break;
      case "office":
        $query = "SELECT * FROM tbl_records WHERE `date` >= :start_date AND `date` <= :end_date AND `office_id` = :office_id  ";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "office_id"=>$id);
        break;
      case "llg":
        $query = "  SELECT a.* 
              FROM tbl_records as a
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS llg ON c.llg_id = llg.ID
              JOIN tbl_area AS district ON llg.parent_ids = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE a.date >= :start_date AND a.date <= :end_date
              AND llg.ID = :llg_id";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "llg_id"=>$id);
        break;
      case "district":
        $query = "  SELECT a.* 
              FROM tbl_records as a
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS district ON c.llg_id = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE a.date >= :start_date AND a.date <= :end_date
              AND district.ID = :district_id";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "district_id"=>$id);
        break;
      case "province":
        $query = "  SELECT a.* 
              FROM tbl_records as a
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS district ON c.llg_id = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE a.date >= :start_date AND a.date <= :end_date
              AND province.ID = :province_id";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "province_id"=>$id);
        break;
      default:
        break;
    }
    $stmt = $this->query($query,$bind_array);
    return $stmt;
  }

  function get_consultation_record($start_date,$end_date,$by, $id){

    switch($by){
      case "clinic_catchment" : 
        $query = "SELECT DISTINCT a.*, b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, c.clinic_name as name, COUNT(*) AS ctr_consultation, d.catchment_area, d.national_health_facility_code 
                  FROM tbl_records as a
                  JOIN tbl_client as b ON b.ID = a.client_id
                  JOIN tbl_clinic AS c ON c.ID = a.clinic_id
                  JOIN tbl_catchment AS d ON d.ID = a.catchment
                  WHERE a.date >= :start_date AND a.date <= :end_date 
                  AND d.clinic_id = :clinic_id 
                  GROUP BY a.ID 
                ORDER BY a.client_id";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "clinic_id"=>$id);
        break;
      case "clinic":
        $query = "SELECT DISTINCT a.*, b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, c.clinic_name as name, COUNT(*) AS ctr_consultation 
                  FROM tbl_records as a
                  JOIN tbl_client as b ON b.ID = a.client_id
                  JOIN tbl_clinic AS c ON c.ID = a.clinic_id
                  WHERE a.date >= :start_date AND a.date <= :end_date 
                  AND a.clinic_id = :clinic_id 
                  GROUP BY a.ID 
                ORDER BY a.client_id";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "clinic_id"=>$id);
        
        break;
        
      case "office":
        if ($id != 0) {
          /* user choose to generate report from specific HC/Office */
           $query = "  SELECT DISTINCT a.*, b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, office.area_name as name, COUNT(*) AS ctr_consultation  
                FROM tbl_records as a
                JOIN tbl_client as b ON b.ID = a.client_id
                JOIN tbl_clinic AS c ON a.clinic_id = c.ID
                JOIN tbl_area AS district ON c.llg_id = district.ID
                JOIN tbl_area AS province ON district.parent_ids = province.ID 
                JOIN tbl_area AS office ON office.ID = a.office_id
                WHERE a.date >= :start_date AND a.date <= :end_date
                AND  a.office_id = :office_id
                GROUP BY a.ID 
                ORDER BY a.client_id";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "office_id"=>$id);
        }else{
          /* user choose to generate report from all HC/Office */
           $query = "  SELECT DISTINCT a.*, b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, office.area_name as name, COUNT(*) AS ctr_consultation  
                FROM tbl_records as a
                JOIN tbl_client as b ON b.ID = a.client_id
                JOIN tbl_clinic AS c ON a.clinic_id = c.ID
                JOIN tbl_area AS district ON c.llg_id = district.ID
                JOIN tbl_area AS province ON district.parent_ids = province.ID 
                JOIN tbl_area AS office ON office.ID = a.office_id
                WHERE a.date >= :start_date AND a.date <= :end_date
                GROUP BY a.ID
                ORDER BY a.client_id";       
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date);
        }
      
        break;
      case "llg":
        $query = "  SELECT DISTINCT a.*, b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, llg.area_name as name, COUNT(*) AS ctr_consultation  
              FROM tbl_records as a
              JOIN tbl_client as b ON b.ID = a.client_id
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS llg ON c.llg_id = llg.ID
              JOIN tbl_area AS district ON llg.parent_ids = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE a.date >= :start_date AND a.date <= :end_date
              AND llg.ID = :llg_id
              GROUP BY a.ID 
                ORDER BY a.client_id";        
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "llg_id"=>$id);
        break;
      case "district":
        $query = "  SELECT DISTINCT a.*, b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, district.area_name as name, COUNT(*) AS ctr_consultation 
              FROM tbl_records as a
              JOIN tbl_client as b ON b.ID = a.client_id
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS district ON c.llg_id = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE a.date >= :start_date AND a.date <= :end_date
              AND district.ID = :district_id
              GROUP BY a.ID 
                ORDER BY a.client_id";      
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "district_id"=>$id);
        break;
      case "province":
        $query = "  SELECT DISTINCT a.*, b.record_number,  CONCAT(b.fname,' ',b.lname) AS fullname, province.area_name as name, COUNT(*) AS ctr_consultation  
              FROM tbl_records as a
              JOIN tbl_client as b ON b.ID = a.client_id
              JOIN tbl_clinic AS c ON a.clinic_id = c.ID
              JOIN tbl_area AS district ON c.llg_id = district.ID
              JOIN tbl_area AS province ON district.parent_ids = province.ID 
              JOIN tbl_area AS office ON office.ID = a.office_id
              WHERE a.date >= :start_date AND a.date <= :end_date
              AND province.ID = :province_id
              GROUP BY a.ID 
                ORDER BY a.client_id";     
        $bind_array = array("start_date"=>$start_date, "end_date"=>$end_date, "province_id"=>$id);
        break;
      default:
        break;
    }
    $stmt = $this->query($query,$bind_array);
    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $array;
  }

  function get_hb_level_record($start_date,$end_date) {
    //fetch all client_id from records within the date
    $query = "SELECT DISTINCT client_id FROM tbl_records 
          WHERE date >= :start_date AND date <= :end_date AND hb_level != ''
         
          ORDER BY hb_level DESC
    ";

    // GROUP BY hb_level 

     


    $arr = array('start_date' => $start_date, "end_date" => $end_date);
    $qobj = $this->query($query, $arr);
    $array = $qobj->fetchAll(PDO::FETCH_ASSOC);

    foreach($array as $idx => $a) {
     
      //get the client info
      $query = "SELECT * from tbl_client where ID = :client_id";
      $arr = array('client_id' => $a['client_id']);
      $qobj = $this->query($query, $arr);
      $client = $qobj->fetchAll(PDO::FETCH_ASSOC);
      $array[$idx]['client']  = $client[0];

      //get the hb_levels;and tbl_clinic.office_id = ".$_SESSION['office_id']."
      //echo "session-->".$_SESSION['type'];

      if($_SESSION['type'] == 'superreporting')
          $query = "SELECT tbl_records.*, tbl_clinic.*, tbl_area.area_name FROM tbl_records  INNER JOIN tbl_clinic on tbl_records.clinic_id = tbl_clinic.ID left join tbl_area on tbl_area.ID = tbl_records.office_id WHERE client_id = :client_id AND hb_level != '' and (date >= :start_date AND date <= :end_date)  ORDER BY date DESC
      "; 
      else
          $query = "SELECT tbl_records.*, tbl_clinic.*, tbl_area.area_name FROM tbl_records  INNER JOIN tbl_clinic on tbl_records.clinic_id = tbl_clinic.ID inner join tbl_area on tbl_area.ID = tbl_records.office_id WHERE client_id = :client_id AND hb_level != '' and (date >= :start_date AND date <= :end_date) AND tbl_records.office_id = ".$_SESSION['office_id']."  ORDER BY date DESC
      ";
      $arr = array('client_id' => $a['client_id'],'start_date' => $start_date, "end_date" => $end_date);
      $qobj = $this->query($query, $arr);
      $records = $qobj->fetchAll(PDO::FETCH_ASSOC);
      $array[$idx]['records'] = $records;
      
    }
    return $array;
  }

  function get_hb_level_all() {
    
    $query = "SELECT DISTINCT client_id FROM tbl_records 
          WHERE hb_level != '' and hb_level != '10+' 
          
          ORDER BY hb_level DESC
    ";

    // GROUP BY hb_level        
    $arr = array();
    $qobj = $this->query($query, $arr);
    $array = $qobj->fetchAll(PDO::FETCH_ASSOC);

   // print_r($array);

    //exit;

    foreach($array as $idx => $a) {
      //get the client info
      $query = "SELECT * from tbl_client where ID = :client_id";
      $arr = array('client_id' => $a['client_id']);
      $qobj = $this->query($query, $arr);
      $client = $qobj->fetchAll(PDO::FETCH_ASSOC);
      $array[$idx]['client']  = $client[0];

      //get the hb_levels;
      $query = "SELECT tbl_records.*, tbl_clinic.*, tbl_area.area_name FROM tbl_records  INNER JOIN tbl_clinic on tbl_records.clinic_id = tbl_clinic.ID left join tbl_area on tbl_area.ID = tbl_records.office_id WHERE client_id = :client_id AND hb_level != ''  
            ORDER BY date DESC  limit 1
      ";

      //AND hb_level != '10+'
      $arr = array('client_id' => $a['client_id']);
      $qobj = $this->query($query, $arr);
      $records = $qobj->fetchAll(PDO::FETCH_ASSOC);
      $array[$idx]['records'] = $records;
      
    }
    return $array;
  }

  function get_hb_level_record_exact($start_date,$end_date,$by, $id) {
    $query = "SELECT DISTINCT client_id FROM tbl_records 
          WHERE date >= :start_date AND date <= :end_date AND hb_level != ''
          GROUP BY hb_level 
          ORDER BY hb_level DESC
    ";
    $arr = array('start_date' => $start_date, "end_date" => $end_date);
    $qobj = $this->query($query, $arr);
    $array = $qobj->fetchAll(PDO::FETCH_ASSOC);

    foreach($array as $idx => $a) {
      //get the client info
      $query = "SELECT * from tbl_client where ID = :client_id";
      $arr = array('client_id' => $a['client_id']);
      $qobj = $this->query($query, $arr);
      $client = $qobj->fetchAll(PDO::FETCH_ASSOC);
      $array[$idx]['client']  = $client[0];

      //get the hb_levels;
      $query = "SELECT * from tbl_records WHERE client_id = :client_id AND date >= :start_date AND date <= :end_date AND hb_level != '' 
            ORDER BY date DESC";
      $arr = array('client_id' => $a['client_id'], 'start_date' => $start_date, 'end_date' => $end_date);
      $qobj = $this->query($query, $arr);
      $records = $qobj->fetchAll(PDO::FETCH_ASSOC);
      $array[$idx]['records'] = $records;
      
    }
    return $array;
  }

  function count_report($data, $arr,$operation="=="){
    $ctr=0;
    foreach($data as $key=>$val){
      foreach($arr as $k=>$v){
        if($operation=="=="){
          if($val[$k]==$v) {
            // Special Conditions
            if($k==="client_type") {
              if($v==="Male" || $v==="Female") {
                if( $val['current_age'] > 14) {
                  $ctr++;
                }
              }
              if($v==="Child") {
                if( $val['current_age'] <= 14) { 
                  $ctr++;
                }
              }
            }
            else {
              $ctr++;
            }
          }
        }
        elseif($operation=="<"){
          if($val[$k]<$v) $ctr++;   
        }       
      }     
    }
    return $ctr;
  }
  function count_under_5_years_old($data){
    $ctr=0;
    foreach($data as $key=>$val){
      if($val['age']<5 && $val['age']>=1) $ctr++;
    }
    return $ctr;
  }
  function count_age_between($data, $start, $end){
    $ctr=0;
    foreach($data as $key=>$val){
      if($val['age']>=$start && $val['age']<=$end) {
        $ctr++;
      }
    }
    return $ctr;
  }

  function count_age_under_1_year_old($data){
    $ctr=0;
    foreach($data as $key=>$val){
      if($val['age']==0) $ctr++;
    }
    return $ctr;
  }

  function count_no_consultation($rep_data){
     $no_of_consultation=0;
     foreach($rep_data as $data ){
        $no_of_consultation+= $data['ctr_consultation'];
     }
     return $no_of_consultation;
  }

  function count_no_referrals($rep_data){
     $no_of_referrals=0;
     foreach($rep_data as $data ){
        $no_of_referrals+= ($data['referral_id'] == 0)? 0 : 1;
     }
     return $no_of_referrals;
  }

   
  function visit_type_reports($rep_data, $c_type){
      global $type;
      $visit_reasons = array();
      $client_type = array();
      $total_consultation=0;
      $total_male = $this->count_report($rep_data,array("client_type"=>"Male"));
      $total_female = $this->count_report($rep_data,array("client_type"=>"Female"));
      $total_Child = $this->count_report($rep_data,array("client_type"=>"Child"));
      $_data = $type->get_all('visit');
         foreach($_data['value'] as $key => $data ){

           // create dynamic variables, used to store number of clients in one specific "visit_reason"
           ${"visit_{$key}"} = 0;
           ${"visit_male_{$key}"} = 0;
           ${"visit_female_{$key}"} = 0;
           ${"visit_Child_{$key}"} = 0;
         }  
        foreach($rep_data as $k => $data ){
            $temp = json_decode($data['visit_reasons'], true);

           if ($temp != false) {
                $temp = implode(",",  $temp); 
                if($temp!=","){
                   $visit_reason = explode(",", $temp);
                   for ($i=0; $i < count($visit_reason) ; $i++) { 
                        $total_consultation++;

                       foreach($_data['value'] as $key => $value ){
                          if($visit_reason[$i] == $value) {
                            ${"visit_{$key}"}++;
                          }
                          if($visit_reason[$i] == $value AND  $data['client_type'] == "Male") ${"visit_male_{$key}"}++;
                          if($visit_reason[$i] == $value AND  $data['client_type'] == "Female") ${"visit_female_{$key}"}++;
                          if($visit_reason[$i] == $value AND  $data['client_type'] == "Child") ${"visit_Child_{$key}"}++; 
                                        
                       }
                   }  
                } //if($temp!=",")
             }else{
                $visit_reason = explode(",", $data['visit_reasons']);
                for ($i=0; $i < count($visit_reason) ; $i++) { 
                   $total_consultation++;
                   foreach($_data['value'] as $key => $value ){
                      if($visit_reason[$i] == $value) ${"visit_{$key}"}++;
                     // if($i == 0){
                        if($visit_reason[$i] == $value AND  $data['client_type'] == "Male") ${"visit_male_{$key}"}++;
                        if($visit_reason[$i] == $value AND  $data['client_type'] == "Female") ${"visit_female_{$key}"}++;
                        if($visit_reason[$i] == $value AND  $data['client_type'] == "Child") ${"visit_Child_{$key}"}++; 
                     // } 
                   }
                }  
             } //else

        }
       if($_data!=false): foreach($_data['value'] as $key => $data ): 
        if(!isset(${"visit_{$key}"})) ${"visit_{$key}"}=0;
        if(!isset(${"visit_male_{$key}"})) ${"visit_male_{$key}"}=0;
        if(!isset(${"visit_female_{$key}"})) ${"visit_female_{$key}"}=0;
        if(!isset(${"visit_Child_{$key}"})) ${"visit_Child_{$key}"}=0;
        if($data === 'Sick') $data = 'Sick (block)';
        ?>
         <tr>
            <td><?php echo $data ?></td>
            <td><?php if($total_consultation!=0) echo round(${"visit_{$key}"}/$total_consultation * 100,1,PHP_ROUND_HALF_DOWN); ?>%</td>
            <td><?php echo ${"visit_{$key}"} ?></td>
            <?php if($c_type==="Male" || $c_type === "") : ?>
              <td><?php if($total_male!=0) echo round(${"visit_male_{$key}"}/$total_male * 100, 1,PHP_ROUND_HALF_DOWN) ?>%</td>
              <td><?php echo ${"visit_male_{$key}"}; ?></td> 
            <?php endif; ?>
            <?php if($c_type==="Female" || $c_type === "") : ?>
              <td><?php if($total_female!=0) echo round(${"visit_female_{$key}"}/$total_female * 100, 1,PHP_ROUND_HALF_DOWN) ?>%</td>
              <td><?php echo ${"visit_female_{$key}"} ?></td>
            <?php endif; ?>
            <?php if($c_type==="Child" || $c_type === "") : ?>
              <td><?php if($total_Child!=0) echo round(${"visit_Child_{$key}"}/$total_Child * 100, 1,PHP_ROUND_HALF_DOWN) ?>%</td>
              <td><?php echo ${"visit_Child_{$key}"} ?></td>                               
            <?php endif; ?>
        </tr>
        <?php
        endforeach; endif;   
  }
  function filter_feeding_by_visit_reason($rep_data, $visit){
     $content = array();
      foreach($rep_data as $k => $data ){

             $temp = json_decode($data['visit_reasons'], true);
             if ($temp != false) {
                  $temp = implode(",",  $temp); 
                  if($temp!=","){
                     $visit_reason = explode(",", $temp);
                     for ($i=0; $i < count($visit_reason) ; $i++) { 
                            if($visit_reason[$i] == $visit){
                                $content[] = array("record_number"=>$data['record_number'], "fullname"=>$data['fullname'], "province"=>$data['province'],
                                              "district"=>$data['district'], "llg"=>$data['llg'], "office"=>$data['office'],
                                              "clinic_name"=>$data['clinic_name'], "feeding_type"=>$data['feeding_type'], "date"=>$data['date'],
                                              "date_birth"=>$data['date_birth']);
                            }
                                            
                    }
                 }  // if($temp!=","){
                 
               }else{
                  $visit_reason = explode(",", $data['visit_reasons']);
                  for ($i=0; $i < count($visit_reason) ; $i++) { 
                    if($visit_reason[$i] == $visit){
                        $content[] = array("record_number"=>$data['record_number'], "fullname"=>$data['fullname'], "province"=>$data['province'],
                                              "district"=>$data['district'], "llg"=>$data['llg'], "office"=>$data['office'],
                                              "clinic_name"=>$data['clinic_name'], "feeding_type"=>$data['feeding_type'], "date"=>$data['date'],
                                              "date_birth"=>$data['date_birth']);
                    }
                  }  
               } //else

    }
    return $content;
  }
  function get_feeding_record($start_date,$end_date, $office_id, $visit_type,$clinic){
    $temp = array("start_date"=>$start_date,"end_date"=>$end_date, "office_id"=>$office_id,
                     "visit_type"=>$visit_type,"clinic"=>$clinic);

        $_data = array_filter($temp);
        $where = "";
        $bind_query = array();

       /* if(array_key_exists("client_type", $_data)){
          $where .= "b.client_type =  :client_type AND ";
          $bind_query['client_type']=$_data['client_type'];
        }
*/
        if(array_key_exists("clinic", $_data)){
          $where .= "a.clinic_id =  :clinic AND ";
           $bind_query['clinic']=$_data['clinic'];
        }
        $bind_query['start_date']= $start_date;
        $bind_query['end_date']= $_data['end_date'];

       
    if($_SESSION['type'] != 'superreporting'){
          $query = "SELECT  DISTINCT b.record_number,
            CONCAT(b.fname,' ',b.lname) AS fullname,
            province.area_name AS province,
            district.area_name AS district,
            office.area_name AS office, 
            c.clinic_name,
            a.feeding_type, 
            a.visit_reasons, 
            a.date,
            b.date_birth        
            FROM tbl_records AS a
            JOIN tbl_client AS b ON a.client_id = b.ID
            JOIN tbl_clinic AS c ON a.clinic_id = c.ID
            JOIN tbl_area AS district ON c.llg_id = district.ID
            JOIN tbl_area AS province ON district.parent_ids = province.ID 
            JOIN tbl_area AS office ON office.ID = a.office_id
            WHERE $where a.date BETWEEN :start_date AND :end_date 
            AND a.feeding_type <>  'N/A'
            AND b.office_id =  :office_id";
      
            /*$bind_array=array("start_date"=>$start_date, "end_date"=>$end_date, "office_id"=>$_SESSION['office_id']);*/
             $bind_query['start_date']= $_data['start_date'];
             $bind_query['end_date']= $_data['end_date'];
            $bind_query['office_id']= $_SESSION['office_id'];
       // }
    }else{
      $query = "SELECT  b.record_number,
            CONCAT(b.fname,' ',b.lname) AS fullname,
            province.area_name AS province,
            district.area_name AS district,
            office.area_name AS office, 
            c.clinic_name,
            a.feeding_type, 
            a.visit_reasons,
            a.date, 
            b.date_birth                      
            FROM tbl_records AS a
            JOIN tbl_client AS b ON a.client_id = b.ID
            JOIN tbl_clinic AS c ON a.clinic_id = c.ID
            JOIN tbl_area AS district ON c.llg_id = district.ID
            JOIN tbl_area AS province ON district.parent_ids = province.ID 
            JOIN tbl_area AS office ON office.ID = a.office_id
            WHERE $where a.date >= :start_date AND a.date <= :end_date 
            AND a.feeding_type <> 'N/A'";
          /*  GROUP BY a.client_id $bind_array=array("start_date"=>$start_date, "end_date"=>$end_date);*/
           $bind_query['start_date']= $start_date;
           $bind_query['end_date']= $_data['end_date'];
    }
    
    $stmt = $this->query($query,$bind_query);
    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $array;
  }


  function get_settings() {
    $query = "SELECT * FROM tbl_email_jobs ORDER BY ID desc";
    $obj1 = $this->query($query, array());
    $data = $obj1->fetchAll(PDO::FETCH_ASSOC);
    return $data;
  }

  function get_additional_record($start_date,$end_date,$by, $id){
    $stmt = $this->switch_by($start_date, $end_date, $by, $id);

    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $array['client_count']=$this->count_client($array);
    
    $consultation = $followup = $feeding_type = 0;
    $c1=$c2=$c3=0;
    $pptct = $nutrition = $sti = 0;

    foreach($array as $key=>$val){
      if($val['record_type']=="consultation") $consultation++;
      if($val['record_type']=="followup") $followup++;
      switch($val['feeding_type']){
        case "Exclusively breastfed": 
          $c1++;        
          break;
        case "Replacement Fed": 
          $c2++; 
          break;
        case "Mixed Feeding": 
          $c3++;
          break; 
      }
      switch($val['followup_type']){
        case "PPTCT": $pptct++; break;
        case "Nutrition": $nutrition++; break;
        case "STI": $sti++; break;
      }
    }

    $array['consultation_count']=$consultation;
    $array['followup_count']=$followup;
    $array['average_consultation']= ($array['client_count']!=0) ? $consultation/$array['client_count'] : 0 ;

    $array['percent_exclusively_breastfed']=($array['client_count']!=0) ? $c1/$array['client_count'] : 0 ;
    $array['percent_replacement_fed']= ($array['client_count']!=0) ? $c2/$array['client_count'] : 0 ;
    $array['percent_mixed_feeding']=($array['client_count']!=0) ? $c3/$array['client_count'] : 0 ;

    $array['followup_pptct']=$pptct;
    $array['followup_nutrition']=$nutrition;
    $array['followup_sti']=$sti;
    return $array;
  }

  function scripts(){
    global $clinic, $office, $llg, $district, $province;
    ?>
    <script>
      $(document).ready(function (){
        $(".col-md-9 form").on('submit',function(){
          show_loader($);
          close_loader($);
        });
      });
      $("#advance_filter_toggle").on('click',function(){
        if($(this).html()=="Show Advance Filter"){
          $(".advance_filter").addClass('active');
          $(this).html("Hide Advance Filter");
        }          
        else{
          $(".advance_filter").removeClass('active');
          $(this).html("Show Advance Filter");
        }
          
      });

      $("#by").change(function(){
        //show_loader($);
        choice = $(this).find('option:selected').val();
        if(choice!=""){
          // GENERATE DYNAMIC FIELD DETAIL
          $("#by_detail").remove();

          element = "<div class='form-group' id='by_detail'><select class='form-control' name='id' id='id' required>";
          switch(choice.toLowerCase()){
            case "clinic":
              element += "<option value=''>--[Choose "+choice.toLowerCase()+"]--</option>";
              <?php $data = $clinic->get_all();
                      if($data!=false): foreach($data as $data ): ?>
                        element += "<option value='<?php echo $data['ID']; ?>'><?php echo $data['clinic_name']; ?></option>";
                      <?php endforeach; endif; ?>
              break;
            case "office":
              element += "<option value=''>--[Choose Health Facility]--</option>";
              <?php 
                if (enablea_and_disable_ele($_SESSION['type'], "generate_all_hc", $_SESSION['consultation_reports']) == true || $_SESSION['type'] == 'superreporting'){
                     ?> element += "<option value='0'>--[All HC]--</option>"; <?php } ?>
              <?php
                    if (enablea_and_disable_ele($_SESSION['type'], "generate_other_hc", $_SESSION['consultation_reports'])){
                        $data = $office->get_all(); // access all office/HC
                    }else{ $data = $office->get_all_wsession(); } // access offices/HC in current district only }
                      if($data!=false): foreach($data as $data ): ?>
                        <?php if ($data['ID'] == $_SESSION['office_id']) {
                          if (enablea_and_disable_ele($_SESSION['type'], "generate_current_hc", $_SESSION['consultation_reports'])){ ?>
                           element += "<option value='<?php echo $data['ID']; ?>'><?php echo $data['area_name']; ?></option>";
                          
                        <?php }
                        }else{
                          ?>
                          element += "<option value='<?php echo $data['ID']; ?>'><?php echo $data['area_name']; ?></option>";
                       <?php  } ?>
                        
                      <?php endforeach; endif; ?>
              break;
            case "llg":
              element += "<option value=''>--[Choose "+choice.toLowerCase()+"]--</option>";
              <?php $data = $llg->get_all();
                      if($data!=false): foreach($data as $data ): ?>
                        element += "<option value='<?php echo $data['ID']; ?>'><?php echo $data['area_name']; ?></option>";
                      <?php endforeach; endif; ?>
                      break;
                    case "district":
              element += "<option value=''>--[Choose "+choice.toLowerCase()+"]--</option>";
              <?php $data = $district->get_all();
                      if($data!=false): foreach($data as $data ): ?>
                        element += "<option value='<?php echo $data['ID']; ?>'><?php echo $data['area_name']; ?></option>";
                      <?php endforeach; endif; ?>
                      break;
                    case "province":
              element += "<option value=''>--[Choose "+choice.toLowerCase()+"]--</option>";
              <?php $data = $province->get_all();
                      if($data!=false): foreach($data as $data ): ?>
                        element += "<option value='<?php echo $data['ID']; ?>'><?php echo $data['area_name']; ?></option>";
                      <?php endforeach; endif; ?>
                      break;
          } 
          element += "</select></div>";
          //close_loader($,"#newClientModal");
          $(this).parent().after(element);
        }
      })
      
      
    </script>
    <?php
  }

  // Create array that separates format per gender
  function separate_by_gender($arr) {
    $res['Female'] = [];
    $res['Male'] = [];
    $res['Unknown'] = [];
    $res['female_unknown_counter'] = 0;
    $res['male_unknown_counter'] = 0;
    $res['unknown_unknown_counter'] = 0;

    foreach($arr as $val) {
      switch($val['client_type']) {
        case "Male" : 
          array_push($res['Male'], $val); 
          if($val['date_birth'] == '0000-00-00') $res['male_unknown_counter']++;
          break;
        case "Female" : 
          array_push($res['Female'], $val); 
          if($val['date_birth'] == '0000-00-00') $res['female_unknown_counter']++;
          break;
        case "Child" : 
          array_push($res['Unknown'], $val); 
          if($val['date_birth'] == '0000-00-00') $res['unknown_unknown_counter']++;
          break;
      }
    }


    return $res;
  }
  function display_visit_reasons($reasons){
      //json_decode( stripslashes( $post_data ) );
     $temp = json_decode($reasons, true);
     if ($temp != false) {
        $temp = implode(", ",  $temp); 
        if($temp==",") return "";
         else return $temp;
     }else{
      if($reasons != 'null')
        return $reasons;
      else
        return '';
     }
   
  }
}