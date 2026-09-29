<?php

session_start();

require_once "../config/database.php";

// Check login
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

$message = "";
$error = "";

// Get employer information
$stmt = $pdo->prepare("
    SELECT
        e.*,
        u.full_name,
        u.email,
        u.phone,
        c.county_name
    FROM employers e
    INNER JOIN users u
        ON e.user_id = u.user_id
    LEFT JOIN counties c
        ON e.county_id = c.county_id
    WHERE e.user_id = ?
");

$stmt->execute([$user_id]);

$employer = $stmt->fetch();

if (!$employer) {
    die("Employer profile not found.");
}


// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $organization_name = trim($_POST['organization_name'] ?? '');
    $organization_type = trim($_POST['organization_type'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $county_id = $_POST['county_id'] ?? '';
    $sub_county = trim($_POST['sub_county'] ?? '');
    $physical_address = trim($_POST['physical_address'] ?? '');
    $website = trim($_POST['website'] ?? '');


    // Validate organization name
    if ($organization_name === '') {

        $error = "Organization name is required.";

    }

    // Validate email
    elseif (
        $contact_email !== '' &&
        !filter_var($contact_email, FILTER_VALIDATE_EMAIL)
    ) {

        $error = "Please enter a valid contact email.";

    }

    else {

        try {

            $update = $pdo->prepare("
                UPDATE employers
                SET
                    organization_name = ?,
                    organization_type = ?,
                    industry = ?,
                    description = ?,
                    contact_person = ?,
                    contact_email = ?,
                    contact_phone = ?,
                    county_id = ?,
                    sub_county = ?,
                    physical_address = ?,
                    website = ?
                WHERE user_id = ?
            ");

            $update->execute([
                $organization_name,
                $organization_type,
                $industry,
                $description,
                $contact_person,
                $contact_email,
                $contact_phone,
                ($county_id !== '' ? $county_id : null),
                $sub_county,
                $physical_address,
                $website,
                $user_id
            ]);

            $message = "Employer profile updated successfully.";


            // Reload information
            $stmt->execute([$user_id]);

            $employer = $stmt->fetch();

        } catch (PDOException $e) {

            $error = "Unable to update employer profile.";

        }

    }

}


// Get counties
$counties = $pdo->query("
    SELECT county_id, county_name
    FROM counties
    ORDER BY county_name
")->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employer Profile | KaziConnect</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .row {
            display: flex;
            gap: 20px;
        }

        .row .form-group {
            flex: 1;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            background: #198754;
            color: white;
            cursor: pointer;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .status {
            background: #fff3cd;
            color: #664d03;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .links {
            margin-top: 25px;
        }

        .links a {
            margin-right: 15px;
        }

        @media (max-width: 700px) {

            .row {
                display: block;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <h1>Employer Profile</h1>

    <p>
        Complete your organization profile before posting opportunities.
    </p>


    <?php if ($message): ?>

        <div class="success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <div class="status">

        Verification Status:

        <strong>
            <?= htmlspecialchars($employer['verification_status']) ?>
        </strong>

    </div>


    <form method="POST">


        <div class="form-group">

            <label>Organization Name *</label>

            <input
                type="text"
                name="organization_name"
                value="<?= htmlspecialchars($employer['organization_name'] ?? '') ?>"
                required
            >

        </div>


        <div class="row">

            <div class="form-group">

                <label>Organization Type</label>

                <input
                    type="text"
                    name="organization_type"
                    placeholder="Company, NGO, Government, etc."
                    value="<?= htmlspecialchars($employer['organization_type'] ?? '') ?>"
                >

            </div>


            <div class="form-group">

                <label>Industry</label>

                <input
                    type="text"
                    name="industry"
                    placeholder="ICT, Finance, Agriculture, etc."
                    value="<?= htmlspecialchars($employer['industry'] ?? '') ?>"
                >

            </div>

        </div>


        <div class="form-group">

            <label>Organization Description</label>

            <textarea
                name="description"
                placeholder="Describe your organization..."
            ><?= htmlspecialchars($employer['description'] ?? '') ?></textarea>

        </div>


        <div class="form-group">

            <label>Contact Person</label>

            <input
                type="text"
                name="contact_person"
                value="<?= htmlspecialchars($employer['contact_person'] ?? '') ?>"
            >

        </div>


        <div class="row">

            <div class="form-group">

                <label>Contact Email</label>

                <input
                    type="email"
                    name="contact_email"
                    value="<?= htmlspecialchars($employer['contact_email'] ?? '') ?>"
                >

            </div>


            <div class="form-group">

                <label>Contact Phone</label>

                <input
                    type="text"
                    name="contact_phone"
                    value="<?= htmlspecialchars($employer['contact_phone'] ?? '') ?>"
                >

            </div>

        </div>


        <div class="row">

            <div class="form-group">

                <label>County</label>

                <select name="county_id">

                    <option value="">
                        -- Select County --
                    </option>

                    <?php foreach ($counties as $county): ?>

                        <option
                            value="<?= $county['county_id'] ?>"
                            <?= ($employer['county_id'] == $county['county_id']) ? 'selected' : '' ?>
                        >

                            <?= htmlspecialchars($county['county_name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label>Sub-County</label>

                <input
                    type="text"
                    name="sub_county"
                    value="<?= htmlspecialchars($employer['sub_county'] ?? '') ?>"
                >

            </div>

        </div>


        <div class="form-group">

            <label>Physical Address</label>

            <input
                type="text"
                name="physical_address"
                placeholder="e.g. Kilifi Town"
                value="<?= htmlspecialchars($employer['physical_address'] ?? '') ?>"
            >

        </div>


        <div class="form-group">

            <label>Website</label>

            <input
                type="url"
                name="website"
                placeholder="https://example.com"
                value="<?= htmlspecialchars($employer['website'] ?? '') ?>"
            >

        </div>


        <button type="submit">
            Save Employer Profile
        </button>

    </form>


    <div class="links">

        <br>

        <a href="employer_dashboard.php">
            Employer Dashboard
        </a>

        |

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>

</body>

</html>