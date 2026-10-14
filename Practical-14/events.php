<?php
require_once "auth.php";
requireAdmin();
require_once "db.php";

$result = $eventsConn->query(
    "SELECT event_id, event_name, event_date, venue
     FROM events
     ORDER BY event_date DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "menu.php"; ?>

    <main class="container">
        <h1>Events</h1>

        <div class="panel table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Venue</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($event = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int) $event["event_id"] ?></td>
                            <td><?= htmlspecialchars($event["event_name"]) ?></td>
                            <td><?= htmlspecialchars($event["event_date"]) ?></td>
                            <td><?= htmlspecialchars($event["venue"]) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No events found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>