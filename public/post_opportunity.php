<?php

session_start();

require_once "../config/database.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only employers
if ($_SESSION['role_id'] != 2) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];


// Get employer ID
$stmt = $pdo->prepare("
    SELECT employer_id, organization_name
    FROM employers
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$employer = $stmt->fetch();

if (!$employer) {
    die("Employer profile not found.");
}

$employer_id = $employer['employer_id'];


// Get categories
$categories = $pdo->query("
    SELECT category_id, category_name
    FROM categories
    ORDER BY category_name
")->fetchAll();


// Get counties
$counties = $pdo->query("
    SELECT county_id, county_name
    FROM counties
    ORDER BY county_name
")->fetchAll();


$error = "";


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title'] ?? '');
    $opportunity_type = trim($_POST['opportunity_type'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $education_level = trim($_POST['education_level'] ?? '');
    $experience_required = trim($_POST['experience_required'] ?? '');
    $county_id = $_POST['county_id'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $employment_type = $_POST['employment_type'] ?? '';
    $positions = $_POST['positions'] ?? 1;
    $salary_min = $_POST['salary_min'] ?? '';
    $salary_max = $_POST['salary_max'] ?? '';
    $application_deadline = $_POST['application_deadline'] ?? '';
    $status = $_POST['status'] ?? 'Draft';


    // Validation
    if ($title === '') {

        $error = "Opportunity title is required.";

    } elseif ($opportunity_type === '') {

        $error = "Please select an opportunity type.";

    } elseif ($description === '') {

        $error = "Opportunity description is required.";

    } elseif (!is_numeric($positions) || $positions < 1) {

        $error = "Positions must be at least 1.";

    } elseif (
        $salary_min !== '' &&
        !is_numeric($salary_min)
    ) {

        $error = "Minimum salary must be a valid number.";

    } elseif (
        $salary_max !== '' &&
        !is_numeric($salary_max)
    ) {

        $error = "Maximum salary must be a valid number.";

    } elseif (
        $salary_min !== '' &&
        $salary_max !== '' &&
        $salary_max < $salary_min
    ) {

        $error = "Maximum salary cannot be lower than minimum salary.";

    } else {

        try {

            $insert = $pdo->prepare("
                INSERT INTO opportunities
                (
                    employer_id,
                    category_id,
                    title,
                    opportunity_type,
                    description,
                    requirements,
                    education_level,
                    experience_required,
                    county_id,
                    location,
                    employment_type,
                    positions,
                    salary_min,
                    salary_max,
                    application_deadline,
                    status
                )
                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");


            $insert->execute([

                $employer_id,

                ($category_id !== '' ? $category_id : null),

                $title,

                $opportunity_type,

                $description,

                ($requirements !== '' ? $requirements : null),

                ($education_level !== '' ? $education_level : null),

                ($experience_required !== '' ? $experience_required : null),

                ($county_id !== '' ? $county_id : null),

                ($location !== '' ? $location : null),

                ($employment_type !== '' ? $employment_type : null),

                (int)$positions,

                ($salary_min !== '' ? $salary_min : null),

                ($salary_max !== '' ? $salary_max : null),

                ($application_deadline !== '' ? $application_deadline : null),

                $status

            ]);


            header("Location: manage_opportunities.php?success=1");

            exit;


        } catch (PDOException $e) {

            $error = "Unable to create opportunity: " . $e->getMessage();

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Post Opportunity | KaziConnect</title>


    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fa;
        }

        .container {
            width: 90%;
            max-width: 950px;
            margin: 35px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            margin-top: 0;
        }

        .intro {
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .row {
            display: flex;
            gap: 20px;
        }

        .row .form-group {
            flex: 1;
        }

        .button {
            background: #198754;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        .button:hover {
            background: #146c43;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
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

    <h1>Post an Opportunity</h1>

    <p class="intro">

        Create a job, internship, industrial attachment,
        apprenticeship, training or scholarship opportunity.

    </p>


    <?php if ($error): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <form method="POST">


        <!-- Title -->

        <div class="form-group">

            <label>
                Opportunity Title *
            </label>

            <input
                type="text"
                name="title"
                placeholder="e.g. Software Developer Intern"
                value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                required
            >

        </div>


        <!-- Opportunity Type -->

        <div class="form-group">

            <label>
                Opportunity Type *
            </label>

            <select name="opportunity_type" required>

                <option value="">
                    -- Select Type --
                </option>

                <option value="Job">
                    Job
                </option>

                <option value="Internship">
                    Internship
                </option>

                <option value="Attachment">
                    Industrial Attachment
                </option>

                <option value="Apprenticeship">
                    Apprenticeship
                </option>

                <option value="Training">
                    Training
                </option>

                <option value="Scholarship">
                    Scholarship
                </option>

            </select>

        </div>


        <!-- Category -->

        <div class="form-group">

            <label>
                Category
            </label>

            <select name="category_id">

                <option value="">
                    -- Select Category --
                </option>

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?= $category['category_id'] ?>"
                    >

                        <?= htmlspecialchars($category['category_name']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- Description -->

        <div class="form-group">

            <label>
                Description *
            </label>

            <textarea
                name="description"
                placeholder="Describe the opportunity..."
                required
            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

        </div>


        <!-- Requirements -->

        <div class="form-group">

            <label>
                Requirements
            </label>

            <textarea
                name="requirements"
                placeholder="List the skills, qualifications or requirements..."
            ><?= htmlspecialchars($_POST['requirements'] ?? '') ?></textarea>

        </div>


        <!-- Education + Experience -->

        <div class="row">


            <div class="form-group">

                <label>
                    Education Level
                </label>

                <input
                    type="text"
                    name="education_level"
                    placeholder="e.g. Diploma, Degree"
                    value="<?= htmlspecialchars($_POST['education_level'] ?? '') ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Experience Required
                </label>

                <input
                    type="text"
                    name="experience_required"
                    placeholder="e.g. No experience required"
                    value="<?= htmlspecialchars($_POST['experience_required'] ?? '') ?>"
                >

            </div>


        </div>


        <!-- County + Location -->

        <div class="row">


            <div class="form-group">

                <label>
                    County
                </label>

                <select name="county_id">

                    <option value="">
                        -- Select County --
                    </option>

                    <?php foreach ($counties as $county): ?>

                        <option
                            value="<?= $county['county_id'] ?>"
                        >

                            <?= htmlspecialchars($county['county_name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label>
                    Location
                </label>

                <input
                    type="text"
                    name="location"
                    placeholder="e.g. Kilifi Town"
                    value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
                >

            </div>


        </div>


        <!-- Employment Type -->

        <div class="form-group">

            <label>
                Employment Type
            </label>

            <select name="employment_type">

                <option value="">
                    -- Select Employment Type --
                </option>

                <option value="Full-time">
                    Full-time
                </option>

                <option value="Part-time">
                    Part-time
                </option>

                <option value="Contract">
                    Contract
                </option>

                <option value="Temporary">
                    Temporary
                </option>

            </select>

        </div>


        <!-- Positions -->

        <div class="form-group">

            <label>
                Number of Positions
            </label>

            <input
                type="number"
                name="positions"
                min="1"
                value="<?= htmlspecialchars($_POST['positions'] ?? '1') ?>"
            >

        </div>


        <!-- Salary -->

        <div class="row">


            <div class="form-group">

                <label>
                    Minimum Salary
                </label>

                <input
                    type="number"
                    step="0.01"
                    name="salary_min"
                    placeholder="e.g. 25000"
                    value="<?= htmlspecialchars($_POST['salary_min'] ?? '') ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Maximum Salary
                </label>

                <input
                    type="number"
                    step="0.01"
                    name="salary_max"
                    placeholder="e.g. 50000"
                    value="<?= htmlspecialchars($_POST['salary_max'] ?? '') ?>"
                >

            </div>


        </div>


        <!-- Deadline -->

        <div class="form-group">

            <label>
                Application Deadline
            </label>

            <input
                type="date"
                name="application_deadline"
                value="<?= htmlspecialchars($_POST['application_deadline'] ?? '') ?>"
            >

        </div>


        <!-- Status -->

        <div class="form-group">

            <label>
                Status
            </label>

            <select name="status">

                <option value="Draft">
                    Save as Draft
                </option>

                <option value="Published">
                    Publish Opportunity
                </option>

            </select>

        </div>


        <button
            type="submit"
            class="button"
        >

            Save Opportunity

        </button>


    </form>


    <div class="links">

        <a href="employer_dashboard.php">
            ← Employer Dashboard
        </a>

        <a href="manage_opportunities.php">
            Manage Opportunities
        </a>

    </div>


</div>


</body>

</html>