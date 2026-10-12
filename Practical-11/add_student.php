
<?php
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $password = $_POST["password"] ?? "";
    $course = $_POST["course"] ?? "";
    $year = $_POST["year"] ?? "";
    $gender = $_POST["gender"] ?? "";

    if (
        $name === "" || $username === "" || $email === "" ||
        $mobile === "" || $password === "" || $course === "" ||
        !in_array($year, ["1", "2", "3", "4"], true) ||
        !in_array($gender, ["Male", "Female"], true) ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $error = "Please enter valid information in all fields.";
    } else {
        $stmt = $conn->prepare(
            "SELECT student_id FROM students WHERE username = ? OR email = ?"
        );
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($exists) {
            $error = "Username or email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $yearValue = (int)$year;

            $stmt = $conn->prepare(
                "INSERT INTO students
                (name, username, email, mobile, password, course, year, gender)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssssis",
                $name,
                $username,
                $email,
                $mobile,
                $hashedPassword,
                $course,
                $yearValue,
                $gender
            );

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: students.php?message=added");
                exit;
            }

            $error = "Unable to add student. Please check the information.";
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container form-container">
    <h1>Add Student</h1>

    <?php if ($error !== ""): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="add_student.php">
        <label>Full Name</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($_POST["name"] ?? "") ?>">

        <label>Username</label>
        <input type="text" name="username" required value="<?= htmlspecialchars($_POST["username"] ?? "") ?>">

        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">

        <label>Mobile</label>
        <input type="tel" name="mobile" required value="<?= htmlspecialchars($_POST["mobile"] ?? "") ?>">

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Course</label>
        <select name="course" required>
            <option value="">Select Course</option>
            <?php foreach (["DIT", "BCA", "B.Tech", "MCA"] as $option): ?>
                <option value="<?= $option ?>" <?= ($_POST["course"] ?? "") === $option ? "selected" : "" ?>>
                    <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Year</label>
        <select name="year" required>
            <option value="">Select Year</option>
            <?php foreach (["1", "2", "3", "4"] as $option): ?>
                <option value="<?= $option ?>" <?= ($_POST["year"] ?? "") === $option ? "selected" : "" ?>>
                    Year <?= $option ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?= ($_POST["gender"] ?? "") === "Male" ? "selected" : "" ?>>Male</option>
            <option value="Female" <?= ($_POST["gender"] ?? "") === "Female" ? "selected" : "" ?>>Female</option>
        </select>

        <button type="submit">Add Student</button>
        <a class="button secondary" href="students.php">Back</a>
    </form>
</div>

</body>
</html>
