<?php
class BanHangModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Tạo đơn hàng bán
     * Quy trình: Insert Khách Hàng -> Lấy ID -> Insert Đơn Hàng -> Insert Chi Tiết
     */
    public function taoDonHang($data, $items) {
        // --- BƯỚC 1: LƯU THÔNG TIN KHÁCH HÀNG VÀO BẢNG khach_hang ---
        // Kiểm tra xem mã khách hàng đã có chưa, nếu chưa có thì tạo mã tự động để tránh lỗi
        $maKh = !empty($data['ma_kh']) ? $data['ma_kh'] : 'KH-' . time();

        // Câu lệnh insert vào bảng khach_hang
        $sqlKH = "INSERT INTO khach_hang (ten_kh, ma_kh, sdt_email, nguoi_lien_he, dia_chi_giao_hd) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmtKH = $this->conn->prepare($sqlKH);
        $stmtKH->bind_param("sssss", 
            $data['ten_kh'], 
            $maKh, 
            $data['sdt_email'], 
            $data['nguoi_lien_he'], 
            $data['dia_chi']
        );

        if (!$stmtKH->execute()) {
            // Nếu lỗi (ví dụ trùng mã KH), có thể xử lý thêm. 
            // Ở đây ta return false để dừng lại.
            return false; 
        }
        
        // Lấy ID khách hàng vừa tạo
        $khachHangId = $this->conn->insert_id;
        $stmtKH->close();


        // --- BƯỚC 2: LƯU ĐƠN HÀNG VÀO BẢNG don_hang_ban ---
        // Sử dụng khach_hang_id vừa lấy được ở trên
        $sqlDH = "INSERT INTO don_hang_ban 
                  (so_don_hang, khach_hang_id, ngay_lap, ngay_giao_du_kien, dieu_khoan_thanh_toan, 
                   phuong_thuc_van_chuyen, dia_diem_giao_hang, user_id, tong_tien, trang_thai) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Moi')";
        
        $stmtDH = $this->conn->prepare($sqlDH);
        
        // s = string, i = integer, d = double
        // Types: s i s s s s s i d (9 tham số)
        $stmtDH->bind_param("sisssssid", 
            $data['so_don'], 
            $khachHangId,       // Dùng ID khách hàng
            $data['ngay_lap'], 
            $data['ngay_giao'], 
            $data['dk_tt'], 
            $data['pt_vc'], 
            $data['dia_diem_giao'], // Địa chỉ giao cụ thể
            $data['user_id'], 
            $data['tong_tien']
        );

        if ($stmtDH->execute()) {
            // Lấy ID đơn hàng vừa tạo
            $donHangId = $this->conn->insert_id;
            $stmtDH->close();
            
            // --- BƯỚC 3: LƯU CHI TIẾT SẢN PHẨM ---
            $sqlDet = "INSERT INTO chi_tiet_don_hang_ban 
                       (don_hang_id, ten_san_pham, size, so_luong, don_gia, thanh_tien) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmtDet = $this->conn->prepare($sqlDet);
            
            foreach ($items as $item) {
                $thanhTien = $item['so_luong'] * $item['don_gia'];
// don_hang_id(i), ten_sp(s), size(s), so_luong(i), don_gia(d), thanh_tien(d)
                $stmtDet->bind_param("issidd", 
                    $donHangId, 
                    $item['ten_sp'], 
                    $item['size'], 
                    $item['so_luong'], 
                    $item['don_gia'], 
                    $thanhTien
                );
                
                $stmtDet->execute();
            }
            $stmtDet->close();
            
            return true;
        } else {
            // Ghi log lỗi nếu insert đơn hàng thất bại
            error_log("Lỗi Insert Đơn Hàng: " . $stmtDH->error);
            return false;
        }
    }
}
?>
