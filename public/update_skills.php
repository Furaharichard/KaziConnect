<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$selectedSkills = $_POST['skills'] ?? [];

try {

    $pdo->beginTransaction();

    // Remove previous skills
    $delete = $pdo->prepare("
        DELETE FROM user_skills
        WHERE user_id = ?
    ");

    $delete->execute([$user_id]);

    // Add selected skills
    if (!empty($selectedSkills)) {

        $insert = $pdo->prepare("
            INSERT INTO user_skills (user_id, skill_id)
            VALUES (?, ?)
        ");

        foreach ($selectedSkills as $skill_id) {

            $insert->execute([
                $user_id,
                $skill_id
            ]);
        }
    }

    $pdo->commit();

    header("Location: skills.php");
    exit();

} catch (PDOException $e) {

    $pdo->rollBack();

    die("Unable to update skills. Please try again.");
}