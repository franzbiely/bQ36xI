<?php ini_set('max_execution_time', 300); ?>
<?php header_nav_bar("home", "catchment") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
          <div class="alert alert-info"><strong></strong></div>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Catchment Management
            <a id="addClient"  type="button" class="btn btn-default <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['clinic']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>" 
            style="float: right;"data-toggle="modal" href="#newClientModal">Add New Catchment</a> 
            <!-- Modal -->
            <?php $catchment->modal(); ?>
                   
          </h1> 
        </div>
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>Catchment Area</th>
              <!-- <th>Internal Record No</th> -->
              <th>National Health Facility Code</th> <!-- clinic location -->
              <th>Clinic Name</th> <!-- clinic location -->
              
              <?php if ($_SESSION['type'] != 'superadmin'): ?>
                <th>Action</th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>            
              <?php 
              $data = $catchment->get_all();
              if($data!=false): foreach($data as $data ): ?>
                <tr>
                  <td class="id name" data-id="<?php echo $data['id']; ?>"><?php echo $data['catchment_area']; ?></td>
                 <!-- <td class="internal_record_no"><?php echo $data['internal_record_no']; ?></td> -->
                  <td class="national_health_facility_code"><?php  echo $data['national_health_facility_code']; ?></td>
                  <td class="clinic_id" clinic_id="<?php echo $data['clinic_id']; ?>"><?php echo $catchment->get_clinic_name($data['clinic_id']); ?></td>
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
              <tr><td colspan="7">No Catchment record found.</td></tr>
              <?php endif; ?>                                  
          </tbody>
        </table>  
        <?php  $catchment->pagination() ?>        
      </div><!--/span-->        
    </div>
  </div>

  <?php  $catchment->scripts(); ?>
