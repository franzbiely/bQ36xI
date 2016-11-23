<?php ini_set('max_execution_time', 300); ?>
<?php header_nav_bar("home", "Clinics") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
          <div class="alert alert-info"><strong></strong></div>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Clinic Management
            <a id="addClient"  type="button" class="btn btn-default <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['clinic']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>" 
            style="float: right;"data-toggle="modal" href="#newClientModal">Add New Clinic</a> 
            <!-- Modal -->
            <?php $clinic->modal(); ?>
                   
          </h1> 
        </div>
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>Clinic Name</th>
              <th>Clinic Type</th>
              <th>Clinic Province</th> <!-- clinic location -->
              <th>Clinic District</th> <!-- llg -->
              <th>Person-In-Charge</th>
              <th>Contact Details</th>
              <?php if ($_SESSION['type'] != 'superadmin'): ?>
                <th>Action</th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>            
              <?php 
              $data = $clinic->get_all();
              if($data!=false): foreach($data as $data ): ?>
                <tr>
                  <td class="id name" data-id="<?php echo $data['ID']; ?>"><?php echo $data['clinic_name']; ?></td>
                  <td class="clinic_type"><?php echo $data['clinic_type']; ?></td>
                  <td class="province"><?php if($clinic->get_areaname($data['province']) == 0) echo $clinic->get_areaname(get_parent_ids($data['llg_id'])); else echo $clinic->get_areaname($data['province']); ?></td>
                  <td class="district"><?php echo $clinic->get_areaname($data['llg_id']); ?></td>
                  <!-- <td class="llg"><?php //echo $llg->get_json_value("llg", $data['llg_id'], true); ?></td> -->              
                  <td class="officer_in_charge"><?php echo $data['officer_in_charge']; ?></td>
                  <td class="contact"><?php if($clinic->display_contacts($data['contact']) == '') echo "(Unset)"; else echo $clinic->display_contacts($data['contact']); ?></td>
                  <td>
                    <div class="btn-group">
                       <a type="button" class="btn btn-default edit <?php if (enablea_and_disable_ele($_SESSION['type'], "edit", $_SESSION['clinic']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>" 
                       id="edit" style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                      <a type="button" class="btn btn-default delete <?php if (enablea_and_disable_ele($_SESSION['type'], "delete", $_SESSION['clinic']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>" 
                      style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  
                    </div> 
                  </td>
                </tr>  
              <?php endforeach; else: ?>
              <tr><td colspan="7">No Client record found.</td></tr>
              <?php endif; ?>                                  
          </tbody>
        </table>  
        <?php $clinic->pagination() ?>        
      </div><!--/span-->        
    </div>
  </div>

  <?php $clinic->scripts(); ?>
