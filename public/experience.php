<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM experience
    WHERE user_id = ?
    ORDER BY 
        CASE WHEN end_date IS NULL THEN 0 ELSE 1 END,
        start_date DESC,
        experience_id DESC
");

$stmt->execute([$user_id]);
$experiences = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Experience - KaziConnect</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 18px;
            cursor: pointer;
        }

        .delete {
            color: red;
        }

        .no-experience {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .optional {
            color: #666;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Experience</h2>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="profile.php">Profile</a> |
        <a href="skills.php">Skills</a> |
        <a href="education.php">Education</a>
    </p>

    <hr>

    <?php if (count($experiences) === 0): ?>

        <div class="no-experience">

            <h3>No work experience added yet</h3>

            <p>
                Don't worry if you are looking for your first opportunity.
                You can continue without work experience.
            </p>

            <p>
                Your skills and education can still help employers understand
                what you can offer.
            </p>

            <p class="optional">
                Experience is optional.
            </p>

        </div>

    <?php else: ?>

        <h3>Your Experience</h3>

        <?php foreach ($experiences as $experience): ?>

            <div class="card">

                <h3>
                    <?= htmlspecialchars($experience['job_title']) ?>
                </h3>

                <strong>
                    <?= htmlspecialchars($experience['organization']) ?>
                </strong>

                <p>
                    <strong>Start:</strong>
                    <?= !empty($experience['start_date'])
                        ? htmlspecialchars($experience['start_date'])
                        : 'Not specified' ?>
                </p>

                <p>
                    <strong>End:</strong>
                    <?= !empty($experience['end_date'])
                        ? htmlspecialchars($experience['end_date'])
                        : 'Present' ?>
                </p>

                <?php if (!empty($experience['description'])): ?>

                    <p>
                        <?= nl2br(htmlspecialchars($experience['description'])) ?>
                    </p>

                <?php endif; ?>

                <a
                    class="delete"
                    href="delete_experience.php?id=<?= $experience['experience_id'] ?>"
                    onclick="return confirm('Are you sure you want to delete this experience?');"
                >
                    Delete
                </a>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <hr>

    <h3>Add Experience</h3>

<p class="optional">
    Have you worked before? Add your experience below.
    If this is your first time looking for work, you can skip this section.
</p>

<div class="card">

    <h3>Don't have work experience?</h3>

    <p>
        That's completely okay! KaziConnect is also designed
        for students, fresh graduates and first-time job seekers.
    </p>

    <a href="projects.php">
    <button type="button">
        Skip — I don't have experience
    </button>
</a>

</div>

    <form action="add_experience.php" method="POST">

        <label>Organization / Company</label>

        <input
            type="text"
            name="organization"
            placeholder="e.g. Kilifi County Government"
            required
        >

        <label>Job Title</label>

        <input
            type="text"
            name="job_title"
            placeholder="e.g. ICT Intern"
            required
        >

        <label>Start Date</label>

        <input
            type="date"
            name="start_date"
        >

        <label>End Date</label>

        <input
            type="date"
            name="end_date"
        >

        <label>Description</label>

        <textarea
            name="description"
            rows="6"
            placeholder="Describe your responsibilities, duties or achievements..."
        ></textarea>

        <button type="submit">
            Add Experience
        </button>

    </form>

</div>

</body>
</html>