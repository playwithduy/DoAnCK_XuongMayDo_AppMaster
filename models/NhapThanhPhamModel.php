<?php
class NhapThanhPhamModel {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    /* ================= LỊCH SỬ ================= */
    public function getHistory(){
        $data = [];

        $sql = "
            SELECT p.id,
                   p.ngay_nhap,
                   t.name AS ten_tp,
                   p.xuong,
                   p.so_luong,
                   p.note,
                   p.qc_ket_qua,
                   u.username
            FROM phieu_nhap_thanh_pham p
            LEFT JOIN ct_nhap_thanh_pham c ON p.id = c.phieu_nhap_id
            LEFT JOIN thanh_pham t ON c.product_id = t.id
            JOIN users u ON p.user_id = u.id
            ORDER BY p.id DESC
            LIMIT 10
        ";

        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_assoc()){
            $data[] = $row;
        }
        return $data;
    }

    /* ================= THÊM PHIẾU ================= */
    public function insertPhieu(
        $ten_tp,
        $xuong,
        $so_luong,
        $note,
        $qc,
        $user_id,
        $username
    ){
        /* ====== CHUẨN HÓA ====== */
        $ten_tp = trim($ten_tp);
        $note   = $note ?? '';
        $qc     = $qc ?? 'dat';

        /* ====== QC KHÔNG ĐẠT → CHỈ LƯU PHIẾU ====== */
        if($qc === 'khong_dat'){
            $this->conn->query("
                INSERT INTO phieu_nhap_thanh_pham
                (user_id, xuong, so_luong, qc_ket_qua, nguoi_nhap, note)
                VALUES
                ($user_id, '$xuong', $so_luong, 'khong_dat', '$username', '$note')
            ");
            return; // ❌ không tạo thành phẩm, không chi tiết
        }

        /* ====== 1️⃣ TẠO PHIẾU ====== */
        $this->conn->query("
            INSERT INTO phieu_nhap_thanh_pham
            (user_id, xuong, so_luong, qc_ket_qua, nguoi_nhap, note)
            VALUES
            ($user_id, '$xuong', $so_luong, 'dat', '$username', '$note')
        ");
        $phieu_id = $this->conn->insert_id;

        /* ====== 2️⃣ THÀNH PHẨM (TÊN) ====== */
        $rs = $this->conn->query("
            SELECT id FROM thanh_pham WHERE name = '$ten_tp'
        ");

        if($rs->num_rows > 0){
            // ĐÃ CÓ
            $tp = $rs->fetch_assoc();
            $tp_id = $tp['id'];

            $this->conn->query("
                UPDATE thanh_pham
                SET ton_kho = ton_kho + $so_luong
                WHERE id = $tp_id
            ");
        }else{
            // CHƯA CÓ → TẠO MỚI
            $this->conn->query("
                INSERT INTO thanh_pham (name, ton_kho)
                VALUES ('$ten_tp', $so_luong)
            ");
            $tp_id = $this->conn->insert_id;
        }

        /* ====== 3️⃣ CHI TIẾT PHIẾU ====== */
        $this->conn->query("
            INSERT INTO ct_nhap_thanh_pham
            (phieu_nhap_id, product_id, so_luong)
            VALUES
            ($phieu_id, $tp_id, $so_luong)
        ");
    }

    /* ================= IN PDF ================= */
    public function getPhieu($id){
        return $this->conn->query("
            SELECT p.*,
                   t.name AS ten_tp
            FROM phieu_nhap_thanh_pham p
            LEFT JOIN ct_nhap_thanh_pham c ON p.id = c.phieu_nhap_id
            LEFT JOIN thanh_pham t ON c.product_id = t.id
            WHERE p.id = $id
        ")->fetch_assoc();
    }
}
