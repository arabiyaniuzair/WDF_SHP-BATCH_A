
<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: students.php");
    exit;
}

$id = filter_input(INPUT_POST, "student_id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: students.php?message=error");
    exit;
}

$stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    $stmt->close();
    header("Location: students.php?message=deleted");
    exit;
}

$stmt->close();
header("Location: students.php?message=error");
exit;
?>
