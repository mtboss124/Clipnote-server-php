<?php
$config = require __DIR__ . '/../config/config.php';

// Handle root info endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && 
    strpos($_SERVER['REQUEST_URI'], '/info/icon') === false &&
    strpos($_SERVER['REQUEST_URI'], '/info/header') === false) {
    header('Content-Type: application/json');
    echo json_encode([
        'title' => $config['serverTitle'],
        'motd' => $config['serverMOTD'],
        'version' => $config['version'],
        'signup' => $config['signupURL']
    ]);
    exit;
}

// Handle icon endpoint
if (strpos($_SERVER['REQUEST_URI'], '/info/icon') !== false) {
    header('Content-Type: image/png');
    readfile($config['root_dir'] . '/icon.png');
    exit;
}

// Handle header endpoint
if (strpos($_SERVER['REQUEST_URI'], '/info/header') !== false) {
    header('Content-Type: image/png');
    readfile($config['root_dir'] . '/header.png');
    exit;
}

// Default 404 response
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
