<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
// Update Personal Info
if (isset($_POST['update_personal_information'])) {
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	if (strlen($fname) > 0 && strlen($lname) > 0) {
		$name = $fname . ' ' . $lname;
		$update = $db->update('users', [
			'fname' => $fname,
			'lname' => $lname,
			'name' => $name,
		], ['id' => LOGGED_IN_USER['id']]);
		if ($update) {
			returnSuccess('Information Updated Successfully',[
				"redirect" => ""
			]);
		}
	}
}
// Change Password
if (isset($_POST['change_password'])) {
	$current_password = $_POST['current_password'];
	$new_password = $_POST['new_password'];
	$confirm_password = $_POST['confirm_password'];
	if ($new_password === $confirm_password) {
		if (password_verify($current_password, LOGGED_IN_USER['password'])) {
			$new_password = password_hash($new_password, PASSWORD_BCRYPT);
			$update = $db->update('users', ['password' => $new_password], [
				'id' => LOGGED_IN_USER['id']
			]);
			if ($update) {
				returnSuccess('Passowrd Changed Successfully',);
			}
		} else {
			returnError('Current Password is wrong. Please enter a correct password');
		}
	} else {
		returnError('New password is not matching with confirm password. Please try again');
	}
}
// Change User Profile Picture
if (isset($_FILES['changeUserImage'])) {
	$image_name = $_FILES['changeUserImage']['name'];
	$image_ext = is_image_file($image_name);
	if (!$image_ext) {
		returnError('Invalid file type');
	}
	$new_name = md5(time()) . LOGGED_IN_USER['id'] . '.' . $image_ext;
	$location = _DIR_ . 'images/users/' . $new_name;
	if (move_uploaded_file($_FILES['changeUserImage']['tmp_name'], $location)) {
		$old_image = LOGGED_IN_USER['image'];
		$update = $db->update('users', ['image' => $new_name], [
			'id' => LOGGED_IN_USER['id']
		]);
		if ($update) {
			if ($old_image !== 'avatar.png')
				unlink_(_DIR_ . 'images/users/' . $old_image);
			returnSuccess('Image Updated Successfully');
		}
	}
}
