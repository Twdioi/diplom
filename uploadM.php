<?php

$uploaddir = getcwd() . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR;
$fileName = basename($_FILES["upfile"]["name"]);
$uploadfile = $uploaddir . $fileName;

move_uploaded_file($_FILES["upfile"]["tmp_name"], $uploadfile);

echo "<img src=\"./files/" . $fileName . "\">";