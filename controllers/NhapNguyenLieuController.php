<?php
session_start();
require_once "../config/database.php";
require_once "../models/SupplierModel.php";
require_once "../models/NhapNguyenLieuModel.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'thukho') {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

$supplierModel = new SupplierModel($conn);
$nhapModel     = new NhapNguyenLieuModel($conn);

/* =======================
   👉 XỬ LÝ KHI BẤM LƯU PHIẾU
======================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_nhap'])) {

    $supplier_id = (int)$_POST['supplier'];
    $ten_nl      = trim($_POST['ten_nl']);
    $loai_nl     = trim($_POST['loai_nl']);
    $so_luong    = (int)$_POST['soluong'];
    $don_gia     = (int)$_POST['dongia'];
    $note        = trim($_POST['note']);
    $user_id     = $user['id'];

    if ($supplier_id && $ten_nl && $loai_nl && $so_luong > 0 && $don_gia > 0) {
        $nhapModel->insertPhieuNhap(
            $supplier_id,
            $ten_nl,
            $loai_nl,
            $so_luong,
            $don_gia,
            $note,
            $user_id
        );
    }

    // 👉 nhập xong quay lại trang
    header("Location: NhapNguyenLieuController.php");
    exit;
}

/* =======================
   👉 LOAD DỮ LIỆU VIEW
======================= */
$data = [
    'user'      => $user,
    'suppliers' => $supplierModel->getAll(),
    'history'   => $nhapModel->getHistory()
];

require "../view/nhap_nguyenlieu.php";
