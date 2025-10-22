<?php
$host = '127.0.0.1';
$db = 'laravel_p2e';
$user = 'root';
$pass = 'root';
$port = 8889; // <- updated to MAMP default port

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    echo "✅ Connected successfully to MySQL.";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
