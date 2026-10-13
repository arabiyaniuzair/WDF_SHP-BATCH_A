
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";

function respond($success, $message, $data = null) {
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

function getInput() {
    $input = json_decode(file_get_contents("php://input"), true);
    return is_array($input) ? $input : [];
}

function validEvent($input) {
    $name = trim($input["event_name"] ?? "");
    $date = $input["event_date"] ?? "";
    $venue = trim($input["venue"] ?? "");
    $description = trim($input["description"] ?? "");

    $dateObject = DateTime::createFromFormat("!Y-m-d", $date);

    if (
        $name === "" ||
        strlen($name) > 150 ||
        $venue === "" ||
        strlen($venue) > 150 ||
        !$dateObject ||
        $dateObject->format("Y-m-d") !== $date
    ) {
        respond(false, "Enter a valid event name, date, and venue.");
    }

    return [$name, $date, $venue, $description];
}

$method = $_SERVER["REQUEST_METHOD"];

try {
    if ($method === "GET") {
        $result = $conn->query(
            "SELECT event_id, event_name, event_date, venue, description
             FROM events ORDER BY event_date ASC"
        );

        $events = $result->fetch_all(MYSQLI_ASSOC);
        respond(true, "Events loaded.", $events);
    }

    if ($method !== "POST") {
        http_response_code(405);
        respond(false, "Method not allowed.");
    }

    $input = getInput();
    $action = $input["action"] ?? "";

    if ($action === "add") {
        [$name, $date, $venue, $description] = validEvent($input);

        $stmt = $conn->prepare(
            "INSERT INTO events (event_name, event_date, venue, description)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $name, $date, $venue, $description);
        $stmt->execute();

        respond(true, "Event added successfully.");
    }

    if ($action === "update") {
        $id = filter_var($input["event_id"] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            respond(false, "Invalid event ID.");
        }

        [$name, $date, $venue, $description] = validEvent($input);

        $stmt = $conn->prepare(
            "UPDATE events
             SET event_name = ?, event_date = ?, venue = ?, description = ?
             WHERE event_id = ?"
        );
        $stmt->bind_param("ssssi", $name, $date, $venue, $description, $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare(
                "SELECT event_id FROM events WHERE event_id = ?"
            );
            $check->bind_param("i", $id);
            $check->execute();

            if (!$check->get_result()->fetch_assoc()) {
                respond(false, "Event not found.");
            }
        }

        respond(true, "Event updated successfully.");
    }

    if ($action === "delete") {
        $id = filter_var($input["event_id"] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            respond(false, "Invalid event ID.");
        }

        $stmt = $conn->prepare(
            "DELETE FROM events WHERE event_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            respond(false, "Event not found.");
        }

        respond(true, "Event deleted successfully.");
    }

    respond(false, "Invalid action.");
} catch (Throwable $e) {
    http_response_code(500);
    respond(false, "A server or database error occurred.");
}
?>
