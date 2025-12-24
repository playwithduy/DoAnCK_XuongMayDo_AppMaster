<?php
class ThongKeModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // 1. Tồn kho thành phẩm
    public function getTonKhoThanhPham() {
        $sql = "SELECT id, name, ton_kho, price, unit FROM products ORDER BY ton_kho ASC";
        $rs = $this->conn->query($sql);
        $data = [];
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $row['gia_tri_ton'] = $row['ton_kho'] * $row['price'];
                $data[] = $row;
            }
        }
        return $data;
    }

    // 2. Tồn kho nguyên liệu
    public function getTonKhoNguyenLieu() {
        $sql = "SELECT id, name, ton_kho, unit FROM nguyen_lieu ORDER BY ton_kho ASC";
        $rs = $this->conn->query($sql);
        $data = [];
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // 3. Lịch sử Xuất - Nhập (Gộp 3 bảng: Nhập NL, Nhập TP, Xuất TP)
    public function getLichSuXuatNhap() {
        $sql = "
            (SELECT 'Nhập Nguyên Liệu' as loai, ngay_nhap as ngay, id as ma_phieu, note as ghi_chu 
             FROM phieu_nhap_nguyen_lieu)
            UNION ALL
            (SELECT 'Nhập Thành Phẩm' as loai, ngay_nhap as ngay, id as ma_phieu, note as ghi_chu 
             FROM phieu_nhap_thanh_pham)
            UNION ALL
            (SELECT 'Xuất Thành Phẩm' as loai, ngay_xuat as ngay, id as ma_phieu, ly_do as ghi_chu 
             FROM phieu_xuat_thanh_pham)
            ORDER BY ngay DESC LIMIT 50
        ";
        $rs = $this->conn->query($sql);
        return $rs ? $rs->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 4. Tổng quan (Dashboard mini)
    public function getTongQuanKho() {
        $tongTP = $this->conn->query("SELECT SUM(ton_kho) as t FROM products")->fetch_assoc()['t'] ?? 0;
        $tongNL = $this->conn->query("SELECT SUM(ton_kho) as t FROM nguyen_lieu")->fetch_assoc()['t'] ?? 0;
        
        // Giá trị tồn kho ước tính
        $valTP = $this->conn->query("SELECT SUM(ton_kho * price) as t FROM products")->fetch_assoc()['t'] ?? 0;

        return [
            'tong_tp' => $tongTP,
            'tong_nl' => $tongNL,
            'gia_tri_tp' => $valTP
        ];
    }
}
?>