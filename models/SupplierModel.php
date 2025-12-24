<?php
class SupplierModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ================= SUPPLIER =================
    public function getAll(): array {
        $data = [];
        $rs = $this->conn->query("SELECT id, name FROM suppliers ORDER BY name");
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // HÀM CHÍNH
    public function create($name, $phone, $address) {
        $stmt = $this->conn->prepare(
            "INSERT INTO suppliers (name, phone, address) VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("sss", $name, $phone, $address);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return $stmt->insert_id;
    }

    // 🔥 ALIAS QUAN TRỌNG – SỬA LỖI KHÔNG THÊM ĐƯỢC
    public function insert($name, $phone, $address) {
        return $this->create($name, $phone, $address);
    }

    // ================= ĐƠN HÀNG =================
    public function getDonChoDuyet() {
        $sql = "SELECT dh.id, dh.ma_don_hang, dh.ngay_lap, dh.tong_tien, dh.trang_thai, 
                       s.name AS ten_ncc, u.username AS nguoi_lap
                FROM don_hang_mua dh
                LEFT JOIN suppliers s ON dh.supplier_id = s.id
                LEFT JOIN users u ON dh.user_id = u.id
                WHERE dh.trang_thai = 'ChoDuyet'
                ORDER BY dh.ngay_lap DESC";
        $rs = $this->conn->query($sql);
        return $rs ? $rs->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getDonById($id) {
        $sql = "SELECT dh.*, s.name AS ten_ncc, s.phone AS sdt_ncc,
                       s.address AS dia_chi_ncc, u.username AS nguoi_lap
                FROM don_hang_mua dh
                LEFT JOIN suppliers s ON dh.supplier_id = s.id
                LEFT JOIN users u ON dh.user_id = u.id
                WHERE dh.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getChiTietDon($donId) {
        $sql = "SELECT ten_san_pham, so_luong, don_gia, thanh_tien
                FROM chi_tiet_don_hang_mua
                WHERE don_hang_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $donId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function pheDuyetDon($id) {
        $stmt = $this->conn->prepare(
            "UPDATE don_hang_mua SET trang_thai = 'DaDuyet' WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function tuChoiDon($id, $lyDo) {
        $stmt = $this->conn->prepare(
            "UPDATE don_hang_mua 
             SET trang_thai = 'TuChoi', ly_do_tu_choi = ?
             WHERE id = ?"
        );
        $stmt->bind_param("si", $lyDo, $id);
        return $stmt->execute();
    }
}
