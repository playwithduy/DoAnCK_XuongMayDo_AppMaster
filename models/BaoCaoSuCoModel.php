<?php
class BaoCaoSuCoModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    // Lấy lịch sử báo cáo của user đó
    public function getHistory($user_id) {
        $data = [];
        $sql = "SELECT * FROM baocaosuco WHERE user_id = $user_id ORDER BY ngay_tao DESC";
        $rs = $this->conn->query($sql);
        if($rs) while($r = $rs->fetch_assoc()) $data[] = $r;
        return $data;
    }

    // Gửi báo cáo mới
    public function createReport($user_id, $loai, $vitri, $mota, $hinhanh) {
        $stmt = $this->conn->prepare("INSERT INTO baocaosuco (user_id, loai_su_co, mo_ta, vi_tri, hinh_anh) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $loai, $mota, $vitri, $hinhanh);
        return $stmt->execute();
    }
}
?>