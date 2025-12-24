<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/YeuCauKiemTraModel.php';

// Check quyền Xưởng trưởng
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'xuongtruong') {
    header("Location: ../login.php"); exit;
}

$model = new YeuCauKiemTraModel($conn);

// --- API AJAX: Lấy chi tiết lô hàng ---
if (isset($_GET['action']) && $_GET['action'] == 'get_detail') {
    $id = intval($_GET['id']);
    $detail = $model->getLotDetail($id);
    header('Content-Type: application/json');
    echo json_encode($detail);
    exit;
}

// --- XỬ LÝ SUBMIT FORM ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_request'])) {
    $keHoachId = $_POST['ke_hoach_id'];
    $soLuong   = $_POST['so_luong'];
    $ghiChu    = $_POST['ghi_chu'];
    $userId    = $_SESSION['user']['id'];
    $maPhieu   = $model->generateMaPhieu();

    if ($model->createRequest($maPhieu, $keHoachId, $userId, $soLuong, $ghiChu)) {
        header("Location: YeuCauKiemTraController.php?msg=success");
    } else {
        header("Location: YeuCauKiemTraController.php?msg=error");
    }
    exit;
}

// --- LOAD VIEW ---
$lots = $model->getAvailableLots(); // Lấy danh sách lô
$msg  = $_GET['msg'] ?? '';

require_once __DIR__ . '/../view/yeu_cau_kiem_tra.php';
?>