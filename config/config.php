<?php
return [
    'root_dir' => dirname(__DIR__),
    'version' => '1.0.0',
    'port' => 8080,

    'upload_dir' => dirname(__DIR__) . '/data/notes',
    'thumbnail_dir' => dirname(__DIR__) . '/data/thumbnails',
    'upload_size_limit' => 10485760, // 10MB
    'notes_file' => dirname(__DIR__) . '/data/notes.txt',
    'users_file' => dirname(__DIR__) . '/data/users.txt',


    'serverTitle' => 'Clipnote PHP Server',
    'serverMOTD' => 'Welcome to Clipnote PHP Server',
    'signupURL' => 'http://mtboss.ddns.net:8080/exp/clipnote/theater/'
];

?>