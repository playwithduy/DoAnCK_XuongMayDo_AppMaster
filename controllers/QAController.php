<?php
session_start();
require_once "../config/database.php";
require_once "../models/QAModel.php";

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php"); exit;
}

$user = $_SESSION['user'];
$model = new QAModel($conn);

$action = $_GET['action'] ?? 'list';
$request_id = isset($_GET['req_id']) ? (int)$_GET['req_id'] : 0;

// === XỬ LÝ POST: LƯU BIÊN BẢN ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_report'])) {
    $phieu_yc_id = $_POST['phieu_yc_id'];
    
    // Dữ liệu Header
    $data = [
        'ma_sp'       => $_POST['ma_sp'] ?? '',
        'ten_sp'      => $_POST['ten_sp'],
        'ngay_sx'     => $_POST['ngay_sx'],
        'lo_sx'       => $_POST['lo_sx'],
        'ten_qa'      => $_POST['ten_qa'],
        'user_id'     => $user['id'],
        'ngay_kt'     => $_POST['ngay_kt'],
        'ket_qua'     => $_POST['ket_qua_chung'],
        'khuyen_nghi' => $_POST['khuyen_nghi'],
        'huong_dan'   => $_POST['huong_dan']
    ];

    // Dữ liệu Detail (Tiêu chí)
    $criteria_list = [];
    if (isset($_POST['tieu_chi'])) {
        for ($i = 0; $i < count($_POST['tieu_chi']); $i++) {
            if (!empty($_POST['tieu_chi'][$i])) {
                $criteria_list[] = [
                    'tieu_chi'   => $_POST['tieu_chi'][$i],
                    'tieu_chuan' => $_POST['tieu_chuan'][$i],
                    'ket_qua'    => $_POST['ket_qua_ct'][$i],
                    'ghi_chu'    => $_POST['ghi_chu'][$i]
                ];
            }
        }
    }

    if ($model->createBienBan($data, $criteria_list, $phieu_yc_id)) {
        echo "<script>alert('Lưu biên bản kiểm tra thành công!'); window.location.href='QAController.php';</script>";
    } else {
        echo "<script>alert('Lỗi hệ thống!');</script>";
    }
    exit;
}

// === XỬ LÝ GET: ĐIỀU HƯỚNG VIEW ===
if ($action == 'form' && $request_id > 0) {
    // Vào form kiểm tra
    $request_data = $model->getRequestById($request_id);
    if (!$request_data) {
        echo "Lỗi: Phiếu không tồn tại!"; exit;
    }
    
    // Thêm các trường giả lập nếu thiếu để view không lỗi
    $request_data['ma_san_pham'] = $request_data['ma_san_pham'] ?? 'SP-' . rand(100,999);
    
    require "../view/qa_form.php";

} elseif ($action == 'history') {
    // Xem lịch sử
    $history_list = $model->getHistory();
    require "../view/qa_history.php";

} else {
    // Mặc định: Xem danh sách chờ
    $pending_list = $model->getPendingRequests();
    require "../view/qa_list.php";
}
?>