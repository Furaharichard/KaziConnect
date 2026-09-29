<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// Basic validation
if (empty($full_name) || empty($email) || empty($phone)) {
    die("Please fill in all required fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}


// Check if another user already has this email
$check = $pdo->prepare("
    SELECT user_id
    FROM users
    WHERE email = ?
    AND user_id != ?
    LIMIT 1
");

$check->execute([$email, $user_id]);

if ($check->fetch()) {
    die("This email address is already being used by another account.");
}


// Get current profile photo
$getPhoto = $pdo->prepare("
    SELECT profile_photo
    FROM users
    WHERE user_id = ?
");

$getPhoto->execute([$user_id]);

$currentUser = $getPhoto->fetch(PDO::FETCH_ASSOC);

$currentPhoto = $currentUser['profile_photo'] ?? null;

$newPhoto = null;


// Handle profile photo
if (
    isset($_FILES['profile_photo']) &&
    $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK
) {

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    $fileName = $_FILES['profile_photo']['name'];
    $tmpName = $_FILES['profile_photo']['tmp_name'];

    $extension = strtolower(
        pathinfo($fileName, PATHINFO_EXTENSION)
    );

    if (!in_array($extension, $allowed)) {
        die("Invalid image format. Please upload JPG, JPEG, PNG or GIF.");
    }

    // Maximum file size: 5 MB
    if ($_FILES['profile_photo']['size'] > 5 * 1024 * 1024) {
        die("Profile photo must not exceed 5 MB.");
    }

    $newPhoto = time() . "_" . uniqid() . "." . $extension;

    $uploadPath = "uploads/profiles/" . $newPhoto;

    if (!move_uploaded_file($tmpName, $uploadPath)) {
        die("Failed to upload profile photo.");
    }
}


// Update database
if ($newPhoto !== null) {

    $sql = "
        UPDATE users
        SET
            full_name = ?,
            email = ?,
            phone = ?,
            profile_photo = ?
        WHERE user_id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $full_name,
        $email,
        $phone,
        $newPhoto,
        $user_id
    ]);

} else {

    $sql = "
        UPDATE users
        SET
            full_name = ?,
            email = ?,
            phone = ?
        WHERE user_id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $full_name,
        $email,
        $phone,
        $user_id
    ]);
}


// Update session name and email
$_SESSION['full_name'] = $full_name;
$_SESSION['email'] = $email;


// Delete old profile photo after successful update
if ($newPhoto !== null && !empty($currentPhoto)) {

    $oldPhotoPath = "uploads/profiles/" . $currentPhoto;

    if (file_exists($oldPhotoPath)) {
        unlink($oldPhotoPath);
    }
}


// Return to profile
header("Location: profile.php");
exit();

?>