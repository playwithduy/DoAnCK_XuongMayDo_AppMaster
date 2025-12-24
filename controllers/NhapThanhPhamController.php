<?php
session_start();
require "../config/database.php";
require "../models/NhapThanhPhamModel.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$model = new NhapThanhPhamModel($conn);
$user  = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_nhap'])) {

    $ten_tp   = trim($_POST['ten_tp']);
    $xuong    = $_POST['xuong'];
    $so_luong = (int)$_POST['so_luong'];
    $note     = $_POST['note'] ?? '';
    $qc       = $_POST['qc'] ?? null; // ✅ KHÔNG còn warning

    $model->insertPhieu(
        $ten_tp,
        $xuong,
        $so_luong,
        $note,
        $qc,
        $user['id'],
        $user['username']
    );

    header("Location: NhapThanhPhamController.php");
    exit;
}

$data = [
    'user'    => $user,
    'history' => $model->getHistory()
];

require "../view/nhap_thanhpham.php";
