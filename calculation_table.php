<?php 
extract($_POST);

$monthly = ($amount + ($amount * ($interest/100))) / $months;
$penalty = $monthly * ($penalty/100);

?>
<hr>
<table width="100%">
	<tr>
		<th class="text-center" width="33.33%">Total Payable Amount</th>
		<th class="text-center" width="33.33%">Monthly Payable Amount</th>
		<th class="text-center" width="33.33%">Penalty Amount</th>
	</tr>
	<tr>
		<td class="text-center"><small><?php echo number_format($monthly * $months,2) ?></small></td>
		<td class="text-center"><small><?php echo number_format($monthly,2) ?></small></td>
		<td class="text-center"><small><?php echo number_format($penalty,2) ?></small></td>
	</tr>
</table>
<hr>
