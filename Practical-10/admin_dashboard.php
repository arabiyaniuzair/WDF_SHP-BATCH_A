<?php

require_once "auth.php";
requireRole("admin");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>

    <p>Welcome, <?= htmlspecialchars($_SESSION["name"], ENT_QUOTES, "UTF-8") ?>!</p>
    <p>You are logged in as an administrator.</p>

    <a href="logout.php">Logout</a>
</body>
</html>