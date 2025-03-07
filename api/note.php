<?php
// Ensure no output before headers
ob_start();

require __DIR__ . '/../db.php';

// Set JSON content type header
header('Content-Type: application/json; charset=UTF-8');

$db = new FileDB($config);

// List notes endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list'])) {
    try {
        $page = intval($_GET['page'] ?? 1);
        $sort = $_GET['sort'] ?? 'time';
        $max = intval($_GET['max'] ?? 6);
        
        $notes = $db->getNotes();
        
        // Transform notes to match JS format
        $transformedNotes = array_map(function($note) {
            return [
                'uuid' => $note['uuid'] ?? $note['filename'] ?? '3_frames.png',
                'filename' => $note['filename'] ?? '3_frames',
                'username' => $note['username'] ?? 'system',
                'timestamp' => $note['timestamp'] ?? date('Y-m-d H:i:s'),
                'rating' => intval($note['rating'] ?? 10),
                'locked' => boolval($note['locked'] ?? 0),
                'spinoff' => boolval($note['spinoff'] ?? 0),
                'author' => $note['author'] ?? 'system'
            ];
        }, $notes);

        // Sorting logic
        usort($transformedNotes, function($a, $b) use ($sort) {
            if ($sort === 'time') {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            } elseif ($sort === 'score') {
                return $b['rating'] - $a['rating'];
            }
            return 0;
        });
        
        // Pagination
        $total = max(1, ceil(count($transformedNotes) / $max));
        $skip = $max * ($page - 1);
        $result = array_slice($transformedNotes, $skip, $max);
        
        echo json_encode([
            'notes' => $result,
            'totalPages' => $total
        ], JSON_UNESCAPED_SLASHES);


        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'error' => 'Internal server error',
            'code' => 500
        ], JSON_UNESCAPED_SLASHES);
    }
    exit;
}

// Get note info endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['info'])) {
    try {
        $noteId = $_GET['info'];
        $notes = $db->getNotes();
        
        foreach ($notes as $note) {
            if ($note['uuid'] === $noteId || $note['filename'] === $noteId) {
                echo json_encode([
                    'uuid' => $note['uuid'] ?? $note['filename'] ?? 'unknown',
                    'filename' => $note['filename'] ?? 'unknown',
                    'username' => $note['username'] ?? 'system',
                    'timestamp' => $note['timestamp'] ?? date('Y-m-d H:i:s'),
                    'rating' => intval($note['rating'] ?? 0),
                    'locked' => boolval($note['locked'] ?? 0),
                    'spinoff' => boolval($note['spinoff'] ?? 0),
                    'author' => $note['author'] ?? 'system'
                ], JSON_UNESCAPED_SLASHES);


                exit;
            }
        }
        
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'error' => 'Note not found',
            'code' => 404
        ], JSON_UNESCAPED_SLASHES);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'error' => 'Internal server error',
            'code' => 500
        ], JSON_UNESCAPED_SLASHES);
    }
    exit;
}

// Download note endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download'])) {
    try {
        $noteId = $_GET['download'];
        
        // Ensure the file has .clip extension
        if (!str_ends_with($noteId, '.clip')) {
            $noteId .= '.clip';
        }

        $filePath = $config['upload_dir'] . '/' . $noteId;
        
        // Debug: Print file path
        error_log("Looking for file at: " . $filePath);
        
        // Check if directory exists and is readable
        if (!is_dir($config['upload_dir'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'error' => 'Notes directory not found',
                'code' => 500
            ], JSON_UNESCAPED_SLASHES);
            exit;
        }
        
        if (!is_readable($config['upload_dir'])) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'error' => 'Notes directory not readable',
                'code' => 500
            ], JSON_UNESCAPED_SLASHES);
            exit;
        }
        
        if (file_exists($filePath)) {


            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'error' => 'File not found',
                'code' => 404
            ], JSON_UNESCAPED_SLASHES);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'error' => 'Internal server error',
            'code' => 500
        ], JSON_UNESCAPED_SLASHES);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['thumbnail'])) {
    $filename = $_GET['thumbnail'];
    $filePath = $config['thumbnail_dir'] . "/" . $filename;


    if (file_exists($filePath)) {
        header("Location: " . $filePath);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Thumbnail not found']);
    }
}
