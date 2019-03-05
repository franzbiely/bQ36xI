<?php header_nav_bar("user", "Clients") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php $client->modal(); ?>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">
            Unknown Client Records
          </h1> 
          <p>Note : The records that shows up below are only consultation records that starts from January 2019.</p>
          <?php
          $paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
              
          $datas = $client->get_all_unknown($paged);
          
            $record_count = $client->get_record_count($paged);
            $to = (count($datas) < ITEM_DISPLAY_COUNT) ? (ITEM_DISPLAY_COUNT*($paged-1))+count($datas) :  ITEM_DISPLAY_COUNT*$paged;
            $from = $paged == 1 ? 1 : (ITEM_DISPLAY_COUNT*($paged-1))+1;
            ?>
            <p>Record <?php echo number_format($from) ?> 
                to <?php echo number_format($to) ?> 
                of page (<?php echo number_format($record_count) ?>)
            </p>
        </div>
       
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>&nbsp;</th>
              <th>Client ID</th>
              <th>Record Number</th>
              <th>Full Name</th>
              <th>Birth Date</th>
              <th>Gender</th>
              <th>Consultation Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>            
            <?php 
                $index = 0;
                if($datas!=false):   
                    foreach($datas as $data ): $index++; ?>
                    <tr <?php if($data['is_archived']==1) echo 'class="is_archived" data-archived-date="'.$data['date_archived'].'"'; ?>>
                    <td><?php echo $index ?></td>
                    <td class="id record" data-id="<?php echo $data['ID']; ?>"><?php echo $data['ID']; ?></td>
                    <td class="fname"><?php echo $data['record_number']; ?></td>
                    <td class="lname"><?php echo $data['fname']." ".$data['lname']; ?></td>
                    <td class="date_birth" data-date-death="<?php echo $data['date_death']; ?>"><?php echo $data['date_birth']; ?></td>   
                    <td class="type"><?php echo ($data['client_type'] != 'Child') ? $data['client_type'] : "Unknown"; ?></td>
                    <td class="consultation_date"><?php echo $data['date']; ?></td>
                    <td>
                    <div class="btn-group">
                        <a type="button" title="Edit" class="btn btn-default edit
                            <?php if (enablea_and_disable_ele($_SESSION['type'], "edit", $_SESSION['client_section']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>"
                            style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                    </div> 
                    </td>  
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="7">No Client record found.</td></tr>
                <?php endif; 
            ?>                                         
          </tbody>
        </table>      
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

