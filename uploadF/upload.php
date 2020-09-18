<?php

//$uploaddir = getcwd().DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR;
/*$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/IUSocial-master/files';
$uploadfile = $uploaddir.basename($_FILES['file']['name']);

move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);*/


// A list of permitted file extensions
$allowed = array('png', 'jpg', 'gif','docx','zip');

if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){

	$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

	if(!in_array(strtolower($extension), $allowed)){
		echo '{"status":"error"}';
		exit;
	}

	if(move_uploaded_file($_FILES['upl']['tmp_name'], 'uploads/'.$_FILES['upl']['name'])){
		echo '{"status":"success"}';
		exit;
	}
}

echo '{"status":"error"}';
exit;