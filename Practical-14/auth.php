
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["student_id"]) || !isset($_SESSION["role"])) {
    header("Location: ../Uzair_Register/Login.html");
    exit;
}

function requireAdmin() {
    if (strtolower((string) $_SESSION["role"]) !== "admin") {
        http_response_code(403);
        exit("Access denied. Admin users only.");
    }
}
?>
