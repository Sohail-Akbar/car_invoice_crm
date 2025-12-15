<?php
session_start();
if (!defined('_DIR_')) define('_DIR_', '../');

// Display errors for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once   "../vendor/autoload.php";
include_once  '../includes/inc/database.php';
include_once  './handlers/functions.php';
require_once "../includes/Classes/SmtpMailer.php";

// Set the header for JSON response
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Get and sanitize the requested URI
$requestUri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

$endpoint = str_replace(API_PATH, '', $requestUri);
$endpoint = "/" . trim($endpoint, "/");

// Attach Values
if ($endpoint === "/login") {
    $_POST['login'] = true;
} else if ($endpoint === "/twofa") {
    $_POST['verify_otp'] = true;
} else if ($endpoint === "/forgot") {
    $_POST['reset_password'] = true;
} else if ($endpoint === "/set_password") {
    $_POST['updatePassword'] = true;
} else if ($endpoint === "/logout") {
    $_POST['accountLogout'] = true;
}

// Define routes
$routes = [
    '/' => __DIR__ . '/routes/welcome.php',
    '/login' => __DIR__ . '/routes/auth.php',
    '/twofa' => __DIR__ . '/routes/auth.php',
    '/forgot' => __DIR__ . '/routes/auth.php',
    '/set_password' => __DIR__ . '/routes/auth.php',
    '/logout' => __DIR__ . '/routes/auth.php',
];

// Switch case for routing
switch (true) {
    case isset($routes[$endpoint]) && file_exists($routes[$endpoint]):
        require_once $routes[$endpoint];
        break;

    case isset($routes[$endpoint]) && !file_exists($routes[$endpoint]):
        sendErrorResponse("Route file not found: {$endpoint}");
        break;

    default:
        sendErrorResponse("Endpoint not found");
}

// Helper function to send error responses
function sendErrorResponse($message, $statusCode = 404)
{
    http_response_code($statusCode);
    echo json_encode([
        "status" => "error",
        "message" => $message
    ]);
    exit;
}
