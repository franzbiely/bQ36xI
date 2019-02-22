<?php if ($_SESSION['type'] == 'superadmin'): ?>
  <?php header_nav_bar("random", "Gender","settings") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="alert alert-info"><strong></strong></div>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Permission Schema
            <a id="addClient" type="button" class="btn btn-default <?php if (enablea_and_disable_ele($_SESSION['type'], "add_new_user", $_SESSION['user']) == false) { echo "hide"; }?>"
             style="float: right;"data-toggle="modal" href="#newClientModal">Add New User</a> 
            <!-- Modal -->
            <?php $user->modal("visit"); ?>  
          </h1> 
        </div>
        <div class="tblcontainer" data-type="client">
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>Lists of System Users</th>
              <th></th>
            </tr>
          </thead>
          <tbody>           
              <tr>
                <th>Name</th>
                <th>Username</th>
                <th>User Type</th>
                <th>Health Facility</th>
                <th>Action</th>
              </tr> 
             <!--  <td><?php //if ($data['ID'] == $_SESSION['id']){ echo '<strong>(own)</strong>'; } ?></td> -->
              <?php 
              $_data = $user->get_all();
              if($_data!=false): foreach($_data as $data ):
                  ?>
                <?php //if ($data['ID'] != $_SESSION['id']): ?>
                    <tr <?php if ($data['ID'] == $_SESSION['id']){ echo 'class=success'; } ?>>
                      <td class="id hide" data-id="<?php echo $data['ID']; ?>"><?php echo $data['ID']; ?></td>
                      <td class="fullname"><?php echo $data['fullname']; ?> </td>
                      <td class="username"><?php echo $data['username']; ?></td>
                       <td class="usertype"><?php if($data['type'] !=''){ echo $data['type']; } else{ echo "(Unset)"; } ?></td>
                       <td class="hc"><?php echo get_area_name($data['office_id']); ?></td>
                       <td class=" hide password"><?php echo $data['password']; ?></td>
                       <td class=" hide email"><?php echo $data['email']; ?></td>
                       <td class=" hide phone"><?php echo $data['phone']; ?></td>
                       <td class=" hide address"><?php echo $data['address']; ?></td>
                       <td class=" hide office_id"><?php echo $data['office_id']; ?></td>
                      <td>
                      <div class="btn-group">
                        <a href="?page=permission_details&ID=<?php echo $data['ID']; ?> "type="button" title="Permission Details" class="btn btn-default permission" 
                          style="padding: 0 5px;" data-original-title="Permission Details"
                           <?php if($data['ID'] != $_SESSION['id']){
                            if (enablea_and_disable_ele($_SESSION['type'], "view_other_profile", $_SESSION['user']) == false) { echo "disabled"; }else{ echo "enabled"; }
                           } ?> 
                          ><span class="glyphicon glyphicon-random"></span></a>
                        <a type="button" title="Edit" id="edit" class="btn btn-default edit 
                        <?php if($data['ID'] == $_SESSION['id']){
                            if (enablea_and_disable_ele($_SESSION['type'], "edit_personal_record", $_SESSION['user']) == false) { echo "disabled"; }else{ echo "enabled"; }
                        }else{
                          if (enablea_and_disable_ele($_SESSION['type'], "edit_other_record", $_SESSION['user']) == false) { echo "disabled"; }else{ echo "enabled"; }
                        } ?>
                        " 
                          id="edit"style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                        <a type="button" title="Delete" class="btn btn-default delete
                        <?php if ($data['type'] == 'superadmin' || $data['type'] == 'superreporting'): ?>
                          <?php echo "disabled"; ?>
                        <?php endif ?>
                        "
                         style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  
                      </div> 
                      </td> 
                    </tr>
                <?php //endif ?>
              <?php endforeach; else: ?>
              <tr><td colspan="7">No User found.</td></tr>
              <?php endif; ?>                                       
          </tbody>
        </table>  
        </div>      
      </div><!--/span-->        
    </div>
  </div>

  <?php $user->script(); ?>
<?php else: ?>
  <?php if ($_SESSION['type'] == 'reporting' || $_SESSION['type'] == 'superreporting'): ?>
    <?php header("Location:?page=reports"); ?>
  <?php else: ?>
  <?php header("Location:?page=dashboard"); ?>
  <?php endif ?>
<?php endif ?>