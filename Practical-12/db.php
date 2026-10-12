
<?php
$conn = new mysqli("localhost", "root", "", "events_db", 3306);

if ($conn->connect_error) {
    die("Database connection failed. Check MySQL and db.php settings.");
}

$conn->set_charset("utf8mb4");
?>