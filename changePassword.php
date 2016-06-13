<?php
$BANK_TITLE = "Change password";

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
                $message .= "Not a valid Email!<br>";
}


if (isset ( $_POST ["okbutton"] )) {
    if ($_POST["password"] != $_POST["confirmpassword"]) 
	$message .= "Your password doesn't match!<br>";
    if (strlen ( checkPassword($_POST["password"])) > 0)
        $message .= checkPassword($_POST["password"]);
}

if ($message == "" && isset ( $_POST ["okbutton"] )) {
        $conn = connectDB ();
        $result = $conn->query ( sprintf ( "SELECT uid FROM users WHERE email = '%s'", mysql_real_escape_string ( $_POST ["email"] ) ) );

        if (mysqli_num_rows ( $result ) > 0) {
		//This is the hashed value the user should've gotten via email
		$hash = $_POST["q"];
                //this key below has to be static and fix and also acts as salt, see resetPassword.php they need to be identical
		//http://www.dreamincode.net/forums/topic/370692-reset-password-system/
                $saltKey = "498#2D83B631%3800EBD!801600D*7E3CC13";
		$resetkey = md5($saltKey.$_POST["email"]);
echo $hash."<br>";
echo $resetkey;
		if(strlen($hash) > 1){
		    if(strcmp($hash, $resetkey)){
		    $saltedPassword = generateSaltedPassword($_POST ["password"]);
                    $row = $result->fetch_assoc();
                    $query = sprintf ( " UPDATE users SET password='%s', salt='%s' ", mysql_real_escape_string ( mysql_real_escape_string ( $saltedPassword["password"] )), mysql_real_escape_string ( mysql_real_escape_string ( $saltedPassword["salt"]  )  ) . "' WHERE uid = '".$row['uid'] );

                    if ($conn->query ( $query ) === true) {
                      $successOutput = "New Password set! Try logging in!";
                    }} else {
                      $errorOutput = $conn->error;
		    }
		} else {
		    $errorOutput = "There has been a serious error! You are not authorized to change any passwords!";
		}
        } else {
                $errorOutput = "No Email address found!";
        }

        $conn->close ();
} else {
        if ($message != "")
                echo "<div class='error'>$message</div>";
?>



<form action="" method="POST">
<div>E-mail Address:</div> <input type="text" name="email" /><br />
<div>New Password:</div> <input type="password" name="password" /><br />
<div>Confirm Password:</div> <input type="password" name="confirmpassword" /><br />
<input type="hidden" name="q" value="<?php
if (isset($_GET["q"])) {
	echo $_GET["q"];
}
?>
	" /><input type="submit" name="okbutton" value=" Change Password " />
</form>




<?php

}

include ("layout/errorSuccessBox.php");
include_once ("layout/footer.php");
?>

