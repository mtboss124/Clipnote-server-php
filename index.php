<?php
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

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Improved routing with middleware-like structure
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/';

// Remove base path and trailing slashes
$route = trim(substr($requestUri, strlen($basePath)), '/');

// Split route into parts
$routeParts = explode('/', $route);
$mainRoute = $routeParts[0] ?? '';

// Route to appropriate endpoint
switch ($mainRoute) {
    case 'info':
        require $config['root_dir'] . '/api/info.php';
        break;
    case 'note':
        // Handle /note/list specifically
        if (isset($routeParts[1]) && $routeParts[1] === 'list') {
            // Forward query parameters to note.php
            $_GET['list'] = true;
        }
        // Handle /note/download specifically
        if (isset($routeParts[1]) && $routeParts[1] === 'download' && isset($routeParts[2])) {
            $_GET['download'] = $routeParts[2];
        }
        require $config['root_dir'] . '/api/note.php';
        break;


    case 'user':
        require $config['root_dir'] . '/api/user.php';
        break;
    case '':

        // Handle root path
        http_response_code(200);
        echo json_encode([
            'status' => 'running',
            'version' => $config['version'],
            'port' => $config['port']
        ]);


    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        echo __DIR__;
        exit;
}
