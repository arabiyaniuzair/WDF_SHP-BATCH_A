<?php

require "db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: Register.html");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$terms = $_POST["terms"] ?? "";

$errors = [];

if ($name == "" || strlen($name) < 3) {
    $errors[] = "Enter a valid name.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Enter a valid email.";
}

if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Enter 10 digit mobile number.";
}

if (strlen($password) < 8) {
    $errors[] = "Password must contain at least 8 characters.";
}

if ($password != $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if ($course == "") {
    $errors[] = "Select your course.";
}

if ($year == "") {
    $errors[] = "Select your year.";
}

if ($gender == "") {
    $errors[] = "Select your gender.";
}

if ($terms != "yes") {
    $errors[] = "Accept the Terms and Conditions.";
}

if (count($errors) > 0) {

    echo "<h1>Registration Error</h1>";

    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }

    echo "<a href='Register.html'>Go Back</a>";

    exit;
}

$check = $pdo->prepare("SELECT student_id FROM students WHERE email = ?");
$check->execute([$email]);

if ($check->fetch()) {
    echo "<h1>Registration Error</h1>";
    echo "<p style='color:red;'>Email already registered.</p>";
    echo "<a href='Register.html'>Go Back</a>";
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO students 
        (name, email, mobile, password, course, year, gender)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $name,
    $email,
    $mobile,
    $hashedPassword,
    $course,
    $year,
    $gender
]);

echo "<h1 style='color:green;'>Registration Successful!</h1>";
echo "<p>Your StudentHub account has been created successfully.</p>";
echo "<a href='Register.html'>Register Another Student</a>";

?>