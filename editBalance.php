<?php
require ("core.php");
checkPermissionBanker ();
$conn = connectDB ();

// exec ( "./c-fileparser/a.out c-fileparser/transaction.csv", $output );
// var_dump ( $output );

if (isset ( $_POST ["submit"] )) {
	$allUsersQuery = $conn->query ( sprintf( "SELECT * FROM users WHERE uname='" . $_POST ["recipient"] . "'" ) );
	
	$amountValid = is_numeric ( $_POST ["amount"] );
	
	$recipientId = "0";
	$userValid = false;
	while ( $row = $allUsersQuery->fetch_assoc () ) {
		if ($row ["uname"] === $_POST ["recipient"]) {
			$userValid = 1;
			$recipientId = $row ["uid"];
			break;
		}
	}
	
	$tan = array ();
	$tanValid = 0;
	
	$select = $conn->query ( sprintf ( "SELECT uid, value FROM tans WHERE uid=" . intval ( $_SESSION ["uid"] ) . " AND value='%s'", mysql_real_escape_string ( $_POST ["tan"] ) ) );
	
	if (mysqli_num_rows ( $select ) == 1)
		$tanValid = 1;
		
	if (doubleval($_POST ["amount"]) < 0)
		$tanValid = 0;
	
	if ($amountValid && $userValid && $_POST ["amount"]>=0 && $_SESSION["isBanker"]) {	
				
		$update1 = $conn->query ( sprintf ( "UPDATE users SET balance=" . doubleval ( $_POST ["amount"] ) . " WHERE uid=%s", intval ( $recipientId ) ) );
				
		if (isset ( $conn->error )) {
			$errorOutput = $conn->error;
		}
		
		$successOutput = "Edit complete! :)";
		
		if (isset ( $conn->error )) {
			$errorOutput = $conn->error;
		}
	} else {
		$errorOutput = "Edit was not valid!";
	}
}

$BANK_TITLE = "Edit Balance";
include ("layout/header.php");
include ("layout/editBalance.php");
include ("layout/errorSuccessBox.php");
include ("layout/footer.php");
?>

