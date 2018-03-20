


<?php header_nav_bar("home", "Records") ?>

  <?php $client_info = $client->get_personal_info($_GET['cid']); ?>
  <?php 
    // check if client has date_birth, date_date and phone.

    
    if (isset($client_info['date_birth'])){ $date_birth = $client_info['date_birth']; }else{ $date_birth = "Unset"; }
    if (isset($client_info['date_death'])){ $date_death = $client_info['date_death']; }else{ $date_death = "Unset"; }
    if (isset($client_info['phone'])){ $phone = $client_info['phone']; }else{ $phone = "Unset"; }
               
if($_GET['p'] != "update") { 
 $datas = $record->get_consultation_records(); 
} 
$showDeprecatedMessage = false;
foreach($datas as $data) {
  if( strtotime($data['date']) < strtotime('2018-04-01') ) {
    $showDeprecatedMessage = true;
  }
}
?>  

  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main" style="margin-bottom:30px;">
        <div class="row">
          <?php  success_message($_GET['page'], $_GET['p']); ?>
           
          <div class="page-header" style="margin-top: 45px;margin-bottom: 50px;">
            <h1 id="overview" style="width: 100%; padding-top: 10px;">Personal Info<?php if($client_info['is_archived']=="1") echo " <span>- (Archived - <span style='font-size: 18px;'>".$client_info['date_archived']."</span>)</span>"; ?></h1> 

              <?php if($_GET['p']=="delete") : ?>
                  <div class="delete-options-entry" style="margin: 0 125px 45px 0;">
                    <form id="frm_client_personal_info_update" role="form" action="" method="post">
                      <input type="hidden" name="class" value="records" />
                      <input type="hidden" name="func" value="remove_consultation_records" />
                       <input type="hidden" name="id" value="<?php echo $_GET['cid']; ?>" />
                      <!-- <p class="delete-message col-md-3 col-md-offset-3">Deleting Record Please...</p> -->
                      <input type="submit" id="btn_submit" class="btn btn-danger pull-right btn_submit" value="Delete All Consultation Records" />
                    </form> 
                    <a type="button" id="btn_transfer" class="btn btn-info" style="float: right; margin-right: 5px;"data-toggle="modal" href="#newClientModal">Transfer Consultation Records</a>
                    <a href="?page=search" type="button" id="btn_back" class="btn btn-default" style="float: right; margin-right: 5px;">Back to Search page</a>
                  </div>
                   <?php $record->transfer_record_modal($_GET['cid']); ?>
              <?php endif; ?>  
              <?php if($showDeprecatedMessage) : ?>
                <p class="yellowme"><strong>Notice : </strong><br />Visit Reasons for Consultations prior to the <strong>1st of April, 2018</strong> have been deprecated and cannot be displayed. <br />Please refer to Client paper Record.</p>
              <?php endif; ?>
              <form id="frm_client_personal_info_update" role="form" action="" method="post">
                <input type="hidden" name="class" value="client" />
                <input type="hidden" name="func" value="edit" />
                	 <a style="margin-left:5px" href="?page=clients" class="btn btn-default pull-right btnedit">Cancel</a> 
                    <?php if($_GET['p']=="view") : ?>
                      <a href="?page=records&cid=<?php echo $_GET['cid'] ?>&p=update" class="btn btn-default pull-right btnedit <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false) { echo "hide"; }?>">
                        Edit Personal Info</a>
                    <?php endif; ?>
                     <?php if($_GET['p']=="update") : ?>
                        <input type="submit" class="btn btn-default pull-right btnedit" value="Save Personal Info"> 
                      <?php endif; ?> 
                      
                </div>
                    <?php if($_GET['p'] != "update"){ ?>  
                     <fieldset style="width: 100%;" disabled>
                    <?php }else{?>
                    <input type="hidden" name="id" value="<?php echo $_GET['cid'] ?>" />
                      <fieldset style="width: 100%;"> 
                      
                    <?php } ?>  
           
              <span class="required_field <?php if($_GET['p']!="update") echo "hide"; ?>" style="position: absolute; top: 70px;">* <span class="required_label">required fields.</span></span>

              <div class="col-xs-4">
                <div class="form-group">
                  <label for="recordnumber">Record Number</label><span class="required_field <?php if($_GET['p']!="update") echo "hide"; ?>">*</span>
                  <input type="text" id="disabledTextInput" class="form-control" autocapitalize="off" autocorrect="off" class="form-control" id="record_number" name="record_number" placeholder="Disabled input" value="<?php echo $client_info['record_number'] ?>" required>
                </div>
                <div class="form-group">
                   <label for="firstname">First Name</label><span class="required_field <?php if($_GET['p']!="update") echo "hide"; ?>">*</span>
                  <input type="text" id="disabledTextInput" class="form-control" autocapitalize="off" autocorrect="off" class="form-control" name="fname" id="fname" placeholder="Disabled input" value="<?php echo $client_info['fname'] ?>" required>
                </div>     
                <div class="form-group">
                  <label for="lastname">Last Name</label><span class="required_field <?php if($_GET['p']!="update") echo "hide"; ?>">*</span>
                  <input type="text" id="disabledTextInput" class="form-control" autocapitalize="off" autocorrect="off" class="form-control" name="lname" id="lname" placeholder="Disabled input" value="<?php echo $client_info['lname'] ?>" required>
                </div>  

                <?php 
                $addClass=""; 
                $note = "";
                if($client_info['date_birth']=="0000-00-00" || $client_info['date_birth']==null) {
                  $addClass=" redme";
                  $note = "Please adjust the birth date.";
                }         
                ?>
                <div class="form-group<?php echo $addClass ?>">
                  <label for="birthdate">Birth Date</label><span class="required_field <?php if($_GET['p']!="update") echo "hide"; ?> " >*</span>

                  <?php
                  $date_birth = $client_info['date_birth'];
                  if($date_birth=="0000-00-00" || $date_birth==null){          
                    ?><input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="date_birth" name="date_birth" value="<?php echo $client_info['date_birth']; ?>" style="border-color:red;" required /><?php
                  }
                  else{
                    ?><input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="date_birth" name="date_birth" value="<?php echo $client_info['date_birth']; ?>" required /><?php
                  }
                  ?>
                  <div class="alert alert-warning birthdate-warning"><strong></strong></div>
                  <?php echo $note ?>
                </div>  
                    
              </div>
              <div class="col-xs-4">
                <div class="form-group ">
                   <label for="deathdate">Date of Death</label><span <?php if($_GET['p']!="update") echo "hide"; ?>></span>
                  <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="date_death" name="date_death" value="<?php echo $date_death  ?>">
                  <div class="alert alert-warning deathdate-warning"><strong></strong></div>
                </div>     

                <?php 
                $addClass=""; 
                $note = "";
                // if($date_birth!="0000-00-00" && $date_birth!=null) {
                //   if($record->get_age($date_birth) >= 15 && $client_info['client_type']=="Child"){
                //     $addClass=" redme";
                //     $note = "Please adjust the client gender.";
                //   }
                // }
                if($client_info['client_type'] != 'Male' && $client_info['client_type'] != 'Female' ) {
                  $addClass = " redme";
                  $note = "Please adjust the client gender.";
                }
                             
                ?>

               <div class="form-group<?php echo $addClass ?>" style="position: relative;">
               <label for="clinictype">Client Gender</label><span class="required_field <?php if($_GET['p']!="update") echo "hide"; ?>">*</span>
                <select class="form-control" name="client_type" id="client_type" required>
                  <option value="">Select Client Gender</option>
                    <?php 
                    $_data = $type->get_all('client');
                  if($_data!=false): foreach($_data['value'] as $data ): ?>
                    <option value="<?php echo $data ?>" <?php if($client_info['client_type'] == $data) echo "selected" ?>><?php echo $data ?></option>  
                  <?php endforeach; endif; ?>
                </select>
                <?php echo $note ?>
              </div> 



                <div class="form-group">
                  <label for="disabledTextInput">Phone Number</label>
                  <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="phone" name="phone" class="form-control" value="<?php echo $phone ?>">
                </div>

                <?php if($date_birth!="0000-00-00" && $date_birth!=null) : ?>
                  <div class="form-group">
                    <label for="disabledTextInput">Age</label>
                    <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="age" name="age" class="form-control" value="<?php echo "  ".$record->get_age($date_birth) ." ";?>" disabled>                    
                  </div>
                <?php else : ?>
                  <div class="form-group">
                    <label for="disabledTextInput">Age</label>
                    <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="age" name="age" class="form-control" value="Please add birth date first" disabled>                    
                  </div>
                <?php endif; ?>
              </div>  

              <div class="col-xs-4">
                <?php
                
                //$related = $record->get_relationship($_GET['cid']);
                if($related = $relationship->checkRelationshipFirst($_GET['cid'])):
                  foreach($related as $rel) : ?>
                    <div class="form-group">
                      <label class="lblrelationship_type" for="disabledTextInput"><?php echo $rel['type'] ?></label>
                        <small class="pull-right">
                          <a href="#relationshipDetailModal" class="relationshipDetailModal" data-relationship-id="<?php echo $rel['ID'] ?>" data-rid="<?php echo $rel['relation_to'] ?>" data-toggle="modal">Details</a>
                        </small>
                      <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="<?php echo $rel['type'] ?>" name="<?php echo $rel['type'] ?>" class="form-control" value="<?php echo $rel['relation_to'] ?>" disabled>
                    </div>
                  <?php endforeach;         
                elseif($related  = $relationship->checkRelationshipSecond($client_info['record_number'])):
                  foreach($related as $rel) : ?>
                    <div class="form-group">
                      <label class="lblrelationship_type" for="disabledTextInput"><?php echo $rel['type'] ?></label>
                        <small class="pull-right">
                          <a href="#relationshipDetailModal" class="relationshipDetailModal" data-relationship-id="<?php echo $rel['ID'] ?>" data-rid="<?php echo $rel['record_number'] ?>" data-toggle="modal">Details</a>
                        </small>
                      <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="<?php echo $rel['type'] ?>" name="<?php echo $rel['type'] ?>" class="form-control" value="<?php echo $rel['record_number'] ?>" disabled>
                    </div>
                  <?php endforeach;
                endif;

                if($related):
                  
                endif;



                if($_GET['p']=="view") : ?>
                  <?php if(!$related) : ?>
                    <div class="relationship-container">
                      <p>This client doesn't have any related clients within this system.</p>
                      <?php
                        // if($client_info['client_type']=='Child'){                          
                        //     echo "
                      ?>
                      <p style='color:red; font-weight:bold;'>Record is a child. Please set a mother relationship.</p>
                    </div>
                  <?php endif; ?>
                <?php else: ?>                  
                  <div class="form-group">
                      <a data-toggle="modal" href="#relationshipModal" class="btn btn-default">Add Relationship</a> 
                  </div>
                <?php endif; ?>
              </div>


            </fieldset>
            <?php if($_GET['p']=="update") : ?>
            <hr />
            <div class="form-group" style="margin-left: 16px;">            
              <input type="checkbox" name="is_archived" id="is_archived" <?php if($client_info['is_archived']=="1") echo "checked"; ?> style="top: 2px;position: relative;">
              <label for="is_archived" style="font-weight:normal;">Check this if you want to archive this client record.</label>
            </div>   
            <?php endif; ?>
          </form>  
          <?php if($_GET['p'] != "update"){ ?>  
           <!-- this should only appear when purpose is viewing record--> 
             <?php are_you_sure_delete(); ?>
        <div class="page-header" style="margin-top: 75px; ">
          <h1 id="overview" style="width: 100%; padding-top: 10px;">Consultation Records
            <br>
              <?php //if ($_GET['p'] != 'delete' && $_GET['p'] != 'update'): ?>
               <!--  <div class="btn-group" style="margin-top: 15px; margin-bottom: -20px;">
                  <form method="POST">
                    <input type="hidden" name="func" value="export_client_records" />
                    <input type="hidden" name="param1" value="excel" />
                    <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-info" value="Export to Excel" />
                  </form>
                  <form method="POST">
                    <input type="hidden" name="func" value="export_client_records" />
                    <input type="hidden" name="param1" value="csv" />
                    <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-warning" value="Export to CSV" />
                  </form>
                </div> -->
              <?php //endif ?>
            <?php if($_GET['p']!="delete") : ?>
             <a type="button" class="btn btn-default <?php if (enablea_and_disable_ele($_SESSION['type'], "add_con_records", $_SESSION['records']) == false) { echo "hide"; }?>" 
              style="float: right; " id="add-consultation-btn" href="#">Add Consultation Schedule</a> 
            <?php endif; ?>
            <!-- Modal -->
            <?php $record->consultation_modal($client_info) ?> 
          </h1> 
        </div>

        <div class="tblcontainer" data-type="client">
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>Consultation Date</th>
              <th>Clinic Attended</th>
              <th>Visit Reasons</th>
              <?php 
              if($client_info['client_type']=="Child") {
                echo '<th>Feeding Type</th>';
              } ?>
              <?php 
              if($client_info['client_type']=="Female" && $record->has_ANC_visits($datas)) {
                echo '<th>HB Level</th>';
              } ?>
              <th></th>
            </tr>
          </thead>
          <tbody>
              <?php 
              if($datas!=false): foreach($datas as $data ):  ?>
               <tr>
                <td class="id record hide" data-id="<?php echo $data['ID']; ?>"><?php echo $data['ID'] ?></td>
                <td><?php echo $data['date'] ?></td>
                <td><?php echo $data['clinic_name'] ?></td>
                <td><?php 
                  if( strtotime($data['date']) < strtotime('2018-04-01') ) {
                    echo "<em class='yellowme'>Deprecated</em>";
                  }
                  else {
                    echo $record->display_visit_reasons($data['visit_reasons']);
                  }
                ?></td>
                <?php if($client_info['client_type']=="Child" && 
                        ($client_info['date_birth']=="0000-00-00" || 
                         $client->get_age($client_info['date_birth']) <= 2) ) {
                        echo '<td>'.$data['feeding_type'].'</td>';
                          } ?>
                <?php 
                if($client_info['client_type']=="Female" && $record->has_ANC_visits($datas)) {
                  $temp = json_decode($data['visit_reasons'], true);
                  if(in_array('ANC', $temp)) {
                    echo '<td>'.$data['hb_level'].'</td>';
                  }
                  else {
                    echo '<td>&nbsp;</td>';
                  }
                } ?>
                 <?php if ($_GET['p'] != 'delete'): ?>
                    <td>
                      <div class="btn-group">
                        <a type="button" title="Delete" class="btn btn-default delete <?php if (enablea_and_disable_ele($_SESSION['type'], "delete_con_records", $_SESSION['records']) == false) { echo "hide"; }?>" 
                          style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  
                      </div> 
                    </td>
                 <?php endif ?>
              </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="4">No consultation record found.</td></tr>
              <?php endif; ?>           
          </tbody>
        </table> 
        </div>     
      </div>
    </div>
  </div>
  <?php
    }
  ?>    
  <?php $relationship_data = $record->relationship_modal($client_info) ?> 
  <?php $relationship_data = $record->relationship_detail_modal() ?> 
  <?php $record->script();  ?>


