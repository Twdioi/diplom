<?php 
include("includes/header.php");
$message_obj = new Message($con, $userLoggedIn);

if(isset($_GET['u']))
	$user_to = $_GET['u'];
else {
	$user_to = $message_obj->getMostRecentUser();
	if($user_to == false)
		$user_to = 'new';
}

if($user_to != "new")
	$user_to_obj = new User($con, $user_to);

if(isset($_POST['post_message'])) {
	 
	if(isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($user_to, 'none', $body, $date,'none');
		header("Location:messages.php"); //работает 
	}
	
}

 ?>
 <div class="user_details column">
		<a href="<?php echo "profile.php?profile_username=".$userLoggedIn; ?>">  <img src="<?php echo $user['profile_pic']; ?>"> </a>

		<div class="user_details_left_right">		
			<a href="<?php echo "profile.php?profile_username=".$userLoggedIn; ?>" style="text-align:center;font-size: 17px;font-weight: bold;">
			<?php echo $user['first_name'] . " " . $user['last_name'];?>
			</a>
			<br><br/>
			<?php echo "<b>Должность:</b><br> " . $user['title']. "<br>"; 
			echo "<br><b>обо мне:</b><br> " . $user['about'];

			?>
		</div>
	</div>

	<div class="main_column column" id="main_column">
		<?php  
		if($user_to != "new"){
			echo "<h4>Вы и <a href='profile.php?profile_username=" . $user_to ."'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";

			echo "<div class='loaded_messages' id='scroll_messages'>";
				echo $message_obj->getMessages($user_to, 'none');
			echo "</div>";
		}
		else {
			echo "<h4>Новое сообщениеe</h4>";
		}
		?>



		<div class="message_post">
			<form action="" method="POST">
				<?php
				if($user_to == "new") {
					echo "Выберите друга, которому вы хотите отправить сообщение <br><br>";
					?> 
					Кому: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Имя' autocomplete='off' id='seach_text_input'>

					<?php
					echo "<div class='results'></div>";
				}
				else {
					echo "<textarea name='message_body' id='message_textarea' placeholder='Напишите сообщение ...'></textarea>";
					
					/*echo "  <div id='drop-zone'>	<span class='text'>Нажмите сюда или перетащите файл для загрузки.</span>
							<input id='file' name = 'file' type='file'> </div>
							<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea> ";*/
					echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Отправить'>";
					//echo "<div id='drop-zone'>	<span class='text'>Нажмите сюда или перетащите файл для загрузки.</span>  <input id='file' name = 'file' type='file' > </div>";
					
					//echo $file_name = $_FILES['file']['name'];
					//echo '<meta http-equiv="refresh" content="5;URL=http://localhost/IUSocial-master/messages.php">'; exit(); // РАБОТАЕТ НО КАК УЕБОК
					//echo '<meta URL=http://localhost/IUSocial-master/messages.php">'; die;// РАБОТАЕТ НО КАК УЕБОК
					//header('http://localhost/IUSocial-master/messages.php'); exit();
				}		
				
				?>		
			</form>	
			

    <div id="drop-zone">
    	<span class="text">Нажмите сюда или перетащите файл для загрузки.</span>
        <input id="file" name = "file" type="file">
    </div>
 		
	</div>


		<script>
			var div = document.getElementById("scroll_messages");
			div.scrollTop = div.scrollHeight;
		</script>
		
<link type="text/css" rel="stylesheet" href="/drop/css/style1.css">			
		<script>
	
	var dropZone = document.getElementById("drop-zone");
	var msgConteiner = document.querySelector("#drop-zone .text");
	
	var eventClear = function (e) {
		e.stopPropagation();
		e.preventDefault();
	}
	
	dropZone.addEventListener("dragenter", eventClear, false);
	dropZone.addEventListener("dragover", eventClear, false);
	
	dropZone.addEventListener("drop", function (e) {
			if(!e.dataTransfer.files) return;
			e.stopPropagation();
			e.preventDefault();

			sendFile(e.dataTransfer.files[0]);
		}, false);
	
	document.getElementById("file").addEventListener("change", function (e) {
			sendFile(e.target.files[0]);
		}, false);
	
	
	var statChange = function (e) {
		if (e.target.readyState == 4) {
			if (e.target.status == 200) {
				msgConteiner.innerHTML = "Загрузка успешно завершена!";
				dropZone.classList.remove("error");
				dropZone.classList.add("success");
				
				
			} else {
				msgConteiner.innerHTML = "Произошла ошибка!";
				dropZone.classList.remove("success");
				dropZone.classList.add("error");
			}
		}
	}
	
	var showProgress = function(e) {
		if (e.lengthComputable) {
			var percent = Math.floor((e.loaded / e.total) * 100);
			msgConteiner.innerHTML = "Загрузка... ("+ percent +"%)";
		}
	};
	
	var sendFile = function(file) {
		dropZone.classList.remove("success");
		dropZone.classList.remove("error");
		
		var re = /(\.jpg|\.jpeg|\.bmp|\.gif|\.png|\.zip|\.rar|\.docx)$/i;
		if (!re.exec(file.name)) {
			msgConteiner.innerHTML = "Недопустимый формат файла!";
			dropZone.classList.remove("success");
			dropZone.classList.add("error");
		}
		else {
			var fd = new FormData();
			fd.append("upfile", file);
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "./uploadM.php", true);
			
			xhr.upload.onprogress = showProgress;
			xhr.onreadystatechange = statChange;
			
			xhr.send(fd);
		}
	}
	
</script>

	</div>

	<div class="user_message_details column" id="conversations">
			<h4>Диалоги</h4>

			<div class="loaded_conversations">
				<?php echo $message_obj->getConvos(); ?>
			</div>
			<br>
			<a href="messages.php?u=new">Новое сообщение</a>

		</div>	