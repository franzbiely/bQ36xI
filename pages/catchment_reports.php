<?php 
  if (isset($_POST['btnGenReport'])){
       $sDate = $_POST['start_date'];
       $eDate = $_POST['end_date'];
       $select_by = $_POST['by'];
       if (isset($_POST['clinic_id'])) {
         $select_id = $_POST['clinic_id'];
       }
   }
 ?>
<?php header_nav_bar("phone-alt", "Catchment Reports","reports") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Catchment Reports</h1>
          <div class="clearfix">
            <form method="POST" action="?page=catchment_reports" id="frmClientReport" class="form-inline" role="form" style="float: right; margin-top: -5px;">
              <input type="hidden" name="by" value="clinic_catchment" />
              <div class="form-group">
                <label class="sr-only" for="startdate"></label>
                <input type="text" autocorrect="off" autocomplete="off" class="form-control" name="start_date" id="start_date" placeholder="Enter Start Date" value="<?php echo isset($_POST["start_date"])?$_POST["start_date"]:""; ?>" required>
              </div>
              <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2"></label>
                <input type="text" autocorrect="off" autocomplete="off" class="form-control" name="end_date" id="end_date" placeholder="Enter End Date" value="<?php echo isset($_POST["end_date"])?$_POST["end_date"]:""; ?>" required>
              </div>
                <?php if(enablea_and_disable_ele($_SESSION['type'], "generate_all_hc", $_SESSION['consultation_reports']) == true || $_SESSION['type'] == 'superreporting') : ?>
                  <div class="form-group">
                    <select class="form-control" id="healthFacility" required>
                      <option value="">--[Choose Health Facility]--</option>
                      <?php $catchment->get_office_options(); ?>
                    </select>
                  </div>
                <?php else : ?>
                  <div class="form-group">
                    <span id="clinic-dropdown">&nbsp;</span>
                  </div>
                <?php endif; ?>
              <input type="submit" value="Generate Report" name="btnGenReport" class="btn btn-default" style="margin-top: 5px;" />
            </form>      
          </div> 
        </div>

        <?php
        if(!isset($_POST['start_date'])) :
          echo "<p>Please set start and end date.</p>";
        else:
         $data = $reports->get_consultation_record($_POST['start_date'], $_POST['end_date'],$_POST['by'],$_POST['clinic_id']);
         $total_no_client = $reports->count_client($data);
         $total_no_consul = $reports->count_no_consultation($data);
         $total_no_referrals = $reports->count_no_referrals($data);
         if($total_no_consul != 0)$ave_no_consul = $total_no_consul/$total_no_client;
        if (isset($_POST['by']) AND isset($_POST['id'])) {
          // use in the url as data to be use in export report function
          $by = $_POST['by'];
          $id = $_POST['id'];
        }
         
          if($data==false):
            echo "<p>No catchment record found in the specified date and \"clinic\" field.</p>";
          else:
        ?>
        <table class="table table-bordered table-striped table-hover table-condensed">
          <tbody>
          <tr>
            <label><h4>Overview</h4></label>
            <td><b>Total No. of Clients</b></td>
            <td><?php echo $total_no_client ?></td>
          </tr>
          <tr>
            <td><b>Total No. of Consultations</b></td>
            <td><?php echo $total_no_consul?></td>
          </tr>
          <tr>
            <td><b>Total No. of Referrals</b></td>
            <td><?php echo $total_no_referrals ?></td>
          </tr>
          <tr>
            <td><b>Average Consultation</b></td>
            <td><?php echo round($ave_no_consul, 1,PHP_ROUND_HALF_DOWN); ?></td>
          </tr>                                   
          </tbody>
        </table>  
        <div class="btn-group">
          <form method="POST">
            <input type="hidden" name="func" value="export_catchment" />
            <input type="hidden" name="param1" value="excel" />
            <input type="hidden" name="sDate" value="<?php echo $sDate ?>" />
            <input type="hidden" name="eDate" value="<?php echo $eDate ?>" />
            <input type="hidden" name="select_by" value="<?php echo $select_by ?>" />
            <input type="hidden" name="select_id" value="<?php echo $select_id ?>" />
            <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-info <?php if (enablea_and_disable_ele($_SESSION['type'], "export_excel", $_SESSION['consultation_reports']) == false) { echo "hide"; }?>" 
            value="Export to Excel" />
          </form>
          <form method="POST">
            <input type="hidden" name="func" value="export_catchment" />
            <input type="hidden" name="param1" value="csv" />
            <input type="hidden" name="sDate" value="<?php echo $sDate ?>" />
            <input type="hidden" name="eDate" value="<?php echo $eDate ?>" />
            <input type="hidden" name="select_by" value="<?php echo $select_by ?>" />
            <input type="hidden" name="select_id" value="<?php echo $select_id ?>" />
            <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-warning <?php if (enablea_and_disable_ele($_SESSION['type'], "export_csv", $_SESSION['consultation_reports']) == false) { echo "hide"; }?>" 
            value="Export to CSV" />
          </form>
        </div>
        <table class="table  table-striped table-hover table-condensed" style="margin-top: 20px; margin-bottom: 70px;">
          <thead>
            <tr>
              <th><b>Record Number</b></th>
              <th><b>Full Name</b></th>
              <th><b>Clinic</b></th>
              <th><b>Date</b></th>
              <th><b>Consultation</b></th>
              <th><b>Catchment Area</b></th>
              <th><b>NHFC</b></th>
            </tr>
          </thead>
          <tbody>
            <?php 
            //$data = $reports->get_consultation_record_details($_POST['start_date'], $_POST['end_date'],$_POST['by'],$_POST['id']);
            $_referral = $type->get_all('referral');
           if($data!=false): foreach($data as $data ):  ?>
            <tr>              
              <td class="id record_number"><?php echo $data['record_number']; ?></td>
              <td class="fullname"><?php echo $data['fullname']; ?></td>
              <td class="clinic"><?php echo $data['name']; ?></td>
              <td class="clinic"><?php echo $data['date']; ?></td>
              <td class="consultation"><?php echo $data['ctr_consultation']; ?></td> 
              <td class="catchment_area"><?php  echo $data['catchment_area']; ?> </td> 
              <td class="national_health_facility_code"><?php  echo $data['national_health_facility_code']; ?> </td> 
            </tr>                             
          <?php endforeach; endif; ?>
          </tbody>
        </table>
          <?php endif; ?>
        <?php endif; ?>                                              
      </div><!--/span-->        
    </div>
  </div>
  <?php  $catchment->scripts_report(); ?>
