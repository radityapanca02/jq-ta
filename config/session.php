<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: .././../index.php");
    exit;
}
$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];
?>
