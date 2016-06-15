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


	
	// Fill DB with Admin Users
	$pass1 = generateSaltedPassword('kjvl4lvn');
	
	$query .= "INSERT INTO `users` 
	(uname, password, salt, isBanker, email, balance, approved, pin)
	VALUES
	('admin', '".$pass1["password"]."', '".$pass1["salt"]."',true, 'Andreas.Paul@in.tum.de', 999999.99, 1, '654321');";
		
	$pass1 = generateSaltedPassword('yonis10');
	$query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('yonis',  '".$pass1["password"]."', '".$pass1["salt"]."', true, 'yonis@in.tum.de', 100.00, 1, '654321');";


	$pass1 = generateSaltedPassword('buik10');
	$query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('buik',  '".$pass1["password"]."', '".$pass1["salt"]."', true, 'buik@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('banker');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('banker',  '".$pass1["password"]."', '".$pass1["salt"]."', true, '', 100.00, 1, '654321');";



	// From now on only normal users
        $pass1 = generateSaltedPassword('user123');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('user',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'test@securecoding.net', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('drexledo01');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('drexledo',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'drexledoq@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('berchtoa01');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('berchtoa',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'berchtoa@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('mitterem02');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('mitterem',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'mitterem@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('langlech02');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('langlech',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'langlech@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('legenc03');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('legenc',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'legenc@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('kriegerd03');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('kriegerd',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'kriegerd@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('niedermp04');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('niedermp',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'niedermp@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('theissi04');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('theissi',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'theissi@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('tipecska05');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('tipecska',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'tipecska@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('zieglmev05');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('zieglmev',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'zieglmev@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('schlagbe06');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('schlagbe',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'schlagbe@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('ottor06');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('ottor',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'ottor@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('kiwus07');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('kiwus',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'kiwus@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('vigo07');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('vigo',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'vigo@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('luebben08');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('luebben',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'luebben@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('werneck08');
        $query .= "INSERT INTO `users` 
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('werneck',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'werneck@in.tum.de', 100.00, 1, '654321');";

        $pass1 = generateSaltedPassword('Max Mustermann');
        $query .= "INSERT INTO `users`
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('Max Mustermann',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'Max@Musterma.nn', 0.00, 0, '654321');";

        $pass1 = generateSaltedPassword('Pauline Schlagmichtot');
        $query .= "INSERT INTO `users`
        (uname, password, salt, isBanker, email, balance, approved, pin)
        VALUES
        ('Pauline Schlagmichtot',  '".$pass1["password"]."', '".$pass1["salt"]."', false, 'Pauline@Schlagmicht.ot', 0.00, 0, '654321');";


	$conn->multi_query($query);
	$pass1 = generateSaltedPassword('yonis10');
	
	$conn->close(); // Close db to prevent out of sync errors
	sleep(1); // Very Very hacky, table transaction did not exist for the first ~20 tan inserts. 
	
	$conn = connectDB();
	// Insert Tans for user Id 1 (admin) and 2 (test)
	insertTansForUser(2, $conn, "yonis@in.tum.de");
	insertTansForUser(3, $conn, "yonis@in.tum.de");
	//insertTansForUser(1, $conn,   "manukru+12345@gmail.com");
}

?>


