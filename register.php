<?php  
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
header('Content-Type:text/html; charset=utf-8');
?>


<html>
<head>
	<meta charset="utf-8">
	<title>Добро пожаловать в Connect!</title>
	<!--link rel="stylesheet" type="text/css" href="assets/css/register_style.css"-->
	<link rel="stylesheet" type="text/css" href="assets/css/register.css">
	<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>
<body>

	<?php  

	if(isset($_POST['register_button']) || isset($_POST['reg_send_code_button'])) {
		echo '
		<script>

		$(document).ready(function() {
			$("#first").hide();
			$("#third").hide();
			$("#second").show();
		});

		</script>

		';
	}

	if(isset($_POST['forgot_password_email']) || isset($_POST['reset_verify_code']) || isset($_POST['reset_password_button'])) {
		echo '
		<script>

		$(document).ready(function() {
			$("#first").hide();
			$("#fifth").hide();
			$("#third").show();
		});

		</script>

		';
	}

	?>

	<div class="wrapper">

		<div class="login_box">

			<div class="login_header">
				<h1><img src ="assets/images/icons/Logo.png" width=99%/></h1>
			</div>
			<br>
			<div id="first">

				<form action="register.php" method="POST">
					<input type="email" name="log_email" placeholder="Адрес электронной почты" value="<?php 
					if(isset($_SESSION['log_email'])) {
						echo $_SESSION['log_email'];
					} 
					?>" required>
					<br>
					<input type="password" name="log_password" placeholder="Пароль">
					<br>
					<?php if(in_array("Email or password was incorrect<br>", $error_array)) echo "<script>alert(\"Неверный адрес электронной почты или пароль.\");</script>"; ; ?>
					<input type="submit" name="login_button" value="Войти">
					<!-- <input type="submit" id="forgot_password" name="forgot_password" value="Forgot Password"> -->
					<br>
					<a href="#" id="signup" style="font-family: 'Quicksand', sans-serif;" class="signup">Еще не зарегистированы? Зарегиструруйтесь здесь!</a><br>
					<a href="#" id="forgot_password" style="font-family: 'Quicksand', sans-serif;" class="forgot_password">Забыли Пароль? Восстановление пароля</a>

				</form>

			</div>

			<div id="third">

				<form action="register.php" method="POST">
					<input type="email" name="fp_email" placeholder="Адрес электронной почты" value="<?php 
					if(isset($_SESSION['fp_email'])) {
						echo $_SESSION['fp_email'];
					} 
					?>" required>
					<br>
					<input type="submit" id="third_button" name="forgot_password_email" value="Отправить код">
					<br>

					<?php if(in_array("No such email found. Please try a valid one<br>", $error_array)) echo "Такого письма не найдено. Пожалуйста, попробуйте действительный вариант<br>";
					else if(in_array("<span style='color: #14C800;'>A verification code has been sent to the provided email. Please enter it below.</span><br>", $error_array)) echo "<span style='color: #14C800;'>На указанный адрес электронной почты был отправлен проверочный код. Пожалуйста, введите его ниже.</span><br>"; ?>

					<input type="text" name="fp_code" placeholder="Введите код подтвержения">
					<br>
					<input type="submit" id="fourth_button" name="reset_verify_code" value="Проверка Кода">
					<!-- <input type="submit" id="fourth_button" value="Verify Code" name="<?php// if(in_array("Code matched", $error_array)) echo "reset_verify_code"; ?>"> -->
					<br>
					<?php if(in_array("<span style='color: #ff0000;'>Verification code did not match, please try again.</span><br>", $error_array)) echo "<span style='color: #ff0000;'>Проверочный код не совпал, пожалуйста, повторите попытку.</span><br>"; ?>

					<input type="password" name="reset_password" placeholder="Новый пароль">
					<br>
					<input type="password" name="reset_password2" placeholder="Подтвердите пароль">
					<br>
					<?php if(in_array("Your passwords do not match<br>", $error_array)) echo "Ваши пароли не совпадают<br>"; 
					else if(in_array("Your new password can only contain letters or numbers<br>", $error_array)) echo "Ваш новый пароль может содержать только буквы или цифры<br>";
					else if(in_array("Your new password must be between 5 and 30 characters<br>", $error_array)) echo "Ваш новый пароль должен содержать от 5 до 30 символов<br>";
					else if(in_array("Your new password has been reset<br>", $error_array)) echo "Ваш новый пароль был сброшен<br>"; ?>

					<input type="submit" id="fifth_button" name="reset_password_button" value="Сбросить пароль" <?php echo $_SESSION['reset_status']?>>
					<br>
					<?php if(in_array("<span style='color: #14C800;'>Your password has been reset! Go ahead and login!</span><br>", $error_array)) echo "<span style='color: #14C800;'>Ваш пароль был сброшен! Войдите в систему!</span><br>"; ?>

					<a href="#" id="reset_password_login" class="signin"> Войдите здесь!</a>

				</form>

			</div>


			<div id="second">

				<form action="register.php" method="POST">
					<input type="text" name="reg_fname" placeholder="Имя" value="<?php 
					if(isset($_SESSION['reg_fname'])) {
						echo $_SESSION['reg_fname'];
					} 
					?>" required>
					<br>
					<?php if(in_array("Your first name must be between 2 and 50 characters<br>", $error_array)) echo "Ваше имя должно быть от 2 до 50 символов<br>"; ?>
					
					


					<input type="text" name="reg_lname" placeholder="Фамилия" value="<?php 
					if(isset($_SESSION['reg_lname'])) {
						echo $_SESSION['reg_lname'];
					} 
					?>" required>
					<br>
					<?php if(in_array("Your last name must be between 2 and 50 characters<br>", $error_array)) echo "Ваша фамилия должна быть от 2 до 50 символов<br>"; ?>

					<input type="email" name="reg_email" placeholder="Адрес электронной почты" value="<?php 
					if(isset($_SESSION['reg_email'])) {
						echo $_SESSION['reg_email'];
					} 
					?>" required>
					<br>

					<input type="email" name="reg_email2" placeholder="Подтверждение электронной почты" value="<?php 
					if(isset($_SESSION['reg_email2'])) {
						echo $_SESSION['reg_email2'];
					} 
					?>" required>
					<br>
					<?php if(in_array("Email already in use<br>", $error_array)) echo "Электронная почта уже используется<br>"; 
					else if(in_array("Invalid email format<br>", $error_array)) echo "Неверный формат электронной почты<br>";
					else if(in_array("Emails do not match<br>", $error_array)) echo "Электронная почта не совпадает<br>"; 
					else if(in_array("Email should be an IU mail<br>", $error_array)) echo "Email should be an IU mail<br>";?>

					<input type="password" name="reg_password" placeholder="Пароль" value="<?php 
					if(isset($_SESSION['reg_pw'])) {
						echo $_SESSION['reg_pw'];
					} 
					?>" required>
					<br>
					<input type="password" name="reg_password2" placeholder="Подтверждение пароля" value="<?php 
					if(isset($_SESSION['reg_pw2'])) {
						echo $_SESSION['reg_pw2'];
					} 
					?>" required>
					<br>
					<?php if(in_array("Your passwords do not match<br>", $error_array)) echo "Ваши пароли не совпадают<br>"; 
					else if(in_array("Your password can only contain letters or numbers<br>", $error_array)) echo "Ваш пароль может содержать только буквы или цифры<br>";
					else if(in_array("Your password must be between 5 and 30 characters<br>", $error_array)) echo "Ваш пароль должен содержать от 5 до 30 символов<br>"; ?>

					<input type="text" name="reg_title" placeholder="Кто вы? (студент, выпускник, доцент)" value="<?php 
					if(isset($_SESSION['reg_title'])) {
						echo $_SESSION['reg_title'];
					} 
					?>" required>
					<br>
					<?php if(in_array("Your title must be between 2 and 20 characters<br>", $error_array)) echo "Ваш заголовок страницы должен содержать от 2 до 20 символов<br>"; ?>

					
					<input type="submit" name="reg_send_code_button" value="Отправить код">
					<br>
					<?php if(in_array("<span style='color: #14C800;'>A verification code has been sent to the provided email. Please enter it below.</span><br>", $error_array)) echo "<span style='color: #14C800;'>На указанный адрес электронной почты был отправлен проверочный код. Пожалуйста, введите его ниже.</span><br>"; ?>

					<input type="text" name="register_codebox" placeholder="Пожалуйста, введите проверочный код здесь">
					<br>
					<?php if(in_array("<span style='color: #ff0000;'>Verification code did not match, please try again.</span><br>", $error_array)) echo "<span style='color: #ff0000;'>Проверочный код не совпал, пожалуйста, повторите попытку.</span><br>"; ?>					
					
					<input type="submit" name="register_button" value="Зарегистрироваться">
					<br>

					<?php if(in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)) echo "<span style='color: #14C800;'>Все готово! Войдите в систему!</span><br>"; ?>
			<!--?php if(in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)) header("Location: register.php"); echo "<span style='color: #14C800;'>Все готово! Войдите в систему!</span><br>"; ?-->

					<a href="#" id="signin" class="signin">Вы зарегистировались! Войдите здесь!</a>
				</form>
			</div>

		</div>

	</div>


</body>
</html>
