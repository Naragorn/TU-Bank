<?php
require ("core.php");

$BANK_TITLE = $_SESSION ["uname"];

$target_dir = "uploads/";
$target_file = $target_dir . $_SESSION ["uid"] . basename ( $_FILES ["fileToUpload"] ["name"] );
$uploadOk = 1;
$imageFileType = pathinfo ( $target_file, PATHINFO_EXTENSION );
// Check if image file is a actual image or fake image

$errorOutput = "";

if (isset ( $_POST ["submit"] )) {
	if (file_exists ( $target_file )) {
		$errorOutput .= "File already exists. ";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if ($imageFileType != "csv") {
		$errorOutput .= "Only .csv files are allowed. ";
		$uploadOk = 0;
	}
	
	if ($uploadOk == 0) {
		$errorOutput .= "Sorry, your file was not uploaded. ";
		// if everything is ok, try to upload file
	} else {

		if (move_uploaded_file ( $_FILES ["fileToUpload"] ["tmp_name"], $target_file )) {
			$successOutput = "The file " . basename ( $_FILES ["fileToUpload"] ["name"] ) . " has been uploaded.";
			exec ( "./c-fileparser/fileparser " . $target_file, $output );
			unlink ( $target_file );
		} else {
			$errorOutput .= "Sorry, there was an error uploading your file."; //. print_r ( $_FILES );
		}
	}
}
include ("layout/header.php");
?>
If you wish to upload a batch file that performs one or multiple transactions you can upload such a file here.<br>
<form action="upload.php" method="post" enctype="multipart/form-data">
	Select a .csv file to upload:  <input type="file" name="fileToUpload"
		id="fileToUpload"> <input type="submit" value="Upload"
		name="submit">
</form>
<br>

The .csv file must have this format:<br>
sourceID, targetID, TAN, Amount<br><br>
The user must exist, have enough money and the TAN must be correct!

<?php
include ("layout/errorSuccessBox.php");
include ("layout/footer.php");
?>
