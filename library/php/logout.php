<?php
include ('ini.php');

var_dump($_SESSION);

if (isset($_SESSION['user'])) {
    session_unset();
    session_destroy();

    $referer = $_SERVER['HTTP_REFERER'];
    header("Location: $referer");
    exit();
}else{
    header("Location: library.php");
}   