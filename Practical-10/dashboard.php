
<?php

require_once "auth.php";

if (!isset($_SESSION["student_id"])) {
    header("Location: login.html");
    exit;
}

if ($_SESSION["role"] !== "student") {
    redirectByRole();
    exit;
}

$stmt = $conn->prepare(
    "SELECT name, username, email, course, year
     FROM students
     WHERE student_id = ?"
);

$stmt->bind_param("i", $_SESSION["student_id"]);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit;
}

$_SESSION["last_activity"] = time();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - StudentHub</title>
    <link rel="stylesheet" href="Dashboard.css">
</head>
<body>

<div class="navbar">
    <a href="Home.html">Home</a>
    <a href="Dashboard.html">Dashboard</a>
    <a href="Profile.html">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="dashboard">
    <h1>Welcome, <?= htmlspecialchars($student["name"]) ?></h1>
    <p>You have successfully logged in to StudentHub.</p>

    <h2>Student Details</h2>

    <p><strong>Username:</strong> <?= htmlspecialchars($student["username"]) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($student["email"]) ?></p>
    <p><strong>Course:</strong> <?= htmlspecialchars($student["course"]) ?></p>
    <p><strong>Year:</strong> <?= htmlspecialchars((string)$student["year"]) ?></p>

    
</div>

</body>
</html>
