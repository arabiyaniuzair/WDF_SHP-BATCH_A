<?php

require_once "db.php";

$isHttps = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";

ini_set("session.use_strict_mode", "1");

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => $isHttps,
    "httponly" => true,
    "samesite" => "Lax"
]);

session_start();

function clearRememberCookie() {
    global $isHttps;

    setcookie("studenthub_remember", "", [
        "expires" => time() - 3600,
        "path" => "/",
        "secure" => $isHttps,
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}

function setRememberToken($studentId) {
    global $conn, $isHttps;

    $selector = bin2hex(random_bytes(12));
    $validator = bin2hex(random_bytes(32));
    $tokenHash = hash("sha256", $validator);
    $expires = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60));

    $stmt = $conn->prepare(
        "UPDATE students
         SET remember_selector = ?, remember_token_hash = ?,
             remember_expires = ?
         WHERE student_id = ?"
    );

    $stmt->bind_param(
        "sssi",
        $selector,
        $tokenHash,
        $expires,
        $studentId
    );

    $stmt->execute();
    $stmt->close();

    setcookie("studenthub_remember", $selector . ":" . $validator, [
        "expires" => time() + (30 * 24 * 60 * 60),
        "path" => "/",
        "secure" => $isHttps,
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}

if (isset($_SESSION["student_id"])) {
    if (
        !isset($_SESSION["last_activity"]) ||
        time() - $_SESSION["last_activity"] > 900
    ) {
        $_SESSION = [];
        session_regenerate_id(true);
        clearRememberCookie();
    } else {
        $_SESSION["last_activity"] = time();
    }
}

if (!isset($_SESSION["student_id"]) && isset($_COOKIE["studenthub_remember"])) {
    $parts = explode(":", $_COOKIE["studenthub_remember"]);

    if (
        count($parts) === 2 &&
        preg_match('/^[a-f0-9]{24}$/', $parts[0]) &&
        preg_match('/^[a-f0-9]{64}$/', $parts[1])
    ) {
        $selector = $parts[0];
        $validator = $parts[1];

        $stmt = $conn->prepare(
            "SELECT student_id, remember_token_hash
             FROM students
             WHERE remember_selector = ?
             AND remember_expires > NOW()
             LIMIT 1"
        );

        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $result = $stmt->get_result();
        $token = $result->fetch_assoc();
        $stmt->close();

        if (
            $token &&
            hash_equals(
                $token["remember_token_hash"],
                hash("sha256", $validator)
            )
        ) {
            $stmt = $conn->prepare(
                "SELECT student_id, name, username, email, role
                 FROM students WHERE student_id = ?"
            );

            $stmt->bind_param("i", $token["student_id"]);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user) {
                session_regenerate_id(true);

                $_SESSION["student_id"] = $user["student_id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["last_activity"] = time();

                setRememberToken($user["student_id"]);
            }
        } else {
            clearRememberCookie();
        }
    } else {
        clearRememberCookie();
    }
}

function redirectByRole() {
    if (($_SESSION["role"] ?? "") === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }

    exit;
}

function requireLogin() {
    if (!isset($_SESSION["student_id"])) {
        header("Location: Login.html");
        exit;
    }
}

function requireRole($role) {
    requireLogin();

    if (($_SESSION["role"] ?? "") !== $role) {
        redirectByRole();
    }
}

?>