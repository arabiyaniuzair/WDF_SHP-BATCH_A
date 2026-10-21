
<?php
require_once "auth.php";
requireRole("admin");
require_once "db.php";

$totalStudents = 0;
$totalAdmins = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM students WHERE role = 'student'"
);
if ($result) {
    $totalStudents = (int)$result->fetch_assoc()["total"];
}

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM students WHERE role = 'admin'"
);
if ($result) {
    $totalAdmins = (int)$result->fetch_assoc()["total"];
}

$adminName = htmlspecialchars(
    $_SESSION["name"] ?? "Admin",
    ENT_QUOTES,
    "UTF-8"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StudentHub</title>
    <link rel="stylesheet" href="Admin.css">
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

<div class="admin">
    <h1>Welcome, <?= $adminName ?>!</h1>
    <p>Welcome to the StudentHub Administration Panel.</p>
    <p>Manage students, events, notices and feedback from here.</p>
</div>

<div class="summary">
    <h2>Portal Summary</h2>
    <div class="stats">
        <div class="stat-card">
            <h3>Registered Students</h3>
            <p><?= $totalStudents ?></p>
        </div>
        <div class="stat-card">
            <h3>Admin Accounts</h3>
            <p><?= $totalAdmins ?></p>
        </div>
    </div>
</div>

<div class="services">
    <h2>Admin Services</h2>
    <div class="service-links">
        <a href="student_management.php">Manage Student Records</a>
        <a href="../Events_Details/Event.php">Manage Events</a>
        <a href="Faq.html">View FAQs</a>
        <a href="notice_management.php">Manage Notices</a>
        <a href="feedback_management.php">View Feedback</a>
    </div>
</div>

<div class="notice">
    <h2>Latest Notices</h2>
    <p>Use Manage Notices to publish academic announcements.</p>
    <a class="button" href="notice_management.php">Manage Notices</a>
</div>

<div class="activity">
    <h2>Administration</h2>
    <p>Use the management links above to maintain StudentHub.</p>
</div>

</body>
</html>
