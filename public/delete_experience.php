<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];
$experience_id = $_GET['id'] ?? '';

if (!is_numeric($experience_id)) {
    header("Location: experience.php");
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM experience
    WHERE experience_id = ?
    AND user_id = ?
");

$stmt->execute([
    $experience_id,
    $user_id
]);

header("Location: experience.php");
exit;