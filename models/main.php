<?php
function modal_container($page, $output, $modal_id="newClientModal"){
	?>
	<div class="modal fade" id="<?php echo $modal_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <span id="errormessage"></span>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h2 class="modal-title">
              <?php if ($page != 'transfer_record'): ?>
               <span class="glyphicon glyphicon-plus" style="margin-right: 10px;"></span>
                <span class="edit_or_add"></span> <?php echo $page ?>
              <?php else: ?>
              <span class="glyphicon glyphicon-log-out" style="margin-right: 10px;"></span>
              Transfer Records
              <?php endif ?>
              <div class="double-record"></div>
            </h2>
          </div>
          <div class="modal-body">
            <?php echo $output ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->   
    <?php
}
?>
