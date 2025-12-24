<?php
session_start();
require_once "../config/database.php";
require_once "../models/BanHangModel.php";

// 1. Kiểm tra đăng nhập và quyền hạn
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// (Tùy chọn) Kiểm tra quyền sales/kinhdoanh nếu cần
// if ($_SESSION['user']['role'] !== 'kinhdoanh') { die("Không có quyền truy cập"); }

$model = new BanHangModel($conn);

// 2. Xử lý khi người dùng bấm nút "LƯU ĐƠN HÀNG"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_luu_ban'])) {
    
    // A. Thu thập dữ liệu Header (Thông tin chung)
    $dataHeader = [
        'so_don'        => $_POST['so_don_hang'],
        'ten_kh'        => $_POST['ten_kh'],
        'ma_kh'         => $_POST['ma_kh'],
        'sdt_email'     => $_POST['sdt_email'],
        'dia_chi'       => $_POST['dia_chi'],
        'nguoi_lien_he' => $_POST['nguoi_lien_he'],
        'ngay_lap'      => $_POST['ngay_lap'],
        'ngay_giao'     => $_POST['ngay_giao'],
        'dk_tt'         => $_POST['dieu_khoan_tt'],
        'pt_vc'         => $_POST['phuong_thuc_vc'],
        'dia_diem_giao' => $_POST['dia_diem_giao'],
        'user_id'       => $_SESSION['user']['id'], // ID nhân viên sale đang đăng nhập
        'tong_tien'     => 0 // Sẽ tính lại bên dưới
    ];

    // B. Thu thập dữ liệu Detail (Danh sách sản phẩm)
    $items = [];
    $tongTien = 0;

    if (isset($_POST['ten_sp']) && is_array($_POST['ten_sp'])) {
        $count = count($_POST['ten_sp']);
        
        for ($i = 0; $i < $count; $i++) {
            $tenSP = trim($_POST['ten_sp'][$i]);
            
            // Chỉ lấy những dòng có tên sản phẩm
            if (!empty($tenSP)) {
                $size   = $_POST['size'][$i] ?? 'Free'; // Lấy size, mặc định Free nếu rỗng
                $soLuong = (int)$_POST['so_luong'][$i];
                $donGia  = (float)$_POST['don_gia'][$i];
                
                // Tính thành tiền cho dòng này để cộng vào tổng
                $thanhTien = $soLuong * $donGia;
                $tongTien += $thanhTien;

                $items[] = [
                    'ten_sp'   => $tenSP,
                    'size'     => $size,
                    'so_luong' => $soLuong,
                    'don_gia'  => $donGia
                ];
            }
        }
    }

    // Cập nhật lại tổng tiền vào mảng header
    $dataHeader['tong_tien'] = $tongTien;

    // C. Gọi Model để lưu vào Database
    if (!empty($items)) {
        if ($model->taoDonHang($dataHeader, $items)) {
            // Thành công -> Báo & Load lại trang (hoặc chuyển hướng)
            echo "<script>
                    alert('✅ Lập đơn bán hàng thành công! Mã đơn: {$dataHeader['so_don']}'); 
                    window.location.href='BanHangController.php';
                  </script>";
        } else {
            echo "<script>alert('❌ Lỗi: Không thể lưu đơn hàng vào CSDL.');</script>";
        }
    } else {
        echo "<script>alert('⚠️ Vui lòng nhập ít nhất một sản phẩm.');</script>";
    }
}

// 3. Load giao diện Form nhập liệu
// Lưu ý: Đường dẫn này dựa trên cấu trúc thư mục của bạn
require_once '../view/ban_hang_form.php';
?>