<?php
// створення бд якщо не існує, структура бд
$dsn = 'sqlite:' . __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            select_service TEXT,
            select_price TEXT,
            comments TEXT,
            fbp TEXT,
            ggl TEXT,
            country TEXT DEFAULT 'Невідомо',
            city TEXT DEFAULT 'Невідомо',
            ip TEXT DEFAULT 'Невідомо',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    die("Connection error: " . $e->getMessage());
}
