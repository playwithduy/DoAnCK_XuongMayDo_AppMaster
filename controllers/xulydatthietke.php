<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['user']['role'] !== 'khachhang') {
    die("Bạn không có quyền đặt thiết kế!");
}

$ten = $_POST['ten_sp'];
$mo_ta = $_POST['mo_ta'];


echo "Đặt thiết kế thành công!";
?>
