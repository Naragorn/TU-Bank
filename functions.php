<?php
require 'libraries/email/PHPMailerAutoload.php';



/*
 * Check a password for safeness (must contain numbers etc.)
 */
function checkPassword($pwd){
    $error = "";
    if( strlen($pwd) < 8 ) 
	$error .= "Password too short!<br>";
    if( strlen($pwd) > 64 ) 
	$error .= "Password too long!<br>";
    if( !preg_match("#[0-9]+#", $pwd) ) 
	$error .= "Password must include at least one number!<br>";
    if( !preg_match("#[a-z]+#", $pwd) ) 
	$error .= "Password must include at least one letter!<br>";
    if( !preg_match("#[A-Z]+#", $pwd) ) 
	$error .= "Password must include at least one CAPS!<br>";
    if( !preg_match("#\W+#", $pwd) ) 
	$error .= "Password must include at least one symbol!<br>";
    return $error;
}

/*
 * Taken from http://stackoverflow.com/questions/12700974/how-to-password-protect-an-uploaded-pdf-in-php
 */
function pdfEncrypt($origFile,  $destFile){

$pdf =& new FPDI_Protection();
$pdf->FPDF('P', 'in', array('6','9'));
$pagecount = $pdf->setSourceFile($origFile);
$tplidx = $pdf->importPage(1);
$pdf->addPage();
$pdf->useTemplate($tplidx);
$pdf->SetProtection(array(),'tubank');
$pdf->Output($destFile, 'F');
return $destFile;
}

/*
 * Generate a salted password. Returns an array of salt and salted password.
 * Using bcrypt instead of md5 would make this thing more secure.
 */

function generateSaltedPassword($password) {
	$salt = makeCode($password);
	return (array("salt" => $salt, "password" => md5($salt.$password)));
}

/*
 * Generate a 15 digit unique code and return it as a string
 */
 
function makeCode() {
		$code = "";
		$letterset = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$length = strlen ( $letterset );
		
		for($i = 0; $i < 15; $i ++) {
			$code .= $letterset [rand ( 0, $length - 1 )];
		}
		return $code;
}

/*
 * Generate 100 unique Codes for a userId. Also checks for duplicates etc.
 * Parameters: 
 * 	$userId: The User Id as int, 
 * 	$conn: A database connections
 */
function insertTansForUser($userId, $conn2, $receiverMail="") {
	
	if ($receiverMail == "")
		$receiverMail = $_POST["email"];
	
	$emailMessage = "Welcome, your tans are: \n\n\n"; // NO HTML PLEASE!!! As also used in the PDF file!
	$tans = array();
					
	// Insert 100 codes into database
	for($i = 0; $i < 100; $i ++) {
		$codeExists = true;
		$tan;
		while ($codeExists)	{
			
			$tanTemp = makeCode (); // Generate a temporary TAN code
			$result = $conn2->query ( sprintf ( "SELECT * FROM tans WHERE value = '%s'", mysql_real_escape_string ( $tanTemp) ) ); // Check if it exits already in the database to avoid duplicates
				echo $conn2->error;
			if (mysqli_num_rows ( $result ) != 0) {
				$codeExists = true;
				echo "Warning: TAN Conflict: Duplicate<br>";
			}
			else {
				$tan = $tanTemp;
				$codeExists = false;
				
				$query = sprintf ( "INSERT INTO tans SET uid=%s, value='%s'", $userId, $tan );
				if ($conn2->query ( $query ) !== true) {
					echo "ERROR AT CREATING TRANSACTION CODE FOR ".$userId.": ".$tan;		
					echo $conn2->error;
				}

			}
		}	

		$emailMessage .= $tan."\n";
		$tans[] = $tan;
	}


	//
	// Make PDF
	//

	$p = PDF_new (); // Note: PDF Generation code taken from PHP documentation: http://php.net/manual/de/pdf.examples-basic.php
	
	// Ususally we would use this to protect the pdf. Unfortunately we can only use it in the PDFLib Pro version:
	//optlist = "masterpassword=tubank permissions={noprint nohiresprint nocopy noaccessible noassemble}" ;
	
	// So we will use the FPDI 
	        
		
	if (PDF_begin_document ( $p, "", "") == 0) {
		die ( "Error: " . PDF_get_errmsg ( $p ) );
	}
	
	PDF_set_info ( $p, "Creator", "Group 2" );
	PDF_set_info ( $p, "Author", "Group 2" );
	PDF_set_info ( $p, "Title", "Transactions" );
	PDF_begin_page_ext ( $p, 595, 842, "" );
	$font = PDF_load_font ( $p, "Helvetica-Bold", "winansi", "" );
	
	PDF_setfont ( $p, $font, 24.0 );
	PDF_set_text_pos ( $p, 50, 700 );
	PDF_show ( $p, "TANS: " );
	
	PDF_setfont ( $p, $font, 12.0 );
	PDF_continue_text ( $p, "-------------------------------" );
	
	$pageIndicator = 0;
	
	for($i = 0; $i<100; $i=$i+3) {
		if ($i!=99)
			PDF_continue_text ( $p, $tans[$i]."   ".$tans[$i+1]."   ".$tans[$i+2]);	
		else
			PDF_continue_text ( $p, $tans[$i]);	
	}
	PDF_end_page_ext ( $p, "" );
	PDF_end_document ( $p, "" );
	
	$buf = PDF_get_buffer ( $p );
	$len = strlen ( $buf );
	
	file_put_contents ("user.pdf", $buf);

	PDF_delete ( $p );
	pdfEncrypt("user.pdf", "user.pdf");
	


	//
	// Send email
	//
	$title = "Your TU Bank Account has been approved";
	$emailmessage = "Your online Banking account at TU Bank has been generated!\nYou will find your TAN numbers In the attached PDF file.\n\n\nBest regards, \n\nTU Bank.";
	$attachment = "user.pdf";
	sendEmail($title, $emailmessage, $attachment, $receiverMail);
}



function sendEmail($title, $emailMessage, $attachment, $receiverMail){


      /*  $subject = $title;
        $message = $emailMessage;
        $headers = 'From: webmaster@gnb.com' . "\r\n" .

                'Reply-To: webmaster@gnb.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        mail($receiverMail, $subject, $message, $headers);
*/


	$mail = new PHPMailer;
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'TUBankTeam2@gmail.com';		// SMTP username
	$mail->Password = 'securecoding2';			// SMTP password
	///$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
	$mail->From = 'tubankteam2@gmail.com';
	$mail->FromName = 'TU Bank';
	$mail->addAddress($receiverMail);               // Name is optional
	$mail->addReplyTo('tubankteam2@gmail.com');
	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);                                  // Set email format to HTML
	if(strlen($attachment) > 0) $mail->AddAttachment($attachment);		//Attach the protected PDF
	$mail->Subject = $subject;
	$mail->Body    = $emailMessage;
	$ret = -1;

	if(!$mail->send()) {
	    echo 'Email could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	    $ret = 0;
	} else {
	   // echo 'Email has been sent.';
	    $ret = 1;
	}
	return $ret;
}
?>
