<?php
error_reporting(E_ALL ^ E_NOTICE);
@include "../config/config.inc.php";
@include "../session_member.php";

$act = $_GET['act'];
$id = $_GET['id'];

// Encrypt the Password
//$_POST[password] = ($_POST[password]);

// Sanitize $_GET parameters to avoid XSS and other attacks
if(strpos(strtolower($id), 'union') || strpos(strtolower($id), 'select') || strpos(strtolower($id), '/*') || strpos(strtolower($id), '*/')) {
   echo "<div class=\"alert alert-warning col-lg-3 col-offset-6 centered col-centered\">
  <strong>Warning!</strong> SQL injection attempt detected.</div>";
   die;
}

if ($act=='del'){
	mysqli_query($con,"DELETE FROM users WHERE id='$id'");
	mysqli_close($con);
	echo "<script language='javascript'>alert('Data Deleted.');
	document.location='../page.php?page=users';</script>";
}

if ($act=='ban'){
	mysqli_query($con,"UPDATE users SET `block` =  'yes' WHERE id='$id'");
	mysqli_close($con);
	echo "<script language='javascript'>alert('Data Deleted.');
	document.location='../page.php?page=users';</script>";
}

if ($act=='add'){

	mysqli_query($con,"INSERT INTO users (`first_name`,`last_name`,`title`) VALUES ('$_POST[first_name]','$_POST[last_name]','$_POST[title]')");
	mysqli_close($con);
	echo "<script language='javascript'>alert('Data Added.');
	document.location='../page.php?page=users';</script>";
}

if ($act=='update'){
	mysqli_query($con,"UPDATE users SET `first_name` =  '$_POST[first_name]',
										`last_name` =  '$_POST[last_name]',
										`title` =  '$_POST[title]'
									WHERE `id` = '$id'");
	mysqli_close($con);
	echo "<script language='javascript'>alert('Data Updated.');
	document.location='../page.php?page=users';</script>";
}

?>