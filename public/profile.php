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
    SELECT 
        users.*,
        counties.county_name
    FROM users
    LEFT JOIN counties 
        ON users.county_id = counties.county_id
    WHERE users.user_id = ?
");

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    die("User profile not found.");
}

?>

<div class="container">

    <h2>My Profile</h2>

    <?php if (!empty($user['profile_photo'])): ?>

        <img
            src="uploads/profiles/<?= htmlspecialchars($user['profile_photo']); ?>"
            alt="Profile Photo"
            width="150"
            height="150"
            style="object-fit: cover; border-radius: 50%;"
        >

        <br><br>

    <?php endif; ?>

    <form
        action="update_profile.php"
        method="POST"
        enctype="multipart/form-data"
    >

        <label>Full Name</label><br>

        <input
            type="text"
            name="full_name"
            value="<?= htmlspecialchars($user['full_name']); ?>"
            required
        >

        <br><br>


        <label>Email</label><br>

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($user['email']); ?>"
            required
        >

        <br><br>


        <label>Phone</label><br>

        <input
            type="text"
            name="phone"
            value="<?= htmlspecialchars($user['phone']); ?>"
            required
        >

        <br><br>


        <label>County</label><br>

        <input
            type="text"
            value="<?= htmlspecialchars($user['county_name'] ?? 'Not selected'); ?>"
            readonly
        >

        <br><br>


        <label>Profile Photo</label><br>

        <input
            type="file"
            name="profile_photo"
            accept="image/*"
        >

        <br><br>


        <button type="submit">
            Update Profile
        </button>

    </form>

</div>

<?php require_once "../includes/footer.php"; ?>