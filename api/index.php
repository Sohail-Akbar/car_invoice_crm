<?php
session_start();

// Display errors for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include_once  '../includes/inc/database.php';
include_once  './handlers/functions.php';

// Set the header for JSON response
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Get and sanitize the requested URI
$requestUri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

$endpoint = str_replace(API_PATH, '', $requestUri);
$endpoint = "/" . trim($endpoint, "/");
// Define routes
$routes = [
    '/' => __DIR__ . '/routes/welcome.php',
    '/login' => __DIR__ . '/routes/auth.php',
    '/register' => __DIR__ . '/routes/auth.php',
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
