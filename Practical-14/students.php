<?php
require_once "auth.php";
requireAdmin();
require_once "db.php";

$result = $conn->query(
    "SELECT student_id, name, username, email, role
     FROM students
     ORDER BY student_id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "menu.php"; ?>

    <main class="container">
        <h1>Students</h1>

        <div class="panel table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($student = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int) $student["student_id"] ?></td>
                            <td><?= htmlspecialchars($student["name"] ?? "") ?></td>
                            <td><?= htmlspecialchars($student["username"] ?? "") ?></td>
                            <td><?= htmlspecialchars($student["email"] ?? "") ?></td>
                            <td><?= htmlspecialchars($student["role"] ?? "student") ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No students found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>