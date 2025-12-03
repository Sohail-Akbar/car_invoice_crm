<?php
session_start();

setcookie("Garage_Remember_Me", "", time() - 3600, "/");

unset($_SESSION['user_id']);
header('Location: login');
exit;
