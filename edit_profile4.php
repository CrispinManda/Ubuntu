<?php
// Start session
session_start();

// Include database connection
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $portfolio_url = $_POST['portfolio_url'];
    $skills = $_POST['skills']; // Expecting skills as an array of skill IDs

    // Update user's information
    $update_user_sql = "UPDATE users SET name = ?, email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_user_sql);
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update freelancer profile
    $update_profile_sql = "UPDATE freelancer_profiles SET bio = ?, portfolio_url = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_profile_sql);
    $stmt->bind_param("ssi", $bio, $portfolio_url, $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete existing skills
    $delete_skills_sql = "DELETE FROM freelancer_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($delete_skills_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Insert new skills
    foreach ($skills as $skill_id) {
        $insert_skill_sql = "INSERT INTO freelancer_profiles (user_id, skill_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_skill_sql);
        $stmt->bind_param("ii", $user_id, $skill_id);
        $stmt->execute();
    }
    $stmt->close();

    // Redirect to profile page
    header('Location: profile.php');
    exit();
}

// Fetch current user and profile data
$sql = "SELECT u.name, u.email, fp.bio, fp.portfolio_url, GROUP_CONCAT(fp.skill_id SEPARATOR ',') AS skill_ids
        FROM users u
        LEFT JOIN freelancer_profiles fp ON u.user_id = fp.user_id
        WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$skill_ids = explode(',', $profile['skill_ids']);
$stmt->close();

// Fetch all available skills
$skills_sql = "SELECT skill_id, skill_name FROM skills";
$skills_result = $conn->query($skills_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Profile</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($profile['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($profile['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="bio" class="form-label">Bio:</label>
                <textarea id="bio" name="bio" class="form-control"><?php echo htmlspecialchars($profile['bio']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="portfolio_url" class="form-label">Portfolio URL:</label>
                <input type="url" id="portfolio_url" name="portfolio_url" class="form-control" value="<?php echo htmlspecialchars($profile['portfolio_url']); ?>">
            </div>

            <div class="mb-3">
                <label for="skills" class="form-label">Skills:</label><br>
                <?php while ($skill = $skills_result->fetch_assoc()): ?>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input" name="skills[]" value="<?php echo $skill['skill_id']; ?>" <?php if (in_array($skill['skill_id'], $skill_ids)) echo 'checked'; ?>>
                        <label class="form-check-label"><?php echo htmlspecialchars($skill['skill_name']); ?></label>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
