<script>
function FunctionDelete(id) {
    var r = confirm("Вы уверены что хотите разблокировать пользователя ?");
    if (r == true) {
        $.get("./Ajax/bannedactions.php?act=ban&id=" + id, function(data, status){
			document.location='./page.php?page=banned';
		});
    }
}
</script>

<div class="panel panel-warning">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-list"></span> Заблокированные пользователи
    </div>
    <div class="panel-body">
		<div class="table-responsive">
			<?php include './pages/loginpagging.php'; ?>		
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
					$SQLshow = mysqli_query($con,"SELECT * FROM users WHERE block = 'yes' ORDER BY id ASC limit $offset, $dataperPage");
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
								<a href="#" OnClick="FunctionDelete(<?php echo $row[id]; ?>)">
									<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-trash"></span></button>
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
				$query = mysqli_query($con,"SELECT COUNT(*) jumData from banned");
				$data = mysqli_fetch_array($query);
				$jumlahData = $data["jumData"];
				?>
                <h5>Общее количество <span class="label label-info"><?php echo $jumlahData; ?></span></h5>
            </div>
			<div class="col-md-6">
                <ul class="pagination pagination-sm pull-right">
					<?php include './pages/banviewpage.php';?>                    
                </ul>
            </div>
        </div>
    </div>
</div>
