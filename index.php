<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration
$config = require __DIR__ . '/config/config.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Create data directories if they don't exist
if (!file_exists($config['upload_dir'])) {
    mkdir($config['upload_dir'], 0777, true);
}
if (!file_exists($config['thumbnail_dir'])) {
    mkdir($config['thumbnail_dir'], 0777, true);
}

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Dynamically detect base path (useful when hosted in a subdirectory)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptName, '/');

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path from request URI to get route
$route = trim(preg_replace("#^$basePath#", '', $requestUri), '/');

// Split route into parts
$routeParts = explode('/', $route);
$mainRoute = $routeParts[0] ?? '';

// Debug log (optional – check Apache's error.log)
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Base Path: $basePath");
error_log("Resolved Route: $route");

// Routing logic
switch ($mainRoute) {
    case 'info':
        require $config['root_dir'] . '/api/info.php';
        break;

    case 'note':
        if (isset($routeParts[1])) {
            if ($routeParts[1] === 'list') {
                $_GET['list'] = true;
            } elseif ($routeParts[1] === 'download' && isset($routeParts[2])) {
                $_GET['download'] = $routeParts[2];
            } elseif ($routeParts[1] === 'thumbnail' && isset($routeParts[2])) {
                $_GET['thumbnail'] = $routeParts[2];
            } elseif ($routeParts[1] === 'info' && isset($routeParts[2])) {
                $_GET['info'] = $routeParts[2];
            }
        }
        
        require $config['root_dir'] . '/api/note.php';
        break;

    case 'user':
        require $config['root_dir'] . '/api/user.php';
        break;

    case '':
        http_response_code(200);
        echo json_encode([
            'status' => 'running',
            'version' => $config['version'],
            'port' => $config['port']
        ]);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
