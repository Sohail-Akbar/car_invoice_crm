<?php
if (!defined('DIR')) define('DIR', './');
if (!defined('_DIR_')) define('_DIR_', DIR . "../");
require_once _DIR_ . "includes/db.php";

if (!LOGGED_IN_USER)
	redirectTo(_DIR_ . 'login');
