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
    FROM certifications
    WHERE user_id = ?
    ORDER BY issue_date DESC, certification_id DESC
");

$stmt->execute([$user_id]);
$certifications = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Certifications - KaziConnect</title>

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

    <h2>My Certifications</h2>

    <p>
        <a href="dashboard.php">Dashboard</a> |
        <a href="profile.php">Profile</a> |
        <a href="skills.php">Skills</a> |
        <a href="education.php">Education</a> |
        <a href="experience.php">Experience</a> |
        <a href="projects.php">Projects</a>
    </p>

    <hr>

    <?php if (count($certifications) === 0): ?>

        <div class="card">

            <h3>No certifications added yet</h3>

            <p>
                Certifications can help employers understand the
                additional skills and training you have completed.
            </p>

            <p class="optional">
                Certifications are optional.
            </p>

        </div>

    <?php else: ?>

        <h3>Your Certifications</h3>

        <?php foreach ($certifications as $certification): ?>

            <div class="card">

                <h3>
                    <?= htmlspecialchars($certification['certification_name']) ?>
                </h3>

                <p>
                    <strong>Issuing Organization:</strong>
                    <?= htmlspecialchars($certification['issuing_organization']) ?>
                </p>

                <?php if (!empty($certification['issue_date'])): ?>

                    <p>
                        <strong>Issue Date:</strong>
                        <?= htmlspecialchars($certification['issue_date']) ?>
                    </p>

                <?php endif; ?>

                <?php if (!empty($certification['expiry_date'])): ?>

                    <p>
                        <strong>Expiry Date:</strong>
                        <?= htmlspecialchars($certification['expiry_date']) ?>
                    </p>

                <?php endif; ?>

                <?php if (!empty($certification['credential_id'])): ?>

                    <p>
                        <strong>Credential ID:</strong>
                        <?= htmlspecialchars($certification['credential_id']) ?>
                    </p>

                <?php endif; ?>

                <?php if (!empty($certification['credential_url'])): ?>

                    <p>
                        <strong>Credential:</strong>

                        <a
                            href="<?= htmlspecialchars($certification['credential_url']) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            View Credential
                        </a>

                    </p>

                <?php endif; ?>

                <?php if (!empty($certification['description'])): ?>

                    <p>
                        <?= nl2br(htmlspecialchars($certification['description'])) ?>
                    </p>

                <?php endif; ?>

                <a
                    class="delete"
                    href="delete_certification.php?id=<?= $certification['certification_id'] ?>"
                    onclick="return confirm('Are you sure you want to delete this certification?');"
                >
                    Delete Certification
                </a>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <hr>

    <h3>Add Certification</h3>

    <form action="add_certification.php" method="POST">

        <label>Certification Name</label>

        <input
            type="text"
            name="certification_name"
            placeholder="e.g. Introduction to Cybersecurity"
            required
        >

        <label>Issuing Organization</label>

        <input
            type="text"
            name="issuing_organization"
            placeholder="e.g. Cisco Networking Academy"
            required
        >

        <label>Issue Date</label>

        <input
            type="date"
            name="issue_date"
        >

        <label>Expiry Date</label>

        <input
            type="date"
            name="expiry_date"
        >

        <label>Credential ID</label>

        <input
            type="text"
            name="credential_id"
            placeholder="Optional"
        >

        <label>Credential URL</label>

        <input
            type="url"
            name="credential_url"
            placeholder="https://example.com/credential"
        >

        <label>Description</label>

        <textarea
            name="description"
            rows="5"
            placeholder="Add any additional information about this certification..."
        ></textarea>

        <button type="submit">
            Add Certification
        </button>

    </form>

    <br>

    <div class="card">

        <h3>Don't have a certification?</h3>

        <p>
            That's completely okay. You can continue building your
            KaziConnect profile without adding a certification.
        </p>

        <a href="dashboard.php">
            <button type="button">
                Skip — I don't have a certification
            </button>
        </a>

    </div>

</div>

</body>

</html>