<?php
class NhapNguyenLieuModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getHistory(): array {
        $data = [];

        $sql = "
            SELECT p.ngay_nhap,
                   s.name AS supplier,
                   n.name,
                   n.loai,
                   c.so_luong,
                   c.don_gia,
                   c.ghi_chu,
                   u.username
            FROM ct_nhap_nguyen_lieu c
            JOIN phieu_nhap_nguyen_lieu p ON c.phieu_nhap_id = p.id
            JOIN suppliers s ON p.supplier_id = s.id
            JOIN nguyen_lieu n ON c.nguyen_lieu_id = n.id
            JOIN users u ON p.user_id = u.id
            ORDER BY p.id DESC
            LIMIT 6
        ";

        $rs = $this->conn->query($sql);
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data; // ✅ LUÔN LÀ MẢNG
    }

    public function insertPhieuNhap(
    $supplier_id,
    $ten_nl,
    $loai_nl,
    $so_luong,
    $don_gia,
    $note,
    $user_id
) {
    // 1️⃣ thêm phiếu nhập
    $this->conn->query("
        INSERT INTO phieu_nhap_nguyen_lieu (supplier_id, user_id)
        VALUES ($supplier_id, $user_id)
    ");

    $phieu_id = $this->conn->insert_id;

    // 2️⃣ thêm nguyên liệu nếu chưa có
    $rs = $this->conn->query("
        SELECT id FROM nguyen_lieu 
        WHERE name='$ten_nl' AND loai='$loai_nl'
    ");

    if ($rs->num_rows > 0) {
        $nl = $rs->fetch_assoc();
        $nguyen_lieu_id = $nl['id'];

        $this->conn->query("
            UPDATE nguyen_lieu 
            SET ton_kho = ton_kho + $so_luong
            WHERE id = $nguyen_lieu_id
        ");
    } else {
        $this->conn->query("
            INSERT INTO nguyen_lieu (name, loai, ton_kho)
            VALUES ('$ten_nl','$loai_nl',$so_luong)
        ");
        $nguyen_lieu_id = $this->conn->insert_id;
    }

    // 3️⃣ chi tiết phiếu
    $this->conn->query("
        INSERT INTO ct_nhap_nguyen_lieu
        (phieu_nhap_id, nguyen_lieu_id, so_luong, don_gia, ghi_chu)
        VALUES
        ($phieu_id, $nguyen_lieu_id, $so_luong, $don_gia, '$note')
    ");
}

}
