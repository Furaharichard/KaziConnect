<?php
require_once "../config/database.php";

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

    <title>Register - KaziConnect</title>

</head>

<body>

<h2>Create Your KaziConnect Account</h2>

<form action="process_register.php" method="POST">

    <div>
        <label>Full Name</label><br>

        <input
            type="text"
            name="full_name"
            required
        >
    </div>

    <br>

    <div>
        <label>Email Address</label><br>

        <input
            type="email"
            name="email"
            required
        >
    </div>

    <br>

    <div>
        <label>Phone Number</label><br>

        <input
            type="text"
            name="phone"
            required
        >
    </div>

    <br>

    <div>
        <label>County</label><br>

        <select name="county_id" required>

            <option value="">
                -- Select County --
            </option>

            <?php foreach ($counties as $county): ?>

                <option value="<?= $county['county_id'] ?>">

                    <?= htmlspecialchars($county['county_name']) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <br>

    <div>
        <label>Account Type</label><br>

        <select name="role_id" required>

            <option value="">
                -- Select Account Type --
            </option>

            <option value="3">
                Job Seeker
            </option>

            <option value="2">
                Employer
            </option>

        </select>

    </div>

    <br>

    <div>
        <label>Password</label><br>

        <input
            type="password"
            name="password"
            required
        >
    </div>

    <br>

    <div>
        <label>Confirm Password</label><br>

        <input
            type="password"
            name="confirm_password"
            required
        >
    </div>

    <br>

    <button type="submit">
        Create Account
    </button>

</form>

</body>

</html>