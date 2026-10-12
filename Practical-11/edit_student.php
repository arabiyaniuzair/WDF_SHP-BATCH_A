
<?php
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: students.php?message=error");
    exit;
}

$stmt = $conn->prepare(
    "SELECT student_id, name, username, email, mobile, course, year, gender
     FROM students WHERE student_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    header("Location: students.php?message=error");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = $_POST["course"] ?? "";
    $year = $_POST["year"] ?? "";
    $gender = $_POST["gender"] ?? "";

    if (
        $name === "" || $username === "" || $email === "" ||
        $mobile === "" || $course === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($year, ["1", "2", "3", "4"], true) ||
        !in_array($gender, ["Male", "Female"], true) ||
        !in_array($course, ["DIT", "BCA", "B.Tech", "MCA"], true)
    ) {
        $error = "Please enter valid information in all fields.";
    } else {
        $stmt = $conn->prepare(
            "SELECT student_id FROM students
             WHERE (username = ? OR email = ?) AND student_id <> ?"
        );
        $stmt->bind_param("ssi", $username, $email, $id);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($exists) {
            $error = "That username or email is already used by another student.";
        } else {
            $yearValue = (int)$year;

            $stmt = $conn->prepare(
                "UPDATE students
                 SET name = ?, username = ?, email = ?, mobile = ?,
                     course = ?, year = ?, gender = ?
                 WHERE student_id = ?"
            );

            $stmt->bind_param(
                "sssssisi",
                $name,
                $username,
                $email,
                $mobile,
                $course,
                $yearValue,
                $gender,
                $id
            );

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: students.php?message=updated");
                exit;
            }

            $error = "Unable to update student.";
            $stmt->close();
        }
    }

    $student = array_merge($student, [
        "name" => $name,
        "username" => $username,
        "email" => $email,
        "mobile" => $mobile,
        "course" => $course,
        "year" => $year,
        "gender" => $gender
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container form-container">
    <h1>Edit Student</h1>

    <?php if ($error !== ""): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="edit_student.php?id=<?= (int)$id ?>">
        <label>Full Name</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($student["name"]) ?>">

        <label>Username</label>
        <input type="text" name="username" required value="<?= htmlspecialchars($student["username"] ?? "") ?>">

        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($student["email"]) ?>">

        <label>Mobile</label>
        <input type="tel" name="mobile" required value="<?= htmlspecialchars($student["mobile"]) ?>">

        <label>Course</label>
        <select name="course" required>
            <?php foreach (["DIT", "BCA", "B.Tech", "MCA"] as $option): ?>
                <option value="<?= $option ?>" <?= $student["course"] === $option ? "selected" : "" ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Year</label>
        <select name="year" required>
            <?php foreach (["1", "2", "3", "4"] as $option): ?>
                <option value="<?= $option ?>" <?= (string)$student["year"] === $option ? "selected" : "" ?>>
                    Year <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Gender</label>
        <select name="gender" required>
            <?php foreach (["Male", "Female"] as $option): ?>
                <option value="<?= $option ?>" <?= $student["gender"] === $option ? "selected" : "" ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Update Student</button>
        <a class="button secondary" href="students.php">Cancel</a>
    </form>
</div>

</body>
</html>
