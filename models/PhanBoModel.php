<?php
class PhanBoModel {
    private $conn;
    
    public function __construct($conn) { 
        $this->conn = $conn; 
    }

    // 1. Lấy kế hoạch đã duyệt nhưng chưa phân bổ
    public function getKeHoachCho() {
        $data = [];
        $sql = "SELECT kh.id, kh.ma_ke_hoach, kh.ngay_bat_dau, kh.ngay_ket_thuc, 
                       kh.san_luong_ngay, kh.trang_thai, dh.so_don_hang, dc.ten_chuyen,
                       GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_sp,
                       SUM(ct.so_luong) as tong_so_luong
                FROM kehoachsanxuat kh 
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id 
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN daychuyen dc ON kh.day_chuyen_id = dc.id
                WHERE kh.trang_thai = 'Đã duyệt'
                GROUP BY kh.id
                ORDER BY kh.ngay_bat_dau ASC";
        
        $rs = $this->conn->query($sql);
        if($rs) while($r = $rs->fetch_assoc()) $data[] = $r;
        return $data;
    }

    // 2. Lấy chi tiết 1 kế hoạch
    public function getKeHoachById($id) {
        $sql = "SELECT kh.*, dh.so_don_hang, dh.dia_diem_giao_hang, dh.ngay_giao_du_kien,
                       dc.ten_chuyen, dc.cong_suat,
                       GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_sp,
                       SUM(ct.so_luong) as tong_so_luong
                FROM kehoachsanxuat kh 
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id 
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN daychuyen dc ON kh.day_chuyen_id = dc.id
                WHERE kh.id = ? GROUP BY kh.id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 3. Lấy danh sách Xưởng trưởng
    public function getXuongTruong() {
        $res = $this->conn->query("SELECT id, username FROM users WHERE role='xuongtruong' AND status='active'");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 4. Tạo lệnh sản xuất & Phân bổ công đoạn (FIX LỖI TẠI ĐÂY)
    public function createPhanBo($kh_id, $xt_id, $note) {
        // A. Tạo Lệnh Sản Xuất
        $ma = "LSX-" . time();
        $stmt = $this->conn->prepare("INSERT INTO lenhsanxuat (ma_lenh, ke_hoach_id, xuong_truong_id, ghi_chu_phan_bo, trang_thai) VALUES (?, ?, ?, ?, 'Mới')");
        $stmt->bind_param("siis", $ma, $kh_id, $xt_id, $note);
        
        if (!$stmt->execute()) return false;
        $stmt->close();
        
        // B. Cập nhật trạng thái Kế hoạch
        $this->conn->query("UPDATE kehoachsanxuat SET trang_thai = 'Đã phân bổ' WHERE id = " . (int)$kh_id);
        
        // C. Tạo các công đoạn mặc định (FIX: Lấy đúng số lượng khách đặt)
        $checkCD = $this->conn->query("SELECT id FROM cong_doan WHERE id_ke_hoach = " . (int)$kh_id);
        
        if ($checkCD->num_rows == 0) {
            // 1. Lấy tổng số lượng sản phẩm khách đặt cho kế hoạch này
            $sqlQty = "SELECT SUM(ct.so_luong) as sl_khach_dat
                       FROM kehoachsanxuat kh
                       JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                       JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                       WHERE kh.id = " . (int)$kh_id;
            
            $resQty = $this->conn->query($sqlQty);
            $rowQty = $resQty->fetch_assoc();
            $slCanLam = (int)$rowQty['sl_khach_dat'];

            // Nếu không lấy được số lượng, mặc định là 0 (tránh lỗi)
            if ($slCanLam <= 0) $slCanLam = 100; 

            // 2. Tạo công đoạn với chỉ tiêu = Số lượng khách đặt
            // Bạn có thể chỉnh tỷ lệ hao hụt nếu muốn (ví dụ Cắt vải cần làm dư 2% -> $slCanLam * 1.02)
            // Ở đây tôi để bằng nhau hết cho dễ quản lý.
            $congDoanMacDinh = [
                ['ten' => 'Cắt vải',  'thu_tu' => 1, 'chi_tieu' => $slCanLam],
                ['ten' => 'May',      'thu_tu' => 2, 'chi_tieu' => $slCanLam],
                ['ten' => 'Ủi',       'thu_tu' => 3, 'chi_tieu' => $slCanLam],
                ['ten' => 'Đóng gói', 'thu_tu' => 4, 'chi_tieu' => $slCanLam] // Quan trọng: Đóng gói phải bằng SL khách đặt
            ];
            
            $stmtCD = $this->conn->prepare("INSERT INTO cong_doan (id_ke_hoach, ten_cong_doan, chi_tieu, thu_tu, trang_thai) VALUES (?, ?, ?, ?, 'cho')");
            
            foreach ($congDoanMacDinh as $cd) {
                $stmtCD->bind_param("isii", $kh_id, $cd['ten'], $cd['chi_tieu'], $cd['thu_tu']);
                $stmtCD->execute();
            }
            $stmtCD->close();
        }
        
        return true;
    }
    
    // 5. Lấy tất cả lệnh
    public function getAllLenhSanXuat() {
        $sql = "SELECT lsx.*, kh.ma_ke_hoach, dh.so_don_hang, u.username as ten_xuong_truong, dc.ten_chuyen,
                       GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_san_pham
                FROM lenhsanxuat lsx
                LEFT JOIN kehoachsanxuat kh ON lsx.ke_hoach_id = kh.id
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN daychuyen dc ON kh.day_chuyen_id = dc.id
                LEFT JOIN users u ON lsx.xuong_truong_id = u.id
                GROUP BY lsx.id ORDER BY lsx.ngay_tao DESC";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
?>