
<?php
require "db.php";

$result = $conn->query(
    "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC"
);
?>
<!DOCTYPE html>
<html>
<head>
    <title>University Events</title>
    <link rel="stylesheet" href="Event.css">
</head>
<body>
    <div class="navbar">
        <a href="Event.php">Events</a>
        <a href="events.php">Manage Events</a>
    </div>

    <div class="events">
        <h1>University Events</h1>
        <p>View upcoming university events.</p>
    </div>

    <div class="upcoming">
        <h2>Upcoming Events</h2>
        <table>
            <tr>
                <th>Poster</th>
                <th>Event Name</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Description</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($event["poster"])): ?>
                                <img class="poster"
                                     src="uploads/<?= htmlspecialchars($event["poster"]) ?>"
                                     alt="Event poster">
                            <?php else: ?>
                                No poster
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($event["event_name"]) ?></td>
                        <td><?= htmlspecialchars($event["event_date"]) ?></td>
                        <td><?= htmlspecialchars($event["venue"]) ?></td>
                        <td><?= nl2br(htmlspecialchars($event["description"] ?? "")) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No upcoming events found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
