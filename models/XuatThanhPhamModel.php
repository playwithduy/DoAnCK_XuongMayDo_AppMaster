<?php
class XuatThanhPhamModel {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    // Lấy danh sách thành phẩm còn tồn kho thực tế
    public function getProducts(){
        $data = [];
        $sql = "
            SELECT t.id, t.name,
                   IFNULL(SUM(c.so_luong),0) - IFNULL((
                       SELECT SUM(cx.so_luong)
                       FROM ct_xuat_thanh_pham cx
                       WHERE cx.product_id = t.id
                   ),0) AS ton_kho
            FROM thanh_pham t
            JOIN ct_nhap_thanh_pham c ON t.id = c.product_id
            GROUP BY t.id, t.name
            HAVING ton_kho > 0
        ";
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_assoc()){
            $data[] = $r;
        }
        return $data;
    }

    // Lấy lịch sử phiếu xuất (mới nhất 10)
    public function getHistory(){
    $data = [];
    $sql = "
        SELECT x.id, x.ngay_xuat,
               t.name AS ten_tp,
               c.so_luong,
               c.don_gia,
               c.thanh_tien,
               x.ly_do,
               u.username
        FROM phieu_xuat_thanh_pham x
        JOIN ct_xuat_thanh_pham c ON x.id = c.phieu_xuat_id
        JOIN thanh_pham t ON c.product_id = t.id
        JOIN users u ON x.user_id = u.id
        ORDER BY x.ngay_xuat DESC
        LIMIT 10
    ";
    $rs = $this->conn->query($sql);
    while($r = $rs->fetch_assoc()){
        $data[] = $r;
    }
    return $data;
}

    // Lưu phiếu xuất
    public function createXuat($user_id, $product_id, $so_luong, $don_gia, $ly_do){
    // 1. Phiếu xuất
    $stmt = $this->conn->prepare(
        "INSERT INTO phieu_xuat_thanh_pham(user_id, ngay_xuat, ly_do)
         VALUES (?, NOW(), ?)"
    );
    $stmt->bind_param("is", $user_id, $ly_do);
    $stmt->execute();
    $phieu_xuat_id = $stmt->insert_id;
    $stmt->close();

    // 2. Chi tiết
    $thanh_tien = $so_luong * $don_gia;
    $stmt = $this->conn->prepare(
        "INSERT INTO ct_xuat_thanh_pham
        (phieu_xuat_id, product_id, so_luong, don_gia, thanh_tien)
        VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iiidd",
        $phieu_xuat_id,
        $product_id,
        $so_luong,
        $don_gia,
        $thanh_tien
    );
    $stmt->execute();
    $stmt->close();

    return $phieu_xuat_id;
}

}
