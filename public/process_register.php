<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: register.php");
    exit;

}

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$county_id = $_POST['county_id'] ?? '';
$role_id = $_POST['role_id'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';


// Validate required fields
if (
    $full_name === '' ||
    $email === '' ||
    $phone === '' ||
    $county_id === '' ||
    $role_id === '' ||
    $password === '' ||
    $confirm_password === ''
) {

    die("Please fill in all required fields.");

}


// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    die("Please enter a valid email address.");

}


// Validate account type
if ($role_id != 2 && $role_id != 3) {

    die("Invalid account type.");

}


// Validate password length
if (strlen($password) < 6) {

    die("Password must be at least 6 characters long.");

}


// Confirm password
if ($password !== $confirm_password) {

    die("Passwords do not match.");

}


try {

    // Check duplicate email
    $checkEmail = $pdo->prepare("
        SELECT user_id
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $checkEmail->execute([$email]);

    if ($checkEmail->fetch()) {

        die("An account with this email already exists.");

    }


    // Check duplicate phone
    $checkPhone = $pdo->prepare("
        SELECT user_id
        FROM users
        WHERE phone = ?
        LIMIT 1
    ");

    $checkPhone->execute([$phone]);

    if ($checkPhone->fetch()) {

        die("An account with this phone number already exists.");

    }


    // Hash password
    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    // Start transaction
    $pdo->beginTransaction();


    // Create user
    $insertUser = $pdo->prepare("
        INSERT INTO users
        (
            role_id,
            county_id,
            full_name,
            email,
            phone,
            password
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $insertUser->execute([
        $role_id,
        $county_id,
        $full_name,
        $email,
        $phone,
        $hashed_password
    ]);


    // Get newly created user ID
    $user_id = $pdo->lastInsertId();


    /*
     * If the account is an employer,
     * create the employer profile automatically.
     */
    if ($role_id == 2) {

        $insertEmployer = $pdo->prepare("
            INSERT INTO employers
            (
                user_id,
                organization_name,
                contact_person,
                contact_email,
                contact_phone
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $insertEmployer->execute([
            $user_id,
            $full_name,
            $full_name,
            $email,
            $phone
        ]);

    }


    // Complete transaction
    $pdo->commit();


    /*
     * Redirect according to account type.
     */

    if ($role_id == 2) {

        // Employer
        header("Location: employer_profile.php");
        exit;

    } else {

        // Job seeker
        header("Location: login.php?registered=1");
        exit;

    }


} catch (PDOException $e) {

    // Roll back if something failed
    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }

    die(
        "Registration failed: " .
        htmlspecialchars($e->getMessage())
    );

}