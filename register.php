<?php include 'header.php'?>
<?php require 'db.php';?>

<?php

// Initialize variables for messages
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error_message = 'All fields are required.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = 'Email is already registered.';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Map roles to role_id
            $role_id_query = $conn->prepare('SELECT role_id FROM roles WHERE role_name = ?');
            $role_id_query->bind_param('s', $role);
            $role_id_query->execute();
            $role_id_query->bind_result($role_id);
            $role_id_query->fetch();
            $role_id_query->close();

            if (!$role_id) {
                $error_message = 'Invalid role selected.';
            } else {
                // Insert new user
                $stmt = $conn->prepare('INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('sssi', $name, $email, $hashed_password, $role_id);

                if ($stmt->execute()) {
                    // Set success message
                    $success_message = 'Registration successful! Redirecting to login page...';

                    // JavaScript for delaying the redirect
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login';
                        }, 3000); // 3 seconds delay
                    </script>";
                } else {
                    $error_message = 'An error occurred: ' . $stmt->error;
                }
            }
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!-- HTML Form -->
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/'; ?>" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="./assets/images/logos/genz-crop.png" width="120" alt="">
                </a>
                <p class='text-center'>Register</p>

                <!-- Display error or success messages -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php elseif (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                  <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                  </div>
                  <div class="row mb-3">
                    <div class="col">
                      <input type="radio" id="talent" name="role" value="talent" <?php if(isset($_POST['role']) && $_POST['role'] == 'talent') echo 'checked'; ?>>
                      <label for="talent"><b>Talent</b></label>
                    </div>
                    <div class="col">
                      <input type="radio" id="client_hiring" name="role" value="client_hiring" <?php if(isset($_POST['role']) && $_POST['role'] == 'client_hiring') echo 'checked'; ?>>
                      <label for="client_hiring"><b>Client Hiring</b></label>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign Up</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">Already have an Account?</p>
                    <a class="text-primary fw-bold ms-2" href="login">Sign In</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="./assets/libs/jquery/dist/jquery.min.js"></script>
<script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
