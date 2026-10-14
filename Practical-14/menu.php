<?php
$role = strtolower((string) ($_SESSION["role"] ?? "student"));
?>
<nav class="navbar">
    <a href="index.php">Dashboard</a>

    <?php if ($role === "admin"): ?>
        <a href="students.php">Students</a>
        <a href="events.php">Events</a>
        <a href="audit.php">Audit Log</a>
    <?php else: ?>
        <a href="../Uzair_Register/Profile.html">Profile</a>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</nav>