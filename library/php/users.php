<?php
include("ini.php");

$fname_err = $username_err = $email_err = $password_err = $confirm_password_err = $mobile_number_err = "";
$fname = $lname = $username = $email = $password = $mobile_number = $countrycode = $role = "";

// Prevent unauthorized access
if (!isset($_SESSION["user"]) || $row['role'] !== "Admin") {
    header("location: library.php");
    exit;
}

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number, default is 1
$records_per_page = 8; // Number of records to display per page
$offset = ($page - 1) * $records_per_page; // Offset for SQL query

// Sorting users and searching
$sort_by = isset($_GET['sort_by']) ? mysqli_real_escape_string($conn, $_GET['sort_by']) : 'role';

$sort_options = [
    'user_id' => 'ID',
    'full_name' => 'Name',
    'username' => 'Username',
    'email' => 'Email',
    'last_online' => 'Online',
    'role' => 'Role'
];

$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
}

if (isset($_POST['add'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $countrycode = mysqli_real_escape_string($conn, $_POST['countrycode']);
    
    $full_num = $countrycode . $mobile_number;

    $err = false;

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (strlen($username) > 30) {
        $username_err = "Username is too long.";
        $err = true;
    } elseif (mysqli_num_rows($result) > 0) {
        $username_err = "Username is already taken.";
        $err = true;
    }

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($result) > 0) {
        $email_err = "Email is already in use.";
        $err = true;
    }

    $result = mysqli_query($conn, "SELECT * FROM users WHERE mobile_number='$full_num'");
    if (mysqli_num_rows($result) > 0) {
        $mobile_number_err = "Mobile number is already in use.";
        $err = true;
    }

    if (strlen($password) < 8) {
        $password_err = "Password must be at least 8 characters long.";
        $err = true;
    } elseif (!preg_match('/^(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $password_err = "Password must include a number and a special character.";
        $err = true;
    }

    if ($password !== $confirm_password) {
        $confirm_password_err = "Passwords do not match.";
        $err = true;
    }

    // Error checking
    if (!$err) {
        // Encrypt password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $full_name = $fname . " " . $lname;

        // Insert user into database
        $query = "INSERT INTO users (full_name, username, email, password, mobile_number, role) 
                  VALUES ('$full_name', '$username', '$email', '$hashed_password', '$full_num', '$role')";
        
        if (mysqli_query($conn, $query)) {
            header('Location: users.php');
            exit;
        }
    }
}

// Handle update submission
if (isset($_POST['update'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $update_query = "UPDATE users SET 
                     full_name = '$full_name', 
                     username = '$username', 
                     email = '$email', 
                     mobile_number = '$mobile_number', 
                     role = '$role' 
                     WHERE user_id = $user_id";

    if (mysqli_query($conn, $update_query)) {
        // Redirect or show success message
        header("location: users.php");
        exit;
    } else {
        echo "Error updating user: " . mysqli_error($conn);
    }
}

// Handle delete action
if (isset($_POST['delete'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['id']);

    $delete_query = "DELETE FROM users WHERE user_id = $user_id";

    if (mysqli_query($conn, $delete_query)) {
        header("location: users.php");
        exit;
    }
}

// Count total records
$count_query = "SELECT COUNT(*) AS total FROM users $search_query";
$count_result = mysqli_query($conn, $count_query);
$count_data = mysqli_fetch_assoc($count_result);
$total_records = $count_data['total'];

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);

// Display users with pagination and sorting
$users_qry = "SELECT user_id, full_name, username, email, mobile_number, last_online, role 
              FROM users $search_query 
              ORDER BY 
              CASE
                WHEN '$sort_by' = 'user_id' THEN user_id
                WHEN '$sort_by' = 'full_name' THEN full_name
                WHEN '$sort_by' = 'username' THEN username
                WHEN '$sort_by' = 'email' THEN email
                ELSE role
              END ASC
              LIMIT $offset, $records_per_page";
$users_rst = mysqli_query($conn, $users_qry);

function formatLastOnline($datetime) {
    if ($datetime === null) {
        return 'Never';
    }

    $time_diff = strtotime(date("Y-m-d H:i:s")) - strtotime($datetime);

    if ($time_diff < 86400) {
        return 'Today';
    } else {
        $days_ago = floor($time_diff / 86400);
        return $days_ago . 'd ago';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <!-- Page header -->
    <?php include('header.php'); ?>
    
    <div class="container" style="margin-top: 110px;">
        <div class="nav-links">
            <span>Navigation</span>
            <nav style="display: flex;flex-direction: column;">
                <a href="dashboard.php">Dashboard</a>
                <a href="users.php"><b>Manage Users</b></a>
                <a href="admin.php">Manage Books</a>
            </nav>
        </div>
        <div class="left-section">
            <div class="flex-container">
                <h2 style="margin-top: 3px;display: flex;justify-content: space-between;align-items:center;">MANAGE USERS
                    <form method="GET" action="users.php" style="display:flex;">
                        <select name="sort_by" onchange="this.form.submit()" style="width: auto; margin-bottom: 0;">
                            <?php foreach ($sort_options as $key => $value): ?>
                                <option value="<?= $key ?>" <?= ($sort_by == $key) ? 'selected' : '' ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="search" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Search">
                        <button type="submit" class="search-button">
                            <img src="../img/search.png" alt="Search">
                        </button>
                    </form>
                </h2>
            </div>
            <!-- Users table section -->
            <table>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email Address</th>
                    <th>Mobile #</th>
                    <th>Online</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php
                if (mysqli_num_rows($users_rst) > 0) {
                    $count = ($page - 1) * $records_per_page + 1;
                    while ($user = mysqli_fetch_assoc($users_rst)) {
                        $last_online_display = formatLastOnline($user['last_online']); ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $user['full_name'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['mobile_number'] ?></td>
                            <td><?= $last_online_display ?></td>
                            <td><?= $user['role'] ?></td>
                            <td>
                                <div style="display: flex; flex-direction: row; justify-content: space-around; align-content: center;">
                                    <!-- Edit Button -->
                                    <form method="POST" action="users.php#editModal">
                                        <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
                                        <button type="submit" name="edit" class="tbl-btn"><img src="../img/edit.png" alt="Edit" style="width: 20px; height: 20px;"></button>
                                    </form>
                                    
                                    <!-- Delete Form -->
                                    <form method="POST" action="users.php">
                                        <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
                                        <button type="submit" name="delete" class="tbl-btn" onclick="return confirm('Confirm delete <?= $user['full_name']; ?>?')"><img src="../img/delete.png" alt="Delete" style="width: 18px; height: 18px;"></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8">No users found.</td>
                    </tr>
                <?php } ?>
            </table>
            <div class="pagination">
                <?php if ($total_pages > 1): ?>
                    <?php if ($page > 1): ?>
                        <a href="manage_users.php?page=<?= ($page - 1) ?>&sort_by=<?= $sort_by ?>&search=<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="manage_users.php?page=<?= $i ?>&sort_by=<?= $sort_by ?>&search=<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" <?= ($page == $i) ? 'class="active"' : '' ?>><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="manage_users.php?page=<?= ($page + 1) ?>&sort_by=<?= $sort_by ?>&search=<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">Next</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <!-- Right Section - Registration Form -->
        <div class="right-section">
            <div class="side">
                <h2 style='margin-top:3px'>ADD A USER</h2>
                <form method="POST" action="users.php">
                    <div class="input-group2">
                        <label for="fname">Full Name:</label>
                        <div style="display:flex;width:100%">
                            <input type="text" id="fname" name="fname" placeholder="First" style="margin-right:1%;" value="<?= $fname ?>" required>
                            <input type="text" id="lname" name="lname" placeholder="Last" value="<?= $lname ?>" required>
                        </div>
                    </div>
                    <div class="input-group2">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username"  placeholder="Username" value="<?= $username ?>" required>
                        <?php if (!empty($username_err)): ?>
                            <div class="error"><?= $username_err; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="input-group2" style="flex-direction: row;">
                        <div style="width:49%;margin-right:1%">
                            <label for="email">Email Address:</label>
                            <input type="email" id="email" name="email" placeholder="Sample@email.com" value="<?= $email ?>" required>
                            <?php if (!empty($email_err)): ?>
                                <div class="error"><?= $email_err; ?></div>
                            <?php endif; ?>
                        </div>
                        <div style="width:50%">
                            <label for="number">Mobile Number*</label>
                            <div class="tele">
                                <select class="country-code" name="countrycode" style="width: auto; border-radius:4px 0 0 4px" required>
                                    <option value="+63" <?= $countrycode == '+63' ? 'selected' : '' ?>>+63</option>
                                    <option value="+44" <?= $countrycode == '+44' ? 'selected' : '' ?>>+44</option>
                                    <option value="+91" <?= $countrycode == '+91' ? 'selected' : '' ?>>+91</option>
                                </select>
                                <input type="tel" id="phone" maxlength="10" name="mobile_number" pattern="[0-9]{10}"
                                    placeholder="##########" value="<?= $mobile_number ?>" required>
                            </div>
                            <?php if (!empty($mobile_number_err)): ?>
                                <div class="error"><?= $mobile_number_err; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="input-group2">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" placeholder="Password" value="<?= $password ?>" required>
                        <?php if (!empty($password_err)): ?>
                            <div class="error"><?= $password_err; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="input-group2">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" placeholder="Confirm Password" name="confirm_password" required>
                        <?php if (!empty($confirm_password_err)): ?>
                            <div class="error"><?= $confirm_password_err; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="input-group2">
                        <label for="role">Role:</label>
                        <select id="role" name="role" style="width: 100%;" required>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                            <option value="User" selected >User</option>
                        </select>
                    </div>
                    <input type="submit" name="add" value="ADD"></input>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content" style="left:0;">
            <span class="close" onclick="window.location.href='users.php'">&times;</span>
            <h2 style='margin-top:0'>Edit User</h2>
            <?php
            if (isset($_POST['edit'])) {
                $user_id = mysqli_real_escape_string($conn, $_POST['id']);
                $edit_query = "SELECT * FROM users WHERE user_id = $user_id";
                $edit_result = mysqli_query($conn, $edit_query);
                $user_data = mysqli_fetch_assoc($edit_result);
            ?>
            <form method="POST" action="users.php" class="edit-form">
                <input type="hidden" name="user_id" value="<?= $user_data['user_id'] ?>">
                
                <div class="input-group">
                    <div class="modal-div">
                        <label for="editFullName">Full Name:</label>
                        <input type="text" id="editFullName" name="full_name" value="<?= $user_data['full_name'] ?>" required>
                    </div>
                </div>

                <div class="input-group">
                    <div class="modal-div">
                        <label for="editUsername">Username:</label>
                        <input type="text" id="editUsername" name="username" value="<?= $user_data['username'] ?>" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="modal-div">
                        <label for="editEmail">Email:</label>
                        <input type="email" id="editEmail" name="email" value="<?= $user_data['email'] ?>" required>
                    </div>

                    <div class="modal-div">
                        <label for="editMobile">Mobile #:</label>
                        <input type="text" id="editMobile" name="mobile_number" value="<?= $user_data['mobile_number'] ?>">
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="modal-div">
                        <label for="editRole">Role:</label>
                        <select id="editRole" name="role" required>
                            <option value="Admin" <?= ($user_data['role'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="Staff" <?= ($user_data['role'] == 'Staff') ? 'selected' : '' ?>>Staff</option>
                            <option value="User" <?= ($user_data['role'] == 'User') ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>
                </div>
                
                <input type="submit" name="update" value="UPDATE"></input>
            </form>
            <?php } ?>
        </div>
    </div>

    <!-- Page footer -->
    <?php include('footer.php'); ?>
</body>
</html>
