<?php include 'db_connect.php' ?>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<large class="card-title">
					<b>Borrower List</b>
				</large>
				<button class="btn btn-primary btn-block col-md-2 float-right" type="button" id="new_borrower"><i class="fa fa-plus"></i> New Borrower</button>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="borrower-list">
					<colgroup>
						<col width="10%">
						<col width="35%">
						<col width="30%">
						<col width="15%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Borrower</th>
							<th class="text-center">Current Loan</th>
							<th class="text-center">Next Payment Schedule</th>
							<th class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
							$qry = $conn->query("SELECT * FROM borrowers order by id desc");
							while($row = $qry->fetch_assoc()):

						 ?>
						 <tr>
						 	
						 	<td class="text-center"><?php echo $i++ ?></td>
						 	<td>
						 		<p>Name :<b><?php echo ucwords($row['lastname'].", ".$row['firstname'].' '.$row['middlename']) ?></b></p>
						 		<p><small>Address :<b><?php echo $row['address'] ?></small></b></p>
						 		<p><small>Contact # :<b><?php echo $row['contact_no'] ?></small></b></p>
						 		<p><small>Email :<b><?php echo $row['email'] ?></small></b></p>
						 		<p><small>Tax ID :<b><?php echo $row['tax_id'] ?></small></b></p>
						 		
						 	</td>
						 	<td class="">None</td>
						 	<td class="">N/A</td>
						 	<td class="text-center">
						 			<button class="btn btn-outline-primary btn-sm edit_borrower" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-edit"></i></button>
						 			<button class="btn btn-outline-danger btn-sm delete_borrower" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></button>
						 	</td>

						 </tr>

						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<style>
	td p {
		margin:unset;
	}
	td img {
	    width: 8vw;
	    height: 12vh;
	}
	td{
		vertical-align: middle !important;
	}
</style>	
<script>
	$('#borrower-list').dataTable()
	$('#new_borrower').click(function(){
		uni_modal("New borrower","manage_borrower.php",'mid-large')
	})
	$('.edit_borrower').click(function(){
		uni_modal("Edit borrower","manage_borrower.php?id="+$(this).attr('data-id'),'mid-large')
	})
	$('.delete_borrower').click(function(){
		_conf("Are you sure to delete this borrower?","delete_borrower",[$(this).attr('data-id')])
	})
function delete_borrower($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_borrower',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("borrower successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>