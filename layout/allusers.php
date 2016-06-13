<h1>All Users</h1>
<?php
while ($user=$allUsers->fetch_assoc()) {
	echo "".$user["uname"]."<a href='transactions.php?uid=".$user["uid"]."'> [Transactions]</a> <a href='newTransaction.php?recipient=".$user["uname"]."'>[Send money]</a> <a href='editBalance.php?recipient=".$user["uname"]."&amount=".$user["balance"]."'>[Edit Balance]</a><br>";

}
?>
