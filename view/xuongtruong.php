<?php
// FILE: view/xuongtruong.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. KIỂM TRA DỮ LIỆU TỪ CONTROLLER
if (!isset($stats)) {
    header("Location: ../controllers/XuongTruongDashboardController.php");
    exit;
}

$pageTitle = "Tổng Quan Xưởng Trưởng";
$user      = $_SESSION['user'] ?? [];
$username  = $user['full_name'] ?? ($user['username'] ?? 'Khách');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* === FONT & RESET === */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f1f5f9; color: #334155; }
        * { box-sizing: border-box; outline: none; text-decoration: none; }
        
        .wrapper { display: flex; height: 100vh; overflow: hidden; }

        /* === MODERN SIDEBAR === */
        .sidebar {
            width: 260px;
            /* Màu nền hiện đại: Tối, sang trọng (Dark Slate) */
            background: #0f172a; 
            color: #94a3b8; /* Màu chữ xám nhạt dễ đọc */
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
        
        /* Logo Area - Giữ nguyên layout cũ, update font */
        .sidebar-brand { 
            padding: 24px 20px; 
            font-size: 1.25rem; 
            font-weight: 800; 
            color: #fff;
            text-transform: uppercase; 
            letter-spacing: 1px;
            border-bottom: 1px solid #1e293b;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand i { color: #38bdf8; } /* Icon màu xanh sáng */

        /* User Panel */
        .user-panel { 
            padding: 20px; text-align: center; 
            border-bottom: 1px solid #1e293b; 
            margin-bottom: 10px; 
        }
        .user-avatar { 
            width: 60px; height: 60px; 
            border-radius: 50%; 
            border: 3px solid #1e293b; 
            margin-bottom: 8px;
        }
        .user-name { font-weight: 600; color: #fff; font-size: 0.95rem; }
        
        /* MENU LINKS - Hiện đại hóa */
        .nav-section {
            font-size: 0.75rem; text-transform: uppercase; font-weight: 700;
            color: #475569; margin: 15px 20px 5px; letter-spacing: 0.5px;
        }

        .nav-link { 
            display: flex; align-items: center; 
            padding: 12px 20px; 
            margin: 4px 12px; /* Tạo khoảng cách 2 bên lề (Floating) */
            border-radius: 8px; /* Bo góc */
            color: #94a3b8; 
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        
        .nav-link i { 
            width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; 
            transition: transform 0.2s;
        }
        
        /* Hover Effect: Sáng lên và trượt nhẹ */
        .nav-link:hover { 
            background: rgba(255,255,255,0.05); 
            color: #fff; 
            transform: translateX(4px); 
        }
        
        /* Active State: Màu gradient xanh hiện đại */
        .nav-link.active { 
            background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff; 
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); /* Bóng đổ nhẹ */
        }
        
        .logout-btn { 
            margin-top: auto; margin-bottom: 20px; 
            border: 1px solid #334155;
            background: transparent;
        }
        .logout-btn:hover { 
            background: #ef4444; border-color: #ef4444; 
            color: white; 
        }

        /* === MAIN CONTENT === */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f8fafc; }
        
        .topbar { 
            background: #fff; padding: 15px 30px; 
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            z-index: 10;
        }
        .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; padding: 5px; }
        .toggle-btn:hover { color: #0f172a; }
        .page-title { margin: 0 0 0 15px; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
        
        .content-body { flex: 1; overflow-y: auto; padding: 30px; }
        
        /* === CARDS === */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 30px; }
        .card { 
            background: #fff; border-radius: 12px; padding: 25px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); 
            border: 1px solid #e2e8f0;
            position: relative; overflow: hidden; 
        }
        
        .stat-card h3 { font-size: 2rem; margin: 10px 0 5px; font-weight: 800; color: #0f172a; }
        .stat-card p { margin: 0; color: #64748b; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Icons in cards */
        .card-icon-bg {
            position: absolute; right: 20px; top: 20px; 
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
            opacity: 0.9;
        }

        /* === GRID LAYOUT FOR CHARTS === */
        .grid-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        @media (max-width: 1024px) { .grid-layout { grid-template-columns: 1fr; } }
        
        .section-header { 
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 15px;
        }
        .section-title { margin: 0; font-size: 1rem; font-weight: 700; color: #334155; }

        /* Progress Bar */
        .progress-bar-bg { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-top: 8px; }
        .progress-bar-fill { height: 100%; border-radius: 10px; transition: width 1s ease; }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cut"></i> <span>AppMasters</span>
            
        </div>

        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Xưởng Trưởng</div>
        </div>

        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../controllers/XuongTruongDashboardController.php" class="nav-link active">
                <i class="fas fa-home"></i> <span class="text-hide">Dashboard</span>
            </a>
            
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="xulychamcong.php" class="nav-link">
                <i class="fas fa-user-check"></i> <span class="text-hide">Chấm Công</span>
            </a>
            
            <a href="YeuCauNguyenLieuController.php" class="nav-link">
                <i class="fas fa-box-open"></i> <span class="text-hide">Yêu Cầu Nguyên Liệu</span>
            </a>
            
            <a href="YeuCauKiemTraController.php" class="nav-link">
                <i class="fas fa-tasks"></i> <span class="text-hide">Phiếu Kiểm Tra (QA)</span>
            </a>
        </nav>

        <a href="../controllers/LogoutController.php" class="nav-link logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span>
        </a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center;">
                <div class="toggle-btn" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </div>
                <h2 class="page-title"><?= $pageTitle ?></h2>
            </div>
            
            <div style="display: flex; align-items: center; gap: 10px; color: #64748b; font-weight: 500; font-size: 0.9rem;">
                <span><?= date('d/m/Y') ?></span>
            </div>
        </div>

        <div class="content-body">
            
            <div class="stat-grid">
                <div class="card stat-card">
                    <div class="card-icon-bg" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3><?= number_format($stats['active_plans'] ?? 0) ?></h3>
                    <p>Đơn Hàng Đang SX</p>
                </div>

                <div class="card stat-card">
                    <div class="card-icon-bg" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3><?= number_format($stats['today_prod'] ?? 0) ?></h3>
                    <p>Sản Phẩm Hôm Nay</p>
                </div>

                <div class="card stat-card">
                    <div class="card-icon-bg" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><?= number_format($stats['workers'] ?? 0) ?></h3>
                    <p>Nhân Sự Có Mặt</p>
                </div>
            </div>

            <div class="grid-layout">
                <div class="card">
                    <div class="section-header">
                        <h4 class="section-title">📊 Sản Lượng 7 Ngày Qua</h4>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="prodChart"></canvas>
                    </div>
                </div>
                
                <div class="card">
                    <div class="section-header">
                        <h4 class="section-title">🚀 Tiến Độ Sản Xuất</h4>
                    </div>
                    <div style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                        <?php if(!empty($stats['plans'])): foreach($stats['plans'] as $p): 
                            $pct = $p['chi_tieu'] > 0 ? round(($p['da_lam']/$p['chi_tieu'])*100) : 0;
                            // Màu sắc thanh tiến độ dựa trên %
                            $color = $pct >= 100 ? '#10b981' : ($pct >= 50 ? '#3b82f6' : '#f59e0b');
                        ?>
                            <div style="margin-bottom: 20px;">
                                <div style="display:flex; justify-content:space-between; font-size:0.85rem; margin-bottom:5px; font-weight: 600;">
                                    <span style="color: #334155;"><?= htmlspecialchars($p['ten_don_hang']) ?></span>
                                    <span style="color: <?= $color ?>;"><?= $pct ?>%</span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width:<?= $pct ?>%; background:<?= $color ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div style="text-align:center; color:#94a3b8; padding-top: 50px;">
                                <i class="fas fa-inbox fa-2x" style="margin-bottom: 10px;"></i><br>
                                Chưa có đơn hàng đang chạy
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Toggle Sidebar Script
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // Cấu hình Biểu đồ (Chart.js)
    const ctx = document.getElementById('prodChart').getContext('2d');
    
    // Tạo gradient màu cho biểu đồ đẹp hơn
    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)'); // Xanh dương nhạt
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($stats['chart_data'] ?? [], 'ngay')) ?>,
            datasets: [{
                label: 'Sản Lượng',
                data: <?= json_encode(array_column($stats['chart_data'] ?? [], 'san_luong')) ?>,
                borderColor: '#3b82f6', // Xanh dương hiện đại
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Đường cong mềm mại
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false } // Ẩn chú thích để gọn
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5], color: '#e2e8f0' },
                    ticks: { color: '#64748b' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b' }
                }
            }
        }
    });
</script>

</body>
</html>