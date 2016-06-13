<?php
include_once("../config.php");
include_once("../database.php");
include_once("../functions.php");
include_once ("../libraries/fpdi/FPDI_Protection.php");
?>
 This file is used to create the mySQL database.
 We used this to keep the database layout up-to-date.
 <br>
<form method="POST" action="">
	<input type="submit" name="button" value="Setup">
</form>

<?php
if ($_POST["button"] == "Setup") {
	echo "Droping old database...<br/>";

	$sql = "CREATE DATABASE ".$DATABASE_NAME;

	$conn = connectDB();
	$conn->query("DROP DATABASE ".$DATABASE_NAME); // Drop old table
	if ($conn->query($sql) === true) { // Create new table
		echo "Created database ".$DATABASE_NAME." successfully.<br>";
	} else {
		echo $conn->error;
	}

	$conn->close();

	echo "Connecting to database...<br/>";	
	$conn = connectDB();

	echo "Creating new TABLES...<br/>";	 // SQL exported from Adminer
	
	$query = "CREATE TABLE `users` (
		`uid` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`uname` text NOT NULL,
		`password` varchar(64) NOT NULL,
		`salt` varchar(64) NOT NULL,
		`isBanker` BOOL NOT NULL,
		`approved` BOOL NOT NULL,
		`email` text NOT NULL,
		`pin` text NOT NULL,
		`balance` double NOT NULL
	);";
	
	$query .= "CREATE TABLE `registrations` (
		`uname` text NOT NULL,
		`password` varchar(64) NOT NULL,
		`isBanker` BOOL NOT NULL,
		`email` text NOT NULL,
		`balance` double NOT NULL
	);";
	
	$query .= "CREATE TABLE `scstans` (
		`uid` int NOT NULL,
		`random` TEXT NOT NULL
	);";
	
	// approved has effectively three values now. TRUE and FALSE for
	// approved and declined as well as NULL for pending.
	$query .= "CREATE TABLE `transactions` (
		`sourceId` int NOT NULL,
		`targetId` int NOT NULL,
		`tan` text NOT NULL,
		`date` DATE NOT NULL,
		`approved` BOOL,
		`amount` DOUBLE NOT NULL,
		 `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
	);";
	
	$query .= "CREATE TABLE `tans` (
		`uid` int NOT NULL,
		`value` varchar(15) NOT NULL
	);";

	$query .= "CREATE TABLE `logins` (
		`ipAddr` varchar(45) NOT NULL,
		`lockCount` int NOT NULL,
		PRIMARY KEY (`ipAddr`)
	);";

	$pass1 = generateSaltedPassword('kjvl4lvn');
	
	$query .= "INSERT INTO `users` 
	(uname, password, salt, isBanker, email, balance, approved, pin)
	VALUES
	('admin', '".$pass1["password"]."', '".$pass1["salt"]."',true, 'noreplay@securecoding.net', 999999.99, 1, '123456');";
		
	$query .= "INSERT INTO `users` 
	(uname, password, salt, isBanker, email, balance, approved, pin)
	VALUES
	('test',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'test@securecoding.net', 99.99, 1, '654321');";

	$conn->multi_query($query);
	
	$conn->close(); // Close db to prevent out of sync errors
	sleep(1); // Very Very hacky, table transaction did not exist for the first ~20 tan inserts. 
	
	$conn = connectDB();
	// Insert Tans for user Id 1 (admin) and 2 (test)
	insertTansForUser(2, $conn, "manukru+123456@gmail.com");
	insertTansForUser(1, $conn,   "manukru+12345@gmail.com");
}

?>


