
<?php
require "admin_check.php";
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["event_name"] ?? "");
    $date = $_POST["event_date"] ?? "";
    $venue = trim($_POST["venue"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $poster = null;

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
                    $poster = bin2hex(random_bytes(16)) . "." . $types[$mime];

                    if (!move_uploaded_file($file["tmp_name"], __DIR__ . "/uploads/" . $poster)) {
                        $error = "Could not save poster.";
                        $poster = null;
                    }
                }
            }
        }
    }

    if ($error === "") {
        $stmt = $conn->prepare(
            "INSERT INTO events (event_name, event_date, venue, description, poster) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $name, $date, $venue, $description, $poster);

        if ($stmt->execute()) {
            header("Location: events.php?message=" . urlencode("Event added successfully."));
            exit;
        }

        if ($poster && is_file(__DIR__ . "/uploads/" . $poster)) {
            unlink(__DIR__ . "/uploads/" . $poster);
        }

        $error = "Could not save event.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="Event.css">
</head>
<body>
    <div class="form-container">
        <h1>Add Event</h1>
        <p class="message"><?= htmlspecialchars($error) ?></p>

        <form method="post" enctype="multipart/form-data">
            <label>Event Name</label>
            <input type="text" name="event_name" maxlength="150" required>

            <label>Event Date</label>
            <input type="date" name="event_date" required>

            <label>Venue</label>
            <input type="text" name="venue" maxlength="150" required>

            <label>Description</label>
            <textarea name="description" rows="4"></textarea>

            <label>Poster (JPG, PNG, WebP; max 2 MB)</label>
            <input type="file" name="poster"
                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

            <button type="submit">Add Event</button>
            <a class="btn" href="events.php">Cancel</a>
        </form>
    </div>
</body>
</html>
