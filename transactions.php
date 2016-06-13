<?php
require ("core.php");
checkPermissionUser ();

if (isset ( $_GET ["uid"] ))
	$userId = $_GET ['uid'];
else
	$userId = $_SESSION ['uid'];

$conn = connectDB ();
$q2 = "SELECT t.sourceId as sourceId, t.approved as approved, t.amount as amount,
t.targetId as targetId, u.uname as uname, u2.uname as uname2, t.date as createDate FROM transactions t ,
users u, users u2 WHERE (t.sourceId=" . intval ( $userId ) . "
OR t.targetId=" . intval ( $userId ) . ") AND
t.sourceId=u.uid
AND t.targetId=u2.uid";

$allTransactions = $conn->query ( $q2 );

if (isset ( $_GET ["action"] ) && $_GET ["action"] == "download") {
	$p = PDF_new (); // Note: PDF Generation code taken from PHP documentation: http://php.net/manual/de/pdf.examples-basic.php
	
	if (PDF_begin_document ( $p, "", "" ) == 0) {
		die ( "Error: " . PDF_get_errmsg ( $p ) );
	}
	
	PDF_set_info ( $p, "Creator", "Group 2" );
	PDF_set_info ( $p, "Author", "Group 2" );
	PDF_set_info ( $p, "Title", "Transactions" );
	PDF_begin_page_ext ( $p, 595, 842, "" );
	$font = PDF_load_font ( $p, "Helvetica-Bold", "winansi", "" );
	
	PDF_setfont ( $p, $font, 24.0 );
	PDF_set_text_pos ( $p, 50, 700 );
	PDF_show ( $p, "Transactions: " );
	
	PDF_setfont ( $p, $font, 12.0 );
	PDF_continue_text ( $p, "-------------------------------" );
	while ( $transaction = $allTransactions->fetch_assoc () ) {
		PDF_continue_text ( $p, $transaction ["uname"] . " to " . $transaction ["uname2"] . " with amount:" . $transaction ["amount"] . " \n" );
	}
	
	PDF_end_page_ext ( $p, "" );
	PDF_end_document ( $p, "" );
	
	$buf = PDF_get_buffer ( $p );
	$len = strlen ( $buf );
	
	header ( "Content-type: application/text" );
	header ( "Content-Length: $len" );
	header ( "Content-Disposition: inline; filename=transactions.pdf" );
	print $buf;
	PDF_delete ( $p );
	die ();
}

echo $conn->error;
$BANK_TITLE = "My Transactions";

include ("layout/header.php");
include ("layout/transactions.php");
include ("layout/footer.php");
?>

