<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/XuatNguyenLieuModel.php';

// Kiểm tra quyền thủ kho (Tùy chọn, nên có)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'thukho') {
    // header("Location: ../login.php"); exit;
}

$model = new XuatNguyenLieuModel($conn);
$user = $_SESSION['user'] ?? [];

// =======================================================
// 1. AJAX: Lấy chi tiết phiếu (Giữ nguyên)
// =======================================================
if (isset($_GET['action']) && $_GET['action'] == 'get_detail') {
    $id = intval($_GET['id']);
    echo json_encode($model->getRequestDetails($id));
    exit;
}

// =======================================================
// 2. POST: Xử lý Xuất hoặc Từ chối (ĐÃ NÂNG CẤP)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy ID và ép kiểu số nguyên để an toàn
    $phieuId = isset($_POST['phieu_id']) ? intval($_POST['phieu_id']) : 0;
    
    // --- Case 1: Lỗi không tìm thấy ID phiếu ---
    if ($phieuId <= 0) {
        header("Location: XuatNguyenLieuController.php?msg=error_id");
        exit;
    }

    // --- Case 2: Xử lý nút XUẤT KHO ---
    if (isset($_POST['btn_export'])) {
        // Gọi Model xử lý xuất kho
        // Model nên trả về TRUE nếu thành công, hoặc CHUỖI LỖI nếu thất bại (ví dụ: Thiếu hàng)
        $result = $model->exportStock($phieuId, $user['id'], "Xuất kho theo yêu cầu");
        
        if ($result === true) {
            header("Location: XuatNguyenLieuController.php?msg=success");
        } else {
            // Trường hợp lỗi nghiệp vụ (Vd: Tồn kho không đủ)
            header("Location: XuatNguyenLieuController.php?msg=error&err=" . urlencode($result));
        }
    } 
    
    // --- Case 3: Xử lý nút TỪ CHỐI ---
    elseif (isset($_POST['btn_reject'])) {
        $reason = trim($_POST['ly_do_tu_choi'] ?? '');

        // --- VALIDATE: Kiểm tra lý do từ chối ---
        if (empty($reason)) {
            header("Location: XuatNguyenLieuController.php?msg=missing_reason");
            exit;
        }

        // Gọi Model từ chối
        if ($model->rejectRequest($phieuId, $reason)) {
            header("Location: XuatNguyenLieuController.php?msg=rejected");
        } else {
            header("Location: XuatNguyenLieuController.php?msg=sys_error");
        }
    }
    exit;
}

// =======================================================
// 3. VIEW DATA & HIỂN THỊ THÔNG BÁO
// =======================================================
$requests = $model->getPendingRequests();

// Xử lý thông báo hiển thị ra màn hình (Mapping msg code -> Text)
$msgCode = $_GET['msg'] ?? '';
$errDetail = $_GET['err'] ?? '';
$alertClass = '';
$alertMessage = '';

switch ($msgCode) {
    case 'success':
        $alertClass = 'alert-success';
        $alertMessage = "✅ Xuất kho thành công! Kho đã được cập nhật.";
        break;
    case 'rejected':
        $alertClass = 'alert-warning'; // Màu vàng cho từ chối
        $alertMessage = "⛔ Đã từ chối phiếu yêu cầu.";
        break;
    case 'missing_reason':
        $alertClass = 'alert-danger';
        $alertMessage = "⚠️ Lỗi: Vui lòng nhập lý do từ chối!";
        break;
    case 'error_id':
        $alertClass = 'alert-danger';
        $alertMessage = "❌ Lỗi: Không tìm thấy ID phiếu yêu cầu.";
        break;
    case 'error':
        $alertClass = 'alert-danger';
        $alertMessage = "❌ Lỗi xuất kho: " . htmlspecialchars($errDetail);
        break;
    case 'sys_error':
        $alertClass = 'alert-danger';
        $alertMessage = "❌ Lỗi hệ thống SQL.";
        break;
}

require_once __DIR__ . '/../view/xuat_nguyen_lieu.php';
?>