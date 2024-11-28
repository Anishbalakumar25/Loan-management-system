<?php include 'db_connect.php' ?>
<?php 
extract($_POST);
if(isset($id) && !(empty($id))){
	$qry = $conn->query("SELECT * FROM payments where id=".$id);
	foreach($qry->fetch_array() as $k => $val){
		$$k = $val;
	}
}
$loan = $conn->query("SELECT l.*,concat(b.lastname,', ',b.firstname,' ',b.middlename)as name, b.contact_no, b.address from loan_list l inner join borrowers b on b.id = l.borrower_id where l.id = ".$loan_id);
foreach($loan->fetch_array() as $k => $v){
	$meta[$k] = $v;
}
$type_arr = $conn->query("SELECT * FROM loan_types where id = '".$meta['loan_type_id']."' ")->fetch_array();

$plan_arr = $conn->query("SELECT *,concat(months,' month/s [ ',interest_percentage,'%, ',penalty_rate,' ]') as plan FROM loan_plan where id  = '".$meta['plan_id']."' ")->fetch_array();
$monthly = ($meta['amount'] + ($meta['amount'] * ($plan_arr['interest_percentage']/100))) / $plan_arr['months'];
$penalty = $monthly * ($plan_arr['penalty_rate']/100);
$payments = $conn->query("SELECT * from payments where loan_id =".$loan_id);
$paid = $payments->num_rows;
$offset = $paid > 0 ? " offset $paid ": "";
	$next = $conn->query("SELECT * FROM loan_schedules where loan_id = '".$loan_id."'  order by date(date_due) asc limit 1 $offset ")->fetch_assoc()['date_due'];
$sum_paid = 0;
while($p = $payments->fetch_assoc()){
	$sum_paid += ($p['amount'] - $p['penalty_amount']);
}

?>
<div class="col-lg-12">
<hr>
<div class="row">
	<div class="col-md-5">
		<div class="form-group">
			<label for="">Payee</label>
			<input name="payee" class="form-control" required="" value="<?php echo isset($payee) ? $payee : (isset($meta['name']) ? $meta['name'] : '') ?>">
		</div>
	</div>
	
</div>
<hr>
<div class="row">
	<div class="col-md-5">
		<p><small>Monthly amount:<b><?php echo number_format($monthly,2) ?></b></small></p>
		<p><small>Penalty :<b><?php echo $add = (date('Ymd',strtotime($next)) < date("Ymd") ) ?  $penalty : 0; ?></b></small></p>
		<p><small>Payable Amount :<b><?php echo number_format($monthly + $add,2) ?></b></small></p>
	</div>
	<div class="col-md-5">
		<div class="form-group">
			<label for="">Amount</label>
			<input type="number" name="amount" step="any" min="" class="form-control text-right" required="" value="<?php echo isset($amount) ? $amount : '' ?>">
			<input type="hidden" name="penalty_amount" value="<?php echo $add ?>">
			<input type="hidden" name="loan_id" value="<?php echo $loan_id ?>">
			<input type="hidden" name="overdue" value="<?php echo $add > 0 ? 1 : 0 ?>">
		</div>
	</div>
</div>
</div>