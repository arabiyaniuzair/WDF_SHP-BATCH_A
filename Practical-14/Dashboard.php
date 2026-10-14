
<?php
require_once "db.php";
require_once "auth.php";

$stmt = $conn->prepare(
    "SELECT student_id, name, enrollment_number, course, year
     FROM students
     WHERE student_id = ?"
);

$stmt->bind_param("i", $_SESSION["student_id"]);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();

if (!$student) {
    http_response_code(404);
    exit("Student record not found.");
}

$enrollmentNumber = $student["enrollment_number"] ?? "";
$displayName = $enrollmentNumber !== ""
    ? $enrollmentNumber
    : $student["name"];

$course = $student["course"] ?? "Not available";
$year = $student["year"] ?? "Not available";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - StudentHub Portal</title>
    <link rel="stylesheet" href="Dashboard.css">
</head>
<body>

<div class="navbar">
    <a href="Dashboard.php">Dashboard</a>
    <a href="../Uzair_Register/Home.html">Home</a>
    <a href="../Uzair_Register/About.html">About</a>
    <a href="../Uzair_Register/Event.html">Events</a>
    <a href="../Uzair_Register/Profile.html">Profile</a>
    <a href="../Uzair_Register/Contact.html">Contact</a>
    <a href="../Uzair_Register/Faq.html">FAQ</a>
    <a href="logout.php">Logout</a>
</div>

<div class="weather">
    <h2>Current Weather</h2>
    <div class="weather-box">
        <p>Location: <span id="location">Loading...</span></p>
        <p>Temperature: <span id="temperature">Loading...</span></p>
        <p>Humidity: <span id="humidity">Loading...</span></p>
        <p>Condition: <span id="condition">Loading...</span></p>
    </div>
</div>

<br>

<div class="dashboard">
    <h1>Welcome, <?= htmlspecialchars($displayName, ENT_QUOTES, "UTF-8") ?>!</h1>
    <p>Welcome to your StudentHub Dashboard. Here you can access all student services.</p>
</div>

<br>

<div class="summary">
    <h2>Student Summary</h2>
    <table border="1">
        <tr>
            <th>Details</th>
            <th>Information</th>
        </tr>
        <tr>
            <td>Enrollment Number</td>
            <td><?= htmlspecialchars($enrollmentNumber !== "" ? $enrollmentNumber : "Not assigned", ENT_QUOTES, "UTF-8") ?></td>
        </tr>
        <tr>
            <td>Department</td>
            <td>Information Technology</td>
        </tr>
        <tr>
            <td>Course</td>
            <td><?= htmlspecialchars((string) $course, ENT_QUOTES, "UTF-8") ?></td>
        </tr>
        <tr>
            <td>Year</td>
            <td><?= htmlspecialchars((string) $year, ENT_QUOTES, "UTF-8") ?></td>
        </tr>
    </table>
</div>

<br>

<div class="services">
    <h2>Dashboard Services</h2>
    <ul>
        <li>View Academic Notices</li>
        <li>View Upcoming Events</li>
        <li>Update Student Profile</li>
        <li>Register for Events</li>
        <li>Submit Feedback</li>
    </ul>
</div>

<br>

<div class="notice">
    <h2>Latest Notices</h2>
    <table border="1">
        <tr>
            <th>Notice</th>
            <th>Date</th>
        </tr>
        <tr>
            <td>Semester Registration Started</td>
            <td>01-Aug-2026</td>
        </tr>
        <tr>
            <td>Web Development Practical Submission</td>
            <td>10-Aug-2026</td>
        </tr>
        <tr>
            <td>Hackathon Registration Open</td>
            <td>15-Aug-2026</td>
        </tr>
    </table>
</div>

<br>

<div class="footer">
    <p>Thank you for using the StudentHub Portal.</p>
</div>

<script src="Dashboard.js"></script>

</body>
</html>
<?php
$conn->close();
$eventsConn->close();
?>