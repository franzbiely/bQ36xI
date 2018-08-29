<?php

$password = $_GET['pass'];
$salt = md5($password);
echo md5($password.$salt);


