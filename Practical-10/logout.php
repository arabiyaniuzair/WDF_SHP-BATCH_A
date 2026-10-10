<?php

require_once "auth.php";

if (isset($_SESSION["student_id"])) {
    $stmt = $conn->prepare(
        "UPDATE students
         SET remember_selector = NULL,
             remember_token_hash = NULL,
             remember_expires = NULL
         WHERE student_id = ?"
    );

    $stmt->bind_param("i", $_SESSION["student_id"]);
    $stmt->execute();
    $stmt->close();
}

clearRememberCookie();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 3600,
        "path" => $params["path"],
        "domain" => $params["domain"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => "Lax"
    ]);
}

session_destroy();

header("Location: Login.html");
exit;

?>