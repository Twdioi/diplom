<?php
include("includes/header.php"); //Header 
?>

<div class="user_details column">
		<a href="<?php echo "profile.php?profile_username=".$userLoggedIn; ?>">  <img src="<?php echo $user['profile_pic']; ?>"> </a>

		<div class="user_details_left_right">
			<a href="<?php echo "profile.php?profile_username=".$userLoggedIn; ?>" style="text-align:center;font-size: 17px;font-weight: bold;">
			<?php echo $user['first_name'] . " " . $user['last_name'];?>
			</a>
			<br><br/>
			<?php echo "<b>Должность:</b><br> " . $user['title']. "<br>"; 
			echo "<br><b>Обо мне:</b><br> " . $user['about'];

			?>
		</div>

	</div>

<div class="main_column column" id="main_column">

	<h4><b>Групповые Запросы</b></h4>

	<?php  

	$query = mysqli_query($con, "SELECT * FROM groups_request WHERE group_owner='$userLoggedIn'");
	if(mysqli_num_rows($query) == 0)
		echo "В настоящее время у вас нет запросов на участие ни в одной из групп!";
	else {

		while($row = mysqli_fetch_array($query)) {
			$user_from = $row['user_from'];
			$group_name = $row['group_to'];
			$user_from_obj = new User($con, $user_from);


			echo $user_from_obj->getFirstAndLastName() . " requested to join " . $group_name ."!";

			$user_from_friend_array = $user_from_obj->getFriendArray();

			if(isset($_POST['accept_request' . $user_from ])) {
				$add_friend_query = mysqli_query($con, "UPDATE groups SET users_array=CONCAT(users_array, '$user_from,'), num_users = num_users+1  WHERE group_name='$group_name'");

				$delete_query = mysqli_query($con, "DELETE FROM groups_request WHERE group_to='$group_name' AND user_from='$user_from'");
				// echo "You are now friends!";
				echo $user_from_obj->getFirstAndLastName() . " был(а) добавлен(а) к " . $group_name ."!";
				header("Location: group_requests.php");
			}

			if(isset($_POST['ignore_request' . $user_from ])) {
				$delete_query = mysqli_query($con, "DELETE FROM groups_request WHERE group_to='$group_name' AND user_from='$user_from'");
				echo "Просьбу проигнорировали!";
				header("Location: group_requests.php");
			}

			?>
			<form action="group_requests.php" method="POST">
				<input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Принять">
				<input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Игнорировать">
			</form>
			<?php


		}

	}

	?>


</div>
