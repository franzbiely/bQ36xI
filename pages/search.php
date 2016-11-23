<?php header_nav_bar("search", "Search") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Search Results
            <?php
            $_data = $client->search();
            $client_details = array();
            if($_data!=false): ?>
            <div class="btn-group" style="float: right; display:none;">
              <form method="POST">
                <input type="hidden" name="func" value="export_search" />
                <input type="hidden" name="param1" value="excel" />
                <input type="hidden" name="result" value="<?php echo json_encode($_data) ?>" />
                <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-info" value="Export to Excel" />
              </form>
              <form method="POST">
                <input type="hidden" name="func" value="export_search" />
                <input type="hidden" name="param1" value="csv" />
                <input type="submit" style="float:left;margin-top: 5px;" class="btn btn-warning" value="Export to CSV" />
              </form>
            </div>            
            <?php endif; ?>          
            
            <a type="button" class="btn btn-default" href="?page=clients">Return to Client Record </a>
            
          </h1> 
        </div>
    <div class="tblcontainer">
      <table class="table  table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>Record Number</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Last Consulted Clinic</th>
            <th>Last Consulted Date</th>
            <th>Client Type</th>
            <th>Records</th>
           <?php if ($_SESSION['type'] != 'superadmin'): ?>
             <th>Remarks</th>
           <?php endif ?>
          </tr>
        </thead>
        <tbody>
          <?php 

          if($_data!=false): foreach($_data as $data ): ?>        
              <?php $client_details[] = array($data['record_number'], $data['fname'], $data['lname'],
                                     $data['last_clinic'], $data['last_date'], $data['client_type']);  
              ?>
                <tr>
                <td class="id record" data-id="<?php echo $data['ID']; ?>"><?php echo $data['record_number']; ?></td>
                <td class="fname"><?php echo $data['fname']; ?></td>
                <td class="lname"><?php echo $data['lname']; ?></td>
                <td class="last_clinic"><?php /*echo $clinic->get_name($data['last_clinic']);*/ echo $data['last_clinic']; ?></td>
                <td class="last_date"><?php echo $data['last_date']; ?></td>
                <td class="client_type"><?php echo $data['client_type']; ?></td>
                <td><a href="<?php echo SITE_URL ?>/?page=records&cid=<?php echo $data['ID'] ?>&p=view">Check Records</a></td>
                 <td>
                    <div class="btn-group">
                      <a href="?page=records&cid=<?php echo $data['ID'] ?>&p=update" type="button" title="Edit" class="btn btn-default
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "edit", $_SESSION['client_section']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>"
                         style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                       <a type="button" title="Delete" class="btn btn-default delete
                       <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>"
                        style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>
                      <!-- <a  href="?page=records&cid=<?php //echo $data['ID'] ?>&p=delete" type="button" title="Delete" class="btn btn-default" style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a> -->  
                    </div> 
                  </td>  
              </tr>   
              <?php endforeach;
                  $_SESSION['content'] = $client_details;
               
                   else: ?>
                    <tr><td colspan="4">No Search record found.</td></tr>
          <?php endif; ?>                                    
        </tbody>
      </table>
    </div>  
      </div><!--/span-->        
    </div>
  </div>

  <?php $type->scripts(); ?>
