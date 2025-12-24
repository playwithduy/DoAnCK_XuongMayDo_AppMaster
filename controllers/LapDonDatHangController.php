<?php
session_start();

// 1. Gọi các file cấu hình và Model
require_once "../config/database.php";
require_once "../models/LapDonDatHangModel.php";

// 2. Kiểm tra đăng nhập (Bắt buộc)
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// 3. Khởi tạo Model
$model = new LapDonDatHangModel($conn);

// Lấy hành động từ URL (mặc định là 'form_nhap')
$action = $_GET['action'] ?? 'form_nhap';

/* ===============================================================
   PHẦN XỬ LÝ LƯU DỮ LIỆU (KHI NGƯỜI DÙNG BẤM NÚT LƯU - POST)
   =============================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_luu_don'])) {
    
    // A. Thu thập thông tin chung của đơn hàng
    $data = [
        'ma_don'    => $_POST['ma_don_hang'],
        'ncc_id'    => $_POST['nha_cung_cap_id'],
        'ngay_lap'  => $_POST['ngay_lap'],
        'ngay_nhan' => $_POST['ngay_nhan_du_kien'],
        'user_id'   => $_SESSION['user']['id'], // Lấy ID nhân viên đang đăng nhập
        'tong_tien' => 0 // Sẽ tính lại dựa trên chi tiết sản phẩm
    ];

    // B. Thu thập và xử lý danh sách sản phẩm
    $items = [];
    $tong_tien_tinh_toan = 0;

    if (isset($_POST['ten_sp'])) {
        $count = count($_POST['ten_sp']);
        for ($i = 0; $i < $count; $i++) {
            $ten_sp = $_POST['ten_sp'][$i];
            $so_luong = (int)$_POST['so_luong'][$i];
            $don_gia  = (float)$_POST['don_gia'][$i];

            // Chỉ lấy những dòng có nhập tên sản phẩm
            if (!empty($ten_sp)) {
                $items[] = [
                    'ten_sp'   => $ten_sp,
                    'so_luong' => $so_luong,
                    'don_gia'  => $don_gia
                ];
                // Cộng dồn vào tổng tiền
                $tong_tien_tinh_toan += ($so_luong * $don_gia);
            }
        }
    }
    
    // Cập nhật lại tổng tiền chính xác
    $data['tong_tien'] = $tong_tien_tinh_toan;

    // C. Gọi Model để lưu vào CSDL
    if ($model->luuDonDatHang($data, $items)) {
        echo "<script>
                alert('✅ Đã lập đơn đặt hàng thành công!'); 
                window.location.href = 'LapDonDatHangController.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Lỗi hệ thống: Không thể lưu đơn hàng.');
              </script>";
    }
}

/* ===============================================================
   PHẦN ĐIỀU HƯỚNG GIAO DIỆN (GET)
   =============================================================== */
switch ($action) {
    case 'form_nhap':
    default:
        // 1. Lấy danh sách Nhà Cung Cấp để hiện trong ô chọn (Dropdown)
        $ds_ncc = $model->getDanhSachNCC();
        
        // 2. Gọi giao diện nhập liệu
        // (Lưu ý: Bạn cần đảm bảo file view này tên là sales_po_form.php hoặc đổi tên cho khớp)
        include '../view/sales_po_form.php'; 
        break;
}
?>