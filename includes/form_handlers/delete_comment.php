<?php 
require '../../config/config.php';

$act = $_GET['act'];
$id = $_GET['id'];

if ($act=='ban'){
	mysqli_query($con,"DELETE FROM comments  WHERE id='$id'");
	mysqli_close($con);
	echo "<script language='javascript'>alert('Data Deleted.');
	//document.location='../page.php?page=users';</script>";
}

	/*if(isset($_GET['comm_id']))
		$com_id = $_GET['comm_id'];

	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true')
			//$query = mysqli_query($con, "DELETE FROM comments WHERE id='$com_id'");
			$query = mysqli_query($con, "UPDATE comments SET removed='ssss' WHERE id='$com_id'");
	}*/

?>