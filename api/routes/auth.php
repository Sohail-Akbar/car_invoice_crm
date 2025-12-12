<?php

$data = json_decode(file_get_contents("php://input"), true);
$data = $_POST;

// login with use react
if (isset($data['login'])) {
    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['password']) ? trim($data['password']) : '';

    if (empty($email) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Email and password are required"
        ]);
        exit;
    }

    $users = $db->select_one("users", "*", ["email" => $email]);
    if ($users) {
        if (password_verify($password, $users['password'])) {
            $token = bin2hex(random_bytes(16));
            $db->update("users", ["token" => $token], ["id" => $users['id']]);

            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "data" => [
                    "user_id" => $users['id'],
                    "username" => $users['username'],
                    "email" => $users['email'],
                    "token" => $token
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid password"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
    }
}

// register
if (isset($data['register'])) {
    $username = isset($data['username']) ? trim($data['username']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['password']) ? trim($data['password']) : '';

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Username, email, and password are required"
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email format"
        ]);
        exit;
    }

    $existingUser = $db->select_one("users", "*", ["email" => $email]);
    if ($existingUser) {
        echo json_encode([
            "status" => "error",
            "message" => "Email already registered"
        ]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(16));
    $userId = $db->insert("users", [
        "username" => $username,
        "email" => $email,
        "password" => $hashedPassword,
        "token" => $token
    ]);

    if ($userId) {
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful",
            "data" => [
                "user_id" => $userId,
                "username" => $username,
                "email" => $email,
                "token" => $token
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Registration failed"
        ]);
    }
}


// user info 
if (isset($data['user_info'])) {
    $token = isset($data['token']) ? trim($data['token']) : '';

    if (empty($token)) {
        echo json_encode([
            "status" => "error",
            "message" => "Token is required"
        ]);
        exit;
    }

    $users = $db->select_one("users", "*", ["token" => $token]);
    if ($users) {
        echo json_encode([
            "status" => "success",
            "message" => "User info retrieved successfully",
            "data" => [
                "user_id" => $users['id'],
                "username" => $users['username'],
                "email" => $users['email']
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid token"
        ]);
    }
}


// update profile
if (isset($data['update_profile'])) {
    $token = isset($data['token']) ? trim($data['token']) : '';
    $username = isset($data['username']) ? trim($data['username']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';

    if (empty($token) || empty($username) || empty($email)) {
        echo json_encode([
            "status" => "error",
            "message" => "Token, username, and email are required"
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email format"
        ]);
        exit;
    }

    $users = $db->select_one("users", "*", ["token" => $token]);
    if ($users) {
        $existingUser = $db->select_one("users", "*", ["email" => $email, "id[!]" => $users['id']]);
        if ($existingUser) {
            echo json_encode([
                "status" => "error",
                "message" => "Email already in use by another account"
            ]);
            exit;
        }

        $db->update("users", [
            "username" => $username,
            "email" => $email
        ], ["id" => $users['id']]);

        echo json_encode([
            "status" => "success",
            "message" => "Profile updated successfully",
            "data" => [
                "user_id" => $users['id'],
                "username" => $username,
                "email" => $email
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid token"
        ]);
    }
}
