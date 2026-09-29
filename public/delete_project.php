<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$project_id = $_GET['id'] ?? '';

if (!is_numeric($project_id)) {
    header("Location: projects.php");
    exit;
}


$stmt = $pdo->prepare("
    DELETE FROM projects
    WHERE project_id = ?
    AND user_id = ?
");


$stmt->execute([
    $project_id,
    $user_id
]);


header("Location: projects.php");
exit;