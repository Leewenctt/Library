<?php
include('ini.php');
$usermail = "";

if (isset($_SESSION['user'])) {
    header('location: library.php');
    exit();
}

$book_param = isset($_GET['book']) && !empty($_GET['book']) ? '?book=' . htmlspecialchars($_GET['book']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usermail = mysqli_real_escape_string($conn, $_POST['usermail']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $err = "";

    if ($usermail != "" && $password != "") {
        $stmt = $conn->prepare("SELECT username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $usermail, $usermail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_username, $db_email, $hashed_pwd);
            $stmt->fetch();

            if (password_verify($password, $hashed_pwd)) {
                session_start();
                $_SESSION['user'] = $db_email;
                
                $update_stmt = $conn->prepare("UPDATE users SET last_online = NOW() WHERE email = ?");
                $update_stmt->bind_param("s", $db_email);
                $update_stmt->execute();
                $update_stmt->close();

                $stmt->close();

                
                if (!empty($_GET['book'])) {
                    header("Location: book.php?book=" . urlencode($_GET['book']));
                } else {
                    header('Location: library.php');
                }
                exit();
            } else {
                $err = "Incorrect username or password.";
            }
        } else {
            $err = "User does not exist.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="wrapper">
        <form method="POST">
            <span style="font-size: 40px; font-weight: light;">LOGIN</span>

            <div>
                <label for="email" style="margin-top: 20px">Username or Email*</label>
                <input type="text" name="usermail" placeholder="Username or Email" value="<?= $usermail ?>"
                    required>
                <?php if (!empty($err)): ?>
                <div class="error"><?= $err; ?></div>
                <?php endif; ?>
            </div>
            <div>
                <label for="password">Password*</label>
                <input type="password" name="password" placeholder="Password" style="margin-bottom:20px;"
                    required>
            </div>
            <div>
                <span><input type="checkbox" name="remember_me" id="remember_me">
                    Remember me</span>
            </div>

            <span style="margin-top: 20px;">Don't have an account? <a
                    href="register.php<?= $book_param ?>" id="reg">Register</a></span>
            <input type="submit" name="login" value="SIGN IN" style="margin-top: 10px">
        </form>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>
