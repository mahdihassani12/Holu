<?php
date_default_timezone_set('Asia/Kabul');

$db = new PDO(
    "mysql:host=localhost;dbname=your_database;charset=utf8",
    "your_username",
    "your_password"
);