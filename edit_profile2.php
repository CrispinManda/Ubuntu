<?php
session_start();
require 'db_connection.php'; // Include your database connection file

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Debugging: Output the POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>';
    var_dump($_POST); // Debugging: Display POST data
    echo '</pre>';
    exit(); // Stop execution to view the POST data
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $portfolio_url = $_POST['portfolio_url'];
    $skills = $_POST['skills']; // Assuming skills are passed as an array

    // Update user information
    $sql = "UPDATE users SET name = ?, email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();

    // Update or insert bio and portfolio URL
    $sql = "INSERT INTO freelancer_profiles (user_id, bio, portfolio_url) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE bio = VALUES(bio), portfolio_url = VALUES(portfolio_url)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $bio, $portfolio_url);
    $stmt->execute();

    // Clear existing skills
    $sql = "DELETE FROM freelancer_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Add new skills
    foreach ($skills as $skill_id) {
        if (!empty($skill_id)) {
            // Insert skill into freelancer_profiles
            $sql = "INSERT INTO freelancer_profiles (user_id, skill_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $skill_id);
            $stmt->execute();
        }
    }

    header('Location: profile.php');
    exit();
}

// Fetch current user information
$sql = "SELECT users.name, users.email, COALESCE(freelancer_profiles.bio, '') as bio, COALESCE(freelancer_profiles.portfolio_url, '') as portfolio_url, GROUP_CONCAT(skills.skill_id) as skills 
        FROM users
        LEFT JOIN freelancer_profiles ON users.user_id = freelancer_profiles.user_id
        LEFT JOIN skills ON freelancer_profiles.skill_id = skills.skill_id
        WHERE users.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch all skills for dropdown
$sql = "SELECT skill_id, skill_name FROM skills";
$skillsResult = $conn->query($sql);

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
    <title>Edit Profile</title>
</head>
<body>
    <h1>Edit Profile</h1>
    <form action="edit_profile.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <br>
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" rows="4" cols="50"><?php echo htmlspecialchars($user['bio']); ?></textarea>
        <br>
        <label for="portfolio_url">Portfolio URL:</label>
        <input type="url" id="portfolio_url" name="portfolio_url" value="<?php echo htmlspecialchars($user['portfolio_url']); ?>">
        <br>
        <label for="skills">Skills (select multiple, use Ctrl/Cmd to select more):</label>
        <select id="skills" name="skills[]" multiple size="5">
            <?php while ($row = $skillsResult->fetch_assoc()): ?>
                <option value="<?php echo $row['skill_id']; ?>" 
                    <?php if (in_array($row['skill_id'], explode(',', $user['skills']))) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['skill_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br>
        <button type="submit">Update Profile</button>
    </form>
    <a href="profile.php">Back to Profile</a>
</body>
</html>
