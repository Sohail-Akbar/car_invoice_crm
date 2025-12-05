<?php
session_start();
date_default_timezone_set('UTC');

if (!defined('DIR')) define('DIR', './');
if (!defined('_DIR_')) define('_DIR_', DIR);
require_once("inc/database.php");
require_once _DIR_ . "vendor/autoload.php";
require_once "Classes/SmtpMailer.php";
$timestamp = date('Y-m-d h:i:s');

$VERIFY_LOGIN = isset($VERIFY_LOGIN) ? $VERIFY_LOGIN : false;

define('LOGGED_IN_USER', login_user([
	'session_key' => 'user_id',
	'user_table' => 'users'
]));
define('LOGGED_IN_USER_ID', LOGGED_IN_USER ? LOGGED_IN_USER['id'] : null);


if (!is_null(LOGGED_IN_USER) && $VERIFY_LOGIN) {
	$url = "user/dashboard";
	if (LOGGED_IN_USER['type'] === "main_admin") {
		$url = "admin/dashboard";
	} else if (LOGGED_IN_USER['type'] === "admin") {
		$url = "admin/dashboard";
	}
	redirectTo($url);
}

define('IS_ADMIN', is_admin());

// print_r($timestamp);
// die;
