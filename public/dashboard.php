<?php

session_start();

require_once "../config/config.php";
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/header.php";
require_once "../includes/navbar.php";

?>

<div class="container">

    <h2>Welcome to KaziConnect</h2>

    <p>
        Hello,
        <strong>
            <?= htmlspecialchars($_SESSION['full_name']); ?>
        </strong>
    </p>

    <p>
        You are logged in as:
        <strong>
            <?= htmlspecialchars($_SESSION['role_name']); ?>
        </strong>
    </p>

    <hr>

    <h3>Dashboard</h3>

    <ul>
        <li><a href="profile.php">My Profile</a></li>
        <li><a href="jobs.php">Browse Jobs</a></li>
        <li><a href="applications.php">My Applications</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>

</div>

<?php require_once "../includes/footer.php"; ?>