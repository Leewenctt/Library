<?php
include ("connection.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    $current_user = mysqli_real_escape_string($conn, $_SESSION['user']);

    $update_stmt = $conn->prepare("UPDATE users SET last_online = NOW() WHERE email = ?");
    $update_stmt->bind_param("s", $current_user);
    $update_stmt->execute();
    $update_stmt->close();

    $users_result = mysqli_query($conn, "SELECT * FROM users WHERE email='$current_user'");

    if ($users_result && mysqli_num_rows($users_result) > 0) {
        $row = mysqli_fetch_assoc($users_result);
    }else{
        session_unset();
        session_destroy();
    
        header('Location: library.php');
        exit();
    }
}