<?php 
// FILE: view/phan_bo_san_xuat.php

// 1. KIỂM TRA DỮ LIỆU TỪ CONTROLLER
if(!isset($data)) { 
    header("Location: ../controllers/PhanBoController.php"); 
    exit;
}

// Lấy thông tin user
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Quản Đốc');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân Bổ Sản Xuất</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
        * { box-sizing: border-box; outline: none; text-decoration: none; }
        
        .wrapper { display: flex; height: 100vh; overflow: hidden; }

        /* SIDEBAR */
        .sidebar {
            width: 260px; background: #0f172a; color: #94a3b8;
            display: flex; flex-direction: column; transition: all 0.3s ease;
            flex-shrink: 0; overflow-y: auto; z-index: 100; border-right: 1px solid #1e293b;
        }
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed .text-hide { display: none !important; }
        .sidebar.collapsed .nav-link { justify-content: center; padding: 12px 0; margin: 5px 10px; }
        .sidebar.collapsed .nav-link i { margin-right: 0; font-size: 1.4rem; }
        
        .sidebar-brand { 
            padding: 24px 20px; font-size: 1.25rem; font-weight: 800; color: #fff;
            text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #1e293b;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand i { color: #38bdf8; }

        .user-panel { padding: 20px; text-align: center; border-bottom: 1px solid #1e293b; margin-bottom: 10px; }
        .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #1e293b; margin-bottom: 8px; }
        
        .nav-section { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 15px 20px 5px; }
        .nav-link { 
            display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px;
            border-radius: 8px; color: #94a3b8; font-weight: 500; transition: all 0.2s;
        }
        .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
        .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
        
        .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
        .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }

        /* MAIN */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
        .topbar { 
            background: #fff; padding: 15px 30px; 
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); z-index: 10;
        }
        .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; padding: 5px; }
        .page-title { margin: 0 0 0 15px; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
        .content-body { flex: 1; overflow-y: auto; padding: 30px; }

        /* CONTENT */
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e2e8f0; }
        
        /* Alert */
        .alert-success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca; }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.95rem; }
        th { background: #f8fafc; padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
        tr:hover { background-color: #f8fafc; }

        /* Buttons */
        .btn { padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; }
        .btn:hover { transform: translateY(-1px); }
        .btn-purple { background: #8b5cf6; color: white; box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3); }
        .btn-purple:hover { background: #7c3aed; }
        .btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); padding: 12px 24px; font-size: 1rem; width: 100%; justify-content: center; }
        .btn-success:hover { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        .btn-secondary:hover { background: #cbd5e1; }
        
        /* Form */
        label { display: block; font-weight: 600; color: #475569; margin-top: 15px; margin-bottom: 8px; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        textarea.form-control { resize: vertical; min-height: 100px; }

        /* Info Box */
        .info-box { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #e2e8f0; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748b; font-weight: 500; }
        .info-value { color: #0f172a; font-weight: 600; }
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
            <div style="font-weight:600; color:#fff; margin-top:10px"><?= htmlspecialchars($username) ?></div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Quản Đốc</div>
        </div>

        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../controllers/QuanDocController.php" class="nav-link">
                <i class="fas fa-home"></i> <span class="text-hide">Dashboard</span>
            </a>
            
            <div class="nav-section text-hide">Quản Lý Sản Xuất</div>
            <a href="../controllers/DonHangController.php" class="nav-link">
                <i class="fas fa-file-invoice-dollar"></i> <span class="text-hide">Danh sách đơn hàng</span>
            </a>
            <a href="../controllers/LapKeHoachController.php" class="nav-link">
                <i class="fas fa-clipboard-list"></i> <span class="text-hide">Lập kế hoạch SX</span>
            </a>
            <a href="../controllers/PhanBoController.php" class="nav-link active">
                <i class="fas fa-dolly-flatbed"></i> <span class="text-hide">Phân bổ sản xuất</span>
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
                <h2 class="page-title">Phân Bổ Sản Xuất</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;">
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">

            <?php if($data['msg']): ?>
                <div class="<?= $data['status'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                    <i class="fas fa-<?= $data['status'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= $data['msg'] ?>
                </div>
            <?php endif; ?>

            <?php if(!$data['selected']): ?>
                <div class="card">
                    <h3 style="margin-top:0; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:15px;">
                        <i class="fas fa-tasks" style="color:#8b5cf6; margin-right:8px;"></i> Kế hoạch đã duyệt - Chờ phân bổ
                    </h3>
                    
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Mã KH</th>
                                    <th>Đơn Hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Dây chuyền</th>
                                    <th style="text-align:center">Sản lượng/ngày</th>
                                    <th style="text-align:center">Thời gian</th>
                                    <th style="text-align: center; width:120px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['plans'])): ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center; padding:40px; color:#94a3b8;">
                                            <i class="fas fa-clipboard-check" style="font-size:2.5rem; display:block; margin-bottom:10px; color:#cbd5e1"></i>
                                            <strong style="font-size:1.1rem; display:block">Không có kế hoạch nào cần phân bổ</strong>
                                            <span style="font-size:0.9rem">Tất cả kế hoạch đã được giao cho xưởng trưởng</span>
                                        </td>
                                    </tr>
                                <?php else: foreach($data['plans'] as $p): ?>
                                    <tr>
                                        <td style="font-weight:bold; color:#3b82f6"><?= htmlspecialchars($p['ma_ke_hoach']) ?></td>
                                        <td style="font-weight:600; color:#0f172a"><?= htmlspecialchars($p['so_don_hang']) ?></td>
                                        <td><?= htmlspecialchars($p['ten_sp']) ?></td>
                                        <td><?= htmlspecialchars($p['ten_chuyen']) ?></td>
                                        <td style="text-align:center; color:#059669; font-weight:bold"><?= number_format($p['san_luong_ngay']) ?></td>
                                        <td style="text-align:center; font-size:0.9rem; color:#64748b">
                                            <?= date('d/m', strtotime($p['ngay_bat_dau'])) ?> - <?= date('d/m', strtotime($p['ngay_ket_thuc'])) ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="PhanBoController.php?id=<?= $p['id'] ?>" class="btn btn-purple">
                                                <i class="fas fa-share-square"></i> Phân bổ
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="card" style="max-width: 700px; margin: 0 auto;">
                    <div style="text-align:center; margin-bottom:25px">
                        <a href="PhanBoController.php" class="btn btn-secondary" style="float:left">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <h3 style="margin:0; color:#0f172a; display:inline-block;">
                            ✍️ Tạo Lệnh Sản Xuất
                        </h3>
                    </div>
                    
                    <div class="info-box">
                        <h4 style="margin:0 0 15px; color:#475569; font-size:0.9rem; text-transform:uppercase">📋 Thông Tin Kế Hoạch</h4>
                        <div class="info-row">
                            <span class="info-label">Mã kế hoạch:</span>
                            <span class="info-value" style="color:#3b82f6"><?= htmlspecialchars($data['selected']['ma_ke_hoach']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Đơn hàng:</span>
                            <span class="info-value"><?= htmlspecialchars($data['selected']['so_don_hang']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sản phẩm:</span>
                            <span class="info-value"><?= htmlspecialchars($data['selected']['ten_sp']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dây chuyền:</span>
                            <span class="info-value"><?= htmlspecialchars($data['selected']['ten_chuyen']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sản lượng/ngày:</span>
                            <span class="info-value" style="color:#059669"><?= number_format($data['selected']['san_luong_ngay']) ?> sp</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Thời gian:</span>
                            <span class="info-value">
                                <?= date('d/m/Y', strtotime($data['selected']['ngay_bat_dau'])) ?> 
                                → 
                                <?= date('d/m/Y', strtotime($data['selected']['ngay_ket_thuc'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="ke_hoach_id" value="<?= $data['selected']['id'] ?>">
                        
                        <label>Giao cho Xưởng trưởng <span style="color:red">*</span></label>
                        <select name="xuong_truong_id" class="form-control" required>
                            <option value="">-- Chọn xưởng trưởng --</option>
                            <?php foreach($data['users'] as $u): ?>
                                <option value="<?= $u['id'] ?>">👤 <?= htmlspecialchars($u['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label>Ghi chú / Yêu cầu kỹ thuật</label>
                        <textarea name="ghi_chu" class="form-control" placeholder="Nhập ghi chú chi tiết, yêu cầu đặc biệt..."></textarea>
                        
                        <button type="submit" class="btn btn-success" style="margin-top:20px">
                            <i class="fas fa-paper-plane"></i> GỬI LỆNH SẢN XUẤT
                        </button>
                    </form>
                </div>
            <?php endif; ?>

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