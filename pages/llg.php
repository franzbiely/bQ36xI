<?php header_nav_bar("tag", "LLG") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">LLG Management
            <a id="addClient" type="button" class="btn btn-default" style="float: right;"data-toggle="modal" href="#newClientModal">Add New LLG</a> 
            <!-- Modal -->
            <div class="modal fade" id="newClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h2 class="modal-title">
                      <span class="glyphicon glyphicon-plus" style="margin-right: 10px;"></span>Add LLG
                    </h2>
                  </div>
                  <div class="modal-body">
                    <form role="form" action="" method="post">
                      <input type="hidden" name="class" value="llg" />
                      <input type="hidden" name="func" value="add" />
                      <input type="hidden" name="entry_type" value="llg" />
                      <input type="hidden" name="office_id" value="<?php echo $_SESSION['office_id']; ?>" />
                            
                      <div class="form-group">
                        <label for="llg_description">LLG Name</label>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="area_name" placeholder="Enter LLG Name" name="area_name">
                      </div>                                        
                      <div class="form-group">
                        <label for="llg_description">LLG Description</label>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="description" placeholder="Enter LLG Description" name="description">
                      </div> 
                      <div class="form-group">
                        <label for="area_name">District Name</label>
                        <select class="form-control" name="district_id" id="district_id">
                          <option>Select District Name</option>
                          <?php                 
                          foreach($district->get_all() as $data ){ 
                            ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
                          }
                          ?>  
                        </select>
                      </div>  
                      <div class="form-group">
                        <label for="contact">Councillor Name</label>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="councillor_name" name="councillor_name" placeholder="Enter Councillor Name">
                      </div> 
                      <div class="form-group">
                        <label for="office_address">Councillor Contact</label>
                        <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="councillor_contact" name="councillor_contact" placeholder="Enter Councillor Contact">
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
              <th>LLG Name</th>
              <th>Description</th>
              <th>District</th>
              <th>Councillor Name</th>
              <th>Councillor Contact</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $data = $llg->get_all();
            if($data!=false): foreach($data as $data ):  ?>
            <tr>              
              <td class="id name" data-id="<?php echo $data['ID'] ?>"><?php echo $data['area_name'] ?></td>
              <td class="description"><?php echo $data['description'] ?></td>
              <td class="district" data-district-id="<?php echo $llg->get_district_id($data['parent_ids']) ?>"><?php echo $llg->get_district_id($data['parent_ids']) ?></td>   
              <td class="councillor_name"><?php echo $llg->get_councillor_name($data['contact']) ?></td>    
              <td class="councillor_contact"><?php echo $llg->get_councillor_contact($data['contact']) ?></td>       
              <td>
                <div class="btn-group">
                  <a type="button" title="Edit" class="btn btn-default edit" style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                  <a type="button" title="Delete" class="btn btn-default delete" style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  
                </div> 
              </td>
            </tr>   
            <?php endforeach; else: ?>
              <tr><td colspan="4">No LLG record found.</td></tr>
            <?php endif; ?>                    
          </tbody>
        </table>        
      </div><!--/span-->        
    </div>
  </div>

  <?php $llg->scripts(); ?>
