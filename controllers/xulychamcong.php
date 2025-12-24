<?php
// FILE: controllers/xulychamcong.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/database.php';

// 1. AUTHENTICATION
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'xuongtruong') {
    header("Location: ../login.php"); 
    exit;
}

$user = $_SESSION['user'];
$xuongTruongId = $user['id'];

// 2. XỬ LÝ POST (Lưu chấm công)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idKeHoach    = (int)($_POST['id_ke_hoach'] ?? 0);
    $idCongDoan   = (int)($_POST['id_cong_doan'] ?? 0);
    $idCa         = (int)($_POST['id_ca'] ?? 0);
    $ngayChamCong = $_POST['ngay_cham_cong'] ?? date('Y-m-d');
    $sanLuongArr  = $_POST['san_luong'] ?? [];
    $ghiChuArr    = $_POST['ghi_chu'] ?? [];

    if ($idKeHoach > 0 && $idCongDoan > 0 && $idCa > 0) {
        // Xóa cũ để tránh trùng lặp
        $stmtDel = $conn->prepare("DELETE FROM cham_cong WHERE id_ke_hoach = ? AND id_cong_doan = ? AND id_ca = ? AND ngay_cham_cong = ?");
        $stmtDel->bind_param("iiis", $idKeHoach, $idCongDoan, $idCa, $ngayChamCong);
        $stmtDel->execute();
        $stmtDel->close();

        // Thêm mới
        $stmtInsert = $conn->prepare("INSERT INTO cham_cong (id_ke_hoach, id_cong_doan, id_cong_nhan, id_ca, ngay_cham_cong, san_luong, ghi_chu) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $count = 0;
        foreach ($sanLuongArr as $idCongNhan => $sanLuong) {
            $sanLuong = (int)$sanLuong;
            $ghiChu = trim($ghiChuArr[$idCongNhan] ?? '');
            
            // Lưu nếu có sản lượng hoặc ghi chú
            if ($sanLuong > 0 || !empty($ghiChu)) {
                $idCongNhan = (int)$idCongNhan;
                $stmtInsert->bind_param("iiiiiss", $idKeHoach, $idCongDoan, $idCongNhan, $idCa, $ngayChamCong, $sanLuong, $ghiChu);
                if ($stmtInsert->execute()) $count++;
            }
        }
        $stmtInsert->close();

        // Cập nhật tổng tiến độ công đoạn
        $stmtSum = $conn->prepare("SELECT SUM(san_luong) as total FROM cham_cong WHERE id_cong_doan = ?");
        $stmtSum->bind_param("i", $idCongDoan);
        $stmtSum->execute();
        $res = $stmtSum->get_result()->fetch_assoc();
        $total = $res['total'] ?? 0;
        
        // Update trạng thái công đoạn
        $stmtUpdate = $conn->prepare("UPDATE cong_doan SET da_san_xuat = ?, trang_thai = IF(da_san_xuat >= chi_tieu, 'hoan_thanh', 'dang_thuc_hien') WHERE id = ?");
        $stmtUpdate->bind_param("ii", $total, $idCongDoan);
        $stmtUpdate->execute();

        $_SESSION['msg'] = "✅ Đã lưu chấm công cho $count công nhân!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "❌ Thiếu thông tin bắt buộc!";
        $_SESSION['msg_type'] = "error";
    }

    header("Location: xulychamcong.php?id_ke_hoach=$idKeHoach&id_cong_doan=$idCongDoan&id_ca=$idCa&ngay_cham_cong=$ngayChamCong");
    exit;
}

// 3. LOGIC LẤY DỮ LIỆU HIỂN THỊ

// --- Lấy danh sách KẾ HOẠCH (đã phân bổ cho Xưởng trưởng này) ---
// Join với lenhsanxuat để check quyền
$sqlKH = "SELECT k.id, k.ma_ke_hoach, 
                 lsx.ma_lenh,
                 dh.so_don_hang,
                 ct.ten_san_pham,
                 ct.size
          FROM kehoachsanxuat k
          JOIN lenhsanxuat lsx ON k.id = lsx.ke_hoach_id
          JOIN don_hang_ban dh ON k.don_hang_id = dh.id
          JOIN chi_tiet_don_hang_ban ct ON dh.id = ct.don_hang_id
          WHERE lsx.xuong_truong_id = ? 
          AND k.trang_thai IN ('Đã phân bổ', 'dang_thuc_hien', 'Đã duyệt')
          GROUP BY k.id
          ORDER BY k.ngay_bat_dau DESC";

