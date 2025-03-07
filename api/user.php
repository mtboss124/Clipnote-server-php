<?php
require __DIR__ . '/../db.php';
header('Content-Type: application/json');

// Basic Authentication
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Clipnote API"');
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$username = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];

$db = new FileDB($config);

// Verify user credentials
$user = $db->getUser($username);
if (!$user || !password_verify($password, $user['hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// User sign-up endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    // Validate username
    if (!preg_match('/^[A-Za-z0-9-_]{1,25}$/', $_POST['username'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username must be 1-25 characters and can only contain letters, numbers, underscore and dash']);
        exit;
    }

    // Validate permissions
    if (!isset($_POST['permissions']) || !preg_match('/^[0-3]$/', $_POST['permissions'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid permissions level (0-3)']);
        exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    $permissions = (int)$_POST['permissions'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Create user object
    $userData = [
        'username' => $username,
        'hash' => $hashedPassword,
        'permissions' => $permissions,
        'stars' => 0,
        'joinDate' => date('Y-m-d H:i:s'),
        'lastLogin' => null,
        'ban' => null
    ];

    // Save user to the database
    $result = $db->saveUser($userData);

    if ($result) {
        http_response_code(201);
        echo json_encode(['message' => 'User created successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'User creation failed']);
    }
    exit;
}

// User profile retrieval endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['username'])) {
    $username = $_GET['username'];
    $user = $db->getUser($username);
    if ($user) {
        echo json_encode([
            'status' => 'success',
            'data' => $user
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'error' => 'User not found',
            'data' => null
        ]);
    }

    exit;
}

// File upload endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = $config['upload_dir'];
    $thumbnailDir = $config['thumbnail_dir'];
    $uuid = uniqid();
    
    // Create directories if they don't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!file_exists($thumbnailDir)) {
        mkdir($thumbnailDir, 0777, true);
    }

    $uploadFile = $uploadDir . '/' . $uuid;
    $thumbnailFile = $thumbnailDir . '/' . $uuid . '.png';

    // Move uploaded file and validate file type
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile) && preg_match('/\.(zip|clip)$/', $_FILES['file']['name'])) {
        // Validate file contents
        $zip = new ZipArchive;
        if ($zip->open($uploadFile) === TRUE) {
            $validFiles = true;
            $validFrames = false;
            $thumbnail = false;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (!preg_match('/\.(png|ogg|ini)$/', $entry)) {
                    $validFiles = false;
                    break;
                }
                if (strpos($entry, '0,') !== false && preg_match('/\.png$/', $entry)) {
                    $validFrames = true;
                }
                if ($entry === 'thumb.png') {
                    $thumbnail = true;
                    file_put_contents($thumbnailFile, $zip->getFromName($entry));
                }
            }

            if ($validFiles && $validFrames) {
                // Save note to database
                $db->insertNote($uuid, $username);
                http_response_code(200);
                echo json_encode(['message' => 'File uploaded successfully']);
            } else {
                // Clean up invalid upload
                if ($thumbnail) {
                    unlink($thumbnailFile);
                }
                unlink($uploadFile);
                http_response_code(400);
                echo json_encode(['error' => 'Invalid file contents']);
            }
            $zip->close();
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid zip file']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'File upload failed']);
    }
    exit;
}

// Default response for unsupported methods
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
