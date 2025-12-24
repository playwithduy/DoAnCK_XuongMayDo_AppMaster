<?php
class YeuCauNguyenLieuModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách kế hoạch đã phân bổ cho xưởng trưởng (từ lệnh sản xuất)
    public function getActivePlans($xuongTruongId = null) {
        if ($xuongTruongId === null) {
            // Nếu không có xuongTruongId, lấy tất cả kế hoạch đã phân bổ
            $sql = "SELECT DISTINCT kh.id, kh.ma_ke_hoach, kh.san_luong_ngay, kh.ngay_bat_dau, kh.ngay_ket_thuc,
                           dh.so_don_hang, 
                           GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_san_pham
                    FROM kehoachsanxuat kh
                    INNER JOIN lenhsanxuat lsx ON kh.id = lsx.ke_hoach_id
                    LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                    LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                    WHERE kh.trang_thai = 'Đã phân bổ'
                      AND lsx.trang_thai IN ('Mới', 'Đang thực hiện')
                    GROUP BY kh.id, kh.ma_ke_hoach, kh.san_luong_ngay, kh.ngay_bat_dau, kh.ngay_ket_thuc, dh.so_don_hang
                    ORDER BY kh.ngay_bat_dau DESC";
            return $this->conn->query($sql);
        } else {
            // Lấy kế hoạch của xưởng trưởng cụ thể
            $sql = "SELECT DISTINCT kh.id, kh.ma_ke_hoach, kh.san_luong_ngay, kh.ngay_bat_dau, kh.ngay_ket_thuc,
                           dh.so_don_hang,
                           GROUP_CONCAT(DISTINCT ct.ten_san_pham SEPARATOR ', ') as ten_san_pham
                    FROM kehoachsanxuat kh
                    INNER JOIN lenhsanxuat lsx ON kh.id = lsx.ke_hoach_id
                    LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                    LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                    WHERE kh.trang_thai = 'Đã phân bổ'
                      AND lsx.xuong_truong_id = ?
                      AND lsx.trang_thai IN ('Mới', 'Đang thực hiện')
                    GROUP BY kh.id, kh.ma_ke_hoach, kh.san_luong_ngay, kh.ngay_bat_dau, kh.ngay_ket_thuc, dh.so_don_hang
                    ORDER BY kh.ngay_bat_dau DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $xuongTruongId);
            $stmt->execute();
            return $stmt->get_result();
        }
    }

    // Lấy nguyên liệu dự kiến dựa trên Kế hoạch -> Đơn hàng -> Sản phẩm -> Định mức
    public function getEstimatedMaterials($keHoachId) {
        // 1. Lấy thông tin kế hoạch từ don_hang_ban và chi_tiet_don_hang_ban
        $sql = "SELECT kh.id as ke_hoach_id,
                       kh.san_luong_ngay,
                       kh.ngay_bat_dau,
                       kh.ngay_ket_thuc,
                       kh.don_hang_id,
                       dh.so_don_hang,
                       ct.ten_san_pham,
                       ct.so_luong as don_hang_so_luong,
                       p.id as product_id,
                       p.name as product_name
                FROM kehoachsanxuat kh
                LEFT JOIN don_hang_ban dh ON kh.don_hang_id = dh.id
                LEFT JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
                LEFT JOIN products p ON ct.ten_san_pham = p.name
                WHERE kh.id = ?
                ORDER BY ct.id ASC
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $keHoachId);
        $stmt->execute();
        $khInfo = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$khInfo) {
            error_log("Không tìm thấy kế hoạch: " . $keHoachId);
            return ['error' => 'Không tìm thấy kế hoạch sản xuất'];
        }

        // Xác định sản phẩm
        $productId = $khInfo['product_id'] ?? null;
        $productName = $khInfo['product_name'] ?? $khInfo['ten_san_pham'] ?? null;
        $donHangSoLuong = (int)($khInfo['don_hang_so_luong'] ?? 0);
        
        // Nếu không có product_id từ join, thử tìm từ tên sản phẩm
        if (empty($productId) && !empty($productName)) {
            $sqlFindProduct = "SELECT id FROM products WHERE name = ? LIMIT 1";
            $stmtFind = $this->conn->prepare($sqlFindProduct);
            $stmtFind->bind_param("s", $productName);
            $stmtFind->execute();
            $productResult = $stmtFind->get_result();
            if ($productRow = $productResult->fetch_assoc()) {
                $productId = $productRow['id'];
            }
            $stmtFind->close();
        }

        // Kiểm tra xem có sản phẩm không
        if (empty($productId) || empty($productName)) {
            error_log("Kế hoạch {$keHoachId} không có sản phẩm - don_hang_id: " . ($khInfo['don_hang_id'] ?? 'NULL'));
            return ['error' => 'Kế hoạch này chưa có sản phẩm được gán. Vui lòng kiểm tra lại đơn hàng (ID: ' . ($khInfo['don_hang_id'] ?? 'N/A') . ').'];
        }

        // 2. Tính tổng số lượng sản phẩm cần sản xuất
        // Có thể dùng: số lượng đơn hàng HOẶC (sản lượng/ngày × số ngày)
        $sanLuongNgay = (int)($khInfo['san_luong_ngay'] ?? 0);
        
        // Tính số ngày làm việc
        $ngayBatDau = new DateTime($khInfo['ngay_bat_dau']);
        $ngayKetThuc = new DateTime($khInfo['ngay_ket_thuc']);
        $soNgay = max(1, $ngayKetThuc->diff($ngayBatDau)->days + 1);
        $tongSanLuongTheoKH = $sanLuongNgay * $soNgay;
        
        // Ưu tiên dùng số lượng đơn hàng, nếu không có thì dùng tính toán từ kế hoạch
        $tongSoLuongSP = $donHangSoLuong > 0 ? $donHangSoLuong : $tongSanLuongTheoKH;

        // 3. Lấy định mức nguyên liệu cho sản phẩm đó
        $sqlBOM = "SELECT nl.id, nl.name, nl.unit, nl.ton_kho, dm.so_luong_can
                   FROM dinh_muc_san_pham dm
                   INNER JOIN nguyen_lieu nl ON dm.nguyen_lieu_id = nl.id
                   WHERE dm.product_id = ?";
        
        $stmtBOM = $this->conn->prepare($sqlBOM);
        $stmtBOM->bind_param("i", $productId);
        $stmtBOM->execute();
        $result = $stmtBOM->get_result();
        
        $materials = [];
        while ($row = $result->fetch_assoc()) {
            // Tính toán: Nhu cầu = Định mức × Tổng số lượng sản phẩm
            $soLuongCanThiet = (float)$row['so_luong_can'] * $tongSoLuongSP;
            $row['so_luong_can_thiet'] = round($soLuongCanThiet, 2);
            $materials[] = $row;
        }
        $stmtBOM->close();
        
        // Nếu không có định mức, trả về thông báo lỗi rõ ràng
        if (empty($materials)) {
            error_log("Sản phẩm {$productName} (ID: {$productId}) không có định mức nguyên liệu");
            return [
                'error' => "Sản phẩm '{$productName}' chưa có định mức nguyên liệu. Vui lòng liên hệ Quản đốc để thiết lập định mức.",
                'product_id' => $productId,
                'product_name' => $productName
            ];
        }
        
        // Debug log
        error_log("getEstimatedMaterials - KH ID: {$keHoachId}, Product: {$productName} (ID: {$productId}), Tong SL: {$tongSoLuongSP}, Found " . count($materials) . " materials");
        
        return $materials;
    }

    // Lưu phiếu yêu cầu
    public function createRequest($maPhieu, $keHoachId, $userId, $ghiChu, $details) {
        // 1. Insert Master
        $sql = "INSERT INTO phieu_yeu_cau_nguyen_lieu (ma_phieu, ke_hoach_id, nguoi_lap_id, ghi_chu) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siis", $maPhieu, $keHoachId, $userId, $ghiChu);
        
        if ($stmt->execute()) {
            $phieuId = $stmt->insert_id;
            
            // 2. Insert Details
            $sqlDetail = "INSERT INTO ct_yeu_cau_nguyen_lieu (phieu_id, nguyen_lieu_id, so_luong_yeu_cau) VALUES (?, ?, ?)";
            $stmtDetail = $this->conn->prepare($sqlDetail);
            
            foreach ($details as $nlId => $qty) {
                if ($qty > 0) {
                    $stmtDetail->bind_param("iid", $phieuId, $nlId, $qty);
                    $stmtDetail->execute();
                }
            }
            return true;
        }
        return false;
    }
    
    public function generateMaPhieu() {
        return "YC" . date("YmdHis");
    }
}
?>