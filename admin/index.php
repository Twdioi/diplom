<?php
date_default_timezone_set('Australia/Victoria');
error_reporting(E_ALL ^ E_NOTICE);
@include "./config/config.inc.php";
session_start();

//$user = mysqli_real_escape_string($con, $_POST['admin_user']);
//$pass = mysqli_real_escape_string($con, $_POST['password']);

$user = 'admin';
$pass = '1234567890';

if ($user AND $pass)
{
	$login = mysqli_query($con,"SELECT * FROM admin_info WHERE admin_user = '$user' AND password = '$pass'");
	//$login = mysqli_query($con,"SELECT * FROM admin_info WHERE admin_user = '$user' AND password = '789456'");
	$match = mysqli_num_rows($login);
	$r     = mysqli_fetch_array($login);
	if ($match > 0)
	//if (1 > 0)
	{
		session_start();
		$_SESSION['admin_user'] = 'admin';
		$_SESSION['password'] = "1234567890";
		//$_SESSION['password'] = $r['password'];
		
			
		} 
		echo "<script>window.location='./page.php?page=dashboard'</script>";
	} else 
	{
		echo "<script>window.alert('не верный логин или пароль');</script>";
	}

?>
