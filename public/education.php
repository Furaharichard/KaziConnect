<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/header.php";
require_once "../includes/navbar.php";

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM education
    WHERE user_id = ?
    ORDER BY end_year DESC, education_id DESC
");

$stmt->execute([$user_id]);

$educationRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">

    <h2>My Education</h2>

    <h3>Add Education</h3>

    <form action="add_education.php" method="POST">

        <label>Institution</label><br>
        <input
            type="text"
            name="institution"
            placeholder="e.g. University of Nairobi"
            maxlength="200"
            required
        >

        <br><br>

        <label>Qualification</label><br>
        <input
            type="text"
            name="qualification"
            placeholder="e.g. Bachelor's Degree"
            maxlength="200"
            required
        >

        <br><br>

        <label>Field of Study</label><br>
        <input
            type="text"
            name="field_of_study"
            placeholder="e.g. Computer Science"
            maxlength="200"
        >

        <br><br>

        <label>Start Year</label><br>
        <input
            type="number"
            name="start_year"
            min="1950"
            max="2100"
        >

        <br><br>

        <label>End Year</label><br>
        <input
            type="number"
            name="end_year"
            min="1950"
            max="2100"
        >

        <br><br>

        <label>Description</label><br>
        <textarea
            name="description"
            rows="4"
            placeholder="Add any additional information about your education..."
        ></textarea>

        <br><br>

        <button type="submit">
            Add Education
        </button>

    </form>

    <hr>

    <h3>My Education History</h3>

    <?php if (empty($educationRecords)): ?>

        <p>No education records added yet.</p>

    <?php else: ?>

        <?php foreach ($educationRecords as $education): ?>

            <div>

                <h4>
                    <?= htmlspecialchars($education['qualification']); ?>
                </h4>

                <p>
                    <strong>Institution:</strong>
                    <?= htmlspecialchars($education['institution']); ?>
                </p>

                <?php if (!empty($education['field_of_study'])): ?>
                    <p>
                        <strong>Field of Study:</strong>
                        <?= htmlspecialchars($education['field_of_study']); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($education['start_year']) || !empty($education['end_year'])): ?>
                    <p>
                        <strong>Period:</strong>
                        <?= htmlspecialchars($education['start_year'] ?? ''); ?>
                        -
                        <?= htmlspecialchars($education['end_year'] ?? 'Present'); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($education['description'])): ?>
                    <p>
                        <strong>Description:</strong>
                        <?= nl2br(htmlspecialchars($education['description'])); ?>
                    </p>
                <?php endif; ?>

                <a
                    href="delete_education.php?id=<?= $education['education_id']; ?>"
                    onclick="return confirm('Delete this education record?');"
                >
                    Delete
                </a>

            </div>

            <hr>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php require_once "../includes/footer.php"; ?>