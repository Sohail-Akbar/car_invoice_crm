<?php
define('DIR', '../');
require_once('../includes/db.php');
require_once _DIR_ . "includes/Classes/TCDelete.php";

$_delete->set([
	'user' => 'users',
]);

// User aur company dono delete karne ke liye callback
$_delete->on('user', function ($delete, $data) {
	global $db;

	$user_id = $data['id'];

	try {
		// Pehle user data fetch karein
		$user_data = $db->select_one('users', ['company_id'], ['id' => $user_id]);

		if ($user_data) {
			$company_id = $user_data['company_id'];

			// Pehle user delete karein
			$db->delete('users', ['id' => $user_id]);

			// Phir company delete karein (agar company_id hai to)
			if ($company_id) {
				$db->delete('companies', ['id' => $company_id]);
			}

			return $delete->next();
		} else {
			return $delete->stop();
		}
	} catch (Exception $e) {
		error_log("Delete error: " . $e->getMessage());
		return $delete->stop();
	}
});

$_delete->init();
