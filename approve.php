<?php
require ("core.php");
checkPermissionBanker();
$conn = connectDB ();

if (isset ( $_GET ["type"] )) {
	if ($_GET ["type"] == "transaction") {
		// Step 1: Get transaction and check if > 10000
		$qTransaction = $conn->query ("SELECT sourceId, targetId, amount FROM transactions WHERE approved=0 AND id=".intval($_GET ["id"] ));
 
		while ($transaction=$qTransaction->fetch_assoc()) {
			if ($transaction["amount"] > 10000) {
				echo "The money is now transfered as this as a transaction > 10000.";
				// if >10000 also change balance!
				$update1 = $conn->query ( sprintf ( "UPDATE users SET balance=balance+" . doubleval ( $transaction["amount"]) . " WHERE uid=%s", intval ( $transaction["targetId"] ) ) );		
				$update2 = $conn->query ( sprintf ( "UPDATE users SET balance=balance-" . doubleval ($transaction["amount"] ) . " WHERE uid=%s", intval ( $transaction ["sourceId"] ) ) );
			}
			// Approve transaction
			$conn->query ( "UPDATE transactions SET approved=1 WHERE id=" . intval($_GET ["id"] ));
		}
	}
	if ($_GET ["type"] == "user") {
		$conn->query ( "UPDATE users SET approved=1 WHERE uid=" . intval($_GET ["id"] ));
	}
}

if (isset ( $_GET ["uid"] ))
	$appendix = "(t.sourceId=" . intval ( $_GET ["uid"] ) . "
OR t.targetId=" . intval ( $_GET ["uid"] ) . ") AND ";
else
	$appendix = "";

$q2 = "SELECT t.id as id, t.sourceId as sourceId, t.approved as approved, t.amount as amount,
t.targetId as targetId, u.uname as uname, u2.uname as uname2 FROM transactions t ,
users u, users u2 WHERE  " . $appendix . "
t.sourceId=u.uid
AND t.targetId=u2.uid AND t.approved=0";

$allTransactions = $conn->query ( $q2 );
$allUsers = $conn->query ( "SELECT uname, uid FROM users where approved != 1" );

echo $conn->error;
$BANK_TITLE = "Approve accounts and transactions";

include ("layout/header.php");
include ("layout/approve.php");
include ("layout/footer.php");
?>

