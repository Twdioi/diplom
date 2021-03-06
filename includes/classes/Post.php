<?php
header('Content-Type:text/html; charset=utf-8');
class Post {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitPost($body, $user_to, $group_to) {
		$body = strip_tags($body); //removes html tags 
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deltes all spaces 
      
		if($check_empty != "") {


			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value) {

				if(strpos($value, "www.youtube.com/watch?v=") !== false) {

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;

				}

			}
			$body = implode(" ", $body_array);



			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			//If user is on own profile, user_to is 'none'
			if($user_to == $added_by)
				$user_to = "none";
			if ($group_to == $added_by)
				$group_to = "none";

			//insert post 
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to','$group_to', '$date_added', 'no', 'no', '0')");
			$returned_id = mysqli_insert_id($this->con);

			//Insert notification
			if($user_to != 'none') {
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "profile_post");
			}

			//Update post count for user 
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

			//Insert notification
			if($group_to != 'none') {
				$update_post_count = mysqli_query($this->con, "UPDATE groups SET num_posts = num_posts+1 WHERE group_name='$group_to'");
			}


		}
	}


	public function loadPostsFriends($data, $limit) {

		$page = $data['page']; 
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				$isGroup = 'false';
				//Prepare user_to string so it can be included even if not posted to a user
				if($row['user_to'] == "none" && $row['group_to'] == "none") {
					$user_to = "";
				}
				else if($row['group_to'] == "none"){
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = " <a href='profile.php?profile_username=" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}
				else {
					$isGroup = 'true';
					$group_to_obj = new Groups($this->con, $row['added_by'], $row['group_to']);
					$group_to_name = $group_to_obj->getGroupName();
					$user_to = "в <a href='group.php?group_name=" . $row['group_to'] ."'>" . $group_to_name . "</a>";
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}
				//Check if group on which it is posted has been deleted
				if($isGroup=='true' && $group_to_obj->isDeleted()) {
					continue;
				}
				
				$group_to_obj2 = new Groups($this->con, $userLoggedIn, $row['group_to']);
				if($isGroup=='true' && !$group_to_obj2->isMember()){
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)){

					if($num_iterations++ < $start)
						continue; 


					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";


					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script> 
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " год назад"; //1 year ago
						else 
							$time_message = $interval->y . " года  назад"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " дня назад";
						}
						else {
							$days = $interval->d . " дня назад";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " месяц". $days;
						}
						else {
							$time_message = $interval->m . " месяца". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Вчера";
						}
						else {
							$time_message = $interval->d . " дня назад";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " час назад";
						}
						else {
							$time_message = $interval->h . " часа назад";
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
							<hr style='background-color:#efefef;height:20px;margin-left:-10px;width:103%'>";
				}

				?>
				<script>

					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Вы действительно хотите удалить пост?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});


					});

				</script>
				<?php

			} //End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> Постов больше нет! </p>";
		}

		echo $str;
	}


	public function loadProfilePosts($data, $limit) {

		$page = $data['page']; 
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND group_to='none' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser')  ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];


					if($num_iterations++ < $start)
						continue; 


					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						//$delete_button = "<button class='delete_button btn-danger' id='post$added_by'>X</button>";
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";


					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script> 
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " год назад"; //1 year ago
						else 
							$time_message = $interval->y . " года  назад"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " дня назад";
						}
						else {
							$days = $interval->d . " дня назад";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " месяц". $days;
						}
						else {
							$time_message = $interval->m . " месяца". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " дня назад";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " час назад";
						}
						else {
							$time_message = $interval->h . " часа назад";
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

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='profile.php?profile_username=$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
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
							<hr style='background-color:#efefef;height:20px;margin-left:-10px;width:103%'>";

				?>
				<script>

					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Вы уверены что хотите удалить пост?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});


					});

				</script>
				<?php

			} //End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> Постов больше нет! </p>";
		}

		echo $str;


	}

	public function getSinglePost($post_id) {

		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

		if(mysqli_num_rows($data_query) > 0) {


			$row = mysqli_fetch_array($data_query); 
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				//Prepare user_to string so it can be included even if not posted to a user
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href=profile.php?profile_username=" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					return;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)){


					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";


					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script> 
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " год назад"; //1 year ago
						else 
							$time_message = $interval->y . " года  назад"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " дня назад";
						}
						else {
							$days = $interval->d . " дня назад";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " месяц". $days;
						}
						else {
							$time_message = $interval->m . " месяца". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Вчера";
						}
						else {
							$time_message = $interval->d . " дня назад";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " час назад";
						}
						else {
							$time_message = $interval->h . " часа назад";
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
							$time_message = "сейчас";
						}
						else {
							$time_message = $interval->s . " секунду назад";
						}
					}

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
				}
				else {
					echo "<p>Вы не можете увидеть этот пост, потому что вы не дружите с этим пользователем.</p>";
					return;
				}
		}
		else {
			echo "<p>Постов не найдено. Если вы нажмете на ссылку, она может не корректна.</p>";
					return;
		}

		echo $str;
	}



	public function loadGroupPosts($data, $limit) {

		$page = $data['page']; 
		$profileGroupName = $data['profileGroupName'];
		$userLoggedIn = $this->user_obj->getUsername();
		$group = new Groups($this->con, $userLoggedIn, $profileGroupName);

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND group_to='$profileGroupName' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];


					if($num_iterations++ < $start)
						continue; 


					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
					else 
						$delete_button = "";


					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


					?>
					<script> 
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " год назад"; //1 year ago
						else 
							$time_message = $interval->y . " года  назад"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " дня назад";
						}
						else {
							$days = $interval->d . " дня назад";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " месяц". $days;
						}
						else {
							$time_message = $interval->m . " месяца". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Вчера";
						}
						else {
							$time_message = $interval->d . " дня назад";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " час назад";
						}
						else {
							$time_message = $interval->h . " часа назад";
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

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='profile.php?profile_username=$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
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
							<hr>";

				?>
				<script>

					$(document).ready(function() {

						$('#post<?php echo $id; ?>').on('click', function() {
							bootbox.confirm("Вы уверены что хотите удалить пост?", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});


					});

				</script>
				<?php

			} //End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> Постов больше нет! </p>";
		}

		echo $str;


	}
}

?>