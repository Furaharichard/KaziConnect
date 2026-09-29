<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$skill_name = trim($_POST['skill_name'] ?? '');

if (empty($skill_name)) {
    die("Please enter a skill.");
}

try {

    // Check whether the skill already exists
    $stmt = $pdo->prepare("
        SELECT skill_id
        FROM skills
        WHERE skill_name = ?
        LIMIT 1
    ");

    $stmt->execute([$skill_name]);

    $skill = $stmt->fetch(PDO::FETCH_ASSOC);

    // If it doesn't exist, create it
    if (!$skill) {

        $stmt = $pdo->prepare("
            INSERT INTO skills (skill_name)
            VALUES (?)
        ");

        $stmt->execute([$skill_name]);

        $skill_id = $pdo->lastInsertId();

    } else {

        $skill_id = $skill['skill_id'];
    }


    // Check whether user already has this skill
    $stmt = $pdo->prepare("
        SELECT *
        FROM user_skills
        WHERE user_id = ?
        AND skill_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $user_id,
        $skill_id
    ]);

    if (!$stmt->fetch()) {

        $stmt = $pdo->prepare("
            INSERT INTO user_skills
            (user_id, skill_id)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $user_id,
            $skill_id
        ]);
    }


    header("Location: skills.php");
    exit();

} catch (PDOException $e) {

    die("Unable to add skill. Please try again.");
}