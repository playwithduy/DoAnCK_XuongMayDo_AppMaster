<?php
// FILE: controllers/xulylogin.php
session_start();
require_once __DIR__ . '/../config/database.php'; 

// 1. Lấy dữ liệu
$username = isset($_POST['taikhoan']) ? trim($_POST['taikhoan']) : ''; 
$password = isset($_POST['matkhau']) ? trim($_POST['matkhau']) : '';   

// 2. Kiểm tra rỗng
if ($username === '' || $password === '') {
    header("Location: ../login.php?error=empty");
    exit;
}

try {
    // 3. Truy vấn User (Dùng MySQLi cho đồng bộ project)
    $sql = "SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    
    $md5Pass = md5($password); 
    $stmt->bind_param("ss", $username, $md5Pass);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Kiểm tra khóa
        if (isset($user['status']) && $user['status'] == 'inactive') {
            header("Location: ../login.php?error=locked");
            exit;
        }

        // 4. Lưu Session (Quan trọng: Lưu đúng cấu trúc mảng để GiamDocController đọc được)
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'full_name'=> $user['full_name'] ?? $user['username'],
            'role'     => $user['role']
        ];

        // 5. Chuyển hướng theo Role
        switch ($user['role']) {
            case 'giamdoc':
                // QUAN TRỌNG: Phải vào Controller, KHÔNG vào View
                header("Location: GiamDocController.php"); 
                break;
                
            case 'thukho':
                header("Location: ThuKhoController.php");
                break;
                
            case 'quandoc':
                header("Location: QuanDocController.php");
                break;
                
            case 'nhanvienqa':
                header("Location: QAController.php");
                break;

            case 'kinhdoanh':
            case 'sale':
                header("Location: BanHangController.php");
                break;
            
            case 'xuongtruong':
                header("Location: XuongTruongDashboardController.php");
                break;

            case 'congnhan':
                header("Location: ../view/congnhan.php");
                break;
                
            case 'khachhang':
            default:
                header("Location: ../index.php");
                break;
        }
        exit;
    } else {
        // Sai tài khoản hoặc mật khẩu
        header("Location: ../login.php?error=invalid");
        exit;
    }
} catch (Exception $e) {
    echo "Lỗi hệ thống: " . $e->getMessage();
    exit;
}
?>