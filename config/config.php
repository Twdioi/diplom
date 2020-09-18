<?php
ob_start(); //Turns on output buffering 
session_start();



$timezone = date_default_timezone_set("Asia/Yakutsk");

//$con = mysqli_connect("localhost", "root", "", "connect"); //Connection variable
$con = mysqli_connect("localhost", "root", "", "con"); //Connection variable
//$con = mysqli_connect("localhost", "root", "", "democon"); //Connection variable
/*$con = mysqli_query('set character_set_client="utf8"');
$con = mysqli_query('set character_set_results="utf8"');
$con = mysqli_query('set collation_connection="utf8_general_ci"');
$con ->display_errors = true;*/

if(mysqli_connect_errno()) 
{
	echo "Failed to connect: " . mysqli_connect_errno();
}
/*mysqli_query("SET NAMES 'utf8'");

mysqli_query("SET CHARACTER SET 'utf8'");*/

$con->set_charset('utf8');
?>