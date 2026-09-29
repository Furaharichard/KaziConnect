<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - KaziConnect</title>

</head>

<body>

<h2>Login to KaziConnect</h2>

<?php if (isset($_GET['registered'])): ?>

    <p style="color: green;">
        Account created successfully. Please login.
    </p>

<?php endif; ?>

<form action="process_login.php" method="POST">

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

        <label>Password</label><br>

        <input
            type="password"
            name="password"
            required
        >

    </div>

    <br>

    <button type="submit">
        Login
    </button>

</form>

</body>

</html>