<?php
class DonHangModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 1. Tạo đơn hàng
    public function taoDonHang($data, $chiTiet) {
        // ... (Giữ nguyên logic cũ của bạn ở đây) ...
        // Để ngắn gọn, tôi tập trung vào 2 hàm cần sửa bên dưới
        // Hãy giữ lại code hàm taoDonHang cũ của bạn
        $sql = "INSERT INTO don_hang_ban (so_don_hang, khach_hang_id, ngay_lap, ngay_giao_du_kien, dia_diem_giao_hang, tong_tien, trang_thai, user_id) 
                VALUES (?, ?, NOW(), ?, ?, ?, 'Moi', ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sisssi", $data['so_don_hang'], $data['khach_hang_id'], $data['ngay_giao'], $data['dia_chi'], $data['tong_tien'], $data['user_id']);
        if ($stmt->execute()) {
            $donHangId = $this->conn->insert_id;
            $sqlChiTiet = "INSERT INTO chi_tiet_don_hang_ban (don_hang_id, ten_san_pham, size, so_luong, don_gia, thanh_tien) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtChiTiet = $this->conn->prepare($sqlChiTiet);
            foreach ($chiTiet as $item) {
                $thanhTien = $item['so_luong'] * $item['don_gia'];
                $stmtChiTiet->bind_param("issidd", $donHangId, $item['ten_san_pham'], $item['size'], $item['so_luong'], $item['don_gia'], $thanhTien);
                $stmtChiTiet->execute();
            }
            return true;
        }
        return false;
    }

    // 2. Lấy danh sách đơn hàng (SỬA ĐỂ LẤY ẢNH, SIZE, USERNAME)
    public function getAllOrders() {
        // Query này sẽ Join bảng chi tiết và products để lấy ảnh
        // GROUP BY dh.id để mỗi đơn hàng chỉ hiện 1 dòng đại diện trong danh sách
        $sql = "SELECT dh.*, 
                       u.username as ten_user_online,
                       ct.ten_san_pham, ct.size, ct.so_luong as sl_san_pham,
                       p.image
                FROM don_hang_ban dh
                LEFT JOIN users u ON dh.user_id = u.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN products p ON ct.ten_san_pham = p.name
                GROUP BY dh.id 
                ORDER BY dh.ngay_lap DESC, dh.id DESC";
                
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 3. Lấy chi tiết đơn hàng (SỬA ĐỂ TRÁNH LỖI UNDEFINED)
    public function getOrderById($id) {
        $stmt = $this->conn->prepare("SELECT dh.*, u.username as ten_user_online 
                                      FROM don_hang_ban dh 
                                      LEFT JOIN users u ON dh.user_id = u.id
                                      WHERE dh.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        if ($order) {
            $stmtDet = $this->conn->prepare("SELECT * FROM chi_tiet_don_hang_ban WHERE don_hang_id = ?");
            $stmtDet->bind_param("i", $id);
            $stmtDet->execute();
            $order['chi_tiet'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $order;
    }
    
    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>