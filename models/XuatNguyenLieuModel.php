<?php
class XuatNguyenLieuModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Lấy danh sách phiếu yêu cầu đang chờ (Trạng thái: cho_xac_nhan)
    public function getPendingRequests() {
        $sql = "SELECT pyc.*, u.username as nguoi_lap 
                FROM phieu_yeu_cau_nguyen_lieu pyc
                JOIN users u ON pyc.nguoi_lap_id = u.id
                WHERE pyc.trang_thai = 'cho_xac_nhan'
                ORDER BY pyc.ngay_lap ASC";
        return $this->conn->query($sql);
    }

    // 2. Lấy chi tiết nguyên liệu của 1 phiếu (Kèm tồn kho hiện tại)
    public function getRequestDetails($phieuId) {
        $sql = "SELECT ct.*, nl.name as ten_nl, nl.unit, nl.ton_kho
                FROM ct_yeu_cau_nguyen_lieu ct
                JOIN nguyen_lieu nl ON ct.nguyen_lieu_id = nl.id
                WHERE ct.phieu_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $phieuId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 3. Xử lý XUẤT KHO (Transaction)
    public function exportStock($phieuId, $userId, $ghiChu) {
        $this->conn->begin_transaction();
        try {
            // A. Kiểm tra tồn kho lần cuối
            $details = $this->getRequestDetails($phieuId);
            foreach ($details as $item) {
                if ($item['ton_kho'] < $item['so_luong_yeu_cau']) {
                    throw new Exception("Nguyên liệu '{$item['ten_nl']}' không đủ tồn kho!");
                }
            }

            // B. Trừ tồn kho
            $updateStock = $this->conn->prepare("UPDATE nguyen_lieu SET ton_kho = ton_kho - ? WHERE id = ?");
            foreach ($details as $item) {
                $updateStock->bind_param("di", $item['so_luong_yeu_cau'], $item['nguyen_lieu_id']);
                $updateStock->execute();
            }

            // C. Tạo phiếu xuất kho
            $maPhieuXuat = "PX" . date("YmdHis");
            $insertExport = $this->conn->prepare("INSERT INTO phieu_xuat_kho (ma_phieu_xuat, phieu_yeu_cau_id, nguoi_xuat_id, ghi_chu) VALUES (?, ?, ?, ?)");
            $insertExport->bind_param("siis", $maPhieuXuat, $phieuId, $userId, $ghiChu);
            $insertExport->execute();

            // D. Cập nhật trạng thái phiếu yêu cầu thành "Đã cấp"
            $updateStatus = $this->conn->prepare("UPDATE phieu_yeu_cau_nguyen_lieu SET trang_thai = 'da_duyet' WHERE id = ?");
            $updateStatus->bind_param("i", $phieuId);
            $updateStatus->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return $e->getMessage();
        }
    }

    // 4. Từ chối phiếu
    public function rejectRequest($phieuId, $reason) {
        $stmt = $this->conn->prepare("UPDATE phieu_yeu_cau_nguyen_lieu SET trang_thai = 'tu_choi', ghi_chu = CONCAT(ghi_chu, ' | Lý do hủy: ', ?) WHERE id = ?");
        $stmt->bind_param("si", $reason, $phieuId);
        return $stmt->execute();
    }
}
?>