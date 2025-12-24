<?php
class LichLamViecModel {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }

    // Lấy lịch làm việc trong khoảng thời gian (Từ ngày A đến ngày B)
    public function getLichLam($user_id, $start_date, $end_date) {
        $data = [];
        $sql = "SELECT * FROM lich_lam_viec 
                WHERE user_id = $user_id 
                AND ngay_lam BETWEEN '$start_date' AND '$end_date' 
                ORDER BY ngay_lam ASC, ca_lam ASC";
        
        $rs = $this->conn->query($sql);
        if($rs) {
            while($row = $rs->fetch_assoc()) {
                // Gom nhóm dữ liệu theo ngày để dễ hiển thị trên lịch tháng
                $data[$row['ngay_lam']][] = $row;
            }
        }
        return $data;
    }
}
?>