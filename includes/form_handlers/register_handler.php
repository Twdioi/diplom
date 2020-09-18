<?php

$fname = ""; //First name
$lname = ""; //Last name
$em = ""; //email
$em2 = ""; //email 2
$password = ""; //password
$password2 = ""; //password 2
$title = "";
$type ="";
$date = ""; //Sign up date 
$ver_code = "";
$ver_code_user = "";
$error_array = array(); //Holds error messages
header('Content-Type:text/html; charset=utf-8');

function ucfirst_utf8($str)
{
    return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr($str, 1, mb_strlen($str)-1, 'utf-8');
}

if(isset($_POST['reg_send_code_button']) || isset($_POST['register_button']) ){
	//First name
	$fname = strip_tags($_POST['reg_fname']); //Remove html tags
	$fname = str_replace(' ', '', $fname); //remove spaces
	$fname = ucfirst_utf8(mb_strtolower($fname)); //Uppercase first letter
	$_SESSION['reg_fname'] = $fname; //Stores first name into session variable

	//Last name
	$lname = strip_tags($_POST['reg_lname']); //Remove html tags
	$lname = str_replace(' ', '', $lname); //remove spaces
	$lname = ucfirst_utf8(mb_strtolower($lname)); //Uppercase first letter
	$_SESSION['reg_lname'] = $lname; //Stores last name into session variable

	//email
	$em = strip_tags($_POST['reg_email']); //Remove html tags
	$em = str_replace(' ', '', $em); //remove spaces
	//$em = ucfirst(strtolower($em)); //Uppercase first letter
	$em = strtolower($em); //Uppercase first letter
	$_SESSION['reg_email'] = $em; //Stores email into session variable

	//email 2
	$em2 = strip_tags($_POST['reg_email2']); //Remove html tags
	$em2 = str_replace(' ', '', $em2); //remove spaces
	$em2 = strtolower($em2); //Uppercase first letter
	$_SESSION['reg_email2'] = $em2; //Stores email2 into session variable

	//Password
	$password = strip_tags($_POST['reg_password']); //Remove html tags
	$_SESSION['reg_pw'] = $password;
	$password2 = strip_tags($_POST['reg_password2']); //Remove html tags
	$_SESSION['reg_pw2'] = $password2;

	//Title
	$title = strip_tags($_POST['reg_title']); //Remove html tags
	// $title = str_replace(' ', '', $title); //remove spaces
	// $title = ucwords(strtolower($title)); //Uppercase first letter
	$title = mb_strtoupper($title); //Uppercase first letter
	$_SESSION['reg_title'] = $title; //Stores last name into session variable


	// echo "Session Var Code: ". $_SESSION['ver_code'];
}


if(isset($_POST['reg_send_code_button'])){


	if($em == $em2) {
		
		if(filter_var($em, FILTER_VALIDATE_EMAIL)) {

			$em = filter_var($em, FILTER_VALIDATE_EMAIL);

			
			$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

			
			$num_rows = mysqli_num_rows($e_check);

			if($num_rows > 0) {
				array_push($error_array, "Email already in use<br>");
			}

		}
		else {
			array_push($error_array, "Invalid email format<br>");
		}

	}
	else{
		array_push($error_array, "Emails do not match<br>");
	}


	if(strlen($fname) > 50 || strlen($fname) < 2){
		array_push($error_array, "Your first name must be between 2 and 50 characters<br>");
	}

	if(strlen($lname) > 50 || strlen($lname) < 2){
		array_push($error_array, "Your last name must be between 2 and 50 characters<br>");
	}

	if($password != $password2) {
		array_push($error_array,  "Your passwords do not match<br>");
	}
	else {
		if(preg_match('/[^A-Za-z0-9]/', $password)) {
			array_push($error_array, "Your password can only contain letters or numbers<br>");
		}
	}

	if(strlen($password) > 30 || strlen($password) < 5){
		array_push($error_array, "Your password must be between 5 and 30 characters<br>");
	}

	if(strlen($title) > 20 || strlen($title) < 2){
		array_push($error_array, "Your title must be between 2 and 20 characters<br>");
	}

	if(empty($error_array)) {
	

		
		$username = mb_strtolower($fname . "_" . $lname);
		$_SESSION['username'] = $username;
		$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");


		$i = 0; 
	
		while(mysqli_num_rows($check_username_query) != 0) {
			$i++; //Add 1 to i
			$username = $username . "_" . $i;
			$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
		}


		$ver_code = 456;
		$_SESSION['ver_code'] = $ver_code;
		// echo "Session Var Code - Inside If: ". $_SESSION['ver_code'];
		// $ver_code = $_SESSION['ver_code'];
		//Sending mail
		$to = $em;
		$subject = "Добро пожаловать в социальную сеть. Пожалуйста, проверьте свой адрес электронной почты."; 
		
		$message = "
		<html> 
		Здравствуйте <strong>$fname</strong>,<br>
		Вы только что создали учетную запись в социальной сети <b><i>Connect!</i></b>.<br>
		Пожалуйста, введите приведенный ниже проверочный код для завершения регистрации:<br>
		$ver_code

		<br><br><i>Note:</i> <br>
		Код действителен только до тех пор, пока вы не останетесь на той же странице регистрации.<br>
		Ваша учетная запись не будет создана до тех пор, пока вы не введете проверочный код.<br><br>

		<strong>Спасибо Вам за создание учетной записи!</strong><br><>

		С уважением,<br>
		Команда Connect.<br>
		</html>";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <admin@gmail.com>' . "\r\n";

		//mail($to,$subject,$message,$headers);

		array_push($error_array, "<span style='color: #14C800;'>A verification code has been sent to the provided email. Please enter it below.</span><br>");
	}
}

if(isset($_POST['register_button'])){

		$ver_code = $_SESSION['ver_code'];
		$ver_code_user = strip_tags($_POST['register_codebox']); //Remove html tags
		// $_SESSION['ver_code_user'] = $ver_code_user; //Stores email into session variable
		if ($ver_code == $ver_code_user){
			// echo "Unhashed password" . $password;
			$password = md5($password); //Encrypt password before sending to database
			// echo "Hashed password" . $password;
			$username = $_SESSION['username'];
			$date = date("Y-m-d"); //Current date

			//Profile picture assignment
			$rand = rand(1, 2); //Random number between 1 and 2

			if($rand == 1)
				$profile_pic = "assets/images/profile_pics/defaults/head_pomegranate.png";
			else if ($rand == 2)
				$profile_pic = "assets/images/profile_pics/defaults/head_belize_hole.png";

			$query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',', '$title', '$type','','','no')");
			if ($type=='Студент'){
				$add_friend_query = mysqli_query($con, "UPDATE groups SET users_array=CONCAT(users_array, '$username,'), num_users = num_users+1  WHERE group_name='IU_Global_Forum'");
			}

			array_push($error_array, "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>");
			//Clear session variables 
			$_SESSION['reg_fname'] = "";
			$_SESSION['reg_lname'] = "";
			$_SESSION['reg_email'] = "";
			$_SESSION['reg_email2'] = "";
			$_SESSION['reg_type'] = "";
			$_SESSION['reg_title'] = "";
			$_SESSION['reg_pw'] = "";
			$_SESSION['reg_pw2'] = "";
			$_SESSION['ver_code'] = "";
			$_SESSION['username']= "";
			//$_SESSION['ver_code_user'] ="";	
		}
		else{
			array_push($error_array, "<span style='color: #ff0000;'>Verification code did not match, please try again.</span><br>");		
		}
		

		
	// }

}
?>