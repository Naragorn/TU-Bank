<?php
require ("session.php");
require ("config.php");
require ("database.php");
require ("functions.php");

if (isset($SESSION_PATH))
	session_set_cookie_params(3600, $SESSION_PATH);
session_start ();
$db = connectDB ();
?>
