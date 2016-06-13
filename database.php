<?php
/**
 * Establish the connection to the database.
 */
function connectDB() {
	global $DATABASE_HOST, $DATABASE_NAME, $DATABASE_PASSWORD, $DATABASE_USERNAME;
	$conn = new mysqli ( $DATABASE_HOST, $DATABASE_USERNAME, $DATABASE_PASSWORD, $DATABASE_NAME );
	if ($conn->connect_error) {
		die ( $conn->connect_error );
	}
	return $conn;
}

?>
