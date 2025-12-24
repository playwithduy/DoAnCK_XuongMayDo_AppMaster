<?php
session_start();
require_once "../config/database.php";
require_once "../models/PhanBoModel.php";

// Kiểm tra quyền Quản đốc
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'quandoc') {
    header("Location: ../login.php"); 
    exit;
}

$model = new PhanBoModel($conn);
$msg = "";
$status = "";

// Xử lý POST (Gửi lệnh sản xuất)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ke_hoach_id = $_POST['ke_hoach_id'] ?? 0;
    $xuong_truong_id = $_POST['xuong_truong_id'] ?? 0;
    $ghi_chu = $_POST['ghi_chu'] ?? '';
    
    // Validate
    if (empty($ke_hoach_id) || empty($xuong_truong_id)) {
        $msg = "Vui lòng điền đầy đủ thông tin!";
        $status = "error";
    } else {
        // Thực hiện phân bổ
        $res = $model->createPhanBo(
            (int)$ke_hoach_id, 
            (int)$xuong_truong_id, 
            $ghi_chu
        );
        
        if ($res) {
            header("Location: PhanBoController.php?status=success");
            exit;
        } else {
            $msg = "Lỗi phân bổ! Vui lòng kiểm tra log để biết thêm chi tiết.";
            $status = "error";
        }
    }
}

// Lấy dữ liệu cho View
$selected = null;
if (isset($_GET['id'])) {
    $selected = $model->getKeHoachById($_GET['id']);
    
    // Nếu không tìm thấy
    if (!$selected) {
        $msg = "Không tìm thấy kế hoạch!";
        $status = "error";
    }
}

// Lấy danh sách kế hoạch chờ phân bổ
$plans = $model->getKeHoachCho();

// DEBUG: Log số lượng kế hoạch
error_log("DEBUG PhanBoController - Number of plans: " . count($plans));
if (count($plans) > 0) {
    error_log("DEBUG PhanBoController - First plan: " . json_encode($plans[0]));
}

// Lấy danh sách xưởng trưởng
$users = $model->getXuongTruong();

// DEBUG: Log số lượng xưởng trưởng
error_log("DEBUG PhanBoController - Number of users: " . count($users));

// Xử lý message
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $msg = "✅ Đã gửi lệnh sản xuất thành công!";
        $status = "success";
    }
}

// DEBUG: Kiểm tra trực tiếp database
$debugQuery = "SELECT id, ma_ke_hoach, trang_thai FROM kehoachsanxuat";
$debugResult = $conn->query($debugQuery);
error_log("DEBUG - All kehoachsanxuat:");
while ($row = $debugResult->fetch_assoc()) {
    error_log("  ID: " . $row['id'] . " - Ma: " . $row['ma_ke_hoach'] . " - Status: " . $row['trang_thai']);
}

// Đóng gói dữ liệu
$data = [
    'plans' => $plans,
    'users' => $users,
    'selected' => $selected,
    'msg' => $msg,
    'status' => $status
];

// Load View
require "../view/phan_bo_san_xuat.php";
?>