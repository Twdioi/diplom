<?php 
include("includes/header.php");
// include("group.php");

if(isset($_GET['group_name'])) {
		$profile_id = $_GET['group_name'];
	}
 //$profile_id = $_GET['group_name'];
// $profile_id = $groupName;
$imgSrc = "";
$result_path = "";
$msg = "";

/***********************************************************
	0 - Remove The Temp image if it exists
***********************************************************/
	if (!isset($_POST['x']) && !isset($_FILES['image']['name']) ){
		//Delete users temp image
			$temppath = 'assets/images/group_pics/'.$profile_id.'_AAAAAAAAAAAAA.jpeg';
			if (file_exists ($temppath)){ @unlink($temppath); }
	} 


if(isset($_FILES['image']['name'])){	
/***********************************************************
	1 - Upload Original Image To Server
***********************************************************/	
	//Get Name | Size | Temp Location		    
		$ImageName = $_FILES['image']['name'];
		$ImageSize = $_FILES['image']['size'];
		$ImageTempName = $_FILES['image']['tmp_name'];
	//Get File Ext   
		$ImageType = @explode('/', $_FILES['image']['type']);		
		$type = $ImageType[1]; //file type	
	//Set Upload directory    
		// $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/Demo/assets/images/profile_pics';
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/IUSocial-master/assets/images/group_pics';
	//Set File name	
		$file_temp_name = $profile_id.'_original.'.md5(time()).'n'.$type; //the temp file name
		$fullpath = $uploaddir."/".$file_temp_name; // the temp file path
		$file_name = $profile_id.'n.jpeg'; //$profile_id.'_temp.'.$type; // for the final resized image ВЕРНЯК
		
		//$file_name = $profile_id.''.md5(time()).'n.jpeg'; 
		
		
		$fullpath_2 = $uploaddir."/".$file_name; //for the final resized image
	//Move the file to correct location
		$move = move_uploaded_file($ImageTempName ,$fullpath) ; 
		chmod($fullpath, 0777);  
		//Check for valid uplaod
		if (!$move) { 
			die ('Файл не обновлен');
		} else { 
			$imgSrc= "assets/images/group_pics/".$file_name; // the image to display in crop area
			$msg= "Фото обновено!";  	//message to page
			$src = $file_name;	 		//the file name to post from cropping form to the resize		
		} 


/***********************************************************
	2  - Resize The Image To Fit In Cropping Area
***********************************************************/		
		//get the uploaded image size	
			clearstatcache();				
			$original_size = getimagesize($fullpath);
			$original_width = $original_size[0];
			$original_height = $original_size[1];	
		// Specify The new size
			$main_width = 500; // set the width of the image
			$main_height = $original_height / ($original_width / $main_width);	// this sets the height in ratio									
		//create new image using correct php func			
			if($_FILES["image"]["type"] == "image/gif"){
				$src2 = imagecreatefromgif($fullpath);
			}elseif($_FILES["image"]["type"] == "image/jpeg" || $_FILES["image"]["type"] == "image/pjpeg"){
				$src2 = imagecreatefromjpeg($fullpath);
			}elseif($_FILES["image"]["type"] == "image/png"){ 
				$src2 = imagecreatefrompng($fullpath);
			}else{ 
				$msg .= "There was an error uploading the file. Please upload a .jpg, .gif or .png file. <br />";
			}
		//create the new resized image
			$main = imagecreatetruecolor($main_width,$main_height);
			imagecopyresampled($main,$src2,0, 0, 0, 0,$main_width,$main_height,$original_width,$original_height);
		//upload new version
			$main_temp = $fullpath_2;
			imagejpeg($main, $main_temp, 90);
			chmod($main_temp,0777);
		//free up memory
			imagedestroy($src2);
			imagedestroy($main);
			//imagedestroy($fullpath);
			@ unlink($fullpath); // delete the original upload					
									
}//ADD Image 	

/***********************************************************
	3- Cropping & Converting The Image To Jpg
***********************************************************/
if (isset($_POST['x'])){
	
	//the file type posted
		$type = $_POST['type'];	
	//the image src
		$src = 'assets/images/group_pics/'.$_POST['src'];	
		$finalname = $profile_id;	
	
	if($type == 'jpg' || $type == 'jpeg' || $type == 'JPG' || $type == 'JPEG'){	
	
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefromjpeg($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "assets/images/group_pics/".$finalname."n.jpeg", 90); 	
			 		
	}else if($type == 'png' || $type == 'PNG'){
		
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefrompng($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );		
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "assets/images/group_pics/".$finalname."n.jpeg", 90); 	
						
	}else if($type == 'gif' || $type == 'GIF'){
		
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefromgif($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );		
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "assets/images/group_pics/".$finalname."n.jpeg", 90); 	
		
	}
		//free up memory
			imagedestroy($img_r); // free up memory
			imagedestroy($dst_r); //free up memory
			@ unlink($src); // delete the original upload					
		
		//return cropped image to page	
		//$result_path ="assets/images/profile_pics/".$finalname."n.jpeg";

		//return cropped image to page	
		$result_path ="assets/images/group_pics/".$file_name;

		//Insert image into database
		$insert_pic_query = mysqli_query($con, "UPDATE groups SET group_pic='$result_path' WHERE group_name='$profile_id'");
		header("Location:group.php?group_name= ".$profile_id);
														
}// post x
?>
<div id="Overlay" style=" width:100%; height:100%; border:0px #990000 solid; position:absolute; top:0px; left:0px; z-index:2000; display:none;"></div>
<div class="main_column column">
 

	<div id="formExample">
	    <p><b> <?=$msg?> </b></p>
	    
	    <form action="upload_group_pic.php" method="post"  enctype="multipart/form-data">
	        Обновление фото<br /><br />
	        <input type="file" id="imgInp" name="image" style="width:500px; height:30px; " /><br /><br />
			  <img id="blah" src = "assets\images\icons\95-958330_png-file-download-arrow-icon-clipart.png" style="width:200px; height:200px;" />
	        <input type="submit" value="Обновить фото" style="width:160px; height:25px;" />
			
	    </form><a href="<?php echo "group.php?group_name=".$profile_id; ?>"> Нажмите здесь чтобы посомтреть изменения. </a><br /><br />
	    
	</div> <!-- Form-->  
	
 
 <?php if($result_path) {
	 ?>
     
     <img src="<?=$result_path?>" style="position:relative; margin:10px auto; width:150px; height:150px;" />
	
	 
 <?php } ?>
 
 
    <br /><br />

<script>
	 function readURL(input) {
	   if (input.files && input.files[0]) {
		 var reader = new FileReader();

		 reader.onload = function(e) {
		   $('#blah').attr('src', e.target.result);
		 }

		 reader.readAsDataURL(input.files[0]);
	   }
	 }

	 $("#imgInp").change(function() {
	   readURL(this);
	 });
</script>