<?php
include("includes/header.php");

if(isset($_POST['cancel'])) {
	header("Location: settings.php");
}

if(isset($_POST['close_account'])) {
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
	//session_destroy();
	//header("Location: register.php");
	header("Location: index.php");
}

?>

<div class="main_column column">

	<h4>Закрыть аккаунт</h4>

	Вы уверены что хотите закрыть аккаунт?<br><br>
	Закрытие вашего аккаунта скроет ваш профиль и всю вашу активность от других пользователей.<br><br>
	Вы можете открыть аккаунт в личных настройках.<br><br>

	<form action="close_account.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Да!" class="danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="Нет!" class="info settings_submit">
	</form>

</div>