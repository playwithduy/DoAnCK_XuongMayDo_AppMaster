<?php
class XuongTruongModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Lấy số lượng đơn hàng đang sản xuất
    public function getActivePlans() {
        $sql = "SELECT COUNT(*) as total FROM kehoachsanxuat 
                WHERE trang_thai IN ('Đã phân bổ', 'dang_thuc_hien', 'Đã duyệt')";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    // 2. Lấy sản lượng hôm nay
    public function getTodayProduction() {
        $sql = "SELECT SUM(san_luong) as total FROM cham_cong WHERE ngay_cham_cong = CURDATE()";
        $result = $this->conn->query($sql);
        return $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    // 3. Đếm số công nhân
    public function countWorkers() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'congnhan' AND status = 'active'";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    // 4. Lấy dữ liệu biểu đồ (7 ngày gần nhất)
    public function getChartData() {
        $data = [];
        // Tạo khung 7 ngày
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('d/m', strtotime("-$i days"));
            $data[$date] = ['ngay' => $label, 'san_luong' => 0];
        }

        // Query dữ liệu
        $sql = "SELECT ngay_cham_cong, SUM(san_luong) as san_luong 
                FROM cham_cong 
                WHERE ngay_cham_cong >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                GROUP BY ngay_cham_cong";
        
        $result = $this->conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Ép kiểu ngày để khớp key mảng
                $dbDate = date('Y-m-d', strtotime($row['ngay_cham_cong']));
                if (isset($data[$dbDate])) {
                    $data[$dbDate]['san_luong'] = (int)$row['san_luong'];
                }
            }
        }
        return array_values($data);
    }

    // 5. Lấy danh sách kế hoạch kèm tiến độ (QUAN TRỌNG)
    public function getPlanProgress() {
        $sql = "SELECT 
                    k.id,
                    k.ma_ke_hoach,
                    dh.so_don_hang,
                    -- Tên sản phẩm
                    (SELECT GROUP_CONCAT(DISTINCT ten_san_pham SEPARATOR ', ') 
                     FROM chi_tiet_don_hang_ban WHERE don_hang_id = dh.id) as ten_don_hang,
                    
                    -- Chỉ tiêu (Tổng SL khách đặt)
                    (SELECT SUM(so_luong) FROM chi_tiet_don_hang_ban WHERE don_hang_id = dh.id) as chi_tieu,

                    -- Đã làm (Tổng sản lượng của CÔNG ĐOẠN CUỐI CÙNG)
                    (
                       SELECT COALESCE(SUM(cc.san_luong), 0)
                       FROM cham_cong cc
                       JOIN cong_doan cd ON cc.id_cong_doan = cd.id
                       WHERE cc.id_ke_hoach = k.id
                       -- Logic: Lấy công đoạn có thứ tự lớn nhất trong kế hoạch đó
                       AND cd.thu_tu = (
                           SELECT MAX(thu_tu) FROM cong_doan WHERE id_ke_hoach = k.id
                       )
                    ) as da_lam,

                    k.trang_thai

                FROM kehoachsanxuat k
                JOIN don_hang_ban dh ON k.don_hang_id = dh.id
                -- Hiển thị tất cả các trạng thái đang chạy
                WHERE k.trang_thai IN ('Đã phân bổ', 'dang_thuc_hien', 'Đã duyệt', 'HoanThanh')
                GROUP BY k.id
                ORDER BY k.ngay_bat_dau DESC
                LIMIT 10";
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            // Debug lỗi nếu có
            error_log("SQL Error: " . $this->conn->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>