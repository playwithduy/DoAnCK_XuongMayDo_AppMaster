<?php
// FILE: view/qa_list.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Nhân Viên QA');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách yêu cầu kiểm tra</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    /* STYLE CHUNG (DARK SLATE) */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
    * { box-sizing: border-box; outline: none; text-decoration: none; }
    .wrapper { display: flex; height: 100vh; overflow: hidden; }

    /* === SIDEBAR (Modern Dark Slate) === */
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
        text-transform: uppercase; letter-spacing: 1px; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        display: flex; align-items: center; gap: 10px;
    }
    .sidebar-brand i { color: #38bdf8; }

    .user-panel { padding: 20px; text-align: center; border-bottom: 1px solid #1e293b; }
    .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #3b82f6; margin-bottom: 8px; }
    .user-name { font-weight: 600; color: #fff; font-size: 0.95rem; }
    
    .nav-section { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 15px 20px 5px; }
    .nav-link { display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px; border-radius: 8px; color: #94a3b8; font-weight: 500; transition: 0.2s; }
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
    .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; }
    .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
    .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
    .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }

    /* CONTENT */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
    .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 70px; }
    .page-title { margin: 0; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
    .content-body { flex: 1; overflow-y: auto; padding: 30px; }

    /* TABLE */
    .card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #f8fafc; padding: 15px; text-align: left; font-weight: 700; color: #64748b; font-size: 0.85rem; border-bottom: 2px solid #e2e8f0; }
    td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    
    .btn-check { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; font-size: 0.9rem; }
    .btn-check:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2); }
</style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-cut"></i> <span>APPMASTERS</span></div>
        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Nhân Viên QA</div>
        </div>
        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="QAController.php" class="nav-link active"><i class="fas fa-tasks"></i> <span class="text-hide">Yêu Cầu Kiểm Tra</span></a>
            <a href="QAController.php?action=history" class="nav-link"><i class="fas fa-history"></i> <span class="text-hide">Lịch Sử Kiểm Tra</span></a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:15px">
                <i class="fas fa-bars" id="sidebarToggle" style="cursor:pointer;color:#64748b;font-size:1.2rem"></i>
                <h2 class="page-title">Danh Sách Yêu Cầu (QC)</h2>
            </div>
            <div style="color:#64748b;font-weight:500"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            <div class="card">
                <h2 style="margin:0 0 20px 0; color:#0f172a; font-size:1.3rem; border-bottom:1px solid #f1f5f9; padding-bottom:15px;">
                    📋 Yêu cầu chờ xử lý
                </h2>
                
                <table style="width:100%">
                    <thead>
                        <tr>
                            <th>Mã YC</th>
                            <th>Sản Phẩm</th>
                            <th>Lô Sản Xuất</th>
                            <th>Số Lượng</th>
                            <th>Ngày Yêu Cầu</th>
                            <th>Người Gửi</th>
                            <th style="text-align:center">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pending_list) || $pending_list->num_rows == 0): ?>
                            <tr><td colspan="7" style="text-align:center; padding: 40px; color:#94a3b8;">Hiện không có yêu cầu nào cần kiểm tra.</td></tr>
                        <?php else: foreach ($pending_list as $req): ?>
                            <tr>
                                <td style="font-weight:700; color:#3b82f6">#<?= $req['ma_phieu'] ?></td>
                                <td>
                                    <div style="font-weight:600"><?= htmlspecialchars($req['ten_san_pham']) ?></div>
                                </td>
                                <td><?= $req['lo_san_xuat'] ?></td>
                                <td style="font-weight:700"><?= $req['so_luong_can_kiem'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($req['ngay_yeu_cau'])) ?></td>
                                <td><?= htmlspecialchars($req['nguoi_yeu_cau']) ?></td>
                                <td style="text-align:center">
                                    <a href="QAController.php?action=form&req_id=<?= $req['id'] ?>" class="btn-check">
                                        <i class="fas fa-clipboard-check"></i> Kiểm Tra
                                    </a>
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