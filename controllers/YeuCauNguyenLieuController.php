<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/YeuCauNguyenLieuModel.php';

$model = new YeuCauNguyenLieuModel($conn);

// 1. XỬ LÝ AJAX (Lấy danh sách nguyên liệu khi chọn Kế hoạch)
if (isset($_GET['action']) && $_GET['action'] == 'get_materials') {
    $keHoachId = intval($_GET['id']);
    $materials = $model->getEstimatedMaterials($keHoachId);
    echo json_encode($materials);
    exit;
}

// 2. XỬ LÝ POST (Lưu phiếu)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_request'])) {
    $keHoachId = $_POST['ke_hoach_id'];
    $ghiChu = $_POST['ghi_chu'];
    $details = $_POST['qty'] ?? []; // Mảng: [nguyen_lieu_id => so_luong]
    
    $userId = $_SESSION['user']['id'];
    $maPhieu = $model->generateMaPhieu();

    if ($model->createRequest($maPhieu, $keHoachId, $userId, $ghiChu, $details)) {
        header("Location: YeuCauNguyenLieuController.php?msg=success");
    } else {
        header("Location: YeuCauNguyenLieuController.php?msg=error");
    }
    exit;
}

// 3. HIỂN THỊ VIEW
// Lấy kế hoạch của xưởng trưởng hiện tại
$xuongTruongId = $_SESSION['user']['id'] ?? null;
$plans = $model->getActivePlans($xuongTruongId);
$msg = $_GET['msg'] ?? '';

// Đóng gói dữ liệu
$data = [
    'plans' => $plans,
    'msg' => $msg
];

// Gọi View
require_once __DIR__ . '/../view/yeu_cau_nguyen_lieu.php';
?>