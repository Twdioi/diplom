	<?php  
	//Get id of post
	/*if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}*/
class Comm {	
public function getcomment($post_id) {
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
				$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";

					
			
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
	$str = ""; //String to return 
			$user_obj = new User($con, $posted_by);
			$userLoggedIn = $user_obj->getUsername();
								$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='profile.php?profile_username=$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$delete_button
								</div>
								<div id='post_body'>
									$body
									<br>
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									Комментарии($comments_check_num)&nbsp;&nbsp;&nbsp;
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<div class='post_comment' id='toggleComment$id' style='display:none;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
							</div>
							<hr style='background-color:#efefef;height:20px;margin-left:-10px;width:103%'> ";


		?>
<script>

					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Вы уверены что хотите удалить этот пост?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});


					});

				</script>
			<?php
						echo $str;
		}
	}
