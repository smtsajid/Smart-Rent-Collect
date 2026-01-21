<?php
//$host = "localhost";
//$port = "5432";
//$dbname = "postgres";
//$dbuser = "postgres";
//$dbpass = "sazid999";


$host = "dpg-d5l9lcuid0rc73eecu8g-a.oregon-postgres.render.com";
$db   = "smt";
$user = "smt_m23m_user";
$pass = "uoIAfRlJV34YSidtgS6l8G78lHl7gJNi";
$port = "5432";

$conn = pg_connect(
    "host=$host port=$port dbname=$db user=$user password=$pass"
);

if (!$conn) {
    die("PostgreSQL connection failed");
}
