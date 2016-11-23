<?php 
   if (isset($_POST['btnGenReport'])){
     $sDate = $_POST['start_date'];
     $eDate = $_POST['end_date'];
     /*$c_type = $_POST['client_type'];*/
     $v_type = $_POST['visit_type'];
      $c_location = $_POST['clinic'];

       //Code added by Eric [to fix php error when checking if start_date is set]
     if($sDate == ""){
      unset($_POST['start_date']);
     }
     //End added code here
   }
 ?>
<?php header_nav_bar("list-alt", "Feeding Reports","reports") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
         <div class="page-header clearfix">
          <div id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;"><h1 style="float: left;">Feeding Reports</h1>
              <form method="POST" action="?page=feeding_reports" id="frmFeedingReport" class="form-inline" role="form" style="float: right; margin-top: -5px;">
               <div style="float:right; margin-top: 20px;">
                   <div class="form-group">
                      <label class="sr-only" for="startdate"></label>
                      <input type="text" class="form-control" name="start_date" id="start_date" placeholder="Enter Start Date" value="<?php echo isset($_POST["start_date"])?$_POST["start_date"]:""; ?>" required>
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="exampleInputPassword2"></label>
                    <input type="text" class="form-control" name="end_date" id="end_date" placeholder="Enter End Date" value="<?php echo isset($_POST["end_date"])?$_POST["end_date"]:""; ?>" required>
                  </div>
                   <input type="submit" value="Generate Report" class="btn btn-default" name="btnGenReport" style="margin-top: 5px;" />
                   <br />
                   <a id="advance_filter_toggle" href="#" style="text-decoration:underline; color:blue;">Show Advance Filter</a>
               </div>
             
             

                <br style="clear:both;" />
              <div class="advance_filter clearfix">
                <h4>Filter by:</h4>
                <div class="form-group ">
                  <!-- <select class="form-control" name="client_type">
                    <option value="">[Client Type]</option>
                        <?php //$_data = $type->get_all('client');
                        //if($_data!=false): foreach($_data['value'] as $data ): ?>
                          <option value="<?php //echo $data ?>" 
                          <?php //if(isset($_POST["client_type"]))
                        //  if($_POST["client_type"] == $data)
                        //  echo "selected" ?>>
                        <?php //echo $data ?>
                    </option>  
                      <?php// endforeach; endif; ?>
                    </select> -->
                </div> 
                 <div class="form-group">
                    <select class="form-control client_report" name="visit_type">
                      <option value="">[Visit Type]</option>
                        <?php $_data = $type->get_all('visit');
                        if($_data!=false): foreach($_data['value'] as $data ): ?>
                          <option value="<?php echo $data ?>"
                            <?php if(isset($_POST["visit_type"]))
                            if($_POST["visit_type"] == $data)
                            echo "selected" ?>>
                            <?php echo $data ?>
                        </option>  
                      <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="form-group">
                  <select class="form-control client_report" name="clinic">
                    <option value="">[Clinic Location]</option>
                    <?php                 
                    foreach($clinic->get_all() as $data ){ 
                      ?><option value="<?php echo $data['ID']; ?>"
                      <?php if(isset($_POST["clinic"]))
                            if($_POST["clinic"] == $data['ID'])
                            echo "selected" ?>>
                      <?php echo $data['clinic_name']; ?>
                      </option>
                      <?php echo "\n";
                    }
                    ?>  
                  </select>
                </div>
             </div>  <!-- // == class: advance_filter clearfix == -->
            </form>  
          </div><!-- // == id: overview == --> 
         </div> <!-- // == class: page-header clearfix == -->
        
        
                
        
       
        <?php
        if(!isset($_POST['start_date'])) :
          echo "<p>Please set start and end date.</p>";
        else:
           if($_POST['visit_type'] != ""){
              $data = $reports->get_feeding_record($_POST['start_date'], $_POST['end_date'],  $_SESSION['office_id'],
                                                  $_POST['visit_type'], $_POST['clinic']);
              $data2 = $reports->filter_feeding_by_visit_reason($data, $_POST['visit_type']);          
           }else{
              $data2 = $reports->get_feeding_record($_POST['start_date'], $_POST['end_date'],  $_SESSION['office_id'],
                                                $_POST['visit_type'], $_POST['clinic']);
           }
          if($data2==false):
            echo "<p>No Record Found in the specified date.</p>";
          else:
              $under_6_exc_fed=$Under_6_rep_fed=$under_6_mixed_fed=0;
              $exc_fed=$mix_fed=$rep_fed=0;
              if($data2!=false): foreach($data2 as $value ): 
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
              $total_exc_fed = round($exc_fed/count($data2) * 100, 2,PHP_ROUND_HALF_DOWN);
              $total_rep_fed = round($rep_fed/count($data2) * 100, 2,PHP_ROUND_HALF_DOWN);
              $total_mix_fed = round($mix_fed/count($data2) * 100, 2,PHP_ROUND_HALF_DOWN);
              $total_6_months = $Under_6_rep_fed + $under_6_mixed_fed +  $under_6_exc_fed;
        ?>
        <table class="table table-bordered table-striped table-hover table-condensed">
          <tbody>
          <tr>
            <label><h4>Overview</h4></label>
            <td><b>Percentage Of Exclusively breastfed</b></td>
            <td><?php if($exc_fed != 0) : echo $total_exc_fed; else:  echo "0"; endif; ?>%</td>
          </tr>
          <tr>
            <td><b>Percentage Of Replacement Fed</b></td>
            <td><?php if($rep_fed != 0) : echo $total_rep_fed; else:  echo "0"; endif; ?>%</td>
          </tr>
          <tr>
            <td><b>Percentage Of Mixed Feeding</b></td>
             <td><?php if($mix_fed != 0) : echo $total_mix_fed; else:  echo "0"; endif;  ?>%</td>
          </tr> 
          <tr>
            <td><b>Percentage Of Under 6mo: Exclusively breastfed</b></td>
            <td><?php if($total_6_months !=0) : echo round($under_6_exc_fed /$total_6_months * 100 ,2,PHP_ROUND_HALF_DOWN); else:  echo "0"; endif; ?>%</td>
          </tr> 
          <tr>
            <td><b>Percentage Of Under 6mo: Replacement Fed</b></td>
            <td><?php if($total_6_months !=0) : echo round($Under_6_rep_fed/$total_6_months * 100, 2,PHP_ROUND_HALF_DOWN); else:  echo "0"; endif; ?>%</td>
          </tr> 
          <tr>
            <td><b>Percentage Of Under 6mo: Mixed Feeding</b></td>
            <td><?php if($total_6_months !=0) : echo round($under_6_mixed_fed/$total_6_months * 100, 2,PHP_ROUND_HALF_DOWN); else:  echo "0"; endif; ?>%</td>
          </tr>                                   
          </tbody>
        </table>        
        
         <div class="btn-group">
          <form method="POST">
            <input type="hidden" name="func" value="export_feeding" />
            <input type="hidden" name="param1" value="excel" />
            <input type="hidden" name="sDate" value="<?php echo $sDate ?>" />
            <input type="hidden" name="eDate" value="<?php echo $eDate ?>" />
           <!--  <input type="hidden" name="client_type" value="<?php //echo $c_type ?>" /> -->
            <input type="hidden" name="visit_type" value="<?php echo $v_type ?>" />
            <input type="hidden" name="clinic" value="<?php echo $c_location ?>" />
            <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-info <?php if (enablea_and_disable_ele($_SESSION['type'], "export_excel", $_SESSION['feeding_reports']) == false) { echo "hide"; }?>"
             value="Export to Excel" />
          </form>
          <form method="POST">
            <input type="hidden" name="func" value="export_feeding" />
            <input type="hidden" name="param1" value="csv" />
            <input type="hidden" name="sDate" value="<?php echo $sDate ?>" />
            <input type="hidden" name="eDate" value="<?php echo $eDate ?>" />
          <!--   <input type="hidden" name="client_type" value="<?php //echo $c_type ?>" /> -->
            <input type="hidden" name="visit_type" value="<?php echo $v_type ?>" />
            <input type="hidden" name="clinic" value="<?php echo $c_location ?>" />
            <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-warning <?php if (enablea_and_disable_ele($_SESSION['type'], "export_csv", $_SESSION['feeding_reports']) == false) { echo "hide"; }?>" 
            value="Export to CSV" />
          </form>
        <table class="table  table-striped table-hover table-condensed" style="margin-top: 20px; margin-bottom: 70px;">
          <thead>
            <tr>
              <th><b>Record Number</b></th>
              <th><b>Name</b></th>
              <th><b>Province</b></th>
              <th><b>District</b></th>
             <!--  <th><b>LLG</b></th>   -->                  
              <!-- <th><b>Health Facility</b></th> -->                    
              <th><b>Clinic</b></th>    
              <th><b>Date</b></th>                    
              <th><b>Feeding</b></th>                    
            </tr> 
          </thead>
          <tbody>
            <?php 
             if($_POST['visit_type'] != ""){
                $data = $reports->get_feeding_record($_POST['start_date'], $_POST['end_date'],  $_SESSION['office_id'],
                                               $_POST['visit_type'], $_POST['clinic']);
                 $data2 = $reports->filter_feeding_by_visit_reason($data, $_POST['visit_type']);   
           }else{
                $data2 = $reports->get_feeding_record($_POST['start_date'], $_POST['end_date'], $_SESSION['office_id'],
                                               $_POST['visit_type'], $_POST['clinic']);
           }
           
            if($data2!=false): foreach($data2 as $data ): ?>
            <tr>              
              <td class="id record_number"><?php echo $data['record_number']; ?></td>
              <td class="fullname"><?php echo $data['fullname']; ?></td>
              <td class="province"><?php echo $data['province']; ?></td>
              <td class="district"><?php echo $data['district']; ?></td>
              <!-- <td class="llg"><?php //echo $data['llg']; ?></td> -->
              <!-- <td class="office"><?php//echo $data['office']; ?></td>  -->  
              <td class="clinic"><?php echo $data['clinic_name']; ?></td>
              <td class="clinic"><?php echo $data['date']; ?></td>
              <td class="feeding"><?php echo $data['feeding_type']; ?></td>
            </tr>                             
          <?php endforeach; endif; ?>
          </tbody>
        </table>    
        <?php endif; ?>
        <?php endif; ?>                                         
      </div><!--/span-->        
    </div>
  </div>

  <?php $reports->scripts(); ?>
