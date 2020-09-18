<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>

	<style type="text/css">
	* {
		font-size: 12px;
		font-family: Arial, Helvetica, Sans-serif;
	}

	</style>

	<?php  
	require 'config/config.php';
	include("includes/classes/User.php");
	include("includes/classes/Post.php");
	include("includes/classes/Notification.php");

	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: register.php");
	}

	?>
	<script>
		function toggle() {
			var element = document.getElementById("comment_section");

			if(element.style.display == "block") 
				element.style.display = "none";
			else 
				element.style.display = "block";
		}
	</script>

	<?php  
	//Get id of post
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

	$user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
	$row = mysqli_fetch_array($user_query);

	$posted_to = $row['added_by'];
	$user_to = $row['user_to'];
	
	if(isset($_POST['postComment' . $post_id])) {
		$post_body = $_POST['post_body'];
		$post_body = mysqli_escape_string($con, $post_body);
		$date_time_now = date("Y-m-d H:i:s");
		$insert_post = mysqli_query($con, "INSERT INTO comments VALUES ('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");

		if($posted_to != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $posted_to, "comment");
		}
		
		if($user_to != 'none' && $user_to != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_to, "profile_comment");
		}


		$get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
		$notified_users = array();
		while($row = mysqli_fetch_array($get_commenters)) {

			if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to 
				&& $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {

				$notification = new Notification($con, $userLoggedIn);
				$notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

				array_push($notified_users, $row['posted_by']);
			}

		}


		echo "<p>Комментарий опубликован! </p>";
	}
	?>

	<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
		<textarea name="post_body" placeholder="Напиите коментарий..."></textarea>
		<input type="submit" name="postComment<?php echo $post_id; ?>" value="Добавить">
	</form>

	<!-- Load comments -->
	<?php  
	$get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
	$count = mysqli_num_rows($get_comments);
	$str = "";
	if($count != 0) {

		while($comment = mysqli_fetch_array($get_comments)) {
			
			$id_c = $comment['id'];
			$comment_body = $comment['post_body'];
			$posted_to = $comment['posted_to'];
			$posted_by = $comment['posted_by'];
			$date_added = $comment['date_added'];
			$removed = $comment['removed'];
			
			if($userLoggedIn == $posted_by)
				/*$delete_button =" <button class='delete btn btn-danger' id='del_<?= $id_c ?>' data-id='<?= $id_c ?>' >Delete</button>";
			$delete_button =" <button class='delete btn btn-danger' >Delete</button>";*/
			/*$delete_button = "<a href='#' OnClick='FunctionBlock($id_c)'>
									<button type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-off'></span></button>
								</a>"; работате */
								
			$delete_button = "<a href='#' OnClick='FunctionBlock($id_c)'>
									<button class='delete btn btn-danger' id='com$id_c' style = 'height: 15px;
						width: 15px;
						padding: 0;
						float: right;
						border-radius: 4px;
						right: 15px;
						position: relative;
						color: #fff;
						background-color: #d43f3a91;
						border-color: #d43f3a0f;'>X</button>
								</a>";					
								
						/*$delete_button = "<button class='delete btn btn-danger' id='com$id_c' style = 'height: 15px;
						width: 15px;
						padding: 0;
						float: right;
						border-radius: 4px;
						right: 15px;
						position: relative;
						color: #fff;
						background-color: #d43f3a91;
						border-color: #d43f3a0f;'>X</button>";*/
					else 
						$delete_button = "";
					
			$user_details_query = mysqli_query($con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$posted_by'");
			$user_row = mysqli_fetch_array($user_details_query);
			$first_name = $user_row['first_name'];
			$last_name = $user_row['last_name'];
			$profile_pic = $user_row['profile_pic'];
					
			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_added); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
			if($interval->y >= 1) {
				if($interval == 1)
					$time_message = $interval->y . " год назад"; //1 year ago
				else 
					$time_message = $interval->y . " лет назад"; //1+ year ago
			}
			else if ($interval->m >= 1) {
				if($interval->d == 0) {
					$days = " ago";
				}
				else if($interval->d == 1) {
					$days = $interval->d . " день назад";
				}
				else {
					$days = $interval->d . " дней назад";
				}


				if($interval->m == 1) {
					$time_message = $interval->m . " месяц". $days;
				}
				else {
					$time_message = $interval->m . " месяцев". $days;
				}

			}
			else if($interval->d >= 1) {
				if($interval->d == 1) {
					$time_message = "Вчера";
				}
				else {
					$time_message = $interval->d . " дней назад";
				}
			}
			else if($interval->h >= 1) {
				if($interval->h == 1) {
					$time_message = $interval->h . " час назад";
				}
				else {
					$time_message = $interval->h . " часов назад";
				}
			}
			else if($interval->i >= 1) {
				if($interval->i == 1) {
					$time_message = $interval->i . " минуту назад";
				}
				else {
					$time_message = $interval->i . " минут назад";
				}
			}
			else {
				if($interval->s < 30) {
					$time_message = "Сейчас";
				}
				else {
					$time_message = $interval->s . " секунд назад";
				}
			}			
					
			$user_obj = new User($con, $posted_by);
								
			$str =	"
			 <div class='comment_section' >
			<div class='post_profile_pic'>
						<img src='$profile_pic' width='30'>
				</div>
			<div class='posted_by' style='color:#ACACAC;'>
			
					<a href='profile.php?profile_username=$posted_by'> $first_name $last_name </a> $time_message
									$delete_button 

			</div>
					
				 $comment_body
				<hr>
			</div>
				"

		?>
					
<script src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>	
<script>
function FunctionBlock(id) {
    var r = confirm("Вы уверены что хотите уалить комментарий?");
    if (r == true) {
        $.get("./includes/form_handlers/delete_comment.php?act=ban&id=" + id, function(data, status){
			// alert("Data: " + data + "\nStatus: " + status);
			location.reload();
		});
    }
}
</script>

		
	<?php
		echo $str;
		}
	}
	else {
		echo "<center><br><br>Комментариев пока нет!</center>";
	}

	
	?>


</body>
</html>