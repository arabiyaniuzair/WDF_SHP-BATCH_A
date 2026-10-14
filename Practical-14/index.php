<?php
require_once "auth.php";
requireAdmin();
require_once "db.php";

$usersResult = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalUsers = (int) $usersResult->fetch_assoc()["total"];
$totalStudents = $totalUsers;

$eventsResult = $eventsConn->query("SELECT COUNT(*) AS total FROM events");
$totalEvents = (int) $eventsResult->fetch_assoc()["total"];

$latest = $conn->query(
    "SELECT student_id, name, email
     FROM students
     ORDER BY student_id DESC
     LIMIT 5"
);

$auditMessage = "";

if (isset($_SESSION["dashboard_message"])) {
    $auditMessage = $_SESSION["dashboard_message"];
    unset($_SESSION["dashboard_message"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "menu.php"; ?>

    <main class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION["name"] ?? "Admin") ?></p>

        <?php if ($auditMessage !== ""): ?>
            <p class="message"><?= htmlspecialchars($auditMessage) ?></p>
        <?php endif; ?>

        <section class="stats">
            <div class="stat-card">
                <h2>Total Users</h2>
                <p><?= $totalUsers ?></p>
            </div>
            <div class="stat-card">
                <h2>Total Students</h2>
                <p><?= $totalStudents ?></p>
            </div>
            <div class="stat-card">
                <h2>Total Events</h2>
                <p><?= $totalEvents ?></p>
            </div>
        </section>

        <section class="panel">
            <h2>Latest Registrations</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($latest && $latest->num_rows > 0): ?>
                        <?php while ($student = $latest->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $student["student_id"]) ?></td>
                                <td><?= htmlspecialchars($student["name"] ?? "") ?></td>
                                <td><?= htmlspecialchars($student["email"] ?? "") ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No student registrations found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>