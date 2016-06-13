<?php
$BANK_TITLE = "Reset password";

include_once ("config.php");
include_once ("database.php");
include_once ("session.php");
include_once ("functions.php");
include_once ("layout/header.php");

$message = "";

if (isset ( $_POST ["okbutton"] )) {
		// Email check taken from http://stackoverflow.com/questions/12026842/how-to-validate-an-email-address-in-php
	$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
	if (preg_match ( $pattern, $_POST ["email"] ) !== 1)
		$message = "Not a valid Email!";
}

if ($message == "" && isset ( $_POST ["okbutton"] )) {
	$conn = connectDB ();
	$result = $conn->query ( sprintf ( "SELECT uid FROM users WHERE email = '%s'", mysql_real_escape_string ( $_POST ["email"] ) ) );
	if (mysqli_num_rows ( $result ) > 0) {
                //this key below has to be static and fix and also acts as salt
		//http://www.dreamincode.net/forums/topic/370692-reset-password-system/
		$salt = "498#2D83B631%3800EBD!801600D*7E3CC13";
		$saltedLink = md5($salt.$_POST["email"]);
		$url =  "https://{$_SERVER['HTTP_HOST']}/securecoding/changePassword.php?q=".$saltedLink;
		$escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
		$title = "New password for TU Bank";
		$mailMessage = "You have requested a new password!"."<br>"."Follow this link to set a new password:"."<br><br>".$escaped_url."<br><br>"."Best Regards!"."<br>"."TU Bank.";

		if (SendEmail($title, $mailMessage, "", $_POST["email"])){
		    $successOutput = "An Email with further instructions to change your password has been sent to the entered Email Address!";
               } else {
                   $errorOutput = "Email couldn't be sent! Check your Internet connection and try again!";
               }
	} else {
		$errorOutput = "No Email address found!";
	}

	$conn->close ();
} else {
	if ($message != "")
		echo "<div class='error'>$message</div>";
	?>

<form method="post" action="">
	<h2>Reset your password!</h2>
	<div>Your Email:</div>
	<input type="text" name="email"
		value="<?php  if (isset ( $_POST ["email"] )) { echo htmlspecialchars($_POST["email"], ENT_QUOTES, 'UTF-8'); }?>">
		<br> <input type="submit" name="okbutton" value="Send">
</form>

<?php

}

include ("layout/errorSuccessBox.php");
include_once ("layout/footer.php");
?>
