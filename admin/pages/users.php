<script>
function FunctionDelete(id) {
    var r = confirm("Are You Sure?");
    if (r == true) {
        $.get("./Ajax/useractions.php?act=del&id=" + id, function(data, status){
			// alert("Data: " + data + "\nStatus: " + status);
			document.location='./page.php?page=users';
		});
    }
}
</script>

<script>
function FunctionBlock(id) {
    var r = confirm("Вы уверены что хотите заблокаровать пользователя?");
    if (r == true) {
        $.get("./Ajax/useractions.php?act=ban&id=" + id, function(data, status){
			// alert("Data: " + data + "\nStatus: " + status);
			document.location='./page.php?page=users';
		});
    }
}
</script>

<div class="row">
	<div class="pull-right" style="padding-bottom: 20px">
		<a data-toggle="modal" data-target="#usuario" href="#" class="btn btn-primary">Добавить пользователя <i class="fa fa-plus"></i></a>
	</div>
</div>
<div class="panel panel-warning">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-list"></span> Пользователи
    </div>
    <div class="panel-body">
		<div class="table-responsive">
			<?php include './pages/userspagging.php'; ?>		
            <table class="table table-bordered table-hover">
				<thead>
					<tr class="bg-primary">
						<th>ID</th>						
						<th>имя</th>
						<th>фамилия</th>
						<th>должность</th>
						<th>изображение</th>
						<th>Action</th>						
					</tr>
				</thead>
				<tbody>
					<?php								
					$SQLshow = mysqli_query($con,"SELECT * FROM users ORDER BY id ASC limit $offset, $dataperPage");
					$noUrut = 1;
					while($row = mysqli_fetch_array($SQLshow)){
					?>
					<tr>            
						<td><?php echo $row[id]; ?></td>						
						<td><?php echo $row[first_name]; ?></td>
						<td><?php echo $row[last_name]; ?></td>
						<td><?php echo $row[title]; ?></td>
						<td><img class="user_profile_details_img" align="middle" src="/connect/<?php echo $row[profile_pic]; ?>" style="width:80px; height:80px;"?></td>

						<td>
							<center>
								<a href="./page.php?page=edituser&id=<?php echo $row[id]; ?>">
									<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil"></span></button>
								</a>
								<a href="#" OnClick="FunctionDelete(<?php echo $row[id]; ?>)">
									<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-trash"></span></button>
								</a>
								<a href="#" OnClick="FunctionBlock(<?php echo $row[id]; ?>)">
									<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-off"></span></button>
								</a>
							</center>
						</td>
					</tr>
					<?php 
					$noUrut++;
					}
					?>
				</tbody>
			</table>
		</div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-md-6">
				<?php
				$query = mysqli_query($con,"SELECT COUNT(*) jumData from users");
				$data = mysqli_fetch_array($query);
				$jumlahData = $data["jumData"];
				?>
                <h5>Общее количество <span class="label label-info"><?php echo $jumlahData; ?></span></h5>
            </div>
			<div class="col-md-6">
                <ul class="pagination pagination-sm pull-right">
					<?php include './pages/usersviewpage.php';?>                    
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="fade modal" id="usuario">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h2 class="modal-title" id="myModalLabel">Добавить пользователя</h2>						
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" id="myForm" name="myForm" onsubmit="return validateForm()" enctype="multipart/form-data" action="./Ajax/useractions.php?act=add">
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
						<!-- File Button -->
						<div class="form-group col-lg-3 col-offset-6 pull-right">
							<button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-save"></i>Сохранить</button>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>