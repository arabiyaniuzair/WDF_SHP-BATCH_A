
<?php
require "admin_check.php";
require "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    exit("Invalid event ID.");
}

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    exit("Event not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["event_name"] ?? "");
    $date = $_POST["event_date"] ?? "";
    $venue = trim($_POST["venue"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $poster = $event["poster"];
    $newPoster = null;

    if ($name === "" || $date === "" || $venue === "") {
        $error = "Name, date, and venue are required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ||
        !checkdate((int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4))) {
        $error = "Enter a valid date.";
    } elseif (mb_strlen($name) > 150 || mb_strlen($venue) > 150) {
        $error = "Name and venue must be 150 characters or fewer.";
    } elseif (isset($_FILES["poster"]) && $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES["poster"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $error = "Poster upload failed.";
        } elseif ($file["size"] > 2097152) {
            $error = "Poster must be 2 MB or smaller.";
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
            $types = [
                "image/jpeg" => "jpg",
                "image/png" => "png",
                "image/webp" => "webp"
            ];

            if (!isset($types[$mime]) || getimagesize($file["tmp_name"]) === false) {
                $error = "Only JPG, PNG, and WebP images are allowed.";
            } else {
                if (!is_dir(__DIR__ . "/uploads") &&
                    !mkdir(__DIR__ . "/uploads", 0755, true)) {
                    $error = "Could not create uploads folder.";
                } else {
                    $newPoster = bin2hex(random_bytes(16)) . "." . $types[$mime];

                    if (move_uploaded_file($file["tmp_name"], __DIR__ . "/uploads/" . $newPoster)) {
                        $poster = $newPoster;
                    } else {
                        $error = "Could not save poster.";
                        $newPoster = null;
                    }
                }
            }
        }
    }

    if ($error === "") {
        $stmt = $conn->prepare(
            "UPDATE events SET event_name=?, event_date=?, venue=?, description=?, poster=? WHERE event_id=?"
        );
        $stmt->bind_param("sssssi", $name, $date, $venue, $description, $poster, $id);

        if ($stmt->execute()) {
            if ($newPoster && $event["poster"]) {
                $old = __DIR__ . "/uploads/" . basename($event["poster"]);
                if (is_file($old)) {
                    unlink($old);
                }
            }

            header("Location: events.php?message=" . urlencode("Event updated successfully."));
            exit;
        }

        if ($newPoster && is_file(__DIR__ . "/uploads/" . $newPoster)) {
            unlink(__DIR__ . "/uploads/" . $newPoster);
        }

        $error = "Could not update event.";
    }

    $event["event_name"] = $name;
    $event["event_date"] = $date;
    $event["venue"] = $venue;
    $event["description"] = $description;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link rel="stylesheet" href="Event.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit Event</h1>
        <p class="message"><?= htmlspecialchars($error) ?></p>

        <form method="post" enctype="multipart/form-data">
            <label>Event Name</label>
            <input type="text" name="event_name" maxlength="150"
                   value="<?= htmlspecialchars($event["event_name"]) ?>" required>

            <label>Event Date</label>
            <input type="date" name="event_date"
                   value="<?= htmlspecialchars($event["event_date"]) ?>" required>

            <label>Venue</label>
            <input type="text" name="venue" maxlength="150"
                   value="<?= htmlspecialchars($event["venue"]) ?>" required>

            <label>Description</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($event["description"] ?? "") ?></textarea>

            <?php if (!empty($event["poster"])): ?>
                <p>Current poster</p>
                <img class="poster" src="uploads/<?= htmlspecialchars($event["poster"]) ?>" alt="Current poster">
            <?php endif; ?>

            <label>Replace Poster (JPG, PNG, WebP; max 2 MB)</label>
            <input type="file" name="poster"
                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

            <button type="submit">Update Event</button>
            <a class="btn" href="events.php">Cancel</a>
        </form>
    </div>
</body>
</html>
