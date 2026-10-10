
<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: Register.html");
    exit;
}

$name = trim($_POST["name"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$terms = $_POST["terms"] ?? "";

$errors = [];

if (strlen($name) < 3 || strlen($name) > 100) {
    $errors[] = "Name must contain 3 to 100 characters.";
}

if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
    $errors[] = "Username must contain 3-50 letters, numbers, or underscores.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $errors[] = "Enter a valid email address.";
}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    $errors[] = "Enter a valid 10-digit mobile number.";
}

if (strlen($password) < 8) {
    $errors[] = "Password must contain at least 8 characters.";
}

if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if (!in_array($course, ["DIT", "BCA", "B.Tech", "MCA"], true)) {
    $errors[] = "Select a valid course.";
}

if (!in_array($year, ["1", "2", "3", "4"], true)) {
    $errors[] = "Select a valid year.";
}

if (!in_array($gender, ["Male", "Female"], true)) {
    $errors[] = "Select a valid gender.";
}

if ($terms !== "yes") {
    $errors[] = "Accept the Terms and Conditions.";
}

if (!empty($errors)) {
    echo "<h2>Registration Error</h2>";

    foreach ($errors as $error) {
        echo "<p>" . htmlspecialchars($error, ENT_QUOTES, "UTF-8") . "</p>";
    }

    echo '<a href="Register.html">Go Back</a>';
    exit;
}

$check = $conn->prepare(
    "SELECT student_id FROM students WHERE email = ? OR username = ?"
);

if (!$check) {
    error_log($conn->error);
    die("Unable to process registration.");
}

$check->bind_param("ss", $email, $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    echo "<h2>Registration Failed</h2>";
    echo "<p>Email or username is already registered.</p>";
    echo '<a href="Register.html">Go Back</a>';
    exit;
}

$check->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$year = (int) $year;

$stmt = $conn->prepare(
    "INSERT INTO students
    (name, username, email, mobile, password, course, year, gender)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    error_log($conn->error);
    die("Unable to process registration.");
}

$stmt->bind_param(
    "ssssssis",
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
    echo "<h2>Registration Successful!</h2>";
    echo "<p>Your StudentHub account has been created successfully.</p>";
    echo '<a href="Register.html">Register Another Student</a>';
} else {
    if ($stmt->errno === 1062) {
        echo "<h2>Registration Failed</h2>";
        echo "<p>Email or username is already registered.</p>";
        echo '<a href="Register.html">Go Back</a>';
    } else {
        error_log($stmt->error);
        echo "<h2>Registration Failed</h2>";
        echo "<p>Something went wrong. Please try again.</p>";
        echo '<a href="Register.html">Go Back</a>';
    }
}

$stmt->close();
$conn->close();

?>
