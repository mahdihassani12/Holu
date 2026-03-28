<?php
session_start();

if (isset($_SESSION['holu_users_id']) && isset($_SESSION['holu_username'])) {
    unset($_SESSION['holu_users_id']);
    unset($_SESSION['holu_username']);
    
    header("location: ../login.php");
    exit();
}
?>