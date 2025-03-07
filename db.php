<?php
$config = require __DIR__ . '/config/config.php';

class FileDB {
    private $notesFile;
    private $usersFile;

    public function __construct($config) {
        $this->notesFile = $config['notes_file'];
        $this->usersFile = $config['users_file'];

        // Ensure directories exist
        $notesDir = dirname($this->notesFile);
        $usersDir = dirname($this->usersFile);
        
        if (!file_exists($notesDir)) {
            mkdir($notesDir, 0777, true);
        }
        if (!file_exists($usersDir)) {
            mkdir($usersDir, 0777, true);
        }

        $this->initializeFiles();
    }

    private function initializeFiles() {
        if (!file_exists($this->notesFile) || !is_file($this->notesFile)) {
            // Add a dummy clipnote
            $dummyNote = [
                'uuid' => 'dummy-note',
                'filename' => 'dummy.clip',
                'username' => 'system',
                'timestamp' => date('Y-m-d H:i:s'),
                'rating' => 0,
                'locked' => 0,
                'spinoff' => 0,
                'author' => 'system'
            ];
            file_put_contents($this->notesFile, json_encode([$dummyNote]));
        }

        if (!file_exists($this->usersFile) || !is_file($this->usersFile)) {
            file_put_contents($this->usersFile, json_encode([]));
        }
    }

    public function getNotes() {
        return json_decode(file_get_contents($this->notesFile), true);
    }

    public function saveNotes($notes) {
        file_put_contents($this->notesFile, json_encode($notes));
    }

    public function insertNote($filename, $username) {
        $notes = $this->getNotes();
        $notes[] = [
            'filename' => $filename,
            'username' => $username,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        return $this->saveNotes($notes);
    }

    public function getUsers() {
        return json_decode(file_get_contents($this->usersFile), true);
    }

    public function saveUsers($users) {
        file_put_contents($this->usersFile, json_encode($users));
    }

    public function saveUser($userData) {
        $users = $this->getUsers();
        
        // Check if user already exists
        foreach ($users as $user) {
            if ($user['username'] === $userData['username']) {
                return false;
            }
        }
        
        // Add new user
        $users[] = $userData;
        return $this->saveUsers($users);
    }

    public function getUser($username) {
        $users = $this->getUsers();
        
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        
        return null;
    }
}

// Initialize FileDB with config
$db = new FileDB($config);
