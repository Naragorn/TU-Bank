<?php
	if (isset ( $errorOutput ) && $errorOutput !== "") {
		echo "<div class='error'>";
		echo $errorOutput;
		echo "</div>";
	}
	
	if(isset ( $successOutput ) && $successOutput !== "") {
		echo "<div class='success'>";
		echo $successOutput;
		echo "</div>";
	}
?>
