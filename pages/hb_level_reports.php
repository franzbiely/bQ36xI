<style>
	.col-f1 {
		width:22%;
	}
	.col-f2 {
		width:78%;
	}
	.td-a {
		padding: 0px 5px;
		vertical-align: top;
		border-bottom: 1px solid #d3d3d3;
		border-right:1px solid #d3d3d3;
	}
	.brd-left {
		border-left:1px solid #d3d3d3;
	} 
	.brd-right {
		border-right:1px solid #d3d3d3;
	}
	.br-wrd {
		word-break: break-word;
	}
</style>

<?php header_nav_bar("indent-left", "HB Level Reports","reports") ?>

<?php

function colorize($hb_level) {
	if($hb_level == '8-') {
		$color = '#FF0000';
	} else if($hb_level == '9-') {
		$color = '#FFA500';
	} else if($hb_level == '10-') {
		$color = '#E8E812';
	} else if($hb_level == '10+') {
		$color = '#008000';
	} else {
		$color = '#000000';
	}
	return '<span style="color:'.$color.'">' . $hb_level . '</span>';
}

function disp_record($arr) {

	foreach($arr as $a):
	?>
		<tr>
			<td class="br-wrd" style="padding:8px 13px;font-weight:bold;"><?= $a['client']['lname'] . ', ' . $a['client']['fname'].' - '.$a['client']['record_number'] ?></td>
			<td>
				<table style="width: 100%;">
	<?php


		$tmp = "";

		foreach($a['records'] as $idx => $c_record) {
			//$tmp .= date('m/d/Y', strtotime($c_record['date'])) . ' : HB ' . colorize($c_record['hb_level']) . ', ';
			//($c_record['hb_level'] == '10-' || $c_record['hb_level'] == '9-')?'8-10':$c_record['hb_level'];
		?>
					<tr <?= ($idx == 0)?'style="border-top:1px solid #d3d3d3"':'';?>>
						<td class="td-a brd-left br-wrd" style="width:20%"><?= date('jS M Y', strtotime($c_record['date'])); ?></td>
						<td class="td-a br-wrd" style="width:10%">
						<?php 
							if($c_record['hb_level'] == '10+'){
								echo " Hb>10g%";
							}else if($c_record['hb_level'] == '10-' || $c_record['hb_level'] == '9-'){
								echo " Hb8-10g%";
							}else if($c_record['hb_level'] == '8-'){
								echo " Hb<8g%";
							}

						?></td>
						<td class="td-a br-wrd" style="width:25%"><?= $c_record['clinic_name']?></td>
						<td class="td-a br-wrd" style="width:20%"><?= $c_record['clinic_type']; ?></td>
						<td class="td-a br-wrd" style="width:25%"><?= $c_record['area_name'] ?></td>
					</tr>
		<?php
		}

	?>
	
				
				</table>
			</td>
		</tr>
	
	<?php endforeach; 
}


?>

<div class="container">
	<div class="row">
		<div class="col-md-3">
			<?php include('parts/sidebar.php') ?>
		</div>
		<div class="col-md-9">
			<div class="page-header">
				<h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">HB Level Reports</h1>
				<div class="clearfix">
					<form method="POST" action="?page=hb_level_reports" id="frmClientReport" class="form-inline" role="form" style="float: right; margin-top: -5px;">
						<div class="form-group">
							<label class="sr-only" for="startdate"></label>
							<input type="text" autocorrect="off" autocomplete="off" class="form-control" name="start_date" id="start_date" placeholder="Enter Start Date" value="<?php echo isset($_POST["start_date"])?$_POST["start_date"]:""; ?>" required>
						</div>
						<div class="form-group">
							<label class="sr-only" for="exampleInputPassword2"></label>
							<input type="text" autocorrect="off" autocomplete="off" class="form-control" name="end_date" id="end_date" placeholder="Enter End Date" value="<?php echo isset($_POST["end_date"])?$_POST["end_date"]:""; ?>" required>
						</div>
						<?php if(!empty($sel_shown)): ?>
							<div class="form-group">
								<select class="form-control" name="by" id="by" required>
									<option value="">Select Type</option>
									<option value="clinic">Clinic</option>
									<option value="office">Health Facility</option>
									<!-- <option value="llg">LLG</option> -->
									<option value="district">District</option>
									<option value="province">Province</option>
								</select>
							</div>
						<?php endif; ?>
						<input type="submit" value="Generate Report" name="btnGenReport" class="btn btn-default" style="margin-top: 5px;" />
					</form>      
				</div> 
			</div>

			<?php
			if(!isset($_POST['start_date'])) :
				echo "<p>Please set start and end date.</p>";
			else:
				$data = $reports->get_hb_level_record($_POST['start_date'], $_POST['end_date']);
				/*$total_no_client = $reports->count_client($data);
				$total_no_consul = $reports->count_no_consultation($data);
				$total_no_referrals = $reports->count_no_referrals($data);
				if($total_no_consul != 0)$ave_no_consul = $total_no_consul/$total_no_client;*/
				if (isset($_POST['by']) AND isset($_POST['id'])) {
		          // use in the url as data to be use in export report function
					$by = $_POST['by'];
					$id = $_POST['id'];
				}

				if($data==false):
					echo "<p>No Record Found in the specified date and \"By\" field.</p>";
				else:
					$a1 = array(); //10+
					$b1 = array(); //10-
					$c1 = array(); //9-
					$d1 = array(); //8-

					foreach($data as $a) {
						if(count($a['records'] > 0)) {
							if($a['records'][0]['hb_level'] == '10+') {
								$a1[] = $a;
							} else if($a['records'][0]['hb_level'] == '10-') {
								$b1[] = $a;
							} else if($a['records'][0]['hb_level'] == '9-') {
								$b1[] = $a;
							} else if($a['records'][0]['hb_level'] == '8-') {
								$d1[] = $a;
							}
						}
					}
					
					?>

					<table class="table">
						<tr>
							<th class="col-f1">Below 8</th>
							<th class="col-f2">
								<table style="width:100%;margin-bottom: 0px;">
									<tr>
										<td class="" style="width:20%">Date</td>
										<td class="" style="width:10%">HB</td>
										<td class="" style="width:25%">Clinic</td>
										<td class="" style="width:20%">Type</td>
										<td class="" style="width:25%">Location</td>
									</tr>
								</table>
							</th>
						</tr>
						<?php disp_record($d1); ?>
					</table>
					<table class="table">
						<tr>
							<th class="col-f1">Between 8-10</th>
							<th class="col-f2">
								<table style="width:100%;margin-bottom: 0px;">
									<tr>
										<td class="" style="width:20%">Date</td>
										<td class="" style="width:10%">HB</td>
										<td class="" style="width:25%">Clinic</td>
										<td class="" style="width:20%">Type</td>
										<td class="" style="width:25%">Location</td>
									</tr>
								</table>
							</th>
						</tr>
						<?php disp_record($b1); ?>
					</table>
					
					<table class="table">
						<tr>
							<th class="col-f1">Above 10</th>
							<th class="col-f2">
								<table style="width:100%;margin-bottom: 0px;">
									<tr>
										<td class="" style="width:20%">Date</td>
										<td class="" style="width:10%">HB</td>
										<td class="" style="width:25%">Clinic</td>
										<td class="" style="width:20%">Type</td>
										<td class="" style="width:25%">Location</td>
									</tr>
								</table>
							</th>
						</tr>
						<?php disp_record($a1); ?>
					</table>

					<?php

				endif;
			endif;
			?>
		</div>
	</div>

</div>

<?php $reports->scripts(); ?>