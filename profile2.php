<?php
session_start();
require 'db.php'; // Include your database connection file

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$sql = "SELECT users.name, users.email, freelancer_profiles.bio, GROUP_CONCAT(skills.skill_name) as skills 
        FROM users
        LEFT JOIN freelancer_profiles ON users.user_id = freelancer_profiles.user_id
        LEFT JOIN skills ON freelancer_profiles.skill_id = skills.skill_id
        WHERE users.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user exists
if (!$user) {
    echo "User not found.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <h1>Profile</h1>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
    <p><strong>Skills:</strong> <?php echo htmlspecialchars($user['skills']); ?></p>
    <a href="edit_profile.php">Edit Profile</a>
</body>
</html>
