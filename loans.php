<?php include 'db_connect.php' ?>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<large class="card-title">
					<b>Loan List</b>
					<button class="btn btn-primary btn-sm btn-block col-md-2 float-right" type="button" id="new_application"><i class="fa fa-plus"></i> Create New Application</button>
				</large>
				
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="loan-list">
					<colgroup>
						<col width="10%">
						<col width="25%">
						<col width="25%">
						<col width="20%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th class="text-center">#</th>
							<th class="text-center">Borrower</th>
							<th class="text-center">Loan Details</th>
							<th class="text-center">Next Payment Details</th>
							<th class="text-center">Status</th>
							<th class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							
							$i=1;
							$type = $conn->query("SELECT * FROM loan_types where id in (SELECT loan_type_id from loan_list) ");
							while($row=$type->fetch_assoc()){
								$type_arr[$row['id']] = $row['type_name'];
							}
							$plan = $conn->query("SELECT *,concat(months,' month/s [ ',interest_percentage,'%, ',penalty_rate,' ]') as plan FROM loan_plan where id in (SELECT plan_id from loan_list) ");
							while($row=$plan->fetch_assoc()){
								$plan_arr[$row['id']] = $row;
							}
							$qry = $conn->query("SELECT l.*,concat(b.lastname,', ',b.firstname,' ',b.middlename)as name, b.contact_no, b.address from loan_list l inner join borrowers b on b.id = l.borrower_id  order by id asc");
							while($row = $qry->fetch_assoc()):
								$monthly = ($row['amount'] + ($row['amount'] * ($plan_arr[$row['plan_id']]['interest_percentage']/100))) / $plan_arr[$row['plan_id']]['months'];
								$penalty = $monthly * ($plan_arr[$row['plan_id']]['penalty_rate']/100);
								$payments = $conn->query("SELECT * from payments where loan_id =".$row['id']);
								$paid = $payments->num_rows;
								$offset = $paid > 0 ? " offset $paid ": "";
								if($row['status'] == 2):
									$next = $conn->query("SELECT * FROM loan_schedules where loan_id = '".$row['id']."'  order by date(date_due) asc limit 1 $offset ")->fetch_assoc()['date_due'];
								endif;
								$sum_paid = 0;
								while($p = $payments->fetch_assoc()){
									$sum_paid += ($p['amount'] - $p['penalty_amount']);
								}

						 ?>
						 <tr>
						 	
						 	<td class="text-center"><?php echo $i++ ?></td>
						 	<td>
						 		<p>Name :<b><?php echo $row['name'] ?></b></p>
						 		<p><small>Contact # :<b><?php echo $row['contact_no'] ?></small></b></p>
						 		<p><small>Address :<b><?php echo $row['address'] ?></small></b></p>
						 	</td>
						 	<td>
						 		<p>Reference :<b><?php echo $row['ref_no'] ?></b></p>
						 		<p><small>Loan type :<b><?php echo $type_arr[$row['loan_type_id']] ?></small></b></p>
						 		<p><small>Plan :<b><?php echo $plan_arr[$row['plan_id']]['plan'] ?></small></b></p>
						 		<p><small>Amount :<b><?php echo $row['amount'] ?></small></b></p>
						 		<p><small>Total Payable Amount :<b><?php echo number_format($monthly * $plan_arr[$row['plan_id']]['months'],2) ?></small></b></p>
						 		<p><small>Monthly Payable Amount: <b><?php echo number_format($monthly,2) ?></small></b></p>
						 		<p><small>Overdue Payable Amount: <b><?php echo number_format($penalty,2) ?></small></b></p>
						 		<?php if($row['status'] == 2 || $row['status'] == 3): ?>
						 		<p><small>Date Released: <b><?php echo date("M d, Y",strtotime($row['date_released'])) ?></small></b></p>
						 		<?php endif; ?>
						 	</td>
						 	<td>
						 		<?php if($row['status'] == 2 ): ?>
						 		<p>Date: <b>
						 		<?php echo date('M d, Y',strtotime($next)); ?>
						 		</b></p>
						 		<p><small>Monthly amount:<b><?php echo number_format($monthly,2) ?></b></small></p>
						 		<p><small>Penalty :<b><?php echo $add = (date('Ymd',strtotime($next)) < date("Ymd") ) ?  $penalty : 0; ?></b></small></p>
						 		<p><small>Payable Amount :<b><?php echo number_format($monthly + $add,2) ?></b></small></p>
						 		<?php else: ?>
					 				N/a
						 		<?php endif; ?>
						 	</td>
						 	<td class="text-center">
						 		<?php if($row['status'] == 0): ?>
						 			<span class="badge badge-warning">For Approval</span>
						 		<?php elseif($row['status'] == 1): ?>
						 			<span class="badge badge-info">Approved</span>
					 			<?php elseif($row['status'] == 2): ?>
						 			<span class="badge badge-primary">Released</span>
					 			<?php elseif($row['status'] == 3): ?>
						 			<span class="badge badge-success">Completed</span>
					 			<?php elseif($row['status'] == 4): ?>
						 			<span class="badge badge-danger">Denied</span>
						 		<?php endif; ?>
						 	</td>
						 	<td class="text-center">
						 			<button class="btn btn-outline-primary btn-sm edit_loan" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-edit"></i></button>
						 			<button class="btn btn-outline-danger btn-sm delete_loan" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></button>
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
	$('#loan-list').dataTable()
	$('#new_application').click(function(){
		uni_modal("New Loan Application","manage_loan.php",'mid-large')
	})
	$('.edit_loan').click(function(){
		uni_modal("Edit Loan","manage_loan.php?id="+$(this).attr('data-id'),'mid-large')
	})
	$('.delete_loan').click(function(){
		_conf("Are you sure to delete this data?","delete_loan",[$(this).attr('data-id')])
	})
function delete_loan($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_loan',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Loan successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>