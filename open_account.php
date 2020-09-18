<?php
include("includes/header.php");

if(isset($_POST['cancel'])) {
	header("Location: settings.php");
}

if(isset($_POST['open_account'])) {
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE username='$userLoggedIn'");
	header("Location: index.php");
}

?>

<div class="main_column column">

	<h4>Открыть аккаунт</h4>

	<form action="open_account.php" method="POST">
		<input type="submit" name="open_account" id="open_account" value="Да!" class="danger settings_submit">
		<input type="submit" name="cancel" id="update_details" value="Нет!" class="info settings_submit">
	</form>

</div>