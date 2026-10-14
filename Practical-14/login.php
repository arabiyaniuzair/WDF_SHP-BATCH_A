```php
<?php
require_once "auth.php";

if (isset($_SESSION["student_id"])) {
    redirectByRole();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: Login.html");
    exit;
}

$identity = strtoupper(trim($_POST["identity"] ?? ""));
$password = $_POST["password"] ?? "";

if ($identity === "" || $password === "") {
    exit('Enter your enrollment number and password. <a href="Login.html">Try again</a>');
}

$stmt = $conn->prepare(
    "SELECT student_id, name, username, email, enrollment_number, password, role
     FROM students
     WHERE UPPER(enrollment_number) = ?
     LIMIT 1"
);

if (!$stmt) {
    error_log($conn->error);
    exit("A database error occurred. Please try again later.");
}

$stmt->bind_param("s", $identity);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user["password"])) {
    exit('Invalid enrollment number or password. <a href="Login.html">Try again</a>');
}

session_regenerate_id(true);

$_SESSION["student_id"] = $user["student_id"];
$_SESSION["name"] = $user["name"];
$_SESSION["enrollment_number"] = strtoupper($user["enrollment_number"]);
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
exit;
?>
```