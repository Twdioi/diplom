<?php
session_start();
if($_SESSION['admin_user'] == '' ){
	echo "<script>window.alert('You must login first!');
			window.location='./index.php'</script>";
	die();
}
?>

<!--?php
session_start();
if($_SESSION['admin_user'] == '' ){
	echo "<script>window.alert('You must login first!');
			window.location='./page.php'</script>";
	die();
}
?-->

<!--?php
session_start();
if($_SESSION['admin_user'] == '' ){
	echo "<script>window.alert('You must login first!');
			window.location='/IUSocial-master/register.php'</script>";
	die();
}
?-->