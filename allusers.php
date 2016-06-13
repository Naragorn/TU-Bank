<?php
require ("core.php");
checkPermissionBanker();
$BANK_TITLE = $_SESSION ["uname"];

$conn = connectDB ();
$q2 =  "SELECT * FROM users";
$allUsers = $conn->query ( $q2 );

include ("layout/header.php");
include ("layout/allusers.php");
include ("layout/footer.php");
?>
