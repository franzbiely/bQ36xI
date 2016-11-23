<?php header_nav_bar("user", "Client Types", "settings") ?>

<?php

function modal_report_setting() {
	ob_start();
	switch($type){
		default:
		?>
		<span class="required_field">* <span class="required_label">required fields.</span></span>
		<form role="form" action="" method="post">
			<input type="hidden" name="class" value="type" />
			<input type="hidden" name="func" value="add" />
			<div class="form-group">
				<label>Type</label><span class="required_field">*</span>
				<select class="form-control" name="type">
					<option value="hb_level">HB Level</option>
				</select>
			</div>
			<div class="form-group">
				<label>Sending</label><span class="required_field">*</span>
				<select name="sending" class="form-control">
					<option value="daily">Daily</option>
					<option value="monday">Every Monday</option>
					<option value="friday">Every Friday</option>
				</select>
			</div>
			<div class="form-group">
				<label>Emails: (comma separated)</label><span class="required_field">*</span>
				<input type="text" name="recipient" class="form-control">
			</div>

			<input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
		</form>
		<?php
	}
	?>
	
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	modal_container("Report",$output);
}

?>

<?php 

$json = json_decode(file_get_contents('anc.json'), true);

?>

<div class="container">    
	<div class="row">
		<div class="col-md-3">
			<?php include('parts/sidebar.php') ?>
		</div>
		<div class="col-md-9" role="main">
			<?php are_you_sure_delete(); ?>
			<div class="alert alert-success" id="settings-saved" style="display:none">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>Settings Saved!</strong> 
			</div>
			<div class="page-header">
				<h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Notifications
					<!-- <a id="addClient" type="button" class="btn btn-default btn_client_type" style="float: right;"data-toggle="modal" href="#newClientModal">New Report</a>  -->
					<!-- Modal -->
					<?php modal_report_setting(); ?>
					<?php //$type->modal("client"); ?>              
				</h1> 
			</div>
			<div>
				<h3>ANC - HB Level</h3>
				<p>
					<div style="width:200px;float:left;margin:10px;" id="schedule-select-div">
						<div class="form-group">
							<label>Schedule</label>
							<select class="form-control" id="schedule-select">
								<option value="daily" <?= ($json['schedule'] == 'daily')?'selected':''; ?>>Daily</option>
								<option value="weekly" <?= ($json['schedule'] == 'weekly')?'selected':''; ?>>Weekly</option>
								<option value="monthly" <?= ($json['schedule'] == 'monthly')?'selected':''; ?>>Monthly</option>
							</select>
						</div>
						<div>
							<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">*Daily and weekly notification are sent at 3am</p>
							<p style="font-size: 90%;">**Monthly notifications are sent on the 1st at 3am</p>
						</div>
					</div>
					<div style="width:200px;float:left;margin:10px;display:none" id="weekly-select-div">
						<div class="form-group">
							<label>Every</label>
							<select class="form-control" id="weekly-select">
								<option value="sunday" <?= ($json['every'] == 'sunday')?'selected':''; ?>>Sunday</option>
								<option value="monday" <?= ($json['every'] == 'monday')?'selected':''; ?>>Monday</option>
								<option value="tuesday" <?= ($json['every'] == 'tuesday')?'selected':''; ?>>Tuesday</option>
								<option value="wednesday" <?= ($json['every'] == 'wednesday')?'selected':''; ?>>Wednesday</option>
								<option value="thursday" <?= ($json['every'] == 'thursday')?'selected':''; ?>>Thursday</option>
								<option value="friday" <?= ($json['every'] == 'friday')?'selected':''; ?>>Friday</option>
								<option value="saturday" <?= ($json['every'] == 'saturday')?'selected':''; ?>>Saturday</option>
							</select>
						</div>
						
						
					</div>
					<div style="width:300px;float:left;margin:10px">
						<div class="form-group">
							<label>Emails</label>
						 	<textarea class="form-control" id="email-inp"><?= $json['email']; ?></textarea>
						</div>
						<div>
							<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">*Email addresses should be separated by a comma</p>							
						</div>
					</div>
					<div style="clear:both"></div>
				</p>
				<p>
					<button class="btn btn-primary" id="save-settings">Save</button>
				</p>
			</div>
			
		</div><!--/span-->        
	</div>
</div>

<?php $type->scripts(); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#schedule-select').on('change', function() {
			if($(this).val() == "weekly") {
				$('#weekly-select-div').show();
			} else {
				$('#weekly-select-div').hide();
			}
		});
		$('#save-settings').on('click', function() {
			$('#settings-saved').hide()
			var emails = $('#email-inp').val();
			var emails_list = emails.replace(/\n|\r/g, "");
			var arr = {schedule: $('#schedule-select').val(), every: $('#weekly-select').val(), email: emails_list};
			$.ajax({
				url: '/json.php',
				type: 'post',
				data: {file: 'anc.json', func: 'write', data: arr},
				success: function() {
					$('#settings-saved').show();
				}

			})
		});
	});
</script>