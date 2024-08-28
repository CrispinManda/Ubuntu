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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $portfolio_url = $_POST['portfolio_url'];
    $skills = isset($_POST['skills']) ? $_POST['skills'] : []; // Default to an empty array if no skills are selected

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
                    <li class='sidebar-item'>
                        <a class="sidebar-link" href="application" aria-expanded="false">
                            <span>
                                <i class="ti ti-user fs-6"></i>
                            </span>
                            <span>
                                <p class="mb-0 fs-3">Application</p>
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
                    <form method="GET" action="admin_dashboard" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search users by name, email, or role" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
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
        <!--  Row 1 -->
        <!-- User Management Table -->



 <div class="container bootstrap snippets bootdey">
        <div class="row">
            <div class="profile-nav col-md-3">
                <div class="panel">
                    <div class="user-heading round shadow p-3 mb-5 bg-white rounded">
                        <a href="#">
                            <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                        </a>
                        <h1><?php echo htmlspecialchars($profile['name']); ?></h1>
                        <p class='text-dark'><?php echo htmlspecialchars($profile['email']); ?></p>
                    </div>

                    <ul class="nav nav-pills nav-stacked shadow rounded p-3">
                        <li><a href="edit_profile.php"> <i class="fa fa-edit"></i> Edit profile</a></li>
                    </ul>
                </div>
            </div>
            <div class="profile-info col-md-9">
                <div class="panel">
                    <div class="bio-graph-heading shadow mb-5 rounded">
                        <!-- You can add some static text or dynamic heading here -->
                        Welcome to your profile!
                    </div>
                    <div class="panel-body bio-graph-info shadow rounded p-4">
                        <h1>Bio Information</h1>
                        <div class="row">
                            <div class="bio-row">
                                <p><span>Name </span>: <?php echo htmlspecialchars($profile['name']); ?></p>
                            </div>
                            <div class="bio-row">
                                <p><span>Email </span>: <?php echo htmlspecialchars($profile['email']); ?></p>
                            </div>
                         <div class="bio-row">
    <p><span>Skills</span>: 
        <?php echo isset($profile['skills']) ? htmlspecialchars($profile['skills']) : 'No skills listed'; ?>
    </p>
</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     
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

  </div>
</div>




<style>
.profile-nav, .profile-info{
    margin-top:100px;   
}

.profile-nav .user-heading {
    background: #FAFAFB; 
    color: #FAFAFB;
    border-radius: 4px 4px 0 0;
    -webkit-border-radius: 4px 4px 0 0;
    padding: 30px;
    text-align: center;
}

.profile-nav .user-heading.round a  {
    border-radius: 50%;
    -webkit-border-radius: 50%;
    border: 10px solid rgba(255,255,255,0.3);
    display: inline-block;
}

.profile-nav .user-heading a img {
    width: 112px;
    height: 112px;
    border-radius: 50%;
    -webkit-border-radius: 50%;
}

.profile-nav .user-heading h1 {
    font-size: 22px;
    font-weight: 300;
    margin-bottom: 5px;
}

.profile-nav .user-heading p {
    font-size: 12px;
}

.profile-nav ul {
    margin-top: 1px;
}

.profile-nav ul > li {
    border-bottom: 1px solid #ebeae6;
    margin-top: 0;
    line-height: 30px;
}

.profile-nav ul > li:last-child {
    border-bottom: none;
}

.profile-nav ul > li > a {
    border-radius: 0;
    -webkit-border-radius: 0;
    color: #89817f;
    border-left: 5px solid #FAFAFB;
}

.profile-nav ul > li > a:hover, .profile-nav ul > li > a:focus, .profile-nav ul li.active  a {
    background: #f8f7f5 !important;
    border-left: 5px solid #fbc02d;
    color: #89817f !important;
}

.profile-nav ul > li:last-child > a:last-child {
    border-radius: 0 0 4px 4px;
    -webkit-border-radius: 0 0 4px 4px;
}

.profile-nav ul > li > a > i{
    font-size: 16px;
    padding-right: 10px;
    color: #bcb3aa;
}

.r-activity {
    margin: 6px 0 0;
    font-size: 12px;
}


.p-text-area, .p-text-area:focus {
    border: none;
    font-weight: 300;
    box-shadow: none;
    color: #c3c3c3;
    font-size: 16px;
}

.profile-info .panel-footer {
    background-color:#f8f7f5 ;
    border-top: 1px solid #e7ebee;
}

.profile-info .panel-footer ul li a {
    color: #7a7a7a;
}

.bio-graph-heading {
    background: #FAFAFB;
    color: black;
    text-align: center;
    font-style: italic;
    padding: 40px 110px;
    border-radius: 4px 4px 0 0;
    -webkit-border-radius: 4px 4px 0 0;
    font-size: 16px;
    font-weight: 300;
}

.bio-graph-info {
    color: #89817e;
}

.bio-graph-info h1 {
    font-size: 22px;
    font-weight: 300;
    margin: 0 0 20px;
}

.bio-row {
    width: 50%;
    float: left;
    margin-bottom: 10px;
    padding:0 15px;
}

.bio-row p span {
    width: 100px;
    display: inline-block;
}

.bio-chart, .bio-desk {
    float: left;
}

.bio-chart {
    width: 40%;
}

.bio-desk {
    width: 60%;
}

.bio-desk h4 {
    font-size: 15px;
    font-weight:400;
}

.bio-desk h4.terques {
    color: #4CC5CD;
}

.bio-desk h4.red {
    color: #e26b7f;
}

.bio-desk h4.green {
    color: #97be4b;
}

.bio-desk h4.purple {
    color: #caa3da;
}

.file-pos {
    margin: 6px 0 10px 0;
}

.profile-activity h5 {
    font-weight: 300;
    margin-top: 0;
    color: #c3c3c3;
}

.summary-head {
    background: #ee7272;
    color: #FAFAFB;
    text-align: center;
    border-bottom: 1px solid #ee7272;
}

.summary-head h4 {
    font-weight: 300;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.summary-head p {
    color: rgba(255,255,255,0.6);
}

ul.summary-list {
    display: inline-block;
    padding-left:0 ;
    width: 100%;
    margin-bottom: 0;
}

ul.summary-list > li {
    display: inline-block;
    width: 19.5%;
    text-align: center;
}

ul.summary-list > li > a > i {
    display:block;
    font-size: 18px;
    padding-bottom: 5px;
}

ul.summary-list > li > a {
    padding: 10px 0;
    display: inline-block;
    color: #818181;
}

ul.summary-list > li  {
    border-right: 1px solid #eaeaea;
}

ul.summary-list > li:last-child  {
    border-right: none;
}

.activity {
    width: 100%;
    float: left;
    margin-bottom: 10px;
}

.activity.alt {
    width: 100%;
    float: right;
    margin-bottom: 10px;
}

.activity span {
    float: left;
}

.activity.alt span {
    float: right;
}
.activity span, .activity.alt span {
    width: 45px;
    height: 45px;
    line-height: 45px;
    border-radius: 50%;
    -webkit-border-radius: 50%;
    background: #eee;
    text-align: center;
    color: #FAFAFB;
    font-size: 16px;
}

.activity.terques span {
    background: #8dd7d6;
}

.activity.terques h4 {
    color: #8dd7d6;
}
.activity.purple span {
    background: #b984dc;
}

.activity.purple h4 {
    color: #b984dc;
}
.activity.blue span {
    background: #90b4e6;
}

.activity.blue h4 {
    color: #90b4e6;
}
.activity.green span {
    background: #aec785;
}

.activity.green h4 {
    color: #aec785;
}

.activity h4 {
    margin-top:0 ;
    font-size: 16px;
}

.activity p {
    margin-bottom: 0;
    font-size: 13px;
}

.activity .activity-desk i, .activity.alt .activity-desk i {
    float: left;
    font-size: 18px;
    margin-right: 10px;
    color: #bebebe;
}

.activity .activity-desk {
    margin-left: 70px;
    position: relative;
}

.activity.alt .activity-desk {
    margin-right: 70px;
    position: relative;
}

.activity.alt .activity-desk .panel {
    float: right;
    position: relative;
}

.activity-desk .panel {
    background: #F4F4F4 ;
    display: inline-block;
}


.activity .activity-desk .arrow {
    border-right: 8px solid #F4F4F4 !important;
}
.activity .activity-desk .arrow {
    border-bottom: 8px solid transparent;
    border-top: 8px solid transparent;
    display: block;
    height: 0;
    left: -7px;
    position: absolute;
    top: 13px;
    width: 0;
}

.activity-desk .arrow-alt {
    border-left: 8px solid #F4F4F4 !important;
}

.activity-desk .arrow-alt {
    border-bottom: 8px solid transparent;
    border-top: 8px solid transparent;
    display: block;
    height: 0;
    right: -7px;
    position: absolute;
    top: 13px;
    width: 0;
}

.activity-desk .album {
    display: inline-block;
    margin-top: 10px;
}

.activity-desk .album a{
    margin-right: 10px;
}

.activity-desk .album a:last-child{
    margin-right: 0px;
}
</style>

        
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>
</body>
</html>