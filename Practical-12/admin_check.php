
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["role"]) || strtolower($_SESSION["role"]) !== "admin") {
    http_response_code(403);
    exit("Access denied. Please log in with an admin account.");
}
?>
