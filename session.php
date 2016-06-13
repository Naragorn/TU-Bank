<?php

header("Strict-Transport-Security:max-age=100"); // Increase max age if necessary

// redirect to https if http
if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) {
    $url = 'https://' . $_SERVER['HTTP_HOST']
                      . $_SERVER['REQUEST_URI'];

    header('Location: ' . $url);
    exit;
}


/**
 * Checks if the current user is logged in.
 */
function isLoggedIn() {
	return isset ( $_SESSION ["uname"] );
}

/**
 * Checks if the client has the permission as a user to stay on this page.
 * If not he will be redirected to the login page.
 */
function checkPermissionUser() {
	if (! isLoggedIn () || ! isset ( $_SESSION ["uname"] )) {
		redirectTo ( "login.php" );
	}
}
/**
 * Checks if the current user is logged in as a banker.
 * If he is not, he will be redirected to the login screen.
 */
function checkPermissionBanker() {
	if (! isLoggedIn () || ! isset ( $_SESSION ["uname"] ) ||  $_SESSION ["isBanker"]==false) {
		redirectTo ( "login.php" );
	}
}

/**
 * Redirects to the given page. Kills afterwards, because stackoverflow says so, see DailyWTF.com
 */
function redirectTo($page) {
	header ( "Location:" . $page );
	die();
}
?>
