<?php header_nav_bar("home", "Clinic Types", "settings") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Clinic Types
            <a id="addClient" type="button" class="btn btn-default btn_clinic_type" style="float: right;"data-toggle="modal" href="#newClientModal">Add New Clinic Type</a> 
            <!-- Modal -->
            <?php $type->modal("clinic"); ?>
          </h1> 
        </div>
        <div class="tblcontainer" data-type="clinic">
			<table class="table  table-striped table-hover table-condensed">
			  <thead>
				<tr >
				  <th>Description</th>
          <th>Action</th>
				  <th></th>
				</tr>
			  </thead>
			  <tbody>				
				  <?php 
          $_data = $type->get_all('clinic');
          if($_data!=false): foreach($_data['value'] as $data ): ?>
          <tr>
					  <td class="id description" data-oldval="<?php echo $data; ?>"><?php echo $data; ?></td>
					  <td>
						<div class="btn-group">
						  <a type="button" title="Edit" class="btn btn-default edit" style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
						                <a type="button" title="Delete" class="btn btn-default delete" style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  
            </div> 
					  </td>  
				  </tr>
				  <?php endforeach; else: ?>
          <tr><td colspan="7">No Clinic Type record found.</td></tr>
          <?php endif; ?>                                         
			  </tbody>
			</table>
		</div>
        </table>        
      </div><!--/span-->        
    </div>
  </div>

  <?php $type->scripts(); ?>
