<?php 
   if (isset($_POST['btnGenReport'])){
       $sDate = $_POST['start_date'];
       $eDate = $_POST['end_date'];
       $c_type = $_POST['client_type'];
       $v_type = $_POST['visit_type'];
       $c_location = $_POST['clinic'];

       //Code added by Eric [to fix php error when checking if start_date is set]
     if($sDate == ""){
      unset($_POST['start_date']);
     }
     //End added code here
   }

  
   ?>
   <?php header_nav_bar("user", "Client Reports","reports") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="page-header clearfix">
          
          <div id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;"><h1 style="float: left;">Client Reports</h1> 
            <form method="POST" action="?page=client_reports" id="frmClientReport" class="form-inline" role="form" style="float: right; margin-top: -5px;">
             <div style="float:right; margin-top: 20px;">
              <div class="form-group">
                <label class="sr-only" for="startdate"></label>
                <input type="text" autocorrect="off" autocomplete="off" class="form-control client_report" name="start_date" id="start_date" placeholder="Enter Start Date" value="<?php echo isset($_POST["start_date"])?$_POST["start_date"]:""; ?>" required>                
              </div>
              <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2"></label>
                <input type="text" autocorrect="off" autocomplete="off" class="form-control client_report" name="end_date" id="end_date" placeholder="Enter End Date" value="<?php echo isset($_POST["end_date"])?$_POST["end_date"]:""; ?>" required>
              </div>

               <input type="submit" value="Generate Report" class="btn btn-default" name="btnGenReport" id="btnGenReport" style="margin-top: 5px;" />
               <br />
               <a id="advance_filter_toggle" href="#" style="text-decoration:underline; color:blue;">Show Advance Filter</a>
             </div>

            
            <br style="clear:both;" />
              <div class="advance_filter clearfix">
                <h4>Filter by:</h4>
                <div class="form-group ">
                  <select class="form-control" name="client_type">
                    <option value="">[Client Gender]</option>
                        <?php $_data = $type->get_all('client');
                        unset($_data['value'][2]);
                        if($_data!=false): foreach($_data['value'] as $data ): ?>
                          <option value="<?php echo $data ?>" 
                          <?php if(isset($_POST["client_type"]))
                          if($_POST["client_type"] == $data)
                          echo "selected" ?>>
                        <?php echo $data ?>
                    </option>  
                      <?php endforeach; endif; ?>
                    </select>
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
                    //if (enablea_and_disable_ele($_SESSION['type'], "generate_other_hc", $_SESSION['client_reports'])){
                        foreach($clinic->get_all() as $data ){ 
                          ?><option value="<?php echo $data['ID']; ?>"
                          <?php if(isset($_POST["clinic"]))
                                if($_POST["clinic"] == $data['ID'])
                                echo "selected" ?>>
                          <?php echo $data['clinic_name']; ?>
                          </option>
                          <?php echo "\n";
                        }
                    //}            
                    ?>  
                  </select>
                </div>
                
             </div> 
             </form>         
          </div> 
          

        </div>

        <?php
        if(!isset($_POST['start_date'])) :
          echo "<p>Please set start and end date.</p>";
        else:  
          if($_POST['visit_type'] != ""){
              $data = $reports->get_client_record($_POST['start_date'], $_POST['end_date'], $_POST['client_type'], $_POST['visit_type'], $_POST['clinic']);
              $data2 = $reports->search_by_visit_reason($data, $_POST['visit_type']);
          }else{
              $data2 = $reports->get_unique_client_record($_POST['start_date'], $_POST['end_date'], $_POST['client_type'], $_POST['visit_type'], $_POST['clinic']);
          }
          if($data2==false):
            echo "<p>No Record Found in the specified date or filter.</p>";
          else:
        ?>
      <?php
      $data2 = $reports->array_key_unique($data2, 'record_number');
      $data2_by_gender = $reports->separate_by_gender($data2);
      ?>
        </br>
        <table class="table table-bordered table-striped table-hover table-condensed">
          <tbody>
          <tr>
            <label><h4>Overview</h4></label>
            <td><b>Total No. of Clients</b></td>
            <td><?php echo count($data2) ?></td>
          </tr>
          <tr>
            <td><b>Total No. of Consultations</b></td>
            <td><?php echo $reports->count_no_consultation($data2) ?></td>
          </tr>
         
              <!-- hide for now will be use in the future development-->
            <tr class="hide">
              <td><b>Total No. of Followups</b></td>
            </tr>  
         

          </tbody>
        </table> 

        <table class="table  table-striped table-hover table-condensed"> 
          <tbody>
            <tr>
              <label><h4>Client Reports</h4></label> 
              <td rowspan="40"><b>Client Gender Totals</b></td>  
              <?php if($c_type === "Male" || $c_type === "") : ?>

                <td>Male</td>
                <td></td>
                <td><?php echo count($data2_by_gender['Male']) ?></td>
              <?php endif; ?>
            </tr>  
            
            <?php if($c_type === "Male" || $c_type === "") : ?>
            <!-- AGE GROPUINGS FOR MALE -->
            <tr><td></td>
              <td>< 12 months of Age</td><td><?php echo $reports->count_age_under_1_year_old($data2_by_gender['Male']) ?></td>
            </tr>  
            <tr>
              <td></td><td>1 - 4 years of Age</td>
              <td><?php echo $reports->count_age_between($data2_by_gender['Male'], 1, 4) ?></td>
            </tr>  
            <tr>
              <td></td><td>5 - 14 years of Age</td>
              <td><?php echo $reports->count_age_between($data2_by_gender['Male'], 5, 14) ?></td>
            </tr>
            <tr>
              <td></td><td>15 - 19 years of Age</td>
              <td><?php echo $reports->count_age_between($data2_by_gender['Male'], 15, 19) ?></td>
            </tr>                                  
            <tr>
              <td></td><td>20 - 24 years of Age</td>
              <td><?php echo $reports->count_age_between($data2_by_gender['Male'], 20, 24) ?></td>
            </tr> 
            <tr>
              <td></td><td>25 - 30 years of Age</td>
              <td><?php echo $reports->count_age_between($data2_by_gender['Male'], 25, 30) ?></td>
            </tr>   
            <tr>
              <td></td><td>31 - 39 years of Age</td>
              <td><?php echo $reports->count_age_between($data2_by_gender['Male'], 31, 39) ?></td>
            </tr>   
            <tr>
              <td></td><td>Older than 40</td>
              <td><?php echo $reports->count_age_between($data2_by_gender['Male'], 40, 200) ?></td>
            </tr>   
            <tr>
              <td></td><td>Unkown</td>
              <td><?php echo $data2_by_gender['male_unknown_counter'] ?></td>
            </tr>  
            <!-- / -->
            <?php endif; ?>

            <?php if($c_type === "Female" || $c_type === "") : ?>    
              <tr>
                <td>Female</td>
                <td></td>
                <td><?php echo count($data2_by_gender['Female']) ?></td>
              </tr>

              <!-- AGE GROPUINGS FOR FEMALE -->
              <tr><td></td>
                <td>< 12 months of Age</td><td><?php echo $reports->count_age_under_1_year_old($data2_by_gender['Female']) ?></td>
              </tr>  
              <tr>
                <td></td><td>1 - 4 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Female'], 1, 4) ?></td>
              </tr>  
              <tr>
                <td></td><td>5 - 14 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Female'], 5, 14) ?></td>
              </tr>
              <tr>
                <td></td><td>15 - 19 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Female'], 15, 19) ?></td>
              </tr>                                  
              <tr>
                <td></td><td>20 - 24 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Female'], 20, 24) ?></td>
              </tr> 
              <tr>
                <td></td><td>25 - 30 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Female'], 25, 30) ?></td>
              </tr>   
              <tr>
                <td></td><td>31 - 39 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Female'], 31, 39) ?></td>
              </tr>   
              <tr>
                <td></td><td>Older than 40</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Female'], 40, 200) ?></td>
              </tr>   
              <tr>
                <td></td><td>Unkown</td>
                <td><?php echo $data2_by_gender['female_unknown_counter'] ?></td>
              </tr>  
            <!-- / -->

            <?php endif; ?>
            <?php if($c_type === "Child" || $c_type === "") : ?>
              <tr>
                <td>Unknown</td>
                <td></td>
                <td><?php echo count($data2_by_gender['Unknown']) ?></td>
              </tr>
            
              <!-- AGE GROPUINGS FOR UNKOWWN -->
              <tr><td></td>
                <td>< 12 months of Age</td><td><?php echo $reports->count_age_under_1_year_old($data2_by_gender['Unknown']) ?></td>
              </tr>  
              <tr>
                <td></td><td>1 - 4 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Unknown'], 1, 4) ?></td>
              </tr>  
              <tr>
                <td></td><td>5 - 14 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Unknown'], 5, 14) ?></td>
              </tr>
              <tr>
                <td></td><td>15 - 19 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Unknown'], 15, 19) ?></td>
              </tr>                                  
              <tr>
                <td></td><td>20 - 24 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Unknown'], 20, 24) ?></td>
              </tr> 
              <tr>
                <td></td><td>25 - 30 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Unknown'], 25, 30) ?></td>
              </tr>   
              <tr>
                <td></td><td>31 - 39 years of Age</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Unknown'], 31, 39) ?></td>
              </tr>   
              <tr>
                <td></td><td>Older than 40</td>
                <td><?php echo $reports->count_age_between($data2_by_gender['Unknown'], 40, 200) ?></td>
              </tr>   
              <tr>
                <td></td><td>Unkown</td>
                <td><?php echo $data2_by_gender['unknown_unknown_counter'] ?></td>
              </tr>  
            <!-- / -->
            <?php endif; ?>
                                             
          </tbody> 
        </table>           
        <table class="table  table-striped table-hover table-condensed"> 
          <tbody>
            <tr>
              <label><h4>Visit Type Reports</h4></label>
              <td rowspan="31"><b>Visit Type Totals</b></td>      
            </tr>      
            <tr>
              <th><b>Type</b></th>
              <th colspan="2"><b>Overall</b></th>
              <?php if($c_type === "Male" || $c_type === "") : ?>
                <th colspan="2"><b>Male</b></th>
              <?php endif; ?>
              <?php if($c_type === "Female" || $c_type === "") : ?>
                <th colspan="2"><b>Female</b></th>
              <?php endif; ?>
              <?php if($c_type === "Child" || $c_type === "") : ?>
                <th colspan="2"><b>Unknown</b></th>              
              <?php endif; ?>      
            </tr> 
            <?php 
            $showDeprecatedMessage = false;
            foreach($data2 as $data) {
              if( strtotime($data['date']) < strtotime('2018-04-01') ) {
                $showDeprecatedMessage = true;
              }
            }
            ?>
            <?php if($showDeprecatedMessage) : ?>
              <p class="yellowme"><strong>Notice : </strong><br />Visit Reasons for Consultations prior to the <strong>1st of April, 2018</strong> have been deprecated and cannot be displayed. <br />Please refer to Client paper Record.</p>
            <?php endif; ?>
            <?php $reports->visit_type_reports($data2, $c_type) ?>
          </tbody> 
        </table>  
        <div class="btn-group">
          <form method="POST">
            <input type="hidden" name="func" value="export_client" />
            <input type="hidden" name="param1" value="excel" />
            <input type="hidden" name="sDate" value="<?php echo $sDate ?>" />
            <input type="hidden" name="eDate" value="<?php echo $eDate ?>" />
            <input type="hidden" name="client_type" value="<?php echo $c_type ?>" />
            <input type="hidden" name="visit_type" value="<?php echo $v_type ?>" />
            <input type="hidden" name="clinic" value="<?php echo $c_location ?>" />
            <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-info <?php if (enablea_and_disable_ele($_SESSION['type'], "export_excel", $_SESSION['client_reports']) == false) { echo "hide"; }?>" 
            value="Export to Excel" />
          </form>
          <form method="POST">
            <input type="hidden" name="func" value="export_client" />
            <input type="hidden" name="param1" value="csv" />
            <input type="hidden" name="sDate" value="<?php echo $sDate ?>" />
            <input type="hidden" name="eDate" value="<?php echo $eDate ?>" />
            <input type="hidden" name="client_type" value="<?php echo $c_type ?>" />
            <input type="hidden" name="visit_type" value="<?php echo $v_type ?>" />
            <input type="hidden" name="clinic" value="<?php echo $c_location ?>" />
            <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-warning <?php if (enablea_and_disable_ele($_SESSION['type'], "export_csv", $_SESSION['client_reports']) == false) { echo "hide"; }?>" 
            value="Export to CSV" />
          </form>
        </div>
        <table class="table  table-striped table-hover table-condensed" style="margin-top: 20px; margin-bottom: 70px;">
          <thead>
            <tr>
              <th><b>&nbsp;</b></th>
              <th><b>Record Number</b></th>
              <th><b>Full Name</b></th>
              <th><b>Province</b></th>
              <th><b>District</b></th>
             <!--  <th><b>LLG</b></th> -->
              <!-- <th><b>Health Facility</b></th> -->
              <th><b>Clinic</b></th>
              <th><b>Date</b></th>
              <th><b>Consultation</b></th>
              <th><b>Age</b></th>
              <th><b>Gender</b></th>
            </tr>
          </thead>
          <tbody>
            <?php
            // print_r($data2);
          //   if($_POST['visit_type'] != ""){
          //     $data = $reports->get_client_record_details($_POST['start_date'], $_POST['end_date'], $_POST['client_type'], $_POST['visit_type'], $_POST['clinic']);
          //     $data2 = $reports->search_visit_reason_details($data, $_POST['visit_type']);   
          // }else{
          //     $data2 = $reports->get_client_record_details($_POST['start_date'], $_POST['end_date'], $_POST['client_type'], $_POST['visit_type'], $_POST['clinic']);
          // }

            if($data2!=false): $x=0; foreach($data2 as $key=>$data ): $x++; 
            ?>
            <tr>  
              <td class="counter"><?php echo $x; ?></td>     
              <td class="id record_number"><?php echo $data['record_number']; ?></td>
              <td class="fullname"><?php echo $data['fullname']; ?></td>
              
              <td class="province"><?php echo $data['province']; ?></td>
              <td class="district"><?php echo $data['district']; ?></td>
             <!--  <td class="llg"><?php //echo $data['llg']; ?></td> -->
              <!-- <td class="office"><?php //echo $data['office']; ?></td>   -->
              <td class="clinic"><?php echo $data['clinic_name']; ?></td>
              <td class="clinic"><?php echo $data['date']; ?></td>
              <td class="consultation"><?php echo $data['ctr_consultation']; ?></td>
              <td class="current_age"><?php echo $data['current_age']; ?></td>
              <td class="gender"><?php echo ($data['client_type']=="Child") ? "Unknown" : $data['client_type']; ?></td>
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
