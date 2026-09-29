<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$certification_name = trim($_POST['certification_name'] ?? '');
$issuing_organization = trim($_POST['issuing_organization'] ?? '');
$issue_date = !empty($_POST['issue_date']) ? $_POST['issue_date'] : null;
$expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
$credential_id = trim($_POST['credential_id'] ?? '');
$credential_url = trim($_POST['credential_url'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($certification_name === '' || $issuing_organization === '') {
    die("Certification name and issuing organization are required.");
}

if ($issue_date && $expiry_date && $expiry_date < $issue_date) {
    die("Expiry date cannot be earlier than the issue date.");
}

if ($credential_url !== '' &&
    !filter_var($credential_url, FILTER_VALIDATE_URL)) {
    die("Please enter a valid credential URL.");
}

$stmt = $pdo->prepare("
    INSERT INTO certifications
    (
        user_id,
        certification_name,
        issuing_organization,
        issue_date,
        expiry_date,
        credential_id,
        credential_url,
        description
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $user_id,
    $certification_name,
    $issuing_organization,
    $issue_date,
    $expiry_date,
    $credential_id !== '' ? $credential_id : null,
    $credential_url !== '' ? $credential_url : null,
    $description !== '' ? $description : null
]);

header("Location: certifications.php");
exit;