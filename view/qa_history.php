<?php
// FILE: view/qa_history.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Lấy thông tin user
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Nhân Viên QA');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch sử kiểm tra QA</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    /* === FONT & RESET === */
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
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
    .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
    .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
    .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
    .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }

    /* === CONTENT === */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
    .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 70px; }
    .page-title { margin: 0; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
    .content-body { flex: 1; overflow-y: auto; padding: 30px; }

    /* CARD & TABLE */
    .card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #f8fafc; padding: 15px; text-align: left; font-weight: 700; color: #64748b; font-size: 0.85rem; border-bottom: 2px solid #e2e8f0; }
    td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; font-size: 0.95rem; }
    tr:hover { background-color: #f8fafc; }
    
    /* BADGE */
    .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
    .badge-pass { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-fail { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* BUTTONS */
    .btn-detail { text-decoration: none; color: #0369a1; font-weight: 600; background: #e0f2fe; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; transition: 0.2s; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 5px; }
    .btn-detail:hover { background: #bae6fd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
</style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cut"></i> <span>APPMASTERS</span>
        </div>
        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Nhân Viên QA</div>
        </div>
        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="QAController.php" class="nav-link"><i class="fas fa-tasks"></i> <span class="text-hide">Yêu Cầu Kiểm Tra</span></a>
            <a href="QAController.php?action=history" class="nav-link active"><i class="fas fa-history"></i> <span class="text-hide">Lịch Sử Kiểm Tra</span></a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:15px">
                <i class="fas fa-bars" id="sidebarToggle" style="cursor:pointer; color:#64748b; font-size:1.2rem"></i>
                <h2 class="page-title">Lịch Sử Kiểm Tra (QC Logs)</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            <div class="card">
                <h2 style="color:#0f172a; margin-top:0; font-size:1.3rem; margin-bottom:10px; border-bottom:1px solid #f1f5f9; padding-bottom:15px;">
                    <i class="fas fa-file-signature" style="color:#3b82f6; margin-right:10px"></i> Danh sách biên bản đã lập
                </h2>
                
                <div style="overflow-x:auto;">
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <th>Mã BB</th>
                                <th>Sản Phẩm</th>
                                <th>Lô Sản Xuất</th>
                                <th>Ngày Kiểm</th>
                                <th>Người Kiểm</th>
                                <th>Kết Quả</th>
                                <th style="text-align:center">Chi Tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history_list) || $history_list->num_rows == 0): ?>
                                <tr><td colspan="7" style="text-align:center; padding:40px; color:#94a3b8;">Chưa có dữ liệu lịch sử nào.</td></tr>
                            <?php else: foreach ($history_list as $h): ?>
                                <tr>
                                    <td style="font-weight:700; color:#3b82f6">#<?= $h['id'] ?></td>
                                    <td>
                                        <div style="font-weight:600; color:#334155"><?= htmlspecialchars($h['ten_san_pham'] ?? 'N/A') ?></div>
                                        <div style="font-size:0.8rem; color:#64748b"><?= htmlspecialchars($h['ma_san_pham'] ?? '') ?></div>
                                    </td>
                                    <td>
                                        <span style="font-family:monospace; background:#f1f5f9; padding:2px 6px; border-radius:4px; font-weight:600; color:#475569">
                                            <?= htmlspecialchars($h['lo_san_xuat']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= !empty($h['ngay_kiem_tra']) ? date('d/m/Y', strtotime($h['ngay_kiem_tra'])) : '---' ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:5px;">
                                            <i class="fas fa-user-circle" style="color:#cbd5e1"></i>
                                            <?= htmlspecialchars($h['nguoi_kiem_tra'] ?? 'N/A') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                            $kq = strtolower(trim($h['ket_qua_chung']));
                                            if($kq == 'dat' || $kq == 'pass' || $kq == '1'): 
                                        ?>
                                            <span class="badge badge-pass"><i class="fas fa-check-circle"></i> Đạt</span>
                                        <?php else: ?>
                                            <span class="badge badge-fail"><i class="fas fa-times-circle"></i> Không Đạt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center">
                                        <a href="#" class="btn-detail" onclick="alert('Chức năng đang cập nhật...'); return false;">
                                            <i class="fas fa-search"></i> Xem
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
</div>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>

</body>
</html>