<?php
session_start();
// Kiểm tra quyền
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'quandoc') {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

// Dữ liệu giả lập cho Dashboard 
$data = [
    'user' => $user,
    'title' => 'Tổng quan quản đốc'
];

require "../view/quandoc.php";
?>