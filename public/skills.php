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

// Get user's skills
$stmt = $pdo->prepare("
    SELECT 
        s.skill_id,
        s.skill_name
    FROM user_skills us
    INNER JOIN skills s
        ON us.skill_id = s.skill_id
    WHERE us.user_id = ?
    ORDER BY s.skill_name
");

$stmt->execute([$user_id]);

$userSkills = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">

    <h2>My Skills</h2>

    <p>Add the skills you have. You can enter any skill.</p>

    <form action="add_skill.php" method="POST">

        <input
            type="text"
            name="skill_name"
            placeholder="Enter a skill e.g. PHP"
            maxlength="100"
            required
        >

        <button type="submit">
            Add Skill
        </button>

    </form>

    <br>

    <h3>Your Skills</h3>

    <?php if (empty($userSkills)): ?>

        <p>You have not added any skills yet.</p>

    <?php else: ?>

        <ul>

            <?php foreach ($userSkills as $skill): ?>

                <li>

                    <?= htmlspecialchars($skill['skill_name']); ?>

                    <a
                        href="delete_skill.php?id=<?= $skill['skill_id']; ?>"
                        onclick="return confirm('Remove this skill?');"
                    >
                        Remove
                    </a>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>

</div>

<?php require_once "../includes/footer.php"; ?>