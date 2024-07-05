<?php
include('ini.php');

$fname_err = $lname_err = $username_err = $email_err = $password_err = $confirm_password_err = $mobile_number_err = "";
$fname = $lname = $username = $email = $password = $mobile_number = $countrycode = "";

if (isset($_SESSION['user'])) {
    header('location: library.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
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
        $password_err = "Password must include at least one number and a special character.";
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
        $query = "INSERT INTO users (full_name, username, email, password, mobile_number) 
                  VALUES('$full_name', '$username', '$email', '$hashed_password', '$full_num')";
        if (mysqli_query($conn, $query)) {
            session_start();
            $_SESSION['user'] = $email;
            
            unset($fname, $lname, $username, $email, $password, $mobile_number, $countrycode);

            if (isset($_GET['book'])) {
                $book = $_GET['book'];
                header("Location: book.php?book=" . urlencode($book));
            } else {
                header('Location: library.php');
            }
            exit();
        } else {
            echo "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/register.css">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="wrapper">
        <form method="POST">
            <div class="banner"></div>
            <div style="width:400px;">
                <span style="font-size: 40px; font-weight: light;">REGISTRATION</span>

                <label for="fname" style="margin-top: 25px">Full Name*</label>
                <div class="input-group">
                    <input type="text" name="fname" placeholder="First" style="margin-right: 10px;"
                        value="<?= $fname ?>" required>
                    <input type="text" name="lname" placeholder="Last" value="<?= $lname ?>" required>
                </div>

                <div>
                    <label for="username">Username*</label>
                    <input type="text" name="username" placeholder="Username" value="<?= $username ?>" required>
                    <?php if (!empty($username_err)): ?>
                        <div class="error"><?= $username_err; ?></div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <div style="width: 48%;">
                        <label for="email">Email*</label>
                        <input type="email" name="email" placeholder="Sample@email.com" value="<?= $email ?>"
                            required>
                        <?php if (!empty($email_err)): ?>
                            <div class="error" style="margin-bottom:0;"><?= $email_err; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="number-input">
                        <label for="number">Mobile Number*</label>
                        <div class="tele">
                            <select class="country-code" name="countrycode" required>
                                <option value="+63" <?= $countrycode == '+63' ? 'selected' : '' ?>>+63</option>
                                <option value="+44" <?= $countrycode == '+44' ? 'selected' : '' ?>>+44</option>
                                <option value="+91" <?= $countrycode == '+91' ? 'selected' : '' ?>>+91</option>
                            </select>
                            <input type="tel" id="phone" maxlength="10" name="mobile_number" pattern="[0-9]{10}"
                                placeholder="##########" value="<?= $mobile_number ?>" required>
                        </div>
                        <?php if (!empty($mobile_number_err)): ?>
                            <div class="error" style="margin-bottom:0;"><?= $mobile_number_err; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label for="password">Password*</label>
                    <input type="password" name="password" placeholder="Password" value="<?= $password ?>"
                        required>
                    <?php if (!empty($password_err)): ?>
                        <div class="error"><?= $password_err; ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="confirm_password">Confirm Password*</label>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <?php if (!empty($confirm_password_err)): ?>
                        <div class="error"><?= $confirm_password_err; ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-top:15px;">
                    <span>Already have an account? <a href="login.php<?= isset($_GET['book']) ? '?book=' . $_GET['book'] : '' ?>" id="reg">Login</a></span>
                    <input type="submit" name="register" value="SIGN UP" style="margin-top: 10px">
                </div>
            </div>
        </form>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>
