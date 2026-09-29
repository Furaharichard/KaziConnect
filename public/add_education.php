<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$institution = trim($_POST['institution'] ?? '');
$qualification = trim($_POST['qualification'] ?? '');
$field_of_study = trim($_POST['field_of_study'] ?? '');
$start_year = $_POST['start_year'] ?? null;
$end_year = $_POST['end_year'] ?? null;
$description = trim($_POST['description'] ?? '');

if (empty($institution) || empty($qualification)) {
    die("Institution and qualification are required.");
}

if ($start_year === '') {
    $start_year = null;
}

if ($end_year === '') {
    $end_year = null;
}

if ($start_year !== null && $end_year !== null && $end_year < $start_year) {
    die("End year cannot be earlier than start year.");
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO education
        (
            user_id,
            institution,
            qualification,
            field_of_study,
            start_year,
            end_year,
            description
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $institution,
        $qualification,
        $field_of_study ?: null,
        $start_year,
        $end_year,
        $description ?: null
    ]);

    header("Location: education.php");
    exit();

} catch (PDOException $e) {

    die("Unable to add education record. Please try again.");
}