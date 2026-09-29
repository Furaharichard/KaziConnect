<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$project_title = trim($_POST['project_title'] ?? '');
$project_description = trim($_POST['project_description'] ?? '');
$technologies = trim($_POST['technologies'] ?? '');
$project_link = trim($_POST['project_link'] ?? '');

$start_date = !empty($_POST['start_date'])
    ? $_POST['start_date']
    : null;

$end_date = !empty($_POST['end_date'])
    ? $_POST['end_date']
    : null;


// Project title is required
if ($project_title === '') {
    die("Project title is required.");
}


// Validate dates
if ($start_date && $end_date && $end_date < $start_date) {
    die("End date cannot be earlier than start date.");
}


// Validate project link if provided
if ($project_link !== '' && !filter_var($project_link, FILTER_VALIDATE_URL)) {
    die("Please enter a valid project URL.");
}


$stmt = $pdo->prepare("
    INSERT INTO projects
    (
        user_id,
        project_title,
        project_description,
        technologies,
        project_link,
        start_date,
        end_date
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
");


$stmt->execute([
    $user_id,
    $project_title,
    $project_description !== '' ? $project_description : null,
    $technologies !== '' ? $technologies : null,
    $project_link !== '' ? $project_link : null,
    $start_date,
    $end_date
]);


header("Location: projects.php");
exit;