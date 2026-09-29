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
    FROM projects
    WHERE user_id = ?
    ORDER BY 
        CASE WHEN end_date IS NULL THEN 0 ELSE 1 END,
        start_date DESC,
        project_id DESC
");

$stmt->execute([$user_id]);
$projects = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Projects - KaziConnect</title>

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

        .optional {
            color: #666;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>My Projects</h2>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="profile.php">Profile</a> |
        <a href="skills.php">Skills</a> |
        <a href="education.php">Education</a> |
        <a href="experience.php">Experience</a>
    </p>

    <hr>

    <?php if (count($projects) === 0): ?>

        <div class="card">

            <h3>No projects added yet</h3>

            <p>
                Projects can help employers see what you can actually do,
                even if you don't have formal work experience.
            </p>

            <p class="optional">
                Projects are optional.
            </p>

        </div>

    <?php else: ?>

        <h3>Your Projects</h3>

        <?php foreach ($projects as $project): ?>

            <div class="card">

                <h3>
                    <?= htmlspecialchars($project['project_title']) ?>
                </h3>

                <?php if (!empty($project['project_description'])): ?>

                    <p>
                        <?= nl2br(htmlspecialchars($project['project_description'])) ?>
                    </p>

                <?php endif; ?>

                <?php if (!empty($project['technologies'])): ?>

                    <p>
                        <strong>Technologies:</strong>
                        <?= htmlspecialchars($project['technologies']) ?>
                    </p>

                <?php endif; ?>

                <?php if (!empty($project['start_date'])): ?>

                    <p>
                        <strong>Started:</strong>
                        <?= htmlspecialchars($project['start_date']) ?>
                    </p>

                <?php endif; ?>

                <?php if (!empty($project['end_date'])): ?>

                    <p>
                        <strong>Completed:</strong>
                        <?= htmlspecialchars($project['end_date']) ?>
                    </p>

                <?php else: ?>

                    <?php if (!empty($project['start_date'])): ?>

                        <p>
                            <strong>Status:</strong> Ongoing
                        </p>

                    <?php endif; ?>

                <?php endif; ?>

                <?php if (!empty($project['project_link'])): ?>

                    <p>
                        <strong>Project Link:</strong>

                        <a
                            href="<?= htmlspecialchars($project['project_link']) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            View Project
                        </a>

                    </p>

                <?php endif; ?>

                <a
                    class="delete"
                    href="delete_project.php?id=<?= $project['project_id'] ?>"
                    onclick="return confirm('Are you sure you want to delete this project?');"
                >
                    Delete Project
                </a>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <hr>

    <h3>Add Project</h3>

    <p class="optional">
        You can add academic projects, personal projects,
        software projects, research projects or other work you have created.
    </p>

    <form action="add_project.php" method="POST">

        <label>Project Title</label>

        <input
            type="text"
            name="project_title"
            placeholder="e.g. KaziConnect Employment Platform"
            required
        >

        <label>Project Description</label>

        <textarea
            name="project_description"
            rows="6"
            placeholder="Explain what the project does, your role and what you achieved..."
        ></textarea>

        <label>Technologies Used</label>

        <input
            type="text"
            name="technologies"
            placeholder="e.g. PHP, MySQL, HTML, CSS, JavaScript"
        >

        <label>Project Link</label>

        <input
            type="url"
            name="project_link"
            placeholder="https://github.com/username/project"
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

        <button type="submit">
            Add Project
        </button>

    </form>

    <br>

    <div class="card">

        <h3>Don't have a project?</h3>

        <p>
            That's okay. You can continue building your KaziConnect profile
            without adding a project.
        </p>

        <a href="certifications.php"></a>
            <button type="button">
                Skip — I don't have a project
            </button>
        </a>

    </div>

</div>

</body>

</html>