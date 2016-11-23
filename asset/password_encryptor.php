<?php 
	
	$pass=$_GET['pass'];
  	$salt= md5($pass);

   $encrypted_pass = md5($pass.$salt);
   echo $encrypted_pass;
 ?>