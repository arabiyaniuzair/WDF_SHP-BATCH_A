
<?php
$conn = new mysqli("localhost", "root", "", "studenthub", 3306);

if ($conn->connect_error) {
    die("StudentHub database connection failed.");
}

$conn->set_charset("utf8mb4");

$eventsConn = new mysqli("localhost", "root", "", "events_db", 3306);

if ($eventsConn->connect_error) {
    die("Events database connection failed.");
}

$eventsConn->set_charset("utf8mb4");
?>