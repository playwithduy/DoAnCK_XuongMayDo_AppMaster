<?php
session_start();
require_once "../config/database.php";
require_once "../models/DonHangModel.php";

// 1. Check quyền
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'quandoc') {
    header("Location: ../login.php"); exit;
}

$model = new DonHangModel($conn);
$user = $_SESSION['user'];

// 2. Logic lấy dữ liệu
$orders = $model->getAllOrders();
$selected_order = null;

// Nếu người dùng bấm xem chi tiết
if (isset($_GET['id'])) {
    $selected_order = $model->getOrderById($_GET['id']);
}

// 3. Đóng gói data
$data = [
    'user' => $user,
    'orders' => $orders,
    'selected' => $selected_order
];

// 4. Gọi View
require "../view/quan_doc_don_hang.php";
?>