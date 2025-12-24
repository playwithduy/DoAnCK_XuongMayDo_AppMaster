<?php
session_start();
require_once "../config/database.php";
require_once "../models/BaoCaoSuCoModel.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'congnhan') {
    header("Location: ../login.php"); exit;
}

$model = new BaoCaoSuCoModel($conn);
$user = $_SESSION['user'];
$msg = ""; $status = "";

// --- XỬ LÝ POST (GỬI BÁO CÁO) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loai = $_POST['loai_su_co'];
    $vitri = $_POST['vi_tri'];
    $mota = $_POST['mo_ta'];
    $hinh_anh = NULL;

    // Xử lý upload ảnh
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
        $target_dir = "../assets/uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $fname = time() . "_" . basename($_FILES["hinh_anh"]["name"]);
        if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $fname)) {
            $hinh_anh = $fname;
        }
    }

    if ($model->createReport($user['id'], $loai, $vitri, $mota, $hinh_anh)) {
        header("Location: BaoCaoSuCoController.php?status=success");
        exit;
    } else {
        $msg = "Lỗi hệ thống!"; $status = "error";
    }
}

$data = [
    'user' => $user,
    'history' => $model->getHistory($user['id']),
    'msg' => isset($_GET['status']) ? "Gửi báo cáo thành công!" : $msg,
    'status' => isset($_GET['status']) ? "success" : $status
];

require "../view/cong_nhan_bao_cao.php";
?>