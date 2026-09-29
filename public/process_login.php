<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.php");
    exit;

}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {

    die("Please enter your email and password.");

}


$stmt = $pdo->prepare("
    SELECT
        u.user_id,
        u.full_name,
        u.email,
        u.password,
        u.role_id,
        u.county_id,
        r.role_name
    FROM users u
    INNER JOIN roles r
        ON u.role_id = r.role_id
    WHERE u.email = ?
    LIMIT 1
");

$stmt->execute([$email]);

$user = $stmt->fetch();


if (!$user) {

    die("Invalid email or password.");

}


if (!password_verify($password, $user['password'])) {

    die("Invalid email or password.");

}


// Store user information in session
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role_id'] = $user['role_id'];
$_SESSION['role_name'] = $user['role_name'];
$_SESSION['county_id'] = $user['county_id'];


// Redirect according to role
if ($user['role_id'] == 2) {

    header("Location: employer_dashboard.php");
    exit;

}

if ($user['role_id'] == 3) {

    header("Location: dashboard.php");
    exit;

}


// Default
header("Location: dashboard.php");
exit;