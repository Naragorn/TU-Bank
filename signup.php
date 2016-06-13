<?php
$BANK_TITLE = "Sign up";

include_once ("config.php");
include_once ("database.php");
include_once ("session.php");
include_once ("functions.php");
include_once ("layout/header.php");
include_once ("libraries/fpdi/FPDI_Protection.php");

$message = "";

if (isset ( $_POST ["okbutton"] )) {
	if (strlen ( $_POST ["uname"] ) < 4)
		$message .= "Username is too short!<br>";
	if (strlen ( $_POST ["uname"] ) > 23)
		$message .= "Username is too long!<br>";
	if (strlen ( checkPassword($_POST["password"])) > 0)
		$message .= checkPassword($_POST["password"]);
	if ( $_POST["password"] != $_POST["confirmpassword"])
		$message .= "You password doesn't match!<br>";
		
		// Email check taken from http://stackoverflow.com/questions/12026842/how-to-validate-an-email-address-in-php
	$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
	if (preg_match ( $pattern, $_POST ["email"] ) !== 1)
		$message .= "Email is invalid!";
}

if ($message == "" && isset ( $_POST ["okbutton"] )) {
	// Add user to database etc....
	$conn = connectDB ();
	
	$isBanker = $_POST ["banker"] == "yes" ? 1 : 0;
	$isSCS = $_POST ["scs"] == "yes" ? 1 : 0;

	$result = $conn->query ( sprintf ( "SELECT uid FROM users WHERE uname = '%s'", mysql_real_escape_string ( $_POST ["uname"] ) ) );
	$result2 = $conn->query ( sprintf ( "SELECT uid FROM users WHERE email = '%s'", mysql_real_escape_string ( $_POST ["email"] ) ) );
	
	$pin2 = substr(md5(microtime()),0,6);
	//echo $pin2;
	if (mysqli_num_rows ( $result ) == 0 && mysqli_num_rows ( $result2 ) == 0) {

		$pass1 = generateSaltedPassword($_POST ["password"]);
		$query = sprintf ( "INSERT INTO users SET pin='%s',uname='%s', password='%s',salt='%s', email='%s', isBanker=%s",  mysql_real_escape_string ( $pin2 ), mysql_real_escape_string ( $_POST ["uname"] ), mysql_real_escape_string ( $pass1 ["password"]),mysql_real_escape_string ( $pass1 ["salt"]), mysql_real_escape_string ( $_POST ["email"] ), $isBanker );
		
		if ($conn->query ( $query ) === true) { // Create new table
			$successOutput = "Registration successful!";
			
			$userId = $conn->insert_id; // Get the id of the inserted user

			if ($isSCS)
				echo "You have selected the SCS system. We will not send you your TANs via Email...";
			else
				insertTansForUser ( $userId, $conn , "", $_POST["email"]);
				
			sendEmail("Your TUBank SCS PIN", "Your SCS PIN is ".$pin2,  "", $_POST["email"]);
		} else {
			$errorOutput = $conn->error;
		}
	} else {
		$errorOutput = "Username or Email already exists!";
	}
	
	$conn->close ();
	// TODO: Check if user exists, check if password ok etc.
} else {
	if ($message != "")
		echo "<div class='error'>$message</div>";
	?>
<form method="post" action="">
	<h2>Username:</h2>
	<input type="text" name="uname"
		value="<?php
	if (isset ( $_POST ["uname"] )) {
		echo htmlspecialchars( $_POST["uname"], ENT_QUOTES, 'UTF-8');
	}
	?>"><br>
	<h2>Banker:</h2>
	<fieldset>
		<input type="radio" id="y" name="banker" value="yes"> <label for="y">
			Yes</label><br> <input checked="checked" type="radio" id="n"
			name="banker" value="no"> <label for="n"> No</label>
	</fieldset>

	<h2>SCS:</h2>
	<fieldset>
		<input type="radio" id="yscs" name="scs" value="yes"> <label for="yscs">
			Yes</label><br> <input checked="checked" type="radio" id="nscs"
			name="scs" value="no"> <label for="nscs"> No</label>
	</fieldset>


	<h2>Password:</h2>
	<input type="password" name="password"
		value="<?php if (isset ( $_POST ["uname"] )) { echo htmlspecialchars($_POST["password"], ENT_QUOTES, 'UTF-8'); }?>"><br>
	<h2>Confirm Password:</h2>
	<input type="password" name="confirmpassword"
		value="<?php if (isset ( $_POST ["uname"] )) { echo htmlspecialchars($_POST["confirmpassword"], ENT_QUOTES, 'UTF-8'); }?>"><br>
	<h2>Email:</h2>
	<input type="text" name="email"
		value="<?php  if (isset ( $_POST ["uname"] )) { echo htmlspecialchars($_POST["email"], ENT_QUOTES, 'UTF-8'); }?>"><br>
	<br> <br> <input type="submit" name="okbutton" value="Sign up now!">
</form>
<?php
}
include ("layout/errorSuccessBox.php");
include_once ("layout/footer.php");
?>
