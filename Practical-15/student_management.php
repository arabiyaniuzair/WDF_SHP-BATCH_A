
<?php
require_once "auth.php";
requireRole("admin");

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {
        $error = "Invalid request. Please refresh the page and try again.";
    } else {
        $deleteId = filter_var(
            $_POST["delete_id"],
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]
        );

        if ($deleteId === false) {
            $error = "Invalid student ID.";
        } elseif ((int)$deleteId === (int)$_SESSION["student_id"]) {
            $error = "You cannot delete your own admin account.";
        } else {
            $stmt = $conn->prepare(
                "DELETE FROM students WHERE student_id = ? AND role = 'student'"
            );
            $stmt->bind_param("i", $deleteId);

            if ($stmt->execute() && $stmt->affected_rows === 1) {
                $message = "Student record deleted successfully.";
            } else {
                $error = "Student not found or the account is not a student.";
            }

            $stmt->close();
        }
    }
}

$search = trim($_GET["search"] ?? "");

if (strlen($search) > 100) {
    $search = substr($search, 0, 100);
}

if ($search !== "") {
    $stmt = $conn->prepare(
        "SELECT student_id, enrollment_number, name, username, email, mobile, course, year, gender
         FROM students
         WHERE name LIKE ? OR enrollment_number LIKE ?
         ORDER BY student_id DESC"
    );

    $term = "%" . $search . "%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $students = $stmt->get_result();
} else {
    $students = $conn->query(
        "SELECT student_id, enrollment_number, name, username, email, mobile, course, year, gender
         FROM students
         ORDER BY student_id DESC"
    );
}

if (!$students) {
    error_log("Student management query failed: " . $conn->error);
    http_response_code(500);
    exit("Unable to load student records. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - StudentHub</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            color: #333;
        }

        .navbar {
            background: #003366;
            padding: 16px;
            text-align: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 10px;
        }

        .container {
            width: 94%;
            max-width: 1400px;
            margin: 25px auto;
            background: white;
            padding: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        h1, h2 {
            color: #003366;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type="text"] {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border: 1px solid #aaa;
            border-radius: 4px;
        }

        button, .button {
            padding: 10px 15px;
            background: #003366;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .delete-button {
            background: #b42318;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #cfe8ff;
            color: #003366;
        }

        .message {
            background: #e2f5e9;
            padding: 12px;
            margin: 15px 0;
        }

        .error {
            background: #ffe5e5;
            padding: 12px;
            margin: 15px 0;
        }

        .footer {
            background: #003366;
            color: white;
            text-align: center;
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="student_management.php">Manage Students</a>
    <a href="../Events_Details/Event.php">Events</a>
    <a href="../Faq/Faq.html">FAQ</a>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
    <h1>Student Management</h1>
    <p>View, search and manage registered student accounts.</p>

    <?php if ($message !== ""): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="search-form" method="GET" action="student_management.php">
        <input
            type="text"
            name="search"
            placeholder="Search by student name or enrollment number"
            value="<?= htmlspecialchars($search) ?>"
        >
        <button type="submit">Search</button>
        <a class="button" href="student_management.php">Show All</a>
    </form>

    <h2>Registered Students</h2>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>ID</th>
                <th>Enrollment Number</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Course</th>
                <th>Year</th>
                <th>Gender</th>
                <th>Action</th>
            </tr>

            <?php if ($students->num_rows > 0): ?>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$student["student_id"] ?></td>
                        <td><?= htmlspecialchars($student["enrollment_number"] ?? "") ?></td>
                        <td><?= htmlspecialchars($student["name"]) ?></td>
                        <td><?= htmlspecialchars($student["username"]) ?></td>
                        <td><?= htmlspecialchars($student["email"]) ?></td>
                        <td><?= htmlspecialchars($student["mobile"]) ?></td>
                        <td><?= htmlspecialchars($student["course"]) ?></td>
                        <td><?= (int)$student["year"] ?></td>
                        <td><?= htmlspecialchars($student["gender"]) ?></td>
                        <td>
                            <?php if ($student["role"] === "student"): ?>
                                <form method="POST"
                                      action="student_management.php<?= $search !== "" ? "?search=" . urlencode($search) : "" ?>"
                                      onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                    <input type="hidden" name="delete_id"
                                           value="<?= (int)$student["student_id"] ?>">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">
                                    <button class="delete-button" type="submit">Delete</button>
                                </form>
                            <?php else: ?>
                                Admin account
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No student records found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<div class="footer">
    <p>StudentHub Administration Panel</p>
</div>

</body>
</html>
<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
?>
