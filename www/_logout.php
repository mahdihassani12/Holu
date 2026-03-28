<?php
	session_start();
	if(isset($_SESSION['holu_users_id']) AND isset($_SESSION['holu_username'])){
		session_unset($_SESSION['holu_users_id']);
		session_unset($_SESSION['holu_username']);
		header("location:../login.php");
		exit();
	}
?>