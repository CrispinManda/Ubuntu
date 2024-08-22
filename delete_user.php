<?php
// Database connection
include 'db.php';
// Get user ID from query string
$user_id = intval($_GET['id']);

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "User deleted successfully";
} else {
    echo "Error deleting user: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back to admin dashboard
header("Location: admin_dashboard");
exit();
?>
