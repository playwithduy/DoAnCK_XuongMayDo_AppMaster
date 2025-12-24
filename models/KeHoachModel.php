<?php
class KeHoachModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    // 1. Lấy đơn hàng CHƯA có kế hoạch (hoặc kế hoạch bị từ chối)
    public function getDonHangCho() {
        $data = [];
        
        // Lấy đơn hàng chưa có KH HOẶC KH bị từ chối (có thể lập lại)
        $sql = "SELECT dh.id, 
                       dh.so_don_hang, 
                       dh.khach_hang_id,
                       dh.ngay_lap,
                       dh.ngay_giao_du_kien, 
                       dh.dia_diem_giao_hang,
                       dh.tong_tien,
                       dh.trang_thai,
                       dh.user_id,
                       u.username as ten_nguoi_dat,
                       COALESCE(SUM(ct.so_luong), 0) as tong_so_luong,
                       GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as danh_sach_sp
                FROM don_hang_ban dh 
                LEFT JOIN users u ON dh.user_id = u.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id 
                WHERE (
                    -- Chưa có kế hoạch
                    dh.id NOT IN (
                        SELECT don_hang_id 
                        FROM kehoachsanxuat 
                        WHERE don_hang_id IS NOT NULL 
                        AND trang_thai NOT IN ('Từ chối')
                    )
                )
                GROUP BY dh.id, dh.so_don_hang, dh.khach_hang_id, dh.ngay_lap, 
                         dh.ngay_giao_du_kien, dh.dia_diem_giao_hang, dh.tong_tien, 
                         dh.trang_thai, dh.user_id, u.username
                ORDER BY dh.ngay_lap DESC";
                
        $rs = $this->conn->query($sql);
        
        if (!$rs) {
            // Debug: In lỗi SQL nếu có
            error_log("SQL Error in getDonHangCho: " . $this->conn->error);
            return [];
        }
        
        while($r = $rs->fetch_assoc()) {
            $data[] = $r;
        }
        
        return $data;
    }

    // 2. Lấy chi tiết 1 đơn hàng (Header + Detail)
    public function getDonHangById($id) {
        // Lấy thông tin chung
        $sql = "SELECT dh.*, u.username as ten_nguoi_dat
                FROM don_hang_ban dh
                LEFT JOIN users u ON dh.user_id = u.id
                WHERE dh.id = " . (int)$id;
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            error_log("SQL Error in getDonHangById: " . $this->conn->error);
            return null;
        }
        
        $order = $result->fetch_assoc();

        if ($order) {
            // Lấy danh sách sản phẩm chi tiết
            $sqlDet = "SELECT ct.*, 
                              p.image, 
                              p.price as don_gia_product,
                              p.name as ten_product
                       FROM chi_tiet_don_hang_ban ct
                       LEFT JOIN products p ON ct.ten_san_pham = p.name
                       WHERE ct.don_hang_id = " . (int)$id;
            
            $rsDet = $this->conn->query($sqlDet);
            
            if ($rsDet) {
                $order['chi_tiet'] = $rsDet->fetch_all(MYSQLI_ASSOC);
                
                // Tính tổng số lượng
                $order['tong_so_luong'] = 0;
                foreach ($order['chi_tiet'] as $item) {
                    $order['tong_so_luong'] += $item['so_luong'];
                }
            } else {
                $order['chi_tiet'] = [];
                $order['tong_so_luong'] = 0;
            }
        }
        
        return $order;
    }

    // 3. Lấy dây chuyền
    public function getDayChuyen() {
        $data = [];
        $sql = "SELECT * FROM daychuyen WHERE trang_thai = 'Hoạt động' ORDER BY id";
        $rs = $this->conn->query($sql);
        
        if (!$rs) {
            error_log("SQL Error in getDayChuyen: " . $this->conn->error);
            return [];
        }
        
        while($r = $rs->fetch_assoc()) {
            $data[] = $r;
        }
        
        return $data;
    }

    // 4. Tạo kế hoạch (Gửi Giám đốc duyệt)
    public function create($dh_id, $dc_id, $bd, $kt, $sl_ngay, $uid) {
        // Kiểm tra đơn hàng tồn tại
        $check = $this->conn->query("SELECT id FROM don_hang_ban WHERE id = " . (int)$dh_id);
        if (!$check || $check->num_rows == 0) {
            error_log("Đơn hàng không tồn tại: " . $dh_id);
            return false;
        }
        
        // Kiểm tra đã có kế hoạch chưa (trừ kế hoạch bị từ chối)
        $checkKH = $this->conn->query("SELECT id FROM kehoachsanxuat 
                                       WHERE don_hang_id = " . (int)$dh_id . " 
                                       AND trang_thai NOT IN ('Từ chối')");
        if ($checkKH && $checkKH->num_rows > 0) {
            error_log("Đơn hàng đã có kế hoạch: " . $dh_id);
            return false;
        }
        
        $ma = "KH-" . time();
        
        // Trạng thái = 'Chờ duyệt'
        $stmt = $this->conn->prepare("INSERT INTO kehoachsanxuat 
            (ma_ke_hoach, don_hang_id, day_chuyen_id, ngay_bat_dau, ngay_ket_thuc, san_luong_ngay, nguoi_lap_id, trang_thai) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Chờ duyệt')");
        
        if (!$stmt) {
            error_log("Prepare Error: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("siissii", $ma, $dh_id, $dc_id, $bd, $kt, $sl_ngay, $uid);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Execute Error: " . $stmt->error);
        }
        
        $stmt->close();
        
        return $result;
    }
    
    // 5. Lấy tất cả kế hoạch (để quản lý)
    public function getAllKeHoach() {
        $sql = "SELECT kh.*, 
                       dh.so_don_hang,
                       dc.ten_chuyen,
                       u.username as nguoi_lap
                FROM kehoachsanxuat kh
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                LEFT JOIN daychuyen dc ON kh.day_chuyen_id = dc.id
                LEFT JOIN users u ON kh.nguoi_lap_id = u.id
                ORDER BY kh.ngay_lap DESC";
        
        $rs = $this->conn->query($sql);
        
        if (!$rs) {
            error_log("SQL Error in getAllKeHoach: " . $this->conn->error);
            return [];
        }
        
        return $rs->fetch_all(MYSQLI_ASSOC);
    }
}
?>