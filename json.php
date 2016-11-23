<?php

$file = $_POST['file'];
$func = $_POST['func'];


if($func == 'read') {
	$json = json_decode(file_get_contents($file), true);
	return json;
} else if($func == 'write') {
	$data = $_POST['data'];
	file_put_contents($file, json_encode($data,TRUE));
}


?>