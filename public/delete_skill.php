<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$skill_id = $_GET['id'] ?? '';

if (empty($skill_id)) {
    die("Invalid skill.");
}

$stmt = $pdo->prepare("
    DELETE FROM user_skills
    WHERE user_id = ?
    AND skill_id = ?
");

$stmt->execute([
    $user_id,
    $skill_id
]);

header("Location: skills.php");
exit();