
<h1>Transactions</h1>
<table>
	<tr>
	<td><b>Source</b></td>
	<td><b>Target</b></td>
	<td><b>Amount</b></td>
	<td><b>Approved</b></td>
	</tr>
	
<?php

while ($transaction=$allTransactions->fetch_assoc()) {

	echo "<tr><td>".htmlspecialchars($transaction["uname"], ENT_QUOTES, 'UTF-8')."</td><td>".htmlspecialchars($transaction["uname2"], ENT_QUOTES, 'UTF-8')."</td>";
	echo "<td>".$transaction["amount"]."</td><td><a href='approve.php?type=transaction&id=".$transaction["id"]."'>Approve</a></td></tr>";
}

?>
</table>


<h1>Users</h1>
<table>
<?php

while ($user=$allUsers->fetch_assoc()) {

	echo "<td>".htmlspecialchars($user["uname"], ENT_QUOTES, 'UTF-8')."</td><td><a href='approve.php?type=user&id=".$user["uid"]."'>Approve</a></td></tr>";
}

?>
</table>
