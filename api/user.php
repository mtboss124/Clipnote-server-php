<?php
require __DIR__ . '/../db.php';
header('Content-Type: application/json');

$db = new FileDB($config);

// --- PUBLIC REGISTRATION ENDPOINT (no auth required) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    // Validate username
    if (!preg_match('/^[A-Za-z0-9-_]{1,25}$/', $_POST['username'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Username must be 1-25 characters and can only contain letters, numbers, underscore and dash']);
        exit;
    }

    // Validate permissions (optional — default to 0 if not set)
    $permissions = isset($_POST['permissions']) && preg_match('/^[0-3]$/', $_POST['permissions'])
        ? (int)$_POST['permissions']
        : 0;

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prevent duplicate users
    if ($db->getUser($username)) {
        http_response_code(409);
        echo json_encode(['error' => 'Username already exists']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // User object
    $userData = [
        'username' => $username,
        'hash' => $hashedPassword,
        'permissions' => $permissions,
        'stars' => 0,
        'joinDate' => date('Y-m-d H:i:s'),
        'lastLogin' => null,
        'ban' => null
    ];

    // Save to database
    if ($db->saveUser($userData)) {
        http_response_code(201);
        echo json_encode(['message' => 'User created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'User creation failed or not try to log in']);
    }
    exit;
}

// --- AUTHENTICATION FOR PROTECTED ENDPOINTS ---
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Clipnote API"');
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$username = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];

// Get user
$user = $db->getUser($username);
if (!$user || !password_verify($password, $user['hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// --- GET USER PROFILE ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['username'])) {
    $targetUser = $_GET['username'];
    $targetData = $db->getUser($targetUser);

    if ($targetData) {
        echo json_encode([
            'status' => 'success',
            'data' => $targetData
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

// --- FILE UPLOAD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    try {
        $uploadDir = $config['upload_dir'];
        $thumbnailDir = $config['thumbnail_dir'];
        $uuid = uniqid();

        // Create dirs if they don't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (!file_exists($thumbnailDir)) {
            mkdir($thumbnailDir, 0777, true);
        }

        $uploadFile = $uploadDir . '/' . $uuid . '.clip';
        $thumbnailFile = $thumbnailDir . '/' . $uuid . '.png';

        // Get original file name
        $originalName = $_FILES['file']['name'];

        // Check extension first
        if (!preg_match('/\.(zip|clip)$/i', $originalName)) {
            http_response_code(400);
            echo json_encode(['error' => 'Only .zip or .clip files are allowed']);
            exit;
        }

        // Move uploaded file
        error_log("TMP FILE: " . $_FILES['file']['tmp_name']);
error_log("UPLOAD TARGET: " . $uploadFile);
error_log("UPLOAD DIR WRITABLE: " . (is_writable(dirname($uploadFile)) ? "yes" : "no"));
error_log("TMP FILE EXISTS: " . (file_exists($_FILES['file']['tmp_name']) ? "yes" : "no"));

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            error_log('Failed to move uploaded file: ' . $originalName);
            http_response_code(500);
            echo json_encode(['error' => 'Could not move uploaded file']);
            exit;
        }

        $zip = new ZipArchive;
        if ($zip->open($uploadFile) === TRUE) {
            $validFiles = true;
            $validFrames = false;
            $thumbnail = false;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (!preg_match('/\.(png|ogg|ini)$/i', $entry)) {
                    $validFiles = false;
                    error_log("Invalid file inside zip: $entry");
                    break;
                }
                if (strpos($entry, '0,') !== false && preg_match('/\.png$/i', $entry)) {
                    $validFrames = true;
                }
                if ($entry === 'thumb.png') {
                    $thumbnailData = $zip->getFromName($entry);
                    if ($thumbnailData !== false) {
                        file_put_contents($thumbnailFile, $thumbnailData);
                        $thumbnail = true;
                    } else {
                        error_log("Failed to extract thumb.png");
                    }
                }
            }

            $zip->close();

            if ($validFiles && $validFrames) {
                $db->insertNote($uuid, $username);
                http_response_code(200);
                echo json_encode(['message' => 'File uploaded successfully']);
            } else {
                if ($thumbnail && file_exists($thumbnailFile)) {
                    unlink($thumbnailFile);
                }
                unlink($uploadFile);
                http_response_code(400);
                echo json_encode(['error' => 'Invalid file contents']);
            }

        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid zip file']);
        }
    } catch (Exception $e) {
        error_log("Upload error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// --- DEFAULT 405 HANDLER ---
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
