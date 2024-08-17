<?php include 'header.php'?>

<?php
// Include database connection file
require_once 'db.php';

// Initialize variables for messages
$error_message = '';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        $error_message = 'Both fields are required.';
    } else {
        // Prepare statement to fetch user details
        $stmt = $conn->prepare('SELECT user_id, password, role_id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password, $role_id);
        $stmt->fetch();

        if ($stmt->num_rows === 0 || !password_verify($password, $hashed_password)) {
            $error_message = 'Invalid email or password. You do not have an account? <a href="register">Register</a>.';
        } else {
            // Start session and set user info
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role_id'] = $role_id;

            // Redirect based on role
            if ($role_id == 1) { // Example role ID for 'talent'
                header('Location: talent_dashboard');
            } elseif ($role_id == 2) { // Example role ID for 'client_hiring'
                header('Location: client_dashboard');
            } elseif ($role_id == 3) { // Example role ID for 'admin'
                header('Location: admin_dashboard');
            } else {
                $error_message = 'Unknown role.';
            }
            exit();
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!-- HTML Login Form -->
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
                <p class='text-center'>Login</p>

                <!-- Display error message -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                  </div>
                  <div class="mb-4">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                  </div>
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                      <label class="form-check-label text-dark" for="flexCheckChecked">
                        Remember this Device
                      </label>
                    </div>
                    <a class="text-primary fw-bold" href="Forgot-password">Forgot Password?</a>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">New to Genz?</p>
                    <a class="text-primary fw-bold ms-2" href="register">Create an account</a>
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