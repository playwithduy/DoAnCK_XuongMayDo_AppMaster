<?php
session_start();
require_once "../config/database.php";
require_once "../models/XuongTruongModel.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'xuongtruong') {
    header("Location: ../login.php"); exit;
}

$model = new XuongTruongModel($conn);

// Xử lý Gửi Yêu Cầu QA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'gui_qa') {
    $cd_id = $_POST['cong_doan_id'];
    $sl = $_POST['so_luong'];
    $uid = $_SESSION['user']['id'];

    if ($model->sendQARequest($cd_id, $uid, $sl)) {
        echo "<script>alert('Đã gửi yêu cầu kiểm tra cho QA!'); window.location.href='XuongTruongDashBoardController.php';</script>";
    } else {
        echo "<script>alert('Lỗi gửi yêu cầu!'); window.location.href='XuongTruongDashBoardController.php';</script>";
    }
    exit;
}

// Lấy dữ liệu hiển thị Dashboard
$stats = [
    'active_plans' => $model->getActivePlans(),
    'today_prod' => $model->getTodayProduction(),
    'workers' => $model->countWorkers(),
    'chart_data' => $model->getChartData()
];

// Lấy danh sách tiến độ để hiển thị bảng
$progress = $model->getPlanProgress();

require "../view/xuongtruong.php";
?>