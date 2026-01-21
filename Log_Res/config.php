<?php
//$host = "localhost";
//$db   = "postgres";
//$user = "postgres";
//$pass = "sazid999";
//$port = "5432";


$host = "dpg-d5l9lcuid0rc73eecu8g-a.oregon-postgres.render.com";
$db   = "smt";
$user = "smt_m23m_user";
$pass = "uoIAfRlJV34YSidtgS6l8G78lHl7gJNi";
$port = "5432";


try {
    // Create PDO connection for PostgreSQL
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
