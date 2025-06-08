<?php

$host = 'localhost';
$dbname   = 'eventoria';
$password = '';
$username = 'root';

$dbuser = new mysqli($host, $username, $password, $dbname);
if ($dbuser->connect_error) {
    die("Connection failed: " . $dbuser->connect_error);
} 
echo "Connected successfully";
?>