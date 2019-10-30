<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container">
      <div class="row">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo SITE_URL ?>/?page=dashboard">eCIS</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="<?php echo SITE_URL ?>/?page=clients"><span class="glyphicon glyphicon-send" style="margin-right: 10px;"></span>Districts</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.row -->
    </div><!-- /.container -->
  </div><!-- /.navbar -->

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <div id="alert-sure-delete" class="alert alert-danger"> 
          <h4>Are you sure you want to delete this record?</h4>
          <a class="btn btn-danger yes" href="#">Yes, delete this one please</a> <a class="btn btn-default" href="#">Cancel</a>
        </div>
        <div class="page-header">
          <div class="alert alert-info"><strong></strong></div>
          <div class="alert alert-warning"><strong></strong></div> 
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">District Management
            <a id="addClient" type="button" class="btn btn-default <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['district']) == false || $_SESSION['type'] == 'admin') { echo "hide"; }?>" 
            style="float: right;"data-toggle="modal" href="#newClientModal">Add New District</a> 
            <!-- Modal -->
            <div class="modal fade" id="newClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h2 class="modal-title">
                      <span class="glyphicon glyphicon-plus" style="margin-right: 10px;"></span><span class="edit_or_add"></span> District 
                    </h2>
                  </div>
                  <div class="modal-body">
                    <span class="required_field">* <span class="required_label">required fields.</span></span>
                    <form role="form" action="" method="post">
                      <input type="hidden" name="class" value="district" />
                      <input type="hidden" name="func" value="add" />
                      <input type="hidden" name="entry_type" value="district" />
                      <input type="hidden" name="office_id" value="<?php echo $_SESSION['office_id']; ?>" />
                      <div class="form-group">
                        <label for="area_name">Name</label><span class="required_field">*</span>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="area_name" name="area_name" placeholder="Enter District Name" required>
                      </div>  
                      <div class="form-group">
                        <label for="area_name">Province</label><span class="required_field">*</span>
                        <select class="form-control" name="province_id" id="province_id" required>
                          <option value="">Select District Province</option>
                          <?php                 
                          foreach($province->get_all() as $data ){ 
                            ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
                          }
                          ?>  
                        </select>
                      </div>                    
                     <!--  <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="description" placeholder="Enter Description" name="description" required>
                      </div>  -->
                      <div class="form-group">
                        <label for="contact">Person in Charge</label>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="contact" name="contact" placeholder="Enter District Person in Charge">
                      </div> 
                     <!--  <div class="form-group">
                        <label for="office_address">Health Facility Address</label><span class="required_field">*</span>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="office_address" name="office_address" placeholder="Enter Office Address" required>
                      </div>    -->                 
                      <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
                    </form>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->         
          </h1> 
        </div>
        <table class="table table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>District Name</th>
              <th>Province</th>
             <!--  <th>Description</th> -->
              <th>Person in Charge</th>
              <!-- <th>Health Facility Address</th> -->
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $data =  array_sort($district->combine_district_province_to_district(), 'province', SORT_ASC); // Sort by surname
            if($data!=false): foreach($data as $data ):  ?>
            <tr>              
              <td class="id name" data-id="<?php echo $data['ID'] ?>"><?php echo $data['area_name'] ?></td>
              <td class="province" data-province-id="<?php echo $district->get_province_id($data['parent_ids']) ?>"><?php if($data['province'] == '') echo "(Unset)"; else echo $data['province']  ?></td>
             <!--  <td class="description"><?php //echo $data['description'] ?></td> -->   
              <td class="contact"><?php if($district->get_contact_name($data['contact']) == '') echo ""; else echo $district->get_contact_name($data['contact']); ?></td>    
              <!-- <td class="office_address"><?php //echo $data['office_address'] ?></td>        -->
              <td>
                <div class="btn-group">
                   <a type="button" title="Edit" class="btn btn-default edit <?php if (enablea_and_disable_ele($_SESSION['type'], "edit", $_SESSION['district']) == false || $_SESSION['type'] == 'admin') { echo "hide"; }?>" 
                   style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                  <a type="button" title="Delete" class="btn btn-default delete <?php if (enablea_and_disable_ele($_SESSION['type'], "delete", $_SESSION['district']) == false || $_SESSION['type'] == 'admin') { echo "hide"; }?>" 
                  style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  
                </div> 
              </td>
            </tr>   
            <?php endforeach; else: ?>
              <tr><td colspan="4">No district record found.</td></tr>
            <?php endif; ?>                        
          </tbody>
        </table>        
      </div><!--/span-->        
    </div>
  </div>

  <?php $district->scripts(); ?>
