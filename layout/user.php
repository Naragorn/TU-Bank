<form action="newTransaction.php">
	<input type="submit" value="Make new transaction" />
</form>
<br>
Your Account Number is: 
<?php
while ($userAccount=$userId->fetch_assoc()) {
	echo $userAccount["uid"];

}

?>
<br>
Your Account Name is: 
<?php
while ($userNamer=$userName->fetch_assoc()) {
	echo $userNamer["uname"];

}

?>
<br>
Your current balance is: 
<?php
while ($user=$userInfo->fetch_assoc()) {
	echo "EUR ".round($user["balance"], 2)."";

}

?>

