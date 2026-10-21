
<?php
require_once "auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: Register.html");
    exit;
}

$enrollment_number = strtoupper(trim($_POST["enrollment_number"] ?? ""));
$name = trim($_POST["name"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

if (
    $enrollment_number === "" ||
    $name === "" ||
    $username === "" ||
    $email === "" ||
    $mobile === "" ||
    $password === "" ||
    $confirmPassword === "" ||
    $course === "" ||
    $year === "" ||
    $gender === ""
) {
    exit('Please fill in all required fields. <a href="Register.html">Try again</a>');
}

if (!preg_match('/^[0-9]{2}[A-Z]{3}[0-9]{3}$/', $enrollment_number)) {
    exit('Invalid enrollment number. Use a format such as 25DIT001. <a href="Register.html">Try again</a>');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Enter a valid email address. <a href="Register.html">Try again</a>');
}

if ($password !== $confirmPassword) {
    exit('Passwords do not match. <a href="Register.html">Try again</a>');
}

if (!$terms) {
    exit('Please accept the terms and conditions. <a href="Register.html">Try again</a>');
}

$stmt = $conn->prepare(
    "SELECT student_id FROM students
     WHERE enrollment_number = ? OR username = ? OR email = ?
     LIMIT 1"
);

if (!$stmt) {
    error_log($conn->error);
    exit("A database error occurred. Please try again later.");
}

$stmt->bind_param("sss", $enrollment_number, $username, $email);
$stmt->execute();
$existingUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existingUser) {
    exit('Enrollment number, username, or email is already registered. <a href="Register.html">Try again</a>');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO students
    (enrollment_number, name, username, email, mobile, password, course, year, gender)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    error_log($conn->error);
    exit("A database error occurred. Please try again later.");
}

$stmt->bind_param(
    "sssssssss",
    $enrollment_number,
    $name,
    $username,
    $email,
    $mobile,
    $hashedPassword,
    $course,
    $year,
    $gender
);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: Login.html?registered=1");
    exit;
}

error_log($stmt->error);
$stmt->close();
exit("Registration failed. Please try again.");
?>
