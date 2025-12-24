<?php
// FILE: view/thukho_list_po.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Kiểm tra quyền
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'thukho') {
    // header("Location: ../login.php"); // Uncomment khi chạy thật
}

$ds_don = $ds_don ?? []; // Dữ liệu từ Controller truyền sang
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Thủ Kho');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh Sách Đơn Hàng NCC</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    /* === FONT & RESET === */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body{margin:0;font-family:'Inter',sans-serif;background:#f4f6f8}
    * { box-sizing: border-box; text-decoration: none; outline: none; }
    
    .wrapper{display:flex;height:100vh; overflow: hidden;}

    /* ====================================================== */
    /* === PHẦN 1: SIDEBAR HIỆN ĐẠI (DARK SLATE) === */
    /* ====================================================== */
    .sidebar {
        width: 260px;
        background: #0f172a; /* Màu tối hiện đại */
        color: #94a3b8;
        display: flex; flex-direction: column; 
        transition: all 0.3s ease;
        flex-shrink: 0; overflow-y: auto; z-index: 100;
        border-right: 1px solid #1e293b;
    }
    .sidebar.collapsed { width: 80px; }
    .sidebar.collapsed .text-hide { display: none !important; }
    .sidebar.collapsed .sidebar-brand span { display: none; }
    .sidebar.collapsed .nav-link { justify-content: center; padding: 12px 0; margin: 5px 10px; }
    .sidebar.collapsed .nav-link i { margin-right: 0; font-size: 1.4rem; }
    .sidebar.collapsed .user-panel { padding: 10px 5px; }
    
    .sidebar-brand { 
        padding: 24px 20px; font-size: 1.25rem; font-weight: 800; color: #fff;
        text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #1e293b;
        display: flex; align-items: center; gap: 10px;
    }
    .sidebar-brand i { color: #38bdf8; }

    .user-panel { padding: 20px; text-align: center; border-bottom: 1px solid #1e293b; margin-bottom: 10px; }
    .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #1e293b; margin-bottom: 8px; }
    .user-name { font-weight: 600; color: #fff; font-size: 0.95rem; }
    
    .nav-section { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 15px 20px 5px; letter-spacing: 0.5px; }

    .nav-link { 
        display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px;
        border-radius: 8px; color: #94a3b8; font-weight: 500; transition: all 0.2s ease-in-out;
    }
    .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
    
    /* Active State */
    .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
    
    .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
    .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }

    /* === MAIN CONTENT === */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
    .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 70px; }
    .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; margin-right: 15px; }
    .page-title { margin: 0; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
    .content-body { flex: 1; overflow-y: auto; padding: 30px; }

    /* === TABLE STYLE === */
    .card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #f8fafc; padding: 15px; text-align: left; font-weight: 700; color: #64748b; font-size: 0.85rem; border-bottom: 2px solid #e2e8f0; }
    td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    tr:hover { background-color: #f8fafc; }
    
    .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .badge-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
</style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-cut"></i> <span class="text-hide">AppMasters</span></div>
        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size:0.75rem; color:#94a3b8">Thủ Kho</div>
        </div>
        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../controllers/ThuKhoController.php" class="nav-link <?php echo (!isset($_GET['action'])) ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> <span class="text-hide">Dashboard</span>
            </a>
            
            <div class="nav-section text-hide">Quản Lý Kho</div>
            <a href="../controllers/NhapNguyenLieuController.php" class="nav-link">
                <i class="fas fa-dolly-flatbed"></i> <span class="text-hide">Nhập Nguyên Liệu</span>
            </a>
            <a href="../controllers/XuatNguyenLieuController.php" class="nav-link">
                <i class="fas fa-truck-loading"></i> <span class="text-hide">Xuất Nguyên Liệu</span>
            </a>

            <a href="../controllers/NhapThanhPhamController.php" class="nav-link">
                <i class="fas fa-box-open"></i> <span class="text-hide">Nhập Thành Phẩm</span>
            </a>
            <a href="../controllers/XuatThanhPhamController.php" class="nav-link">
                <i class="fas fa-shipping-fast"></i> <span class="text-hide">Xuất Thành Phẩm</span>
            </a>

            <a href="../controllers/ThuKhoController.php?action=xem_don_ncc" class="nav-link <?php echo (isset($_GET['action']) && $_GET['action'] == 'xem_don_ncc') ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice"></i> <span class="text-hide">Đơn Hàng NCC</span>
            </a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex;align-items:center">
                <div class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
                <h2 class="page-title">Danh Sách Đơn Đặt Hàng (PO)</h2>
            </div>
            <div style="color:#64748b"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            <div class="card">
                <h3 style="margin-top:0;color:#0f172a;border-bottom:1px solid #f1f5f9;padding-bottom:15px;margin-bottom:20px">
                    📦 Các đơn hàng sắp về kho
                </h3>
                
                <table style="width:100%">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Nhà Cung Cấp</th>
                            <th>Ngày Lập</th>
                            <th>Dự Kiến Nhập</th>
                            <th>Tổng Tiền</th>
                            <th>Người Lập</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($ds_don)): ?>
                            <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8">Chưa có đơn đặt hàng nào.</td></tr>
                        <?php else: foreach($ds_don as $d): ?>
                            <tr>
                                <td style="font-weight:700;color:#3b82f6"><?= $d['ma_don_hang'] ?></td>
                                <td><?= htmlspecialchars($d['ten_ncc']) ?></td>
                                <td><?= date('d/m/Y', strtotime($d['ngay_lap'])) ?></td>
                                <td style="color:#ef4444;font-weight:600"><?= date('d/m/Y', strtotime($d['ngay_nhan_du_kien'])) ?></td>
                                <td><?= number_format($d['tong_tien']) ?> ₫</td>
                                <td><?= htmlspecialchars($d['username']) ?></td>
                                <td>
                                    <?php if($d['trang_thai'] == 'ChoDuyet'): ?>
                                        <span class="badge badge-pending">Chờ Duyệt</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?= $d['trang_thai'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>

</body>
</html>