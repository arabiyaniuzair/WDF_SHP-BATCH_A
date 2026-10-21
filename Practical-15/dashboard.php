
<?php
require_once "auth.php";
requireRole("student");
require_once "db.php";

$id = (int)$_SESSION["student_id"];

$stmt = $conn->prepare(
    "SELECT enrollment_number, username, email, course, year
     FROM students WHERE student_id = ? LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    exit("Student record not found.");
}

function studentValue($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
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

<nav class="navbar" id="navbar">
    <a href="Home.html">Home</a>
    <a href="About.html">About</a>
    <a href="Dashboard.php">Student Dashboard</a>
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="../Events_Details/Event.php">Events</a>
    <a href="Profile.php">Profile</a>
    <a href="Contact.html">Contact</a>
    <a href="Faq.html">FAQ</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="student-summary">
    <h2>Student Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Details</th>
                <th>Information</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Enrollment Number</td><td><?= studentValue($student["enrollment_number"]) ?></td></tr>
            <tr><td>Username</td><td><?= studentValue($student["username"]) ?></td></tr>
            <tr><td>Email</td><td><?= studentValue($student["email"]) ?></td></tr>
            <tr><td>Department</td><td>Information Technology</td></tr>
            <tr><td>Course</td><td><?= studentValue($student["course"]) ?></td></tr>
            <tr><td>Year</td><td><?= studentValue($student["year"]) ?></td></tr>
        </tbody>
    </table>
</div>

</body>
</html>
