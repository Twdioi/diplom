<?php
error_reporting(E_ALL ^ E_NOTICE);
@include "../config/config.inc.php";
@include "../session_member.php";

$id = $_GET['id'];

$data	= "SELECT * FROM users WHERE id='$id'";
$hasil	= mysqli_query($con,$data);
$row	= mysqli_fetch_array($hasil);
?>
<form class="form-horizontal" action="./Ajax/useractions.php?act=update&id=<?php echo $id; ?>" method="post">
    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Edit User</h3>
		</div>
	    <fieldset>
		<!-- Form Name -->
		<!-- Prepended text-->							
			<div class="form-group">
				<label class="col-md-2 control-label" for="first_name">Имя</label>
				<div class="col-md-4">
					<div class="input-group">
						<input id="first_name" name="first_name" class="form-control" placeholder="first_name" type="text" value="<?php echo $row['first_name'];?>" >
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="last_name">Фамилия</label>
				<div class="col-md-4">
					<div class="input-group">
						<input id="last_name" name="last_name" class="form-control" placeholder="last_name" type="text" value="<?php echo $row['last_name'];?>" >
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="title">Должность</label>
				<div class="col-md-4">
					<div class="input-group">
						<input id="title" name="title" class="form-control" placeholder="title" type="text" value="<?php echo $row['title'];?>" >
					</div>
				</div>
			</div>
		</fieldset>
        <div class="panel-footer">
			<button class="btn btn-flat btn-success" name="edit" type="submit">Save</button>
		</div>
	</div>
</form>