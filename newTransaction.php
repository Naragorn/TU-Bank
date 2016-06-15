<?php
require ("core.php");
checkPermissionUser ();
$conn = connectDB ();

// exec ( "./c-fileparser/a.out c-fileparser/transaction.csv", $output );
// var_dump ( $output );

/*
	Function to perform a single transaction. Returns empty string if success and error message otherwise.
	The tans must be validated BEFORE!!
*/
function transact( $targetName, $amount, $tan, $conn) {
	
	$balance = 0;
	$meQuery = $conn->query ( "SELECT balance FROM users WHERE uid=" .$_SESSION["uid"]);
	while ( $row = $meQuery->fetch_assoc () ) { $balance = $row ["balance"]; }
		
	$allUsersQuery = $conn->query ( sprintf( "SELECT * FROM users WHERE uname='" .$targetName."'" ) );	
	$amountValid = is_numeric ( $amount);
	
	$recipientId = "0";
	$userValid = false;
	$errStr = "";
	while ( $row = $allUsersQuery->fetch_assoc () ) {
		if ($row ["uname"] === $targetName) {
			$userValid = true;
			$recipientId = $row ["uid"];


			break;
		}
	}
	
	if (doubleval($amount) < 0)
		$errStr = "You can not sent negative amounts of money!";
	else if (doubleval($amount) > $balance)
		$errStr = "You dont have that much money!";
	else if ($amountValid && $userValid ) {
		$approved = "1";
		if (doubleval ( $amount ) > 10000.0)
			$approved = "0";
		
		$select = $conn->query ( sprintf ( "INSERT INTO transactions (sourceId, targetId, tan, date, amount,approved) VALUES (%s,%s,'%s',CURDATE(),%s," . $approved . ")", intval ( $_SESSION ["uid"] ), intval ( $recipientId ), mysql_real_escape_string ( $tan ), doubleval ( $amount ) ) );
		
		if ($amount<10000) 
		{

			$update1 = $conn->query ( sprintf ( "UPDATE users SET balance=balance+" . doubleval ( $amount) . " WHERE uid=%s", intval ( $recipientId ) ) );		
			$update2 = $conn->query ( sprintf ( "UPDATE users SET balance=balance-" . doubleval ($amount ) . " WHERE uid=%s", intval ( $_SESSION ["uid"] ) ) );
		}


		$delete = $conn->query ( sprintf ( "DELETE FROM tans WHERE uid=%s AND value='%s'", intval ( $_SESSION ["uid"] ), mysql_real_escape_string ( $tan) ) );
		

	} else {
		$errStr = "Transaction was not valid!";
	}
	
	return $errStr;

}


if (isset ( $_POST ["submit"] )) {
	
	if (strlen($_POST ["tan"])>20) {
		$encrypted = $_POST ["tan"];

		$parts = explode(";", $encrypted); // Extract the initial vector
		
		$resultPin = $conn->query ( sprintf ( "SELECT pin FROM users WHERE uid = '%s'", mysql_real_escape_string ( $_SESSION ["uid"] ) ) );
		while ($pinRow=$resultPin->fetch_assoc()) {
			$pin =  $pinRow["pin"];
		}

		$parts = explode(';', $encrypted); // info, hash

		 $checkhash = base64_encode(
		   hash('sha256', $parts[0].$pin, true )
		 );
	
		$checkhash = trim(preg_replace('/\s\s+/', ' ',  $checkhash));
		$parts[0] = trim(preg_replace('/\s\s+/', ' ',  $parts[0]));

		if ($checkhash != $parts[1])
			$errorOutput = "Checkhash is invalid, probably because of wrong pin!";
		else {	
			$parts2 = explode(',', $parts[0]); // random, receiver1, amount1, receiver2, amount2

		
			// The random value is used to prevent a tan being used a second time
			$randomValue = $parts2[0];

			$select = $conn->query ( sprintf ( "SELECT uid, random FROM scstans WHERE uid=" . intval ( $_SESSION ["uid"] ) . " AND random='%s'", mysql_real_escape_string ( $randomValue ) ) );
			if (mysqli_num_rows ( $select ) > 0) {
				$errorOutput = "TAN was already used!!!";
			}
			else {
				$conn->query ( sprintf ( "INSERT INTO scstans SET uid=" . intval ( $_SESSION ["uid"] ) . ", random='%s'", mysql_real_escape_string ( $randomValue )));
				$successOutput = "Transaction OK!";
				

				for ($i = 1; $i<Count($parts2); $i=$i+2) {

					transact( $parts2[$i],$parts2[$i+1], $encrypted, $conn) ;
				}
			}
		}
	}
	else {
		$tanValid = false;
		$select = $conn->query ( sprintf ( "SELECT uid, value FROM tans WHERE uid=" . intval ( $_SESSION ["uid"] ) . " AND value='%s'", $_POST ["tan"]  ) );

		if (mysqli_num_rows ( $select ) == 1)
			$tanValid = true;
		if ($tanValid) {
			$error = transact( $_POST ["recipient"], $_POST ["amount"],  $_POST ["tan"], $conn);
			if ($error=="")
				$successOutput = "Transaction OK!";
			else 
			     $errorOutput = $error;
		} else {
			$errorOutput = "TAN was not valid!";
		}
	}
}

$BANK_TITLE = "New Transaction";
include ("layout/header.php");
include ("layout/newTransaction.php");
include ("layout/errorSuccessBox.php");
include ("layout/footer.php");
?>

