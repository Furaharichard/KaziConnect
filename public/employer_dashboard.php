<?php

session_start();

require_once "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only employers can access this page
if ($_SESSION['role_id'] != 2) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];


// Get employer information
$stmt = $pdo->prepare("
    SELECT
        e.employer_id,
        e.organization_name,
        e.verification_status,
        e.county_id,
        c.county_name
    FROM employers e
    LEFT JOIN counties c
        ON e.county_id = c.county_id
    WHERE e.user_id = ?
");

$stmt->execute([$user_id]);

$employer = $stmt->fetch();

if (!$employer) {
    die("Employer profile not found.");
}

$employer_id = $employer['employer_id'];


// Count opportunities
$opportunityStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM opportunities
    WHERE employer_id = ?
");

$opportunityStmt->execute([$employer_id]);

$total_opportunities = $opportunityStmt->fetchColumn();


// Count published opportunities
$publishedStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM opportunities
    WHERE employer_id = ?
    AND status = 'Published'
");

$publishedStmt->execute([$employer_id]);

$published_opportunities = $publishedStmt->fetchColumn();


// Count applications received
$applicationStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications a
    INNER JOIN opportunities o
        ON a.opportunity_id = o.opportunity_id
    WHERE o.employer_id = ?
");

$applicationStmt->execute([$employer_id]);

$total_applications = $applicationStmt->fetchColumn();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employer Dashboard | KaziConnect</title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: #198754;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .welcome h1 {
            margin-top: 0;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            background: #fff3cd;
            color: #664d03;
            border-radius: 5px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
        }

        .card h2 {
            font-size: 35px;
            margin: 10px 0;
        }

        .actions {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        .actions a {
            display: inline-block;
            background: #198754;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 6px;
            margin: 5px;
        }

        .actions a.secondary {
            background: #555;
        }

        @media (max-width: 700px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .navbar {
                display: block;
            }

            .navbar a {
                display: inline-block;
                margin-top: 10px;
                margin-left: 0;
                margin-right: 15px;
            }

        }

    </style>

</head>

<body>


<div class="navbar">

    <h2>KaziConnect</h2>

    <div>

        <a href="employer_profile.php">
            My Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<div class="container">


    <div class="welcome">

        <h1>
            Welcome, <?= htmlspecialchars($employer['organization_name']) ?>
        </h1>

        <p>
            Manage your organization and connect with qualified
            job seekers through KaziConnect.
        </p>

        <p>

            Verification Status:

            <span class="status">
                <?= htmlspecialchars($employer['verification_status']) ?>
            </span>

        </p>

        <?php if ($employer['county_name']): ?>

            <p>
                County:
                <?= htmlspecialchars($employer['county_name']) ?>
            </p>

        <?php endif; ?>

    </div>


    <div class="cards">


        <div class="card">

            <p>Total Opportunities</p>

            <h2>
                <?= $total_opportunities ?>
            </h2>

        </div>


        <div class="card">

            <p>Published Opportunities</p>

            <h2>
                <?= $published_opportunities ?>
            </h2>

        </div>


        <div class="card">

            <p>Applications Received</p>

            <h2>
                <?= $total_applications ?>
            </h2>

        </div>


    </div>


    <div class="actions">

        <h2>Employer Actions</h2>

        <p>
            From here you will be able to manage your opportunities
            and applications.
        </p>


        <a href="post_opportunity.php">
            + Post Opportunity
        </a>


        <a href="manage_opportunities.php">
            Manage Opportunities
        </a>


        <a href="employer_profile.php">
            Edit Profile
        </a>


        <a href="employer_applications.php">
            View Applications
        </a>


        <a href="logout.php" class="secondary">
            Logout
        </a>

    </div>


</div>


</body>

</html>