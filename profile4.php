<?php
// Start session
session_start();

// Include database connection
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's profile information
$sql = "SELECT u.name, u.email, fp.bio, fp.portfolio_url, GROUP_CONCAT(s.skill_name SEPARATOR ', ') AS skills
        FROM users u
        LEFT JOIN freelancer_profiles fp ON u.user_id = fp.user_id
        LEFT JOIN skills s ON fp.skill_id = s.skill_id
        WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
} else {
    echo "No profile found.";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Freelancer Profile</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($profile['bio'])); ?></p>
                <p><strong>Skills:</strong> <?php echo htmlspecialchars($profile['skills']); ?></p>
                <p><strong>Portfolio URL:</strong> <a href="<?php echo htmlspecialchars($profile['portfolio_url']); ?>" target="_blank"><?php echo htmlspecialchars($profile['portfolio_url']); ?></a></p>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