$stmtKH = $conn->prepare($sqlKH);
$stmtKH->bind_param("i", $xuongTruongId);
$stmtKH->execute();
$resKH = $stmtKH->get_result();
$danhSachKeHoach = [];
while ($row = $resKH->fetch_assoc()) {
    $danhSachKeHoach[] = $row;
}
$stmtKH->close();

// Lấy danh sách Ca
$caResult = $conn->query("SELECT * FROM ca_lam_viec ORDER BY gio_bat_dau");
$caList = [];
while ($row = $caResult->fetch_assoc()) {
    $caList[$row['id']] = $row;
}

// Tham số filter
$selectedKeHoach  = (int)($_GET['id_ke_hoach'] ?? 0);
$selectedCongDoan = (int)($_GET['id_cong_doan'] ?? 0);
$selectedCa       = (int)($_GET['id_ca'] ?? 0);
$selectedDate     = $_GET['ngay_cham_cong'] ?? date('Y-m-d');

// --- Lấy danh sách CÔNG ĐOẠN ---
$congDoanList = [];
if ($selectedKeHoach > 0) {
    $stmtCD = $conn->prepare("SELECT * FROM cong_doan WHERE id_ke_hoach = ? ORDER BY thu_tu ASC");
    $stmtCD->bind_param("i", $selectedKeHoach);
    $stmtCD->execute();
    $resCD = $stmtCD->get_result();
    while ($row = $resCD->fetch_assoc()) {
        $congDoanList[] = $row;
    }
    $stmtCD->close();
}

// Công đoạn hiện tại
$currentStage = null;
if ($selectedCongDoan > 0) {
    foreach ($congDoanList as $cd) {
        if ($cd['id'] == $selectedCongDoan) {
            $currentStage = $cd;
            break;
        }
    }
}

// --- Lấy danh sách CÔNG NHÂN ---
// Logic: Lấy tất cả công nhân đang active (có thể mở rộng để lọc theo tổ nếu cần)
$sqlWorker = "SELECT id, username as ho_ten FROM users WHERE role = 'congnhan' AND status = 'active' ORDER BY username";
$resWorker = $conn->query($sqlWorker);
$allWorkers = [];
if ($resWorker) {
    while ($row = $resWorker->fetch_assoc()) {
        $allWorkers[] = $row;
    }
}

// --- Lấy dữ liệu ĐÃ CHẤM ---
$existingData = [];
if ($selectedKeHoach > 0 && $selectedCongDoan > 0 && $selectedCa > 0) {
    $stmtEx = $conn->prepare("SELECT id_cong_nhan, san_luong, ghi_chu FROM cham_cong WHERE id_ke_hoach=? AND id_cong_doan=? AND id_ca=? AND ngay_cham_cong=?");
    $stmtEx->bind_param("iiis", $selectedKeHoach, $selectedCongDoan, $selectedCa, $selectedDate);
    $stmtEx->execute();
    $resEx = $stmtEx->get_result();
    while ($row = $resEx->fetch_assoc()) {
        $existingData[$row['id_cong_nhan']] = $row;
    }
    $stmtEx->close();
}

// Message
$msg = $_SESSION['msg'] ?? '';
$msgType = $_SESSION['msg_type'] ?? '';
unset($_SESSION['msg'], $_SESSION['msg_type']);

// Data pack
$data = [
    'user'             => $user,
    'danhSachKeHoach'  => $danhSachKeHoach,
    'caList'           => $caList,
    'congDoanList'     => $congDoanList,
    'currentStage'     => $currentStage,
    'allWorkers'       => $allWorkers,
    'existingData'     => $existingData,
    'selectedKeHoach'  => $selectedKeHoach,
    'selectedCongDoan' => $selectedCongDoan,
    'selectedCa'       => $selectedCa,
    'selectedDate'     => $selectedDate,
    'msg'              => $msg,
    'msgType'          => $msgType
];

require __DIR__ . '/../view/cham_cong.php';
?>