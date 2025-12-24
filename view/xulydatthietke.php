<?php
session_start();
require_once "../config/database.php";

// 0️⃣ Chặn truy cập trực tiếp
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/thietkesanpham.php");
    exit;
}

// 1️⃣ Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// 2️⃣ Kiểm tra quyền
if ($_SESSION['user']['role'] !== 'khachhang') {
    die("Bạn không có quyền đặt thiết kế!");
}

// 3️⃣ Nhận dữ liệu
$user_id   = $_SESSION['user']['id'];
$ten_sp    = $_POST['ten_sp'] ?? '';
$loai_sp   = $_POST['loai_sp'] ?? '';
$so_luong  = (int)($_POST['so_luong'] ?? 0);
$mau_sac   = $_POST['mau_sac'] ?? '';
$mo_ta     = $_POST['mo_ta'] ?? '';
$thoi_gian = $_POST['thoi_gian'] ?? '';

// 4️⃣ Upload file mẫu (nếu có)
$file_mau = null;
if (!empty($_FILES['file_mau']['name'])) {
    $target_dir = "../uploads/thietke/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['file_mau']['name'], PATHINFO_EXTENSION));
    $allow_ext = ['jpg','jpeg','png','pdf','psd'];

    if (!in_array($ext, $allow_ext)) {
        die("File không hợp lệ!");
    }

    $file_mau = time() . "_" . basename($_FILES['file_mau']['name']);
    move_uploaded_file(
        $_FILES['file_mau']['tmp_name'],
        $target_dir . $file_mau
    );
}

// 5️⃣ Lưu database
$sql = "INSERT INTO thietke
        (user_id, ten_sp, loai_sp, so_luong, mau_sac, mo_ta, file_mau, thoi_gian)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ississss",
    $user_id,
    $ten_sp,
    $loai_sp,
    $so_luong,
    $mau_sac,
    $mo_ta,
    $file_mau,
    $thoi_gian
);

$stmt->execute();

// 6️⃣ Quay về form + báo thành công
header("Location: thietkesanpham.php?success=1");
exit;

