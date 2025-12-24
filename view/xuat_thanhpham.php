<?php
// FILE: view/xuat_thanhpham.php

if (!isset($data) || !is_array($data)) {
    header("Location: ../controllers/XuatThanhPhamController.php");
    exit;
}

$user     = $data['user'];
$products = $data['products'] ?? [];
$history  = $data['history'] ?? [];

// Tạo Avatar và Username
$username = $user['full_name'] ?? ($user['username'] ?? 'Thủ Kho');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xuất Kho Thành Phẩm</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    /* === FONT & RESET === */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
    * { box-sizing: border-box; outline: none; text-decoration: none; }
    
    .wrapper { display: flex; height: 100vh; overflow: hidden; }

    /* ====================================================== */
    /* === PHẦN 1: SIDEBAR HIỆN ĐẠI (DARK SLATE) === */
    /* ====================================================== */
    .sidebar {
        width: 260px;
        background: #0f172a; 
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

    /* === MAIN LAYOUT === */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
    
    .topbar { 
        background: #fff; padding: 15px 30px; 
        display: flex; justify-content: space-between; align-items: center; 
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); z-index: 10;
    }
    .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; padding: 5px; }
    .page-title { margin: 0 0 0 15px; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
    
    .content-body { flex: 1; overflow-y: auto; padding: 30px; }

    /* ====================================================== */
    /* === PHẦN 2: CSS NỘI DUNG (Content Style) === */
    /* ====================================================== */
    .grid { display: grid; grid-template-columns: 3fr 2fr; gap: 25px; }
    @media(max-width: 1024px) { .grid { grid-template-columns: 1fr; } }

    .card { background: #fff; border-radius: 14px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 20px; }
    .card h2, .card h3 { text-align: left; margin-top: 0; color: #334155; font-size: 1.1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px; }
    
    label { font-weight: 600; margin-top: 15px; display: block; color: #475569; font-size: 0.9rem; }
    input, select, textarea { width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; margin-top: 6px; font-family: inherit; font-size: 0.95rem; }
    input:focus, select:focus, textarea:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    
    /* Buttons */
    .btn { padding: 10px 16px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
    .save { background: #ef4444; color: #fff; width: 100%; margin-top: 25px; font-size: 1rem; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2); }
    .save:hover { background: #dc2626; transform: translateY(-2px); }

    /* Table */
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: center; font-size: 0.9rem; }
    th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; text-align: left; }
    td { text-align: left; color: #334155; }
    tr:hover { background: #f8fafc; }
</style>
</head>

<body>
<div class="wrapper">

    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cut"></i> <span class="text-hide">AppMasters</span>
        </div>

        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Thủ Kho</div>
        </div>

        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../controllers/ThuKhoController.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'ThuKhoController.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> <span class="text-hide">Dashboard</span>
            </a>
            
            <div class="nav-section text-hide">Quản Lý Kho</div>
            <a href="../controllers/NhapNguyenLieuController.php" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'NhapNguyenLieu') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-dolly-flatbed"></i> <span class="text-hide">Nhập Nguyên Liệu</span>
            </a>
            <a href="../controllers/XuatNguyenLieuController.php" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'XuatNguyenLieu') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-truck-loading"></i> <span class="text-hide">Xuất Nguyên Liệu</span>
            </a>

            <a href="../controllers/NhapThanhPhamController.php" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'NhapThanhPham') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-box-open"></i> <span class="text-hide">Nhập Thành Phẩm</span>
            </a>
            <a href="../controllers/XuatThanhPhamController.php" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'XuatThanhPham') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-shipping-fast"></i> <span class="text-hide">Xuất Thành Phẩm</span>
            </a>
            <a href="../controllers/ThuKhoController.php?action=xem_don_ncc" class="nav-link <?php echo (isset($_GET['action']) && $_GET['action'] == 'xem_don_ncc') ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice"></i> <span class="text-hide">Đơn Hàng NCC</span>
            </a>
    
        </nav>

        <a href="../controllers/LogoutController.php" class="nav-link logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span>
        </a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center;">
                <div class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
                <h2 class="page-title">Xuất Kho Thành Phẩm</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;">
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">
            <div class="grid">

                <div class="card">
                    <h2><i class="fas fa-file-export" style="color: #ef4444; margin-right: 10px;"></i> Phiếu Xuất Kho</h2>

                    <form method="post" id="xuatForm">
                        <label>Sản phẩm xuất kho</label>
                        <select name="product_id" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?> (Tồn: <?= $p['ton_kho'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label>Số lượng</label>
                        <input type="number" name="so_luong" min="1" required placeholder="Nhập số lượng...">

                        <label>Đơn giá xuất</label>
                        <input type="number" name="don_gia" min="1" required placeholder="Nhập giá xuất...">

                        <label>Lý do xuất kho</label>
                        <textarea name="ly_do" required placeholder="Ví dụ: Xuất bán cho đại lý A..." rows="3"></textarea>

                        <label>Thành tiền (Dự tính)</label>
                        <input type="text" id="thanh_tien" disabled style="background: #f1f5f9; font-weight: bold; color: #ef4444;">
                        
                        <input type="hidden" name="confirm_xuat" value="1">
                        <button type="submit" class="btn save">💾 Xác nhận xuất kho</button>
                    </form>
                </div>

                <div class="card">
                    <h3><i class="fas fa-history" style="color: #3b82f6; margin-right: 10px;"></i> Lịch Sử Xuất Gần Đây</h3>

                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Sản phẩm</th>
                                    <th>SL</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                    <th>Người xuất</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($history)): ?>
                                    <tr>
                                        <td colspan="6" style="color:#94a3b8; text-align: center; padding: 20px;">Chưa có dữ liệu xuất kho</td>
                                    </tr>
                                <?php else: foreach($history as $h): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($h['ngay_xuat'])) ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($h['ten_tp']) ?></td>
                                        <td style="font-weight: bold; color: #ef4444;">-<?= $h['so_luong'] ?></td>
                                        <td><?= number_format($h['don_gia']) ?></td>
                                        <td style="color: #10b981; font-weight: bold;"><?= number_format($h['thanh_tien']) ?></td>
                                        <td><?= htmlspecialchars($h['username']) ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    document.getElementById("sidebarToggle").addEventListener('click', function(){
        document.getElementById("sidebar").classList.toggle("collapsed");
    });

    // Auto Calculate Total Price
    document.addEventListener("DOMContentLoaded", function () {
        const sl  = document.querySelector("input[name='so_luong']");
        const gia = document.querySelector("input[name='don_gia']");
        const tt  = document.getElementById("thanh_tien");

        function tinhTien(){
            const s = parseFloat(sl.value || 0);
            const g = parseFloat(gia.value || 0);
            tt.value = (s * g).toLocaleString('vi-VN') + " VNĐ";
        }

        sl.addEventListener("input", tinhTien);
        gia.addEventListener("input", tinhTien);
    });
</script>

</body>
</html>