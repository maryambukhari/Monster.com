<?php
// db.php
$host = 'localhost';
$dbname = 'dbyaho67hgp841';
$username = 'uczrllawgyzfy';
$password = 'tmq3v2ylpxpl';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
