<?php
/** Error reporting */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
  die('This example should only be run from a Web Browser');
/** Caching to discISAM 1.0*/

$title = array( "Type", "Overall", "Male", "Female", "Childs", "six", "seven");
/** connection with the database 1.0 */
   
/** Query 1.0 */
    $data = $reports->get_client_record($_GET['srtDate'], $_GET['endDate']);
/** Create a new PHPExcel object 1.0 */
   $objPHPExcel = new PHPExcel();
   $objPHPExcel->getActiveSheet()->setTitle('Data');
   
     $objPHPExcel->getActiveSheet()->fromArray($title , null, 'A1');
/** Loop through the result set 1.0 */
    $rowNumber = 2; //start in cell 1
    while ($row = mysql_fetch_row($result)) {
       $col = 'A'; // start at column A
       foreach($row as $cell) {
          $objPHPExcel->getActiveSheet()->setCellValue($col.$rowNumber,$cell);
          $col++;
       }
       $rowNumber++;
}
   
/** Create Excel 2007 file with writer 1.0 */
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Technical.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
exit;

?>