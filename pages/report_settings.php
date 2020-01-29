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
				<form role="form" action="" method="post">
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
								<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">**Daily notifications are sent at 12am.</p>
								<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">**Weekly notifications are sent every selected day of the week at 12am.</p>
								<p style="font-size: 90%;">**Monthly notifications are sent on the 1st at 12am.</p>
							</div>
						</div>
						<div style="width:200px;float:left;margin:10px;<?php echo $json['schedule'] != 'weekly' ? 'display:none;' : '' ?>" id="weekly-select-div">
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
						<p class="yellowme" style="float:right; width: 300px;"><strong>Notice : </strong><br>Notifications are triggered when a client's Hb level is < 8mg.</p>
						<div style="clear:both"></div>
						
					</p>
					<hr />
				
					<h3>Malnutrition Reporting</h3>
					<input type="hidden" name="class" value="Malnutrition" />
					<input type="hidden" name="func" value="storeNotificationSettingsForMalnutrition" />
					<p>
						<?php
						$malnutrition_data = $Malnutrition->fetchNotificationSettingsForMalnutrition();
						$malnutrition_schedule = array_values(array_filter($malnutrition_data, function($var) {
							return ($var['label'] == 'malnutrition_schedule');
						}))[0]['value'];
						$malnutrition_weekly = array_values(array_filter($malnutrition_data, function($var) {
							return ($var['label'] == 'malnutrition_weekly');
						}))[0]['value'];
						$province = array(
							array('label'=>'Central', 'value'=>''),
							array('label'=>'Eastern Highlands', 'value'=>''),
							array('label'=>'Hela', 'value'=>''),
							array('label'=>'Jiwaka', 'value'=>''),
							array('label'=>'Morobe', 'value'=>''),
							array('label'=>'NCD', 'value'=>''),
							array('label'=>'Western Highlands', 'value'=>''),
							array('label'=>'All Provinces', 'value'=>''),
						);
						if($malnutrition_data) {
							$province = $malnutrition_data;
						}
						foreach($province as $key=>$val) {
							switch($val['label']) {
								case 'malnutrition_schedule':
								case 'malnutrition_weekly':
								case 'immunisation_schedule':
								case 'immunisation_weekly':
								unset($province[$key]);
							}
						}
						?>
						<div style="width:40%;float:left;margin:10px; margin-right:20px;" id="malnut-schedule-select-div">
							<div class="form-group">
								<label>Schedule</label>
								<select class="form-control" name="malnutrition_schedule" id="malnut-schedule-select">
									<option value="daily" <?= ($malnutrition_schedule == 'daily')?'selected':''; ?>>Daily</option>
									<option value="weekly" <?= ($malnutrition_schedule == 'weekly')?'selected':''; ?>>Weekly</option>
									<option value="monthly" <?= ($malnutrition_schedule == 'monthly')?'selected':''; ?>>Monthly</option>
								</select>
							</div>
							<div>
								<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">**Daily notifications are sent at 12am.</p>
								<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">**Weekly notifications are sent every selected day of the week at 12am.</p>
								<p style="font-size: 90%;">**Monthly notifications are sent on the 1st at 12am.</p>
							</div>
						</div>
						<div style="width:40%;float:left;margin:10px;<?php echo $malnutrition_schedule!='weekly' ? 'display:none;' : '' ?>" id="malnut-weekly-select-div">
							<div class="form-group">
								<label>Every</label>
								<select class="form-control" name="malnutrition_weekly" id="malnut-weekly-select">
									<option value="sunday" <?= ($malnutrition_weekly == 'sunday')?'selected':''; ?>>Sunday</option>
									<option value="monday" <?= ($malnutrition_weekly == 'monday')?'selected':''; ?>>Monday</option>
									<option value="tuesday" <?= ($malnutrition_weekly == 'tuesday')?'selected':''; ?>>Tuesday</option>
									<option value="wednesday" <?= ($malnutrition_weekly == 'wednesday')?'selected':''; ?>>Wednesday</option>
									<option value="thursday" <?= ($malnutrition_weekly == 'thursday')?'selected':''; ?>>Thursday</option>
									<option value="friday" <?= ($malnutrition_weekly == 'friday')?'selected':''; ?>>Friday</option>
									<option value="saturday" <?= ($malnutrition_weekly == 'saturday')?'selected':''; ?>>Saturday</option>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php
						
						foreach($province as $key=>$val) {
							if (substr($val['label'], 0, 3 ) !== 'im_') {
                                ?>
							<div style="width:250px;float:left;margin:10px">
								<div class="form-group">
									<label><?php echo $val['label'] ?></label>
									<textarea name="<?php echo $val['label'] ?>" class="form-control"><?php echo $val['value'] ?></textarea>
								</div>
								<div>
									<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">*Email addresses should be separated by a comma</p>							
								</div>
							</div>
							<?php
							}
							else {
								
							}
						} 
						?>
						<div style="clear:both"></div>
					</p>
					<hr />
				
					<h3>Immunisation Reporting</h3>
					<input type="hidden" name="class" value="Immunisation" />
					<input type="hidden" name="func" value="storeNotificationSettingsForImmunisation" />
					<p>
						<?php
						$immunisation_data = $Immunisation->fetchNotificationSettingsForImmunisation();
						$immunisation_schedule = array_values(array_filter($immunisation_data, function($var) {
							return ($var['label'] == 'immunisation_schedule');
						}))[0]['value'];
						$immunisation_weekly = array_values(array_filter($immunisation_data, function($var) {
							return ($var['label'] == 'immunisation_weekly');
						}))[0]['value'];
						$province = array(
							array('label'=>'Central', 'value'=>''),
							array('label'=>'Eastern Highlands', 'value'=>''),
							array('label'=>'Hela', 'value'=>''),
							array('label'=>'Jiwaka', 'value'=>''),
							array('label'=>'Morobe', 'value'=>''),
							array('label'=>'NCD', 'value'=>''),
							array('label'=>'Western Highlands', 'value'=>''),
							array('label'=>'All Provinces', 'value'=>''),
						);
						if($immunisation_data) {
							$province = $immunisation_data;
						}
						foreach($province as $key=>$val) {
							switch($val['label']) {
								case 'malnutrition_schedule':
								case 'malnutrition_weekly':
								case 'immunisation_schedule':
								case 'immunisation_weekly':
								unset($province[$key]);
							}
						}
						?>
						<div style="width:40%;float:left;margin:10px; margin-right:20px;" id="immune-schedule-select-div">
							<div class="form-group">
								<label>Schedule</label>
								<select class="form-control" name="immunisation_schedule" id="immune-schedule-select">
									<option value="daily" <?= ($immunisation_schedule == 'daily')?'selected':''; ?>>Daily</option>
									<option value="weekly" <?= ($immunisation_schedule == 'weekly')?'selected':''; ?>>Weekly</option>
									<option value="monthly" <?= ($immunisation_schedule == 'monthly')?'selected':''; ?>>Monthly</option>
								</select>
							</div>
							<div>
								<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">**Daily notifications are sent at 12am.</p>
								<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">**Weekly notifications are sent every selected day of the week at 12am.</p>
								<p style="font-size: 90%;">**Monthly notifications are sent on the 1st at 12am.</p>
							</div>
						</div>
						<div style="width:40%;float:left;margin:10px;<?php echo $immunisation_schedule!='weekly' ? 'display:none;' : '' ?>" id="immune-weekly-select-div">
							<div class="form-group">
								<label>Every</label>
								<select class="form-control" name="immunisation_weekly" id="immune-weekly-select">
									<option value="sunday" <?= ($immunisation_weekly == 'sunday')?'selected':''; ?>>Sunday</option>
									<option value="monday" <?= ($immunisation_weekly == 'monday')?'selected':''; ?>>Monday</option>
									<option value="tuesday" <?= ($immunisation_weekly == 'tuesday')?'selected':''; ?>>Tuesday</option>
									<option value="wednesday" <?= ($immunisation_weekly == 'wednesday')?'selected':''; ?>>Wednesday</option>
									<option value="thursday" <?= ($immunisation_weekly == 'thursday')?'selected':''; ?>>Thursday</option>
									<option value="friday" <?= ($immunisation_weekly == 'friday')?'selected':''; ?>>Friday</option>
									<option value="saturday" <?= ($immunisation_weekly == 'saturday')?'selected':''; ?>>Saturday</option>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php
						foreach($province as $key=>$val) {
                            if (substr($val['label'], 0, 3) == 'im_') {
                                ?>
							<div style="width:250px;float:left;margin:10px">
								<div class="form-group">
									<label><?php echo substr($val['label'], 3, strLen($val['label'])) ?></label>
									<textarea name="<?= (substr($val['label'], 0, 3) == 'im_') ? $val['label'] : 'im_'.$val['label'] ?>" class="form-control"><?php echo $val['value'] ?></textarea>
								</div>
								<div>
									<p style="padding-bottom: 0px; margin-bottom: 4px; font-size: 90%;">*Email addresses should be separated by a comma</p>							
								</div>
							</div>
							<?php
                            }
						} 
						?>
						<div style="clear:both"></div>
					</p>
					<hr />
					<p>
						<button class="btn btn-primary" id="save-settings">Save</button>
					</p>
				</form>
			</div>
			
		</div><!--/span-->        
	</div>
</div>

<?php $type->scripts(); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#malnut-schedule-select').on('change', function() {
			if($(this).val() == "weekly") {
				$('#malnut-weekly-select-div').show();
			} else {
				$('#malnut-weekly-select-div').hide();
			}
		});
		$('#immune-schedule-select').on('change', function() {
			console.log('immune');
			if($(this).val() == "weekly") {
				$('#immune-weekly-select-div').show();
			} else {
				$('#immune-weekly-select-div').hide();
			}
		});
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
					_data = $(this).serialize();

					$.post(window.location.href,_data, function(data){
						if($.trim(data)!="success"){
							console.log(data);
						}
						else{
							alert('Saved Successfully!')
						}
						close_loader($);										
					})

					$('#settings-saved').show();
					$('html,body').animate({
						scrollTop: $("#settings-saved").offset().top - 50
	     			}, 1000);
				}
			})
			
		});
	});
</script>