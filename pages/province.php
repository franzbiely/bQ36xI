<?php ini_set('max_execution_time', 300); ?>
<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container">
      <div class="row">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo SITE_URL ?>/?page=dashboard">Susu Mamas</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="<?php echo SITE_URL ?>/?page=province"><span class="glyphicon glyphicon-plane" style="margin-right: 10px;"></span>Province</a></li>
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
          <a class="btn btn-danger yes" href="#">Yes, delete this one please</a><a class="btn btn-default" href="#">Cancel</a>
        </div>
        <div class="page-header">
          <div class="alert alert-info"><strong></strong></div>
          <div class="alert alert-warning"><strong></strong></div> 
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Province Management
            <a id="addClient" type="button" class="btn btn-default <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['province']) == false || $_SESSION['type'] == 'admin') { echo "hide"; }?>" 
            style="float: right;"data-toggle="modal" href="#newClientModal">Add New Province</a> 
            <!-- Modal -->
            <div class="modal fade" id="newClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <span id="errormessage"></span>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h2 class="modal-title">
                      <span class="glyphicon glyphicon-plus" style="margin-right: 10px;"></span><span class="edit_or_add"></span> Province
                    </h2>
                  </div>

                  <div class="modal-body">
                    <span class="required_field">* <span class="required_label">required fields.</span></span>
                    <form role="form" action="" method="post">
                      <input type="hidden" name="class" value="province" />
                      <input type="hidden" name="func" value="add" />
                      <input type="hidden" name="entry_type" value="province" />
                      <input type="hidden" name="office_id" value="<?php echo $_SESSION['office_id']; ?>" />
                      <div class="form-group">
                        <label for="province_name">Name</label><span class="required_field">*</span>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="area_name" name="area_name" placeholder="Enter Province Name" required>
                      </div>                      
                      <div class="form-group">
                        <label for="province_description">Description</label><span class="required_field">*</span>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="description" placeholder="Enter Province Description" name="description" required>
                      </div>                                          
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
              <th>Province Name</th>
              <th>Province Description</th>
              <!-- <th>Health Facility</th> -->
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $data = $province->get_all();
            if($data!=false){
              foreach($data as $data ): ?>
              <tr>              
                <td class="id name" data-id="<?php echo $data['ID']; ?>"><?php echo $data['area_name'] ?></td>
                <td class="description"><?php if($data['description'] =='') echo ""; else echo $data['description'];  ?></td>
               <!--  <td class="office_name"><?php //echo $province->get_office_name($data['parent_ids']) ?></td> -->          
                <td>
                  <div class="btn-group">
                      <a type="button" title="Edit"  class="btn btn-default edit <?php if (enablea_and_disable_ele($_SESSION['type'], "edit", $_SESSION['province']) == false || $_SESSION['type'] == 'admin') { echo "hide"; }?>" 
                      style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                    <a type="button" title="Delete"  class="btn btn-default delete <?php if (enablea_and_disable_ele($_SESSION['type'], "delete", $_SESSION['province']) == false || $_SESSION['type'] == 'admin') { echo "hide"; }?>" 
                    style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>
                  </div> 
                </td>
              </tr>   
              <?php endforeach;    
            }
            else{
              ?>
              <tr><td colspan="4">No province record found.</td></tr>
              <?php
            }
             ?>                            
          </tbody>
        </table>        
      </div><!--/span-->        
    </div>
  </div>

  <?php $province->scripts(); ?>
