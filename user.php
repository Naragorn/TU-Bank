<?php
require ("core.php");

$BANK_TITLE = $_SESSION ["uname"];

$conn = connectDB();
$userInfo = $conn->query ("SELECT balance FROM users WHERE uid=".$_SESSION["uid"]);
$userId = $conn->query ("SELECT uid FROM users WHERE uid=".$_SESSION["uid"]);
$userName = $conn->query ("SELECT uname FROM users WHERE uid=".$_SESSION["uid"]);

include ("layout/header.php");
include ("layout/user.php");
include ("layout/footer.php");
?>
