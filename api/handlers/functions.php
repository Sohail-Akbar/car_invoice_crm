<?php
// Helper function for Bearer Token validation
function validateBearerToken($column_name = "token")
{
    global $db;
    $headers = getallheaders();
    // Check Authorization header
    if (!isset($headers['Authorization'])) {
        sendErrorResponse("Authorization header is required", 401);
    }

    $authHeader = $headers['Authorization'];
    if (strpos($authHeader, 'Bearer ') !== 0) {
        sendErrorResponse("Invalid Authorization format. Expected 'Bearer <token>'", 401);
    }

    // Extract token
    $token = substr($authHeader, 7);

    // Check token in the database
    $user = $db->select_one('users', '*', [$column_name => $token]);
    if (!$user) {
        sendErrorResponse("Invalid or expired token", 403);
    }

    return $user;
}


function validateEmail($email)
{
    // Check if the email is valid
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}
