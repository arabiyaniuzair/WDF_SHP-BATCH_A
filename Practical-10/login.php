 
<?php

require_once "auth.php";

if (isset($_SESSION["student_id"])) {
    redirectByRole();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit;
}

$identity = trim($_POST["identity"] ?? "");
$password = $_POST["password"] ?? "";

if ($identity === "" || $password === "") {
    exit('Enter your username/email and password. <a href="login.html">Try again</a>');
}

$stmt = $conn->prepare(
    "SELECT student_id, name, username, email, password, role
     FROM students
     WHERE username = ? OR email = ?
     LIMIT 1"
);

if (!$stmt) {
    error_log($conn->error);
    exit("A database error occurred. Please try again later.");
}

$stmt->bind_param("ss", $identity, $identity);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user["password"])) {
    exit('Invalid username/email or password. <a href="login.html">Try again</a>');
}

session_regenerate_id(true);

$_SESSION["student_id"] = $user["student_id"];
$_SESSION["name"] = $user["name"];
$_SESSION["role"] = $user["role"];
$_SESSION["last_activity"] = time();

if (($_POST["remember"] ?? "") === "yes") {
    setRememberToken($user["student_id"]);
} else {
    $stmt = $conn->prepare(
        "UPDATE students
         SET remember_selector = NULL,
             remember_token_hash = NULL,
             remember_expires = NULL
         WHERE student_id = ?"
    );

    $stmt->bind_param("i", $user["student_id"]);
    $stmt->execute();
    $stmt->close();

    clearRememberCookie();
}

redirectByRole();
?>
 