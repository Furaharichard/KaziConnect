<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];
$education_id = $_GET['id'] ?? '';

if (empty($education_id)) {
    die("Invalid education record.");
}

$stmt = $pdo->prepare("
    DELETE FROM education
    WHERE education_id = ?
    AND user_id = ?
");

$stmt->execute([
    $education_id,
    $user_id
]);

header("Location: education.php");
exit();