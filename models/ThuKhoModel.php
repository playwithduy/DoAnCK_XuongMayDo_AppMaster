<?php
class ThuKhoModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Hàm lấy danh sách đơn mua hàng (PO)
    public function getDonHangNCC() {
        $data = [];
        // JOIN với bảng suppliers, lấy cột name
        $sql = "SELECT dh.*, s.name as ten_ncc, u.username 
                FROM don_hang_mua dh 
                LEFT JOIN suppliers s ON dh.supplier_id = s.id 
                LEFT JOIN users u ON dh.user_id = u.id
                ORDER BY dh.ngay_lap DESC";
        
        $rs = $this->conn->query($sql);
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
?>