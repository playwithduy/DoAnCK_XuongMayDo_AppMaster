<?php
// FILE: controllers/ThuKhoController.php

session_start();
require_once "../config/database.php";
require_once "../models/DashboardModel.php";
require_once "../models/ThuKhoModel.php"; // Load thêm model cho chức năng mới

// 1. KIỂM TRA QUYỀN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'thukho') {
    header("Location: ../login.php");
    exit;
}

// 2. KHỞI TẠO MODELS
$dashboardModel = new DashboardModel($conn);
$thuKhoModel    = new ThuKhoModel($conn);

$uid = $_SESSION['user']['id'];

// 3. XỬ LÝ ĐIỀU HƯỚNG (ROUTING)
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    // ====================================================
    // CASE 1: XEM DANH SÁCH ĐƠN HÀNG NCC (CHỨC NĂNG MỚI)
    // ====================================================
    case 'xem_don_ncc':
        // Lấy dữ liệu từ Model ThuKho
        $ds_don = $thuKhoModel->getDonHangNCC();
        
        // Gọi View hiển thị danh sách
        require "../view/thukho_list_po.php"; 
        break;

    // ====================================================
    // CASE 2: DASHBOARD THỐNG KÊ (CHỨC NĂNG CŨ)
    // ====================================================
    case 'dashboard':
    default:
        // Lấy thông tin user mới nhất
        $user = $conn->query("SELECT username, gender, role, status FROM users WHERE id = $uid")->fetch_assoc();

        // Tổng hợp dữ liệu cho Dashboard (Giữ nguyên logic cũ của bạn)
        $data = [
            'user'         => $user,
            
            // Số liệu tổng quan
            'tongNL'       => $dashboardModel->tongNguyenLieu(),
            'tongTP'       => $dashboardModel->tongThanhPham(),
            'phieuNhap'    => $dashboardModel->phieuNhapHomNay(),
            'phieuXuat'    => $dashboardModel->phieuXuatHomNay(),
            'tongDoanhThu' => $dashboardModel->tongDoanhThu(),

            // Bảng chi tiết
            'dsNhapTP'     => $dashboardModel->chiTietNhapTP(),
            'dsXuatTP'     => $dashboardModel->chiTietXuatTP(),

            // Dữ liệu biểu đồ
            'bieuDoXuat7'  => $dashboardModel->thongKeXuat7Ngay(),
            'bieuDoXuat30' => $dashboardModel->thongKeXuat30Ngay(),
            'bieuDoTon'    => $dashboardModel->thongKeTonKho()
        ];

        // Gọi View Dashboard chính
        require "../view/thukho.php";
        break;
}
?>