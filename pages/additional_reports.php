<?php 
//Code added by Eric [to fix php error when checking if start_date is set]
   if (isset($_POST['btnGenReport'])){
     $sDate = $_POST['start_date'];
     $eDate = $_POST['end_date'];
       
     if($sDate == ""){
      unset($_POST['start_date']);
     }

   }
        //End added code here
 ?>

<?php header_nav_bar("plus", "Additional Reports","reports") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Additional Reports
          <div class="clearfix">
            <form method="POST" action="?page=additional_reports" id="frmFeedingReport" class="form-inline" role="form" style="float: right; margin-top: -5px;">
              <div class="form-group">
                <label class="sr-only" for="startdate"></label>
                <input type="text" class="form-control" name="start_date" id="start_date" placeholder="Enter Start Date" value="<?php echo isset($_POST["start_date"])?$_POST["start_date"]:""; ?>" required>
              </div>
              <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2"></label>
                <input type="text" class="form-control" name="end_date" id="end_date" placeholder="Enter End Date" value="<?php echo isset($_POST["end_date"])?$_POST["end_date"]:""; ?>" required>
              </div>
              <div class="form-group">
                <select class="form-control" name="by" id="by" required>
                  <option value="">Select Type</option>
                  <option value="clinic">Clinic</option>
                  <option value="office">Health Facility</option>
                 <!--  <option value="llg">LLG</option> -->
                  <option value="district">District</option>
                  <option value="province">Province</option>
                </select>
              </div>
              <input type="submit" value="Generate Report" class="btn btn-default" style="margin-top: 5px;" />
            </form>   
          </div>   
          </h1> 
        </div>

        <?php
        if(!isset($_POST['start_date'])) :
          echo "<p>Please set start and end date.</p>";
        else:
         $data = $reports->get_additional_record($_POST['start_date'], $_POST['end_date'], $_POST['by'],$_POST['id']);
          if($data==false):
            echo "<p>No Record Found in the specified date.</p>";
          else:
        ?>

        <table class="table table-bordered table-striped table-hover table-condensed">
          <tbody>
          <tr>
            <label><h4>Client Statistics</h4></label>
            <td><b>Total No. Of Clients</b></td>
            <td><?php echo $data['client_count'] ?></td>
          </tr>
          <tr>
            <td><b>Total Consultations</b></td>
            <td><?php echo $data['consultation_count'] ?></td>
          </tr>
        
            <!-- hide for now, will be use in the future deveplopment-->
            <tr class="hide">
            <td><b>Total FollowUps</b></td>
            <td><?php echo $data['followup_count'] ?></td>
          </tr>
       
           
          <tr>
            <td><b>Average Consultation</b></td>
            <td><?php echo round($data['average_consultation'], 2) ?>%</td>
          </tr>                          
          </tbody>
        </table>  

        <table class="table table-bordered table-striped table-hover table-condensed">
          <tbody>
          <tr>
            <label><h4>Feeding Percentage</h4></label>
            <td><b>Exclusively breastfed</b></td>
            <td><?php echo round($data['percent_exclusively_breastfed'], 2) ?> %</td>
          </tr>
          <tr>
            <td><b>Replacement Fed  </b></td>
            <td><?php echo round($data['percent_replacement_fed'], 2) ?> %</td>
          </tr>
          <tr>
            <td><b>Mixed Feeding </b></td>
            <td><?php echo round($data['percent_mixed_feeding'], 2) ?> %</td>
          </tr>                   
          </tbody>
        </table>

        <div class="hide">
            <!-- hide for now, will be use in the future deveplopment-->
            <table class="table table-bordered table-striped table-hover table-condensed">
            <tbody>
            <tr>
              <td></td>
              <td>Completed</td>
              <td>Pending</td>
            </tr>
            <tr class="hide">
              <label><h4>FollowUp Totals</h4></label>
              <td><b>PPTCT</b></td>
              <td><?php echo $data['followup_pptct'] ?></td>
              <td>0</td>
            </tr>
            <tr>
              <td><b>Nutrition  </b></td>
              <td><?php echo $data['followup_nutrition'] ?></td>
              <td>0</td>
            </tr>
            <tr>
              <td><b>STI </b></td>
              <td><?php echo $data['followup_sti'] ?></td>
              <td>0</td>
            </tr>                   
            </tbody>
          </table> 

        </div>
       

        <?php endif; ?>
        <?php endif; ?>                                              
      </div><!--/span-->        
    </div>
  </div>

  <?php $reports->scripts(); ?>
