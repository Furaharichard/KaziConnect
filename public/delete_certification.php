<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];
$certification_id = $_GET['id'] ?? '';

if (!is_numeric($certification_id)) {
    header("Location: certifications.php");
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM certifications
    WHERE certification_id = ?
    AND user_id = ?
");

$stmt->execute([
    $certification_id,
    $user_id
]);

header("Location: certifications.php");
exit;