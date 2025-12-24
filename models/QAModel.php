<?php
class QAModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 1. Lấy danh sách yêu cầu cần kiểm tra (Lọc theo Lô sản phẩm)
    public function getPendingRequests() {
        $sql = "SELECT 
                    p.id, 
                    p.ma_phieu, 
                    p.ngay_lap as ngay_yeu_cau, 
                    p.so_luong_yeu_cau as so_luong_can_kiem, 
                    p.trang_thai,
                    k.ma_ke_hoach as lo_san_xuat,
                    u.username as nguoi_yeu_cau,
                    ct.ten_san_pham
                FROM phieu_yeu_cau_kiem_tra p
                LEFT JOIN users u ON p.nguoi_lap_id = u.id
                LEFT JOIN kehoachsanxuat k ON p.ke_hoach_id = k.id
                LEFT JOIN don_hang_ban dh ON k.don_hang_id = dh.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                WHERE p.trang_thai IN ('cho_duyet', 'dang_kiem_tra')
                GROUP BY p.id
                ORDER BY p.ngay_lap DESC";

        $result = $this->conn->query($sql);
        if (!$result) die("Lỗi SQL (getPendingRequests): " . $this->conn->error);
        return $result;
    }

    // 2. Lấy lịch sử kiểm tra
    public function getHistory() {
        $sql = "SELECT 
                    bb.id, 
                    bb.ma_san_pham, 
                    bb.ten_san_pham, 
                    bb.lo_san_xuat, 
                    bb.ngay_kiem_tra, 
                    bb.ket_qua_chung,
                    u.username as nguoi_kiem_tra
                FROM qa_bien_ban bb
                LEFT JOIN users u ON bb.user_id = u.id
                ORDER BY bb.ngay_kiem_tra DESC, bb.id DESC";
                
        $result = $this->conn->query($sql);
        if (!$result) {
            error_log("Lỗi SQL getHistory: " . $this->conn->error);
            return [];
        }
        return $result;
    }
    // 3. Cập nhật trạng thái phiếu
    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE phieu_yeu_cau_kiem_tra SET trang_thai = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    // 4. Lấy chi tiết 1 phiếu để thực hiện kiểm tra (Fill form)
    public function getRequestById($id) {
        $sql = "SELECT p.*, k.ma_ke_hoach as lo_san_xuat, ct.ten_san_pham, ct.size
                FROM phieu_yeu_cau_kiem_tra p
                JOIN kehoachsanxuat k ON p.ke_hoach_id = k.id
                JOIN don_hang_ban dh ON k.don_hang_id = dh.id
                JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                WHERE p.id = ? GROUP BY p.id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 5. Lưu biên bản kiểm tra (Lưu Header & Detail)
    public function createBienBan($data, $criteria_list, $phieu_yc_id) {
        // A. Cập nhật trạng thái phiếu yêu cầu -> Hoàn thành
        $this->updateStatus($phieu_yc_id, 'hoan_thanh');

        // B. Lưu biên bản chính (Header)
        $sql = "INSERT INTO qa_bien_ban (phieu_yeu_cau_id, ma_san_pham, ten_san_pham, lo_san_xuat, user_id, ngay_kiem_tra, ket_qua_chung, khuyen_nghi, huong_dan_khac_phuc) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssissss", 
            $phieu_yc_id, 
            $data['ma_sp'], 
            $data['ten_sp'], 
            $data['lo_sx'], 
            $data['user_id'], 
            $data['ngay_kt'], 
            $data['ket_qua'], 
            $data['khuyen_nghi'], 
            $data['huong_dan']
        );
        
        if (!$stmt->execute()) {
            error_log("Lỗi Insert QA Header: " . $stmt->error);
            return false;
        }
        
        $bien_ban_id = $stmt->insert_id;
        $stmt->close();

        // C. Lưu chi tiết tiêu chí (Detail)
        if (!empty($criteria_list)) {
            $sqlDet = "INSERT INTO qa_chi_tiet (bien_ban_id, tieu_chi, tieu_chuan, ket_qua, ghi_chu) VALUES (?, ?, ?, ?, ?)";
            $stmtDet = $this->conn->prepare($sqlDet);
            
            foreach ($criteria_list as $row) {
                $stmtDet->bind_param("issss", $bien_ban_id, $row['tieu_chi'], $row['tieu_chuan'], $row['ket_qua'], $row['ghi_chu']);
                $stmtDet->execute();
            }
            $stmtDet->close();
        }

        return true;
    }
}
?>