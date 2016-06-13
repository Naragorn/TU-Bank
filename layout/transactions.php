<a href="?action=download">Download PDF</a>

<hr>

<table border="1">
	<tr>
	<td><b>Source</b></td>
	<td><b>Source Account</b></td>
	<td><b>Target</b></td>
	<td><b>Target Account</b></td>
	<td><b>Amount</b></td>
	<td><b>Approved</b></td>
	<td><b>Date</b></td>
	</tr>
<?php

while ($transaction=$allTransactions->fetch_assoc()) {
	echo "<tr><td>".$transaction["uname"]."</td><td>".$transaction["sourceId"]."</td><td>".$transaction["uname2"]."<td>".$transaction["targetId"]."</td></td>";
	echo "<td>".$transaction["amount"]."</td><td>".$transaction["approved"]."</td><td>".$transaction["createDate"]."</td></tr>";
}

?>
</table>
