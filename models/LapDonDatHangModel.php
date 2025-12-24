<?php
class LapDonDatHangModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Lấy danh sách Nhà Cung Cấp (Từ bảng suppliers)
     */
    public function getDanhSachNCC() {
        $data = [];
        // Alias name thành ten_ncc để khớp với View cũ nếu cần, hoặc sửa View
        $sql = "SELECT id, name as ten_ncc, address as dia_chi, phone as sdt FROM suppliers ORDER BY name ASC";
        $result = $this->conn->query($sql);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Lưu Đơn Đặt Hàng (PO)
     */
    public function luuDonDatHang($data, $items) {
        // Cập nhật câu SQL: Dùng cột supplier_id thay vì nha_cung_cap_id
        $sql = "INSERT INTO don_hang_mua 
                (ma_don_hang, supplier_id, ngay_lap, ngay_nhan_du_kien, user_id, tong_tien, trang_thai) 
                VALUES (?, ?, ?, ?, ?, ?, 'ChoDuyet')";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            die("Lỗi SQL (Insert Don Hang): " . $this->conn->error);
        }

        $stmt->bind_param("sissid", 
            $data['ma_don'], 
            $data['ncc_id'], // Giá trị này lấy từ form (là ID của supplier)
            $data['ngay_lap'], 
            $data['ngay_nhan'], 
            $data['user_id'], 
            $data['tong_tien']
        );

        if ($stmt->execute()) {
            $don_hang_id = $stmt->insert_id;
            
            $sqlDetail = "INSERT INTO chi_tiet_don_hang_mua 
                          (don_hang_id, ten_san_pham, so_luong, don_gia, thanh_tien) 
                          VALUES (?, ?, ?, ?, ?)";
            
            $stmtDetail = $this->conn->prepare($sqlDetail);

            foreach ($items as $item) {
                if(!empty($item['ten_sp'])) {
                    $thanh_tien = $item['so_luong'] * $item['don_gia'];
                    
                    $stmtDetail->bind_param("isidd", 
                        $don_hang_id, 
                        $item['ten_sp'], 
                        $item['so_luong'], 
                        $item['don_gia'], 
                        $thanh_tien
                    );
                    $stmtDetail->execute();
                }
            }
            return true;
        } else {
            error_log("Lỗi Execute: " . $stmt->error);
            return false;
        }
    }
}
?>