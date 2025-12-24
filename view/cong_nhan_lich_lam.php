<?php 
// FILE: view/lich_lam_viec.php

if(!isset($data)) { header("Location: ../controllers/LichLamViecController.php"); exit; }
$sch = $data['schedules'];
$mode = $data['mode'];

// Lấy thông tin user để hiển thị Sidebar
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Công Nhân');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"> <title>Lịch Làm Việc</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* === FONT & RESET === */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
    * { box-sizing: border-box; outline: none; text-decoration: none; }
    
    .wrapper { display: flex; height: 100vh; overflow: hidden; }

    /* ====================================================== */
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
    
    /* HEADER SIDEBAR */
    .sidebar-brand { 
        padding: 24px 20px; font-size: 1.25rem; font-weight: 800; color: #fff;
        text-transform: uppercase; letter-spacing: 1px; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        display: flex; align-items: center; gap: 10px;
    }
    .sidebar-brand i { color: #38bdf8; }

    /* USER PANEL */
    .user-panel { 
        padding: 20px; text-align: center; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        margin-bottom: 10px; 
    }
    .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #1e293b; margin-bottom: 8px; }
    .user-name { font-weight: 600; color: #fff; font-size: 0.95rem; }
    
    .nav-section { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 15px 20px 5px; letter-spacing: 0.5px; }

    .nav-link { 
        display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px;
        border-radius: 8px; color: #94a3b8; font-weight: 500; transition: all 0.2s ease-in-out;
    }
    .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
    .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
    
    .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
    .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }

    /* ====================================================== */
    /* === CONTENT AREA === */
    /* ====================================================== */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
    
    .topbar { 
        background: #fff; padding: 15px 30px; 
        display: flex; justify-content: space-between; align-items: center; 
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); z-index: 10;
        height: 70px;
    }
    
    .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; padding: 5px; margin-right: 15px; }
    .toggle-btn:hover { color: #0f172a; }

    .page-title { margin: 0; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
    
    .content-body { flex: 1; overflow-y: auto; padding: 30px; }

    /* --- CALENDAR HEADER --- */
    .header-bar { 
        display:flex; justify-content:space-between; align-items:center; 
        background:white; padding:15px 20px; border-radius:12px; 
        box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom:25px; border: 1px solid #e2e8f0;
    }
    .nav-title { font-size:1.1rem; font-weight:700; color:#334155; margin:0 20px; text-transform: uppercase; }
    
    .btn-nav { 
        text-decoration:none; padding:8px 16px; background:#f1f5f9; color:#475569; 
        border-radius:8px; transition:0.2s; font-weight: 500; font-size: 0.9rem;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-nav:hover { background:#e2e8f0; color: #0f172a; }

    .view-modes { display:flex; background: #f1f5f9; padding: 4px; border-radius: 8px; }
    .mode-item { 
        padding:6px 16px; text-decoration:none; color:#64748b; font-weight:600; border-radius: 6px; font-size: 0.9rem; transition: 0.2s;
    }
    .mode-item:hover { color: #334155; }
    .mode-item.active { background:#fff; color:#0f172a; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    /* --- CALENDAR GRID --- */
    .month-grid { display:grid; grid-template-columns:repeat(7, 1fr); gap:10px; }
    .day-name { text-align:center; font-weight:700; color:#64748b; padding:10px; background:#f8fafc; border-radius:8px; text-transform: uppercase; font-size: 0.8rem; }
    
    .day-cell { 
        background:white; min-height:120px; padding:10px; 
        border:1px solid #e2e8f0; border-radius:12px; cursor:pointer; transition:0.2s; 
    }
    .day-cell:hover { border-color:#3b82f6; box-shadow:0 4px 12px rgba(59, 130, 246, 0.15); transform:translateY(-2px); }
    .day-cell.today { border:2px solid #3b82f6; background:#eff6ff; }
    
    .day-number { font-weight:700; color:#334155; margin-bottom:8px; display:block; font-size: 1.1rem; }
    
    .task-dot { 
        font-size:0.75rem; background:#e0f2fe; color:#0369a1; 
        padding:4px 8px; border-radius:6px; margin-bottom:4px; display:block; font-weight: 500;
        border-left: 3px solid #0ea5e9;
    }

    /* --- TABLE LIST --- */
    .schedule-card { background:white; padding:0; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f8fafc; color:#64748b; padding:15px; text-align:left; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
    td { padding:15px; border-bottom:1px solid #f1f5f9; color: #334155; }
    tr:hover { background-color: #f8fafc; }
    
    .today-row { background:#eff6ff; }
    .today-row td { color: #1e3a8a; font-weight: 500; }
    
    .link-date { color:#0f172a; text-decoration:none; font-weight:700; }
    .link-date:hover { color: #3b82f6; text-decoration: underline; }
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
            <div style="font-size:0.75rem; color:#94a3b8">Công Nhân</div>
        </div>

        <nav style="flex:1; padding-top:10px">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../view/congnhan.php" class="nav-link"><i class="fas fa-home"></i> <span class="text-hide">Dashboard</span></a>
            
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="../controllers/LichLamViecController.php" class="nav-link active"><i class="fas fa-calendar-alt"></i> <span class="text-hide">Lịch Làm Việc</span></a>
            <a href="../controllers/BaoCaoSuCoController.php" class="nav-link"><i class="fas fa-exclamation-triangle"></i> <span class="text-hide">Báo Cáo Sự Cố</span></a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center;">
                <div class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
                <h2 class="page-title">Lịch Làm Việc Cá Nhân</h2>
            </div>
            <div style="color:#64748b"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            <div class="header-bar">
                <div>
                    <a href="<?= $data['links']['prev'] ?>" class="btn-nav"><i class="fas fa-chevron-left"></i> Trước</a>
                    <span class="nav-title"><?= $data['title'] ?></span>
                    <a href="<?= $data['links']['next'] ?>" class="btn-nav">Sau <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="view-modes">
                    <a href="?mode=day&date=<?= date('Y-m-d') ?>" class="mode-item <?= $mode=='day'?'active':'' ?>">Ngày</a>
                    <a href="?mode=week&date=<?= $data['date_ref'] ?>" class="mode-item <?= $mode=='week'?'active':'' ?>">Tuần</a>
                    <a href="?mode=month&date=<?= $data['date_ref'] ?>" class="mode-item <?= $mode=='month'?'active':'' ?>">Tháng</a>
                </div>
            </div>

            <?php if ($mode == 'month'): ?>
                <div class="month-grid">
                    <div class="day-name">Thứ 2</div><div class="day-name">Thứ 3</div><div class="day-name">Thứ 4</div><div class="day-name">Thứ 5</div><div class="day-name">Thứ 6</div><div class="day-name">Thứ 7</div><div class="day-name" style="color:#ef4444">CN</div>
                    <?php
                    $start = strtotime($data['range']['start']);
                    $dow = date('N', $start); 
                    for($i=1; $i<$dow; $i++) echo "<div></div>"; // Ô trống đầu tháng
                    $total = date('t', $start);
                    for($d=1; $d<=$total; $d++):
                        $currDate = date('Y-m-', $start) . str_pad($d, 2, '0', STR_PAD_LEFT);
                        $isToday = ($currDate == date('Y-m-d')) ? 'today' : '';
                        $tasks = $sch[$currDate] ?? [];
                    ?>
                        <div class="day-cell <?= $isToday ?>" onclick="window.location.href='?mode=day&date=<?= $currDate ?>'">
                            <span class="day-number"><?= $d ?></span>
                            <?php foreach($tasks as $t): ?>
                                <span class="task-dot">
                                    <i class="fas fa-clock" style="margin-right:4px"></i> <?= $t['ca_lam'] ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endfor; ?>
                </div>

            <?php else: ?>
                <?php if($mode == 'day'): ?>
                    <a href="?mode=month&date=<?= $data['date_ref'] ?>" class="btn-nav" style="display:inline-flex; margin-bottom:15px; background: #fff; border: 1px solid #e2e8f0;">
                        <i class="fas fa-arrow-left"></i> Quay lại lịch tháng
                    </a>
                <?php endif; ?>
                
                <div class="schedule-card">
                    <table>
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Ca làm việc</th>
                                <th>Vị trí (Xưởng - Chuyền)</th>
                                <th>Mã Đơn Hàng</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $curr = strtotime($data['range']['start']);
                        $end = strtotime($data['range']['end']);
                        $hasData = false;

                        while($curr <= $end):
                            $dStr = date('Y-m-d', $curr);
                            $isToday = ($dStr == date('Y-m-d')) ? 'today-row' : '';
                            $dayName = ['Sunday'=>'CN','Monday'=>'Thứ 2','Tuesday'=>'Thứ 3','Wednesday'=>'Thứ 4','Thursday'=>'Thứ 5','Friday'=>'Thứ 6','Saturday'=>'Thứ 7'][date('l',$curr)];
                            
                            if(isset($sch[$dStr])):
                                $hasData = true;
                                foreach($sch[$dStr] as $row): ?>
                                <tr class="<?= $isToday ?>">
                                    <td>
                                        <a href="?mode=day&date=<?= $dStr ?>" class="link-date">
                                            <?= date('d/m', $curr) ?> <span style="font-weight:400; color:#64748b">(<?= $dayName ?>)</span>
                                        </a>
                                    </td>
                                    <td><span style="font-weight:600; color:#0f172a"><?= $row['ca_lam'] ?></span></td>
                                    <td><?= $row['xuong'] ?> - <?= $row['chuyen'] ?></td>
                                    <td style="color:#ef4444; font-weight:700"><?= $row['ma_don_hang'] ?></td>
                                    <td style="color:#64748b; font-style:italic"><?= $row['ghi_chu'] ?></td>
                                </tr>
                                <?php endforeach;
                            elseif($mode == 'day'): ?>
                                <tr><td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">Hôm nay không có lịch làm việc.</td></tr>
                            <?php endif;
                            $curr = strtotime('+1 day', $curr);
                        endwhile; 
                        
                        if ($mode == 'week' && !$hasData): ?>
                             <tr><td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">Tuần này chưa có lịch làm việc.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>

</body>
</html>