<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');
require_once _DIR_ . "includes/Classes/SmtpMailer.php";
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
				'date_added' => CREATED_AT
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
	$remember_me    = isset($_POST['remember_me']) ? 1 : 0;
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
		$_SESSION['pending_2fa'] = [
			"user_id" =>	$user_id,
			"email" => $email,
		];

		$otp = rand(111111, 999999);
		if (ENV === "local") $_SESSION['pending_2fa']["otp"] = $otp;

		$db->update('users', [
			'twofa_code' => $otp,
			'twofa_expire' => CREATED_AT,
		], ['id' => $user_id]);

		// Send Email
		// $_tc_email->send([
		// 	'template' => 'sendOtp',
		// 	// 'to'       => $email,
		// 	'to'       => "sohailakbar3324@gmail.com",
		// 	'subject'  => "Your Verification Code: $otp",
		// 	'vars'     => [
		// 		'OTP' => $otp
		// 	]
		// ]);

		// ------------ REMEMBER TOKEN ------------
		if ($remember_me) {
			$raw_token = bin2hex(random_bytes(32));
			$token_hash = hash("sha256", $raw_token);

			// Save hashed token
			$db->update('users', [
				'remember_token' => $token_hash,
			], ['id' => $user_id]);

			// Set raw token cookie
			setcookie(
				'Garage_Remember_Me',
				$user_id . ":" . $raw_token,
				time() + (86400 * 30),
				"/",
				"",
				false,
				true
			);
		} else {

			// Ensure no old token remains
			$db->update('users', [
				'remember_token' => null,
			], ['id' => $user_id]);

			setcookie("Garage_Remember_Me", "", time() - 3600, "/");
		}
		// ------------ END REMEMBER TOKEN ------------

		echo success('OTP sent to your email.', [
			'redirect' => 'verify-otp'
		]);
	} else {
		echo error('Email or Password is wrong. Please Try with a valid email and password');
	}
}

// verify otp
if (isset($_POST['verify_otp'])) {
	$otp    = $_POST['otp'];
	$forgot_password    = isset($_POST['forgot_password']) ? $_POST['forgot_password'] : null;

	if (!isset($_SESSION['pending_2fa']) || !is_array($_SESSION['pending_2fa'])) {
		returnError('Verification session not found. Please log in again to request a new verification code.');
	}
	$user_id = arr_val($_SESSION['pending_2fa'], 'user_id', null);
	if (empty($user_id)) {
		returnError('Unable to proceed with verification: missing user information. Please request a new code from the login page.');
	}

	$user = $db->select_one("users", "*", ['id' => $user_id]);

	if ($user['twofa_code'] == $otp) {
		// Clear OTP
		$db->update("users", [
			"twofa_code" => null,
			"twofa_expire" => null,
			"verify_status" =>  1
		], [
			"id" => $user_id
		]);
		// Now login
		if (!$forgot_password) {
			unset($_SESSION["pending_2fa"]);
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
		} else {
			echo success('logged in successfully', [
				'redirect' => "forgot?type=reset",
			]);
		}
	} else {
		returnError("OTP does not match. Please check the code sent to your email and try again.");
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



// Send Reset Password OTP
if (isset($_POST['send_reset_password_otp'])) {

	$email = $_POST['email'];
	$user = $db->select_one('users', '*', ['email' => $email]);

	if (!$user) {
		echo error("You've entered the incorrect email address. Please try again.");
		die();
	}

	if ($user['verify_status'] != 1) {
		echo error('Your account is not verified. First verify your account');
		die();
	}

	// Generate OTP
	$otp = rand(111111, 999999);

	// Save OTP + expiry (10 mins)
	$update = $db->update('users', [
		'twofa_code' => $otp,
	], ['id' => $user['id']]);

	if ($update) {

		// Send Email
		// $_tc_email->send([
		// 	'template' => 'sendOtp',
		// 	// 'to'       => $email,
		// 	'to'       => "sohailakbar3324@gmail.com",
		// 	'subject'  => "Your Verification Code: $otp",
		// 	'vars'     => [
		// 		'OTP' => $otp
		// 	]
		// ]);

		$_SESSION['pending_2fa'] = [
			"user_id" =>	$user['id'],
			"email" => $user['email'],
		];

		if (ENV === "local") $_SESSION['pending_2fa']["otp"] = $otp;

		echo success('We sent you a 6-digit OTP. Please check your email.', [
			"redirect" => "verify-otp?type=forgot"
		]);
	}
}


// Update Password 
if (isset($_POST['updatePassword'])) {

	// Get posted data
	$new_password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];

	// Get user ID from session (2FA pending user)
	$user_id = $_SESSION['pending_2fa']["user_id"];

	// Validate input
	if ($new_password != $confirm_password) {
		echo error("Passwords do not match");
		exit;
	}

	if (empty($user_id)) {
		echo error("Invalid session. Please login again.");
		exit;
	}

	// Update password
	$update = $db->update("users", [
		"password" => password_hash($new_password, PASSWORD_DEFAULT)
	], ["id" => $user_id]);

	if ($update) {

		// Clear pending 2FA session
		unset($_SESSION['pending_2fa']);

		echo success("Password updated successfully. Please login now.");
	} else {
		echo error("Failed to update password");
	}

	exit;
}
