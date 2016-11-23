<?php header_nav_bar("user", "Clients") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="alert alert-info"><strong></strong></div>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Clients Records
            <?php $client->pagination() ?>   
            <a id="addClient" type="button" class="btn btn-default
              <?php if($_SESSION['type']!='superadmin') {
                 if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false) { echo "hide"; }
               }else{  echo "hide"; }
               ?>" 
              style="float: right;"data-toggle="modal" href="#newClientModal"
            >Add New Client </a> 
            <?php $client->modal(); ?>             
          </h1> 
        </div>
       
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>Record Number</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Birth Date</th>
              <th>Client Type</th>
              <th <?php if (enablea_and_disable_ele($_SESSION['type'], "view_con_records", $_SESSION['records']) == false) { echo 'class="hide"'; }?>>Records</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>            
              <?php 
              $paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
              $datas = $client->get_all($paged);
              if($datas!=false): foreach($datas as $data ): ?>
                <tr <?php if($data['is_archived']==1) echo 'class="is_archived" data-archived-date="'.$data['date_archived'].'"'; ?>>
                  <td class="id record" data-id="<?php echo $data['ID']; ?>"><?php echo $data['record_number']; ?></td>
                  <td class="fname"><?php echo $data['fname']; ?></td>
                  <td class="lname"><?php echo $data['lname']; ?></td>
                  <td class="date_birth" data-date-death="<?php echo $data['date_death']; ?>"><?php echo $data['date_birth']; ?></td>   
                  <td class="type"><?php echo $data['client_type']; ?></td>
                  <td class="phone hide"><?php echo $data['phone']; ?></td>
                  <td class="place_of_birth hide"><?php echo $data['place_of_birth']; ?></td>
                  <td class="province hide"><?php echo $data['province']; ?></td>
                  <td class="district hide"><?php echo $data['district']; ?></td>
                  <td class="relationship hide"><?php echo $data['relation_to']; ?></td>
                  <td class="current_address hide"><?php echo $data['current_address']; ?></td>
                  <td  <?php if (enablea_and_disable_ele($_SESSION['type'], "view_con_records", $_SESSION['records']) == false) { echo 'class="hide"'; }?>>
                     <a class="check_records" href="<?php echo SITE_URL ?>/?page=records&cid=<?php echo $data['ID'] ?>&p=view">Check Records</a></td>
                  <td>
                    <div class="btn-group">
                        <a type="button" title="Edit" class="btn btn-default edit
                         <?php if (enablea_and_disable_ele($_SESSION['type'], "edit", $_SESSION['client_section']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>"
                         style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                        <a type="button" title="Delete" class="btn btn-default delete  <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>" 
                        style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  

                      
                    </div> 
                  </td>  
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="7">No Client record found.</td></tr>
              <?php endif; ?>                                         
          </tbody>
        </table>  
        <?php $client->pagination() ?>      
      </div><!--/span-->        
    </div>
  </div>

  <?php $client->scripts(); ?>

 <?php  
 if (isset($_GET['modal'])) {
  $modal = $_GET['modal'];  ?>
<script type="text/javascript">
 $(window).load(function(){
    var modal = "<?php echo "{$modal}"; ?>";
    if (modal) {
       $('#newClientModal').modal('show');
       $('.edit_or_add').html('Add New');
    };

    
  });
</script>
<?php
}

