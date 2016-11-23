
<?php header_nav_bar("globe", "Offices") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="alert alert-info"><strong></strong></div>
        <div class="alert alert-warning"><strong></strong></div>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Health Facility 
        		 
        		<!-- Hide, don't delete this -->
        		<a id="addClient" type="button" class="btn btn-default  <?php if($_SESSION['type'] != 'superadmin') { if (check_add_user($_SESSION['add_hc']) == false) { echo "hide"; } }?>" 
            style="float: right;"data-toggle="modal" href="#newClientModal">Add New Health Facility</a>
        		             <!-- Modal -->
            <?php $office->modal() ?>
          </h1> 
        </div>
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>Health Facility</th>
              <th>Address</th>
              <th>Contact Number</th>
              <th>Province</th>
              <th>District</th>
               <?php if($_SESSION['type'] == 'superadmin'){ echo "<th>Action</th>"; }?>
            </tr>
          </thead>
          <tbody>
            <?php $office2 = $office->get_all_wsession(); ?>
            <?php if (is_array($office2)): ?>
              <?php 
                foreach($office->get_all_wsession() as $data ): ?>
                <tr>
                  <td class="id name" data-id="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></td>
                  <td class="address"><?php if($data['office_address'] == '') echo ""; else echo $data['office_address']; ?></td>
                  <td class="phone"><?php if($data['contact'] == '') echo ""; else echo $data['contact']; ?></td>  
                  <td class="province"><?php echo get_area_name(get_parent_ids($data['parent_ids'])); ?></td>
                  <td class="district"><?php echo get_area_name($data['parent_ids']); ?></td>
                  <td>
                    <div class="btn-group">
                      <a type="button" title="Edit"  class="btn btn-default edit <?php if($_SESSION['type'] != 'superadmin'){ echo "hide"; }?>" 
                      style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                       <a type="button" title="Delete" class="btn btn-default delete <?php if($_SESSION['type'] != 'superadmin'){ echo "hide"; }?>" 
                        style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  
                    </div> 
                  </td>
                </tr> 
              <?php endforeach; ?>
            <?php endif ?>
          </tbody>
        </table>        
      </div><!--/span-->        
    </div>
  </div>

  <?php $office->scripts(); ?>
