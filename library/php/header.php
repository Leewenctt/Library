<?php
include ('ini.php');
?>

<link rel="stylesheet" href="../css/header.css">

<nav class="navbar">
    <div class="nav-left">
        <div class="logo">
            <a href="library.php"><img src="../img/logo.svg" alt="BBL" height="40"></a>
        </div>
        <ul class="nav-list">
            <li class="nav-item"><a href="library.php">Library</a></li>
            <li class="nav-item"><a href="about.php">About</a></li>
        </ul>
    </div>
    <div class="nav-right">
        <ul class="nav-list">
            <?php if (isset($_SESSION['user'])) { ?>
                <li class="nav-item"><span style="color: white; margin-right: 10px;"><?= $row['username'] ?></span></li>
                <li class="nav-item dropdown">
                    <div class="dropbtn profile-pic-header" tabindex="0"
                        style="background-image: url('<?= $row['profile_pic'] ?>');"></div>
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <?php if ($row['role'] == 'Admin') { ?>
                            <a href="dashboard.php" style="color: #1da840;">Admin</a>
                        <?php } ?>
                        <a href="logout.php" onclick="return confirm('Log out of <?= $row['username'] ?>?')" style="color: #d71010">Logout</a>
                    </div>
                </li>
            <?php } else { ?>
                <li class="nav-item"><a href="register.php" style="margin-right: 10px;">Register</a></li>
                <li class="nav-item"><a href="login.php" class="login">Login</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>