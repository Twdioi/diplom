<?php  
require 'config/config.php';
header('Content-Type:text/html; charset=utf-8');

if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}
else {
	header("Location: register.php");
}

?>

<html>
<head>
	<title>Добро пожаловать в Connect!</title>
  <style>
    .logobutton {
      cursor: pointer;
      display: block;
      margin:  0 auto; 
      opacity: 1.0;
      border: 0;
      background: transparent;
      position: fixed;
    }
    .logobutton:hover {
      opacity: 0.5;
    }
  </style>

	<!-- Javascript -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/bootbox.min.js"></script>
	<script src="assets/js/demo.js"></script>
	<!-- <script src="assets/js/jquery.jcrop.js"></script> -->
	<!-- <script src="assets/js/jcrop_bits.js"></script> -->


	<!-- CSS -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
</head>
<body>

	<div class="top_bar"> 

		<div class="logo">
 
			<img src="assets/images/icons/logo1.png" style="margin-left: 5px; height: 42px;"/>
 
		</div>


		<nav>

      		&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="includes/handlers/logout.php">
				<i class="fa fa-sign-out fa-lg"></i>
			</a>

		</nav>

		<div class="dropdown_data_window" style="height:0px; border:none;"></div>
		<input type="hidden" id="dropdown_data_type" value="">


	</div>

	<div class="wrapper">

<div class="main_column column" id="main_column">
	<h4>Ваша страница заблокирована !!! </h4>
		Пользователь Заблокирован
</div>
