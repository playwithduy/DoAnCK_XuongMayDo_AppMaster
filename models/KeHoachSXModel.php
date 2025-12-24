<?php
class KeHoachSXModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 1. Lấy danh sách kế hoạch chờ duyệt
    public function getKeHoachChoDuyet() {
        // Sửa lại: JOIN đúng với don_hang_ban thay vì donhangkh
        // Lấy thêm thông tin sản phẩm từ chi_tiet_don_hang_ban
        $sql = "SELECT kh.id, 
                       kh.ma_ke_hoach,
                       kh.ngay_bat_dau, 
                       kh.ngay_ket_thuc, 
                       kh.san_luong_ngay,
                       kh.trang_thai,
                       kh.ngay_lap,
                       dh.so_don_hang as ma_don_kh,
                       dh.tong_tien,
                       dc.ten_chuyen,
                       dc.cong_suat,
                       u.username as nguoi_lap,
                       GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_san_pham,
                       SUM(ct.so_luong) as so_luong
                FROM kehoachsanxuat kh
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN daychuyen dc ON kh.day_chuyen_id = dc.id
                LEFT JOIN users u ON kh.nguoi_lap_id = u.id
                WHERE kh.trang_thai = 'Chờ duyệt'
                GROUP BY kh.id, kh.ma_ke_hoach, kh.ngay_bat_dau, kh.ngay_ket_thuc, 
                         kh.san_luong_ngay, kh.trang_thai, kh.ngay_lap,
                         dh.so_don_hang, dh.tong_tien, dc.ten_chuyen, dc.cong_suat, u.username
                ORDER BY kh.ngay_lap DESC";
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            error_log("SQL Error in getKeHoachChoDuyet: " . $this->conn->error);
            return null;
        }
        
        return $result;
    }

    // 2. Lấy chi tiết 1 kế hoạch (Phục vụ Modal)
    public function getKeHoachById($id) {
        $sql = "SELECT kh.*, 
                       dh.so_don_hang as ma_don_hang,
                       dh.dia_diem_giao_hang,
                       dh.ngay_giao_du_kien,
                       dc.ten_chuyen, 
                       dc.cong_suat,
                       dc.trang_thai as trang_thai_chuyen,
                       u.username as nguoi_lap,
                       GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_san_pham,
                       SUM(ct.so_luong) as so_luong
                FROM kehoachsanxuat kh
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN daychuyen dc ON kh.day_chuyen_id = dc.id
                LEFT JOIN users u ON kh.nguoi_lap_id = u.id
                WHERE kh.id = ?
                GROUP BY kh.id";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare Error in getKeHoachById: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return $result;
    }

    // 3. Phê duyệt (Thêm log)
    public function pheDuyetKeHoach($id, $userId) {
        // Kiểm tra kế hoạch tồn tại
        $check = $this->conn->query("SELECT id, trang_thai FROM kehoachsanxuat WHERE id = " . (int)$id);
        if (!$check || $check->num_rows == 0) {
            error_log("Kế hoạch không tồn tại: " . $id);
            return false;
        }
        
        $current = $check->fetch_assoc();
        if ($current['trang_thai'] !== 'Chờ duyệt') {
            error_log("Kế hoạch không ở trạng thái chờ duyệt: " . $id . " - Trạng thái: " . $current['trang_thai']);
            return false;
        }
        
        $sql = "UPDATE kehoachsanxuat 
                SET trang_thai = 'Đã duyệt' 
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare Error in pheDuyetKeHoach: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Execute Error in pheDuyetKeHoach: " . $stmt->error);
        }
        
        $stmt->close();
        return $result;
    }

    // 4. Từ chối
    public function tuChoiKeHoach($id, $lyDo) {
        // Kiểm tra kế hoạch tồn tại
        $check = $this->conn->query("SELECT id, trang_thai FROM kehoachsanxuat WHERE id = " . (int)$id);
        if (!$check || $check->num_rows == 0) {
            error_log("Kế hoạch không tồn tại: " . $id);
            return false;
        }
        
        $sql = "UPDATE kehoachsanxuat 
                SET trang_thai = 'Từ chối', 
                    ly_do_tu_choi = ? 
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare Error in tuChoiKeHoach: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("si", $lyDo, $id);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Execute Error in tuChoiKeHoach: " . $stmt->error);
        }
        
        $stmt->close();
        return $result;
    }
    
    // 5. Lấy tất cả kế hoạch (cho trang quản lý)
    public function getAllKeHoach($trangThai = null) {
        $where = "";
        if ($trangThai) {
            $where = "WHERE kh.trang_thai = '" . $this->conn->real_escape_string($trangThai) . "'";
        }
        
        $sql = "SELECT kh.*, 
                       dh.so_don_hang,
                       dc.ten_chuyen,
                       u.username as nguoi_lap,
                       GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_san_pham,
                       SUM(ct.so_luong) as so_luong
                FROM kehoachsanxuat kh
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN daychuyen dc ON kh.day_chuyen_id = dc.id
                LEFT JOIN users u ON kh.nguoi_lap_id = u.id
                $where
                GROUP BY kh.id
                ORDER BY kh.ngay_lap DESC";
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            error_log("SQL Error in getAllKeHoach: " . $this->conn->error);
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>