<?php

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

$name = htmlspecialchars($name);
$email = htmlspecialchars($email);
$mobile = htmlspecialchars($mobile);
$course = htmlspecialchars($course);
$year = htmlspecialchars($year);
$gender = htmlspecialchars($gender);

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

$file = "registrations.json";

if (file_exists($file)) {
    $data = file_get_contents($file);
    $students = json_decode($data, true);

    if (!is_array($students)) {
        $students = [];
    }
} else {
    $students = [];
}

$student = [
    "name" => $name,
    "email" => $email,
    "mobile" => $mobile,
    "password" => password_hash($password, PASSWORD_DEFAULT),
    "course" => $course,
    "year" => $year,
    "gender" => $gender
];

$students[] = $student;

$data = json_encode($students, JSON_PRETTY_PRINT);

file_put_contents($file, $data);

echo "<h1 style='color:green;'>Registration Successful!</h1>";
echo "<p>Your registration has been saved successfully.</p>";
echo "<a href='Register.html'>Register Another Student</a>";

?>
