
<?php
require "admin_check.php";
require "db.php";

$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Events</title>
    <link rel="stylesheet" href="Event.css">
</head>
<body>
    <div class="form-container">
        <h1>Manage Events</h1>

        <?php if (isset($_GET["message"])): ?>
            <p class="message"><?= htmlspecialchars($_GET["message"]) ?></p>
        <?php endif; ?>

        <a class="btn" href="add_event.php">Add New Event</a>
        <a class="btn" href="Event.php">Public Events</a>

        <table>
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Name</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Actions</th>
            </tr>

            <?php while ($event = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$event["event_id"] ?></td>
                    <td>
                        <?php if (!empty($event["poster"])): ?>
                            <img class="poster"
                                 src="uploads/<?= htmlspecialchars($event["poster"]) ?>"
                                 alt="Poster">
                        <?php else: ?>
                            No poster
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($event["event_name"]) ?></td>
                    <td><?= htmlspecialchars($event["event_date"]) ?></td>
                    <td><?= htmlspecialchars($event["venue"]) ?></td>
                    <td>
                        <a class="btn" href="edit_event.php?id=<?= (int)$event["event_id"] ?>">Edit</a>

                        <form method="post" action="delete_event.php"
                              onsubmit="return confirm('Delete this event?');">
                            <input type="hidden" name="id" value="<?= (int)$event["event_id"] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
