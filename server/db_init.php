<?php
$dbPath = __DIR__ . '/../messages.db'; 

if (file_exists($dbPath)) return;

$db = new PDO('sqlite:' . $dbPath);
$db->exec("
    CREATE TABLE messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender TEXT NOT NULL,
        message TEXT NOT NULL,
        timestamp INTEGER NOT NULL
    );
    CREATE INDEX idx_timestamp ON messages(timestamp);
");