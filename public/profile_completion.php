<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get basic profile information
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT full_name, email, phone, profile_photo
    FROM users
    WHERE user_id = ?
");

$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User account not found.");
}


/*
|--------------------------------------------------------------------------
| Check Skills
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM user_skills
    WHERE user_id = ?
");

$stmt->execute([$user_id]);
$skills_count = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Check Education
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM education
    WHERE user_id = ?
");

$stmt->execute([$user_id]);
$education_count = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Check Experience
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM experience
    WHERE user_id = ?
");

$stmt->execute([$user_id]);
$experience_count = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Check Projects
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM projects
    WHERE user_id = ?
");

$stmt->execute([$user_id]);
$projects_count = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Check Certifications
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM certifications
    WHERE user_id = ?
");

$stmt->execute([$user_id]);
$certifications_count = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Calculate Profile Completion
|--------------------------------------------------------------------------
|
| Required sections:
| 1. Basic information
| 2. Profile photo
| 3. Skills
| 4. Education
|
| Optional sections:
| Experience
| Projects
| Certifications
|
|--------------------------------------------------------------------------
*/

$basic_complete =
    !empty($user['full_name']) &&
    !empty($user['email']) &&
    !empty($user['phone']);

$photo_complete = !empty($user['profile_photo']);

$skills_complete = $skills_count > 0;

$education_complete = $education_count > 0;


/*
|--------------------------------------------------------------------------
| Calculate percentage
|--------------------------------------------------------------------------
*/

$total_sections = 4;
$completed_sections = 0;

if ($basic_complete) {
    $completed_sections++;
}

if ($photo_complete) {
    $completed_sections++;
}

if ($skills_complete) {
    $completed_sections++;
}

if ($education_complete) {
    $completed_sections++;
}

$completion = round(($completed_sections / $total_sections) * 100);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Profile Completion - KaziConnect</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .progress-container {
            background: #ddd;
            border-radius: 20px;
            height: 25px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: <?= $completion ?>%;
            background: #28a745;
            text-align: center;
            color: white;
            line-height: 25px;
        }

        .complete {
            color: green;
            font-weight: bold;
        }

        .incomplete {
            color: #cc0000;
            font-weight: bold;
        }

        .optional {
            color: #666;
        }

        a {
            text-decoration: none;
        }

        button {
            padding: 10px 18px;
            cursor: pointer;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Profile Completion</h2>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="profile.php">Profile</a>
    </p>

    <hr>

    <div class="card">

        <h2>Your Profile is <?= $completion ?>% Complete</h2>

        <div class="progress-container">

            <div class="progress-bar">

                <?= $completion ?>%

            </div>

        </div>

        <br>

        <?php if ($completion == 100): ?>

            <h3 class="complete">
                🎉 Your profile is complete!
            </h3>

            <p>
                Your profile is ready. You can now start exploring
                opportunities on KaziConnect.
            </p>

        <?php else: ?>

            <p>
                Complete the important sections below to make your
                profile stronger and easier for employers to understand.
            </p>

        <?php endif; ?>

    </div>


    <!-- Basic Profile -->

    <div class="card">

        <h3>
            <?= $basic_complete ? "✅" : "❌" ?>
            Basic Information
        </h3>

        <?php if ($basic_complete): ?>

            <p class="complete">
                Completed
            </p>

        <?php else: ?>

            <p class="incomplete">
                Please complete your basic profile information.
            </p>

        <?php endif; ?>

        <a href="profile.php">
            <button type="button">
                <?= $basic_complete ? "View Profile" : "Complete Profile" ?>
            </button>
        </a>

    </div>


    <!-- Profile Photo -->

    <div class="card">

        <h3>
            <?= $photo_complete ? "✅" : "❌" ?>
            Profile Photo
        </h3>

        <?php if ($photo_complete): ?>

            <p class="complete">
                Profile photo added.
            </p>

        <?php else: ?>

            <p class="incomplete">
                Adding a profile photo helps employers recognize your profile.
            </p>

        <?php endif; ?>

        <a href="profile.php">
            <button type="button">
                <?= $photo_complete ? "Change Photo" : "Add Photo" ?>
            </button>
        </a>

    </div>


    <!-- Skills -->

    <div class="card">

        <h3>
            <?= $skills_complete ? "✅" : "❌" ?>
            Skills
        </h3>

        <?php if ($skills_complete): ?>

            <p class="complete">
                <?= $skills_count ?> skill(s) added.
            </p>

        <?php else: ?>

            <p class="incomplete">
                Add at least one skill.
            </p>

        <?php endif; ?>

        <a href="skills.php">
            <button type="button">
                <?= $skills_complete ? "Manage Skills" : "Add Skills" ?>
            </button>
        </a>

    </div>


    <!-- Education -->

    <div class="card">

        <h3>
            <?= $education_complete ? "✅" : "❌" ?>
            Education
        </h3>

        <?php if ($education_complete): ?>

            <p class="complete">
                <?= $education_count ?> education record(s) added.
            </p>

        <?php else: ?>

            <p class="incomplete">
                Add your education background.
            </p>

        <?php endif; ?>

        <a href="education.php">
            <button type="button">
                <?= $education_complete ? "Manage Education" : "Add Education" ?>
            </button>
        </a>

    </div>


    <!-- Optional Experience -->

    <div class="card">

        <h3>
            💼 Work Experience
        </h3>

        <?php if ($experience_count > 0): ?>

            <p class="complete">
                <?= $experience_count ?> experience record(s) added.
            </p>

        <?php else: ?>

            <p class="optional">
                No experience added.
                This is completely okay for first-time job seekers.
            </p>

        <?php endif; ?>

        <a href="experience.php">
            <button type="button">
                <?= $experience_count > 0
                    ? "Manage Experience"
                    : "Add Experience" ?>
            </button>
        </a>

    </div>


    <!-- Optional Projects -->

    <div class="card">

        <h3>
            🚀 Projects
        </h3>

        <?php if ($projects_count > 0): ?>

            <p class="complete">
                <?= $projects_count ?> project(s) added.
            </p>

        <?php else: ?>

            <p class="optional">
                No projects added.
                You can skip this if you don't have any projects.
            </p>

        <?php endif; ?>

        <a href="projects.php">
            <button type="button">
                <?= $projects_count > 0
                    ? "Manage Projects"
                    : "Add Project" ?>
            </button>
        </a>

    </div>


    <!-- Optional Certifications -->

    <div class="card">

        <h3>
            📜 Certifications
        </h3>

        <?php if ($certifications_count > 0): ?>

            <p class="complete">
                <?= $certifications_count ?> certification(s) added.
            </p>

        <?php else: ?>

            <p class="optional">
                No certifications added.
                This section is optional.
            </p>

        <?php endif; ?>

        <a href="certifications.php">
            <button type="button">
                <?= $certifications_count > 0
                    ? "Manage Certifications"
                    : "Add Certification" ?>
            </button>
        </a>

    </div>


    <div class="card">

        <h3>Ready for opportunities?</h3>

        <p>
            Once your profile contains your basic information,
            skills and education, you can start looking for
            opportunities on KaziConnect.
        </p>

        <a href="dashboard.php">
            <button type="button">
                Go to Dashboard
            </button>
        </a>

    </div>

</div>

</body>

</html>