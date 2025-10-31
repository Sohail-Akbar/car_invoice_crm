<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
require_once _DIR_ . "includes/Classes/TCEmails.php";
// Sign Up
if (isset($_POST['register_new_user'])) {
	$fname      = $_POST['fname'];
	$lname      = $_POST['lname'];
	$name       = $fname . " " . $lname;
	$email      = $_POST['email'];
	$password   = $_POST['password'];
	$c_password = $_POST['c_password'];
	if ($c_password === $password) {
		$check = $db->select_one("users", '*', ['email' => $email]);
		if (gettype($check) === "array") {
			echo error('Email Already Exists. Go to Log In Page');
		} else {
			$password = password_hash($password, PASSWORD_BCRYPT);
			$add_user = $db->insert('users', [
				'fname' => $fname,
				'lname' => $lname,
				'name' => $name,
				'email' => $email,
				'image' => 'avatar.png',
				'password' => $password,
				'verify_status' => 0,
				'date_added' => $timestamp
			]);
			if ($add_user) {
				$user_id = $add_user;
				sendVerifyToken($email);
				echo success('We sent a verfication link to your email. Please Verify your account');
			}
		}
	}
}
// Login
if (isset($_POST['login'])) {
	$email    = $_POST['email'];
	$password = $_POST['password'];
	$user = $db->select_one('users', '*', [
		'email' => $email
	]);
	if ($user) {
		$account_password = $user['password'];
		if (!password_verify($password, $account_password)) {
			echo error('Password is wrong. Please enter a valid passowrd');
			die();
		}
		$user_id = $user['id'];
		if ($user['verify_status'] != '1') {
			$token  = md5(time() . "" . $user_id);
			$db->update('users', ['verify_token' => $token, 'token_expiry_date' => $timestamp], ['id' => $user_id]);
			echo error('Please verify your account. <a href="action?type=verifyEmail&email=' . $email . '">Click here to resend</a>', [
				'html' => true
			]);
		} else {
			$_SESSION['user_id'] = $user_id;

			$url = "user/dashboard";
			if ($user['type'] === "main_admin") {
				$url = "admin/dashboard";
			} else if ($user['type'] === "admin") {
				$url = "admin/dashboard";
			}
			echo success('logged in successfully', [
				'redirect' => $url,
			]);
		}
	} else {
		echo error('Email or Password is wrong. Please Try with a valid email and password');
	}
}
// Send Reset Password Link
if (isset($_POST['send_reset_password_link'])) {
	$email = $_POST['email'];
	$user = $db->select_one('users', '*', ['email' => $email]);
	if ($user) {
		if ($user['verify_status'] != 1) {
			echo error('Your account is not verified. First verify you account');
			die();
		}
		$forgot_token = md5(time() . $user['id'] . rand(0, 999));
		$token_expiry_date = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . " + 1 days"));
		$update = $db->update('users', ['password_forgot_token' => $forgot_token, 'token_expiry_date' => $token_expiry_date], ['id' => $user['id']]);
		if ($update) {
			$_tc_email->send([
				'template' => 'forgotEmail',
				'to' => $email,
				'vars' => [
					'token' => $forgot_token,
					'to' => $email,

				]
			]);
			echo success('Reset Password link sent to your email. You can reset the password with in 24 hours');
		}
	} else {
		echo error("You've entered the incorrect email address. Please try again.");
	}
}
// Reset Password
if (isset($_POST['reset_password'])) {
	$variables = ['token', 'email', 'new_password', 'confirm_password'];
	foreach ($variables as $value) {
		if (!isset($_POST[$value])) {
			die();
		}
	}
	$token = $_POST['token'];
	$email = $_POST['email'];
	$new_password = $_POST['new_password'];
	$confirm_password = $_POST['confirm_password'];
	$user = $db->select_one('users', '*', ['email' => $email]);
	if ($user) {
		if ($token == $user['password_forgot_token']) {
			$expiry_date = $user['token_expiry_date'];
			$expiry_date = date("Y-m-d h:i:s", strtotime($expiry_date));
			$current_date = date("Y-m-d h:i:s");
			if ($current_date < $expiry_date) {
				if ($new_password === $confirm_password) {
					$password = password_hash($new_password, PASSWORD_BCRYPT);
					$expiry_date = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s') . " -3 days"));
					$update = $db->update('users', [
						'password' => $password,
						'token_expiry_date' => $expiry_date,
					], ['id' => $user['id']]);
					if ($update) {
						echo success("Password changed successfully", [
							'redirect' => 'login?success=Password changed successfully'
						]);
					}
				} else {
					echo error('Password is not matching');
				}
			}
		}
	}
}
