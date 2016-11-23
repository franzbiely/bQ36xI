<?php  $data = $client->view_dummy_data(); ?>
<?php //print_r($data); ?>
<?php echo count($data); ?>
<br><br>
<table>
	<thead>
		<tr>
			<td>Record Number</td>
			<td>First Name</td>
			<td>last Name</td>
			<td>Phone</td>
			<td>Place of Birth</td>
			<td>Current Address</td>
		</tr>
	</thead>
	<tbody>
<?php foreach ($data as $key => $data): ?>
	<tr>
		<td><?php echo $data['record_number']; ?></td>
		<td><?php echo $data['fname']; ?></td>
		<td><?php echo $data['lname']; ?></td>
		<td><?php echo $data['phone']; ?></td>
		<td><?php echo $data['place_of_birth']; ?></td>
		<td><?php echo $data['current_address']; ?></td>
	</tr>
<?php endforeach ?>
	</tbody>
</table>