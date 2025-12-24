<?php
// FILE: controllers/GiamDocController.php

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/SupplierModel.php';
require_once __DIR__ . '/../models/KeHoachSXModel.php';
require_once __DIR__ . '/../models/ThongKeModel.php';

// ======================================================
// 1. KIỂM TRA QUYỀN TRUY CẬP
// ======================================================
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giamdoc') {
    if(isset($_GET['action']) || isset($_POST['action'])) { 
        echo "Access Denied"; 
        exit; 
    }
    header("Location: ../login.php"); 
    exit;
}

$user = $_SESSION['user'];

// ======================================================
// 2. XỬ LÝ REQUEST POST (AJAX)
// ======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $supplierModel = new SupplierModel($conn);
    $khModel = new KeHoachSXModel($conn);

    switch ($_POST['action']) {
        // --- ĐƠN HÀNG NCC ---
        case 'phe_duyet':
            $result = $supplierModel->pheDuyetDon((int)$_POST['id'], $user['id']);
            echo $result ? "OK" : "ERROR"; 
            exit;

        case 'tu_choi':
            $lydo = $_POST['ly_do'] ?? '';
            if (trim($lydo) === '') { echo "EMPTY_REASON"; exit; }
            $result = $supplierModel->tuChoiDon((int)$_POST['id'], $lydo);
            echo $result ? "OK" : "ERROR";
            exit;

        // --- KẾ HOẠCH SẢN XUẤT ---
        case 'phe_duyet_kehoach':
            $result = $khModel->pheDuyetKeHoach((int)$_POST['id'], $user['id']);
            echo $result ? "OK" : "ERROR";
            exit;

        case 'tu_choi_kehoach':
            $lydo = $_POST['ly_do'] ?? '';
            if (trim($lydo) === '') { echo "EMPTY_REASON"; exit; }
            $result = $khModel->tuChoiKeHoach((int)$_POST['id'], $lydo);
            echo $result ? "OK" : "ERROR";
            exit;
    }
}

// ======================================================
// 3. XỬ LÝ REQUEST GET
// ======================================================
if (isset($_GET['action'])) {
    
    $supplierModel = new SupplierModel($conn);
    $khModel = new KeHoachSXModel($conn);
    $tkModel = new ThongKeModel($conn);

    switch ($_GET['action']) {
        
        // ----------------------------------------
        // NHÓM 1: ĐƠN HÀNG NCC
        // ----------------------------------------
        case 'duyet_don_ncc':
            $dsDonNCC = $supplierModel->getDonChoDuyet();
            require "../view/giamdoc_duyet_don_ncc.php";
            exit;
        
        case 'get_chi_tiet_don':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $chiTiet = $supplierModel->getChiTietDon($id);
                
                if (empty($chiTiet)) {
                    echo '<p style="text-align:center; color:#64748b">Không có thông tin sản phẩm.</p>';
                } else {
                    echo '<table style="width:100%; border-collapse:collapse; margin-top:5px; border:1px solid #eee">';
                    echo '<thead style="background:#f8fafc; color:#64748b; font-size:0.9rem">';
                    echo '<tr>';
                    echo '<th style="padding:10px; text-align:left">Tên Sản Phẩm / Vật Tư</th>';
                    echo '<th style="padding:10px; text-align:center">SL</th>';
                    echo '<th style="padding:10px; text-align:right">Đơn Giá</th>';
                    echo '<th style="padding:10px; text-align:right">Thành Tiền</th>';
                    echo '</tr>';
                    echo '</thead><tbody>';
                    
                    foreach ($chiTiet as $sp) {
                        echo '<tr style="border-bottom:1px solid #f1f5f9">';
                        echo '<td style="padding:10px; font-weight:500">' . htmlspecialchars($sp['ten_san_pham']) . '</td>';
                        echo '<td style="padding:10px; text-align:center; font-weight:bold">' . $sp['so_luong'] . '</td>';
                        echo '<td style="padding:10px; text-align:right">' . number_format($sp['don_gia']) . '</td>';
                        echo '<td style="padding:10px; text-align:right; font-weight:bold; color:#334155">' . number_format($sp['thanh_tien']) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                exit;
            }
            break;

        // ----------------------------------------
        // NHÓM 2: KẾ HOẠCH SẢN XUẤT
        // ----------------------------------------
        case 'duyet_kehoach_sx':
            $dsKeHoach = $khModel->getKeHoachChoDuyet();
            require "../view/giamdoc_duyet_kehoach_sx.php";
            exit;

        case 'get_chi_tiet_kehoach':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $kh = $khModel->getKeHoachById($id);

                if (!$kh) {
                    echo '<p style="text-align:center; color:#94a3b8; padding:20px">';
                    echo '<i class="fas fa-exclamation-triangle" style="font-size:2rem; display:block; margin-bottom:10px; color:#fbbf24"></i>';
                    echo 'Không tìm thấy dữ liệu kế hoạch.';
                    echo '</p>';
                } else {
                    // Header thông tin chung
                    echo '<div style="background:#f8fafc; padding:20px; border-radius:8px; margin-bottom:20px">';
                    echo '<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px">';
                    
                    // Cột 1
                    echo '<div>';
                    echo '<div style="margin-bottom:12px">';
                    echo '<strong style="color:#64748b; font-size:0.85rem; display:block; margin-bottom:4px">MÃ KẾ HOẠCH</strong>';
                    echo '<span style="color:#0f172a; font-size:1.1rem; font-weight:600">' . htmlspecialchars($kh['ma_ke_hoach']) . '</span>';
                    echo '</div>';
                    
                    echo '<div style="margin-bottom:12px">';
                    echo '<strong style="color:#64748b; font-size:0.85rem; display:block; margin-bottom:4px">ĐƠN HÀNG</strong>';
                    echo '<span style="color:#3b82f6; font-weight:600">' . htmlspecialchars($kh['ma_don_hang']) . '</span>';
                    echo '</div>';
                    echo '</div>';
                    
                    // Cột 2
                    echo '<div>';
                    echo '<div style="margin-bottom:12px">';
                    echo '<strong style="color:#64748b; font-size:0.85rem; display:block; margin-bottom:4px">NGƯỜI LẬP</strong>';
                    echo '<span style="color:#0f172a">' . htmlspecialchars($kh['nguoi_lap'] ?? 'Không rõ') . '</span>';
                    echo '</div>';
                    
                    echo '<div style="margin-bottom:12px">';
                    echo '<strong style="color:#64748b; font-size:0.85rem; display:block; margin-bottom:4px">NGÀY LẬP</strong>';
                    echo '<span style="color:#0f172a">' . date('d/m/Y H:i', strtotime($kh['ngay_lap'])) . '</span>';
                    echo '</div>';
                    echo '</div>';
                    
                    echo '</div>';
                    echo '</div>';

                    // Bảng chi tiết sản xuất
                    echo '<h4 style="margin:20px 0 10px; color:#0f172a; font-size:0.95rem; font-weight:700">📋 CHI TIẾT SẢN XUẤT</h4>';
                    echo '<table style="width:100%; border-collapse:collapse; border:1px solid #e2e8f0">';
                    echo '<thead style="background:#f8fafc">';
                    echo '<tr>';
                    echo '<th style="padding:12px; text-align:left; border-bottom:2px solid #e2e8f0; color:#64748b; font-weight:600">Sản Phẩm</th>';
                    echo '<th style="padding:12px; text-align:center; border-bottom:2px solid #e2e8f0; color:#64748b; font-weight:600">Chuyền</th>';
                    echo '<th style="padding:12px; text-align:center; border-bottom:2px solid #e2e8f0; color:#64748b; font-weight:600">Tổng SL</th>';
                    echo '<th style="padding:12px; text-align:center; border-bottom:2px solid #e2e8f0; color:#64748b; font-weight:600">SL/Ngày</th>';
                    echo '<th style="padding:12px; text-align:center; border-bottom:2px solid #e2e8f0; color:#64748b; font-weight:600">Thời Gian</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    echo '<tr style="border-bottom:1px solid #f1f5f9">';
                    echo '<td style="padding:12px; font-weight:600; color:#0f172a">' . htmlspecialchars($kh['ten_san_pham']) . '</td>';
                    echo '<td style="padding:12px; text-align:center">' . htmlspecialchars($kh['ten_chuyen']) . '<br><small style="color:#64748b">(CS: ' . number_format($kh['cong_suat']) . ' sp/ngày)</small></td>';
                    echo '<td style="padding:12px; text-align:center; font-weight:bold; color:#ef4444; font-size:1.1rem">' . number_format($kh['so_luong']) . '</td>';
                    echo '<td style="padding:12px; text-align:center; font-weight:bold; color:#059669">' . number_format($kh['san_luong_ngay']) . '</td>';
                    echo '<td style="padding:12px; text-align:center">';
                    echo '<div style="font-weight:600">' . date('d/m/Y', strtotime($kh['ngay_bat_dau'])) . '</div>';
                    echo '<div style="color:#cbd5e1; margin:5px 0">↓</div>';
                    echo '<div style="font-weight:600">' . date('d/m/Y', strtotime($kh['ngay_ket_thuc'])) . '</div>';
                    echo '</td>';
                    echo '</tr>';
                    
                    echo '</tbody>';
                    echo '</table>';
                    
                    // Tính toán thời gian
                    $start = new DateTime($kh['ngay_bat_dau']);
                    $end = new DateTime($kh['ngay_ket_thuc']);
                    $soNgay = $start->diff($end)->days + 1;
                    $duKienHoanThanh = $soNgay * $kh['san_luong_ngay'];
                    
                    echo '<div style="margin-top:20px; padding:15px; background:#eff6ff; border-left:4px solid #3b82f6; border-radius:6px">';
                    echo '<strong style="color:#1e40af">💡 Phân Tích:</strong><br>';
                    echo '<ul style="margin:10px 0 0 20px; color:#475569">';
                    echo '<li>Thời gian thực hiện: <strong>' . $soNgay . ' ngày</strong></li>';
                    echo '<li>Dự kiến hoàn thành: <strong>' . number_format($duKienHoanThanh) . ' sản phẩm</strong></li>';
                    
                    if ($duKienHoanThanh >= $kh['so_luong']) {
                        echo '<li style="color:#059669"><i class="fas fa-check-circle"></i> Có thể hoàn thành đúng hạn</li>';
                    } else {
                        echo '<li style="color:#dc2626"><i class="fas fa-exclamation-triangle"></i> Cần điều chỉnh sản lượng hoặc thời gian</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                    
                    // Địa điểm giao hàng
                    if (!empty($kh['dia_diem_giao_hang'])) {
                        echo '<div style="margin-top:15px; padding:12px; background:#fef3c7; border-left:4px solid #f59e0b; border-radius:6px">';
                        echo '<strong style="color:#92400e">📍 Địa điểm giao hàng:</strong> ';
                        echo htmlspecialchars($kh['dia_diem_giao_hang']);
                        echo '</div>';
                    }
                }
                exit; 
            }
            break;

        // ----------------------------------------
        // NHÓM 3: THỐNG KÊ & BÁO CÁO
        // ----------------------------------------
        case 'thong_ke':
            $tab = $_GET['tab'] ?? 'dashboard';
            $dataReport = [];

            if ($tab == 'dashboard') {
                $dataReport = $tkModel->getTongQuanKho();
            } elseif ($tab == 'kho_tp') {
                $dataReport = $tkModel->getTonKhoThanhPham();
            } elseif ($tab == 'kho_nl') {
                $dataReport = $tkModel->getTonKhoNguyenLieu();
            } elseif ($tab == 'xuat_nhap') {
                $dataReport = $tkModel->getLichSuXuatNhap();
            }

            require "../view/thongke_baocao.php"; 
            exit;
    }
}

// ======================================================
// 4. MẶC ĐỊNH: DASHBOARD TỔNG QUAN
// ======================================================

// Đếm đơn hàng chờ duyệt
$sqlDon = "SELECT COUNT(*) as c FROM don_hang_mua WHERE trang_thai='ChoDuyet'";
$resDon = $conn->query($sqlDon);
$donChoDuyet = $resDon ? $resDon->fetch_assoc()['c'] : 0;

// Đếm kế hoạch sản xuất chờ duyệt
$sqlKH = "SELECT COUNT(*) as c FROM kehoachsanxuat WHERE trang_thai='Chờ duyệt'";
$resKH = $conn->query($sqlKH);
$khChoDuyet = $resKH ? $resKH->fetch_assoc()['c'] : 0;

// Tính tổng nhập
$sqlNhapNL = "SELECT SUM(so_luong) as total FROM ct_nhap_nguyen_lieu";
$resNL = $conn->query($sqlNhapNL);
$tongNhapNL = $resNL ? ($resNL->fetch_assoc()['total'] ?? 0) : 0;

$sqlNhapTP = "SELECT SUM(so_luong) as total FROM ct_nhap_thanh_pham";
$resTP = $conn->query($sqlNhapTP);
$tongNhapTP = $resTP ? ($resTP->fetch_assoc()['total'] ?? 0) : 0;

// Đóng gói dữ liệu
$data = [
    'donNCCChoDuyet' => $donChoDuyet,
    'keHoachSXChoDuyet' => $khChoDuyet,
    'tongNhapNL' => $tongNhapNL,
    'tongNhapTP' => $tongNhapTP
];

// Load View Dashboard
require_once __DIR__ . '/../view/giamdoc.php';
?>