<?php

$host = "localhost";
$db   = "crud_example";
$user = "root";   // default XAMPP user
$pass = "";       // default XAMPP password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

