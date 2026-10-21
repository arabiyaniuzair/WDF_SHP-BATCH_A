
<?php
require_once "db.php";
require_once "auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identity = trim($_POST["identity"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT student_id, name, username, email, enrollment_number, password, role
         FROM students
         WHERE enrollment_number = ? OR username = ? OR email = ?
         LIMIT 1"
    );

    $stmt->bind_param("sss", $identity, $identity, $identity);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);

        $_SESSION["student_id"] = (int)$user["student_id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["enrollment_number"] = $user["enrollment_number"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["last_activity"] = time();

        if ($user["role"] === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: Dashboard.php");
        }
        exit;
    }

    $error = "Invalid login details.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StudentHub</title>
    <link rel="stylesheet" href="Login.css">
</head>
<body>

<nav class="navbar" id="navbar">
    <a href="Home.html">Home</a>
    <a href="About.html">About</a>
    <a href="Dashboard.php">Student Dashboard</a>
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="../Events_Details/Event.php">Events</a>
    <a href="Profile.php">Profile</a>
    <a href="Contact.html">Contact</a>
    <a href="Faq.html">FAQ</a>
    <a href="Register.html">Register</a>
    <a href="Login.html">Login</a>
</nav>

<h1>StudentHub Login</h1>

<?php if ($error !== ""): ?>
    <p><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
<?php endif; ?>

<form action="login.php" method="post">
    <label for="identity">Enrollment Number, Username or Email</label>
    <input type="text" id="identity" name="identity" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>

</body>
</html>
