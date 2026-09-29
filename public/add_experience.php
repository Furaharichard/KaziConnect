<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$organization = trim($_POST['organization'] ?? '');
$job_title = trim($_POST['job_title'] ?? '');
$start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
$description = trim($_POST['description'] ?? '');

if ($organization === '' || $job_title === '') {
    die("Organization and Job Title are required.");
}

// Make sure end date is not before start date
if ($start_date && $end_date && $end_date < $start_date) {
    die("End date cannot be earlier than start date.");
}

$stmt = $pdo->prepare("
    INSERT INTO experience
    (
        user_id,
        organization,
        job_title,
        start_date,
        end_date,
        description
    )
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $user_id,
    $organization,
    $job_title,
    $start_date,
    $end_date,
    $description !== '' ? $description : null
]);

header("Location: experience.php");
exit;