<?php
$host = "localhost";
$db   = "postgres";
$user = "postgres";
$pass = "sazid999";
$port = "5432";

try {

    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
