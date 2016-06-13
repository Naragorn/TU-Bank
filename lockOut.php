<?php
$BANK_TITLE = "Locked out!";

include_once ("config.php");
include_once ("database.php");
include_once ("session.php");
include_once ("functions.php");
include_once ("layout/header.php");

$errorOutput = "You have failed to log in too many times!<br>You can try logging in again but be warned that your IP has been stored and repercussions can happen!";

include ("layout/errorSuccessBox.php");
include_once ("layout/footer.php");
?>

