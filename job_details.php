<?php
include 'db.php'; 

// Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login"); // Redirect to login if not logged in
    exit();
}

// Check if the job ID is provided
if (!isset($_GET['job_id'])) {
    header("Location: jobs.php"); // Redirect to jobs list if no job ID
    exit();
}

$job_id = $conn->real_escape_string($_GET['job_id']);

// Fetch the job details from the database
$sql = "SELECT job_postings.*, users.name, skills.skill_name 
        FROM job_postings 
        JOIN users ON job_postings.user_id = users.user_id 
        JOIN skills ON job_postings.skill_id = skills.skill_id 
        WHERE job_id = '$job_id'";
$job = $conn->query($sql)->fetch_assoc();

if (!$job) {
    header("Location: jobs."); // Redirect to jobs list if job not found
    exit();
}

// Handle job application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $applicant_id = $_SESSION['user_id'];
    
    // Check if user has already applied for this job
    $check_sql = "SELECT * FROM applications WHERE job_id = '$job_id' AND user_id = '$applicant_id'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-warning'>You have already applied for this job.</div>";
    } else {
        $apply_sql = "INSERT INTO applications (job_id, user_id) VALUES ('$job_id', '$applicant_id')";
        if ($conn->query($apply_sql) === TRUE) {
            $message = "<div class='alert alert-success'>You have successfully applied for the job!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}


$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($conn) {
    // Fetch non-admin users with search functionality and order by latest registration
    $sql = "SELECT users.user_id, users.name, users.email, roles.role_name 
            FROM users 
            INNER JOIN roles ON users.role_id = roles.role_id 
            WHERE users.role_id != 3";

    if (!empty($search)) {
        $sql .= " AND (users.name LIKE '%$search%' OR users.email LIKE '%$search%' OR roles.role_name LIKE '%$search%')";
    }

    $sql .= " ORDER BY users.created_at DESC"; // Order by registration date (latest first)

    $users = $conn->query($sql);
    
    // Fetch count of different user roles and jobs
    $userCount = $conn->query("SELECT COUNT(*) as count FROM users WHERE users.role_id != 3")->fetch_assoc()['count'];
    $freelancerCount = $conn->query("SELECT COUNT(*) as count FROM users WHERE role_id = 1")->fetch_assoc()['count'];
    $clientsCount = $conn->query("SELECT COUNT(*) as count FROM users WHERE role_id = 2")->fetch_assoc()['count'];
    $jobCount = $conn->query("SELECT COUNT(*) as count FROM job_postings")->fetch_assoc()['count'];
} else {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php include 'header.php'; ?>

<!-- Rest of your HTML code here -->

<!--  Body Wrapper -->
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
        <!-- Sidebar scroll-->
        <div>
            <div class="brand-logo d-flex align-items-center justify-content-between">
                <a href="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/admin_dashboard'; ?>" class="text-nowrap logo-img">
                    <img src="./assets/images/logos/genz-crop.png" style="width: 150px; height: auto;" />
                </a>
                <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                    <i class="ti ti-x fs-8"></i>
                </div>
            </div>
            <!-- Sidebar navigation-->
            <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                <ul id="sidebarnav">
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Home</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="#" aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Admin Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="talents" aria-expanded="false">
                            <span>
                                <i class="ti ti-article"></i>
                            </span>
                            <span class="hide-menu">Talents</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="clients" aria-expanded="false">
                            <span>
                                <i class="ti ti-alert-circle"></i>
                            </span>
                            <span class="hide-menu">Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="jobs" aria-expanded="false">
                            <span>
                                <i class="ti ti-cards"></i>
                            </span>
                            <span class="hide-menu">Jobs</span>
                        </a>
                    </li>
                    <li class='sidebar-item'>
                        <a class="sidebar-link" href="Profile" aria-expanded="false">
                            <span>
                                <i class="ti ti-user fs-6"></i>
                            </span>
                            <span>
                                <p class="mb-0 fs-3">My Profile</p>
                            </span>
                        </a>
                    </li>
                    <li class ='sidebar-item'>
                        <a class="sidebar-link" href="task" aria-expanded="false">
                            <span>
                                <i class="ti ti-list-check fs-6"></i>
                            </span>
                            <span>
                                <p class="mb-0 fs-3">Task</p>
                            </span>
                        </a>
                    </li>
                                        <li class ='sidebar-item'>
                        <a class="sidebar-link" href="skills" aria-expanded="false">
                            <span>
                                <i class="ti ti-list-check fs-6"></i>
                            </span>
                            <span>
                                <p class="mb-0 fs-3">skills</p>
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
        <!--  Header Start -->
        <header class="app-header">
            <nav class="navbar navbar-expand-lg navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item d-block d-xl-none">
                        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <a href="post-job"><button type="button" class="btn btn-primary btn-lg">Post a New job</button></a>
                </ul>
                <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                <div class="message-body">
                                    <a  href="profile" class="d-flex align-items-center gap-2 dropdown-item">
                                        <i class="ti ti-user fs-6"></i>
                                        <p class="mb-0 fs-3">My Profile</p>
                                    </a>
                                    <a href="logout" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!--  Header End -->
    <div class="container-fluid">
        <div class="row">
            <!-- Dashboard Cards -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text"><?php echo $userCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Freelancers</h5>
                        <p class="card-text"><?php echo $freelancerCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Clients</h5>
                        <p class="card-text"><?php echo $clientsCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Jobs</h5>
                        <p class="card-text"><?php echo $jobCount; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <!--  Row 1 -->
        <!-- User Management Table -->

        <div class="container mt-5">
        <?php if (isset($message)) { echo $message; } ?>
        <h2><?php echo htmlspecialchars($job['title']); ?></h2>
        <p><strong>Posted by:</strong> <?php echo htmlspecialchars($job['name']); ?> on <?php echo htmlspecialchars($job['created_at']); ?></p>
        <p><strong>Required Skill:</strong> <?php echo htmlspecialchars($job['skill_name']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="">
                <button type="submit" class="btn btn-success">Apply for this Job</button>
            </form>
        <?php else: ?>
            <p><a href="login.php" class="btn btn-primary">Log in to Apply</a></p>
        <?php endif; ?>
    </div>


</div>

    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>
</body>
</html>
