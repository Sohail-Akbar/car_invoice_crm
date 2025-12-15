<?php

$data = json_decode(file_get_contents("php://input"), true);
$data = $_POST;

// Login
if (isset($data['login'])) {
    $email    = isset($data['email']) ? $data['email'] : "";
    $remember_me    = isset($data['remember_me']) ? 1 : 0;
    $password = isset($data['password']) ? $data['password'] : "";

    if (empty($email)) {
        echo json_encode([
            "status" => "error",
            "message" => "Email is required",
        ]);
        die;
    }
    if (empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Password is required",
        ]);
        die;
    }

    if (strlen($password) < 6) {
        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 6 characters long",
        ]);
        die;
    }

    $user = $db->select_one('users', '*', [
        'email' => $email
    ]);
    if ($user) {
        $pending_2fa_data = [];

        $account_password = $user['password'];
        if (!password_verify($password, $account_password)) {
            echo json_encode([
                "status" => "error",
                "message" => "Password is wrong. Please enter a valid passowrd",
            ]);
            die();
        }

        $otp = rand(111111, 999999);
        if (ENV === "local") $_SESSION['pending_2fa']["otp"] = $otp;
        $token = bin2hex(random_bytes(16));

        $db->update('users', [
            'twofa_code' => $otp,
            "verify_token" => $token
        ], ['id' => $user['id']]);

        $pending_2fa_data["otp"] = $otp;
        $pending_2fa_data["token"] = $token;

        // Send Email
        $_tc_email->send([
            'template' => 'sendOtp',
            // 'to'       => $email,
            // 'to'       => "sohailakbar3324@gmail.com",
            'to'       => "haider.ali@intellectualbunch.com",
            'subject'  => "Your Verification Code: $otp",
            'vars'     => [
                'OTP' => $otp
            ]
        ]);

        // ------------ REMEMBER TOKEN ------------
        if ($remember_me) {
            $raw_token = bin2hex(random_bytes(32));
            $token_hash = hash("sha256", $raw_token);

            // Save hashed token
            $db->update('users', [
                'remember_token' => $token_hash,
            ], ['id' => $user['id']]);

            $pending_2fa_data["remember_token"] = $token . ":" . $raw_token;
        } else {

            // Ensure no old token remains
            $db->update('users', [
                'remember_token' => null,
            ], ['id' => $user['id']]);
        }

        echo json_encode([
            "status" => "success",
            "message" => "OTP sent to your email",
            "data" => $pending_2fa_data
        ]);
        die;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email or Password is wrong. Please Try with a valid email and password",
        ]);
        die;
    }
}


// verify otp
if (isset($data['verify_otp'])) {
    $otp    = isset($data['otp']) ? $data['otp'] : "";
    $forgot_password = isset($data['forgot_password']) ? $data['forgot_password'] : "";
    $user =  validateBearerToken("verify_token");

    if (empty($otp)) {
        echo json_encode([
            "status" => "error",
            "message" => "OTP is required",
        ]);
        die;
    }

    $token = $user["verify_token"];

    if ($user['twofa_code'] == $otp) {

        if ($forgot_password) {
            // Clear OTP
            $db->update("users", [
                "twofa_code" => null,
                "twofa_expire" => null,
                "verify_status" =>  1,
                "verify_token" => $token,
            ], [
                "id" => $user['id']
            ]);

            echo json_encode([
                "status" => "success",
                "message" => "OTP verify successfully",
                "data" => [
                    "token" => $token
                ]
            ]);
        } else {
            // Clear OTP
            $db->update("users", [
                "twofa_code" => null,
                "twofa_expire" => null,
                "verify_status" =>  1,
                "verify_token" => "",
                "token" => $token
            ], [
                "id" => $user['id']
            ]);

            echo json_encode([
                "status" => "success",
                "message" => "OTP verify successfully",
                "data" => [
                    "token" => $token
                ]
            ]);
        }
        die;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "OTP does not match. Please check the code sent to your email and try again",
        ]);
    }
}

// Password Reset 
if (isset($data['reset_password'])) {
    $email = isset($data['email']) ? $data['email'] : "";

    if (empty($email)) {
        echo json_encode([
            "status" => "error",
            "message" => "Email is required",
        ]);
        die;
    }

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
    $raw_token = bin2hex(random_bytes(32));
    $token_hash = hash("sha256", $raw_token);

    // Save OTP + expiry (10 mins)
    $update = $db->update('users', [
        'twofa_code' => $otp,
        "verify_token" => $token_hash,
        "password" => ""
    ], ['id' => $user['id']]);

    if ($update) {

        // Send Email
        $_tc_email->send([
            'template' => 'sendOtp',
            // 'to'       => $email,
            'to'       => "haider.ali@intellectualbunch.com",
            // 'to'       => "sohailakbar3324@gmail.com",
            'subject'  => "Your Verification Code: $otp",
            'vars'     => [
                'OTP' => $otp
            ]
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "We sent you a 6-digit OTP. Please check your email",
            "data" => [
                "otp" => $otp,
                "token" => $token_hash
            ]
        ]);
    }
}


// Set Password 
if (isset($data['updatePassword'])) {

    // Get posted data
    $new_password = isset($data['password']) ? $data['password'] : "";
    $confirm_password = isset($data['confirm_password']) ? $data['confirm_password'] : "";
    $user =  validateBearerToken("verify_token");

    if (empty($new_password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Password is required",
        ]);
        die;
    }
    if (empty($confirm_password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Confirm Password is required",
        ]);
        die;
    }

    if (strlen($new_password) < 6) {
        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 6 characters long",
        ]);
        die;
    }

    // Validate input
    if ($new_password != $confirm_password) {
        echo json_encode([
            "status" => "error",
            "message" => "Passwords do not match",
        ]);
        exit;
    }

    if (!$user) {
        echo json_encode([
            "status" => "error",
            "message" => "User not found",
        ]);
        die();
    }

    // Update password
    $update = $db->update("users", [
        "password" => password_hash($new_password, PASSWORD_DEFAULT)
    ], ["id" => $user['id']]);

    if ($update) {
        echo json_encode([
            "status" => "success",
            "message" => "Password updated successfully. Please login now",
        ]);
        die();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update password",
        ]);
        die;
    }

    exit;
}


// Logout
if (isset($data['accountLogout'])) {
    $user =  validateBearerToken("verify_token");

    if (!$user) {
        echo json_encode([
            "status" => "error",
            "message" => "User not found",
        ]);
        die();
    }

    // Update password
    $update = $db->update("users", [
        "verify_token" => "",
        "token" => ""
    ], ["id" => $user['id']]);

    if ($update) {
        echo json_encode([
            "status" => "success",
            "message" => "Logout successfully",
        ]);
        die();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Something went wrong",
        ]);
        die;
    }

    exit;
}
