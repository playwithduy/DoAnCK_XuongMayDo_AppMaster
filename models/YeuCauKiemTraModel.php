<?php
class YeuCauKiemTraModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Lấy danh sách LÔ để hiển thị trong Dropdown
    public function getAvailableLots() {
        // FIX: Bỏ điều kiện HAVING tong_da_lam > 0 để hiển thị cả những lô đang chạy nhưng chưa có thành phẩm cuối
        // Điều này giúp bạn dễ dàng test các trường hợp biên.
        
        $sql = "SELECT k.id, k.ma_ke_hoach, 
                       dh.so_don_hang,
                       ct.ten_san_pham,
                       ct.size,
                       ct.so_luong as sl_khach_dat,
                       
                       -- Tính sản lượng của công đoạn cuối cùng (Đóng gói)
                       (
                           SELECT COALESCE(SUM(cc.san_luong), 0)
                           FROM cham_cong cc
                           JOIN cong_doan cd ON cc.id_cong_doan = cd.id
                           WHERE cc.id_ke_hoach = k.id
                           AND cd.thu_tu = (
                               SELECT MAX(thu_tu) FROM cong_doan WHERE id_ke_hoach = k.id
                           )
                       ) as tong_da_lam

                FROM kehoachsanxuat k
                JOIN don_hang_ban dh ON k.don_hang_id = dh.id
                JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                WHERE 
                    -- Lấy các trạng thái hợp lệ
                    k.trang_thai IN ('Đã phân bổ', 'dang_thuc_hien', 'HoanThanh', 'Đã duyệt')
                    
                    -- Loại bỏ các lô ĐANG chờ duyệt hoặc ĐÃ xong kiểm tra (Chỉ giữ lại lô chưa gửi hoặc bị từ chối)
                    AND k.id NOT IN (
                        SELECT ke_hoach_id FROM phieu_yeu_cau_kiem_tra 
                        WHERE ke_hoach_id IS NOT NULL 
                        AND trang_thai IN ('cho_duyet', 'dang_kiem_tra', 'hoan_thanh')
                    )
                GROUP BY k.id
                ORDER BY k.ngay_bat_dau DESC";
        
        $result = $this->conn->query($sql);
        if (!$result) die("Lỗi SQL: " . $this->conn->error);
        return $result;
    }

    // 2. Lấy chi tiết (Giữ nguyên logic lấy công đoạn cuối)
    public function getLotDetail($id) {
        $sql = "SELECT k.id, k.ma_ke_hoach, 
                       dh.so_don_hang,
                       ct.ten_san_pham, 
                       ct.size,
                       ct.so_luong as sl_khach_dat,
                       p.image,
                       
                       -- Lấy sản lượng công đoạn cuối
                       (
                           SELECT COALESCE(SUM(cc.san_luong), 0)
                           FROM cham_cong cc
                           JOIN cong_doan cd ON cc.id_cong_doan = cd.id
                           WHERE cc.id_ke_hoach = k.id
                           AND cd.thu_tu = (
                               SELECT MAX(thu_tu) FROM cong_doan WHERE id_ke_hoach = k.id
                           )
                       ) as da_san_xuat

                FROM kehoachsanxuat k
                JOIN don_hang_ban dh ON k.don_hang_id = dh.id
                JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN products p ON ct.ten_san_pham = p.name
                WHERE k.id = ?
                GROUP BY k.id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 3. Tạo phiếu
    public function createRequest($maPhieu, $keHoachId, $userId, $soLuong, $ghiChu) {
        $sql = "INSERT INTO phieu_yeu_cau_kiem_tra 
                (ma_phieu, ke_hoach_id, nguoi_lap_id, so_luong_yeu_cau, ghi_chu, trang_thai, ngay_lap) 
                VALUES (?, ?, ?, ?, ?, 'cho_duyet', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siiis", $maPhieu, $keHoachId, $userId, $soLuong, $ghiChu);
        return $stmt->execute();
    }

    public function generateMaPhieu() {
        return "QC-" . date("dmY") . "-" . rand(10, 99);
    }
}
?>