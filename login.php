<?php
require ("core.php");

$BANK_TITLE = "Login";

// Info about Database
require_once 'config.php';

// Connection to Database
$con = mysql_connect ( $DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD ) or die ( "Failed to connect to MySQL: " . mysql_error () );
// Choosing Database from MySQL
$db = mysql_select_db ( $DATABASE_NAME, $con ) or die ( "Failed to connect to MySQL: " . mysql_error () );

if (isset ( $_POST ['submit'] )) {
	if (! empty ( $_POST ['user'] )) {
		// Selecting rows from table users for input values: username and password.
		$query = mysql_query ( "SELECT * FROM users WHERE uname='".mysql_real_escape_string($_POST["user"])."' " ) or die ( mysql_error () );
		$rowCount = mysql_num_rows ( $query );
		
		// Checking if quantity of elements in row are more than 0(dies if it is 0 and code below it is not compiled)
		if ($rowCount > 0) {
			// reset lockOut, no brute-force
			mysql_query( sprintf ( "INSERT INTO `logins` (ipAddr, lockCount)
                                        VALUES ('%s', 0) ON DUPLICATE KEY UPDATE lockCount=0", mysql_real_escape_string ( $_SERVER['REMOTE_ADDR'] ) ) ) or die ( mysql_error () );
			
			// continue with semantic
			$row = mysql_fetch_array ( $query ) or die ( mysql_error () );
			if (! empty ( $row ['uname'] ) && ! empty ( $row ['password'] )) {

				if ( md5 (  $row ["salt"].$_POST ["pswd"] ) !=  $row ['password']) {

					$errorOutput = "Wrong Password.";
				}
				else if($row['approved'])
				{
					session_regenerate_id(true);
					// In case if user logged in successfully
					$_SESSION ['uid'] = $row ['uid'];
					$_SESSION ['uname'] = $row ['uname'];
					$_SESSION ['isBanker'] = $row ['isBanker'];
					header ( "Location:index.php" );
				}
				else
				{
					//unapproved user trying to log in
					$errorOutput = "User not Approved";
				}	
			}
		} else {
			// In case if username or password is incorrect
			$errorOutput = "Wrong username or password";	
			$query = mysql_query( sprintf ( "INSERT INTO `logins` (ipAddr, lockCount)
                                        VALUES ('%s', 1) ON DUPLICATE KEY UPDATE lockCount=lockCount+1", mysql_real_escape_string ( $_SERVER['REMOTE_ADDR'] ) ) ) or die ( mysql_error () );
			$numLocks = mysql_query ( sprintf ( "SELECT lockCount FROM logins WHERE ipAddr = '%s'", mysql_real_escape_string ( $_SERVER['REMOTE_ADDR'] ) ) );
			$row = mysql_fetch_array ( $numLocks ) or die ( mysql_error () );

			//brute-forcing detected, Lock out!
			if($row['lockCount'] > 2){
				header ( "Location:lockOut.php" );
				die();
			}
		}
	} else {
		// if username is empty
		$errorOutput = "Go back and enter Username";
		header ( "Location:login.php" );
		die();
	}
}

include ("layout/header.php");
include ("layout/login.php");
include ("layout/errorSuccessBox.php");
include ("layout/footer.php");
?>
