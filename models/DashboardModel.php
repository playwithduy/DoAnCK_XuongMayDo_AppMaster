<?php
class DashboardModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /* ===== TỔNG ===== */

    public function tongNguyenLieu() {
        return $this->conn
            ->query("SELECT IFNULL(SUM(ton_kho),0) FROM nguyen_lieu")
            ->fetch_row()[0];
    }

    public function tongThanhPham() {
        $tongNhap = $this->conn
            ->query("SELECT IFNULL(SUM(so_luong),0) FROM ct_nhap_thanh_pham")
            ->fetch_row()[0];

        $tongXuat = $this->conn
            ->query("SELECT IFNULL(SUM(so_luong),0) FROM ct_xuat_thanh_pham")
            ->fetch_row()[0];

        return $tongNhap - $tongXuat;
    }

    public function tongDoanhThu() {
        return $this->conn
            ->query("SELECT IFNULL(SUM(thanh_tien),0) FROM ct_xuat_thanh_pham")
            ->fetch_row()[0];
    }

    public function phieuNhapHomNay() {
        return $this->conn
            ->query("SELECT COUNT(*) FROM phieu_nhap_thanh_pham WHERE DATE(ngay_nhap)=CURDATE()")
            ->fetch_row()[0];
    }

    public function phieuXuatHomNay() {
        return $this->conn
            ->query("SELECT COUNT(*) FROM phieu_xuat_thanh_pham WHERE DATE(ngay_xuat)=CURDATE()")
            ->fetch_row()[0];
    }

    /* ===== BẢNG NHẬP ===== */

    public function chiTietNhapTP() {
        return $this->conn->query("
            SELECT 
                p.ngay_nhap,
                t.name AS ten_tp,
                c.so_luong,
                p.xuong,
                p.qc_ket_qua,
                u.username
            FROM phieu_nhap_thanh_pham p
            JOIN ct_nhap_thanh_pham c ON p.id = c.phieu_nhap_id
            JOIN thanh_pham t ON c.product_id = t.id
            JOIN users u ON p.user_id = u.id
            ORDER BY p.ngay_nhap DESC
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC);
    }

    /* ===== BẢNG XUẤT (CÓ ĐƠN GIÁ + THÀNH TIỀN) ===== */

    public function chiTietXuatTP() {
        return $this->conn->query("
            SELECT 
                x.ngay_xuat,
                tp.name AS ten_tp,
                ct.so_luong,
                ct.don_gia,
                ct.thanh_tien,
                u.username
            FROM phieu_xuat_thanh_pham x
            JOIN ct_xuat_thanh_pham ct ON x.id = ct.phieu_xuat_id
            JOIN thanh_pham tp ON ct.product_id = tp.id
            JOIN users u ON x.user_id = u.id
            ORDER BY x.ngay_xuat DESC
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC);
    }

    /* ===== BIỂU ĐỒ ===== */

    public function thongKeXuat7Ngay() {
        return $this->conn->query("
            SELECT 
                DATE(x.ngay_xuat) ngay,
                SUM(ct.so_luong) tong_sl,
                SUM(ct.thanh_tien) doanh_thu
            FROM phieu_xuat_thanh_pham x
            JOIN ct_xuat_thanh_pham ct ON x.id = ct.phieu_xuat_id
            WHERE x.ngay_xuat >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(x.ngay_xuat)
            ORDER BY ngay
        ")->fetch_all(MYSQLI_ASSOC);
    }

    public function thongKeXuat30Ngay() {
        return $this->conn->query("
            SELECT 
                DATE(x.ngay_xuat) ngay,
                SUM(ct.so_luong) tong_sl,
                SUM(ct.thanh_tien) doanh_thu
            FROM phieu_xuat_thanh_pham x
            JOIN ct_xuat_thanh_pham ct ON x.id = ct.phieu_xuat_id
            WHERE x.ngay_xuat >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
            GROUP BY DATE(x.ngay_xuat)
            ORDER BY ngay
        ")->fetch_all(MYSQLI_ASSOC);
    }

    public function thongKeTonKho() {
        return $this->conn->query("
            SELECT 
                t.name,
                IFNULL(SUM(n.so_luong),0) -
                IFNULL((
                    SELECT SUM(x.so_luong)
                    FROM ct_xuat_thanh_pham x
                    WHERE x.product_id = t.id
                ),0) AS ton_kho
            FROM thanh_pham t
            LEFT JOIN ct_nhap_thanh_pham n ON t.id = n.product_id
            GROUP BY t.id
            HAVING ton_kho > 0
        ")->fetch_all(MYSQLI_ASSOC);
    }
}