<script type="text/javascript" src="/js/moment.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('input[value="ANC"]').on('change', function() {
      if($('input[value="ANC"]').prop('checked')) {
        $('#hb-form').show();
      } else {
        $('#hb-form').hide();
        $('#hb-warning').hide();
        if($('#referral_id').val() == 1) {
          $('#referral_id').val(0);
        }
      }
    })
    $('#add-consultation-btn').on('click', function() {
	  
      console.log($('#client_type').val());
      if($('#client_type').val() == "Female") {
        $('#newClientModal').find('input[value="ANC"]').parent().show()
      } else {
        $('#newClientModal').find('input[value="ANC"]').parent().hide()
      }
      $('#newClientModal').modal('show');
	  $('#hb-warning').hide();
    })
    $('#hb_level').on('change', function() {
      if($(this).val() == '8-') {
        $('#referral_id').val(1);
      } else if($(this).val() == '10-') {
        $('#referral_id').val(0);
        if($('#datepicker3').val() != "") {
          $('#datepicker-review_date').val(moment($('#datepicker3').val(), 'YYYY-MM-DD').add(2, 'months').format('YYYY-MM-DD'));
        }
      } else {
        $('#referral_id').val(0);
      }
      $.ajax({
        url: '/ajax.php?func=count_anc_visit&client_id=<?= $_GET["cid"]; ?>',
        type: 'get',
        dataType: 'json',
        success: function(data) {
          console.log('return:'+data[0].count);
          if(data[0].count == 0) {
            if($('#hb_level').val() != "") {
              $('#hb-warning').show();
            }
            
            if($('#hb_level').val() == '10+') {
			  $('#hb-warning').hide()
            } else if($('#hb_level').val() == '10-') {
              $('#hb-warning').attr('class', 'alert alert-warning')
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '9-') {
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '8-') {
              $('#hb-warning').attr('class', 'alert alert-danger')
              $('#hb-warning').find('.content').html('<strong>Refer to Hospital. </strong>');
            }
            
          } else if (data[0].count == 1) {
            if($('#hb_level').val() != "" && $('#hb_level').val() != "10+") {
              $('#hb-warning').show();
            }

            if($('#hb_level').val() == '10+') {
           
              $('#hb-warning').hide();
            } else if($('#hb_level').val() == '10-') {
              $('#hb-warning').attr('class', 'alert alert-warning')
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '9-') {
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '8-') {
              $('#hb-warning').attr('class', 'alert alert-danger')
              $('#hb-warning').find('.content').html('<strong>Refer to Hospital. </strong>');
            }
          } else if (data[0].count == 2) {
            if($('#hb_level').val() != "" && $('#hb_level').val() != "10+") {
              $('#hb-warning').show();
            }
            
            if($('#hb_level').val() == '10+') {
			  $('#hb-warning').hide();
            } else if($('#hb_level').val() == '10-') {
              $('#hb-warning').attr('class', 'alert alert-warning')
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '9-') {
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '8-') {
              $('#hb-warning').attr('class', 'alert alert-danger')
              $('#hb-warning').find('.content').html('<strong>Refer to Hospital. </strong>');
            }
          } else{
            if($('#hb_level').val() != "" && $('#hb_level').val() != "10+") {
              $('#hb-warning').show();
            }
            
            if($('#hb_level').val() == '10+') {
			  $('#hb-warning').hide();
            } else if($('#hb_level').val() == '10-') {
              $('#hb-warning').attr('class', 'alert alert-warning')
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '9-') {
              $('#hb-warning').find('.content').html('<strong>Continue Iron & folic acid (Fefol) treatment and test for Malaria. Review in 2 months’ time.</strong>');
            } else if($('#hb_level').val() == '8-') {
              $('#hb-warning').attr('class', 'alert alert-danger')
              $('#hb-warning').find('.content').html('<strong>Refer to Hospital. </strong>');
            }
          }
        }
      })
    });
    $('#datepicker3').on('change', function() {
      if($('#hb_level').val() == '10-') {
        $('#datepicker-review_date').val(moment($('#datepicker3').val(), 'YYYY-MM-DD').add(2, 'months').format('YYYY-MM-DD'));
      }
    });
   })
</script>

 <?php  
 if (isset($_GET['modal'])) {
  $modal = $_GET['modal'];  ?>
  <script type="text/javascript">
   $(window).load(function(){
      var modal = "<?php echo "{$modal}"; ?>";
      if (modal) {
         $('#newClientModal').modal('show');
      };
    });


  </script>
  <?php
}