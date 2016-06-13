<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo $BANK_TITLE ?> | Bank</title>
<link rel="stylesheet" href="css/main.css">
</head>

<body>
	<div class="container">
		<div class="header">
		<?php
		if (! isLoggedIn ()) {
			// TODO: Check if logged in here
			?>
			<a href="index.php">TU BANK </a> <span style="float: right"> <a
				href="login.php">Login</a> - <a href="signup.php">Signup</a>
			</span>
		<?php
		} else {
			?>
			<a href="index.php">TU BANK </a>
			
			
			 <span style="float: right">
				 <?php
			if ($_SESSION["isBanker"]) {
				echo "<a href='approve.php'>Approvals</a> - <a href='allusers.php'>All Users</a>  - ";
				}
			?> 
				  <a
				href="transactions.php">Transactions</a>  -   
				 <a
				href="upload.php">Upload</a> - 
				 <a
				href="user.php">My Account</a> - <a
				href="logout.php">Logout</a>
			</span>
		<?php
		}
		?>
	</div>
		<div class="main">
