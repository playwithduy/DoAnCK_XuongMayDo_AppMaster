<?php
session_start();
require_once "../config/database.php";
require_once "../models/KeHoachModel.php";

// Kiểm tra quyền Quản đốc
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'quandoc') {
    header("Location: ../login.php"); 
    exit;
}

$model = new KeHoachModel($conn);
$msg = ""; 
$status = "";

// Xử lý POST (Lưu kế hoạch)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_plan'])) {
    $res = $model->create(
        $_POST['don_hang_id'], 
        $_POST['day_chuyen_id'], 
        $_POST['ngay_bat_dau'], 
        $_POST['ngay_ket_thuc'], 
        $_POST['san_luong_ngay'], 
        $_SESSION['user']['id']
    );
    
    if ($res) {
        header("Location: LapKeHoachController.php?status=success");
        exit;
    } else {
        $msg = "Lỗi hệ thống! Không thể tạo kế hoạch.";
        $status = "error";
    }
}

// Chuẩn bị dữ liệu View
$selected = null;
if (isset($_GET['order_id'])) {
    $selected = $model->getDonHangById($_GET['order_id']);
}

// Lấy danh sách đơn hàng chờ lập kế hoạch
$orders = $model->getDonHangCho();

// DEBUG: Kiểm tra xem có đơn hàng không
if (empty($orders)) {
    // Kiểm tra có đơn hàng nào trong database không
    $checkOrders = $conn->query("SELECT COUNT(*) as total FROM don_hang_ban");
    $totalOrders = $checkOrders->fetch_assoc()['total'];
    
    if ($totalOrders > 0) {
        $msg = "Có $totalOrders đơn hàng trong hệ thống, nhưng tất cả đã có kế hoạch sản xuất. ";
        
        // Kiểm tra chi tiết
        $checkDetails = $conn->query("
            SELECT dh.id, dh.so_don_hang, kh.trang_thai 
            FROM don_hang_ban dh
            LEFT JOIN kehoachsanxuat kh ON dh.id = kh.don_hang_id
        ");
        
        $msg .= "<br><strong>Chi tiết:</strong><ul>";
        while ($row = $checkDetails->fetch_assoc()) {
            $msg .= "<li>Đơn #{$row['id']} ({$row['so_don_hang']}): " . 
                    ($row['trang_thai'] ? "Đã có KH - Trạng thái: {$row['trang_thai']}" : "Chưa có KH") . 
                    "</li>";
        }
        $msg .= "</ul>";
    } else {
        $msg = "Chưa có đơn hàng nào trong hệ thống.";
    }
}

$data = [
    'orders' => $orders,                 // Danh sách đơn chờ lập KH
    'lines' => $model->getDayChuyen(),   // Danh sách chuyền
    'selected' => $selected,             // Đơn hàng đang chọn để lập
    'msg' => isset($_GET['status']) && $_GET['status'] == 'success' 
             ? "✅ Đã gửi kế hoạch lên Giám đốc phê duyệt!" 
             : $msg,
    'status' => isset($_GET['status']) ? $_GET['status'] : $status
];

require "../view/lap_ke_hoach.php";
?>