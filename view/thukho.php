<?php
// FILE: view/thukho.php

if (!isset($data)) {
    header("Location: ../controllers/ThuKhoController.php");
    exit;
}

$user     = $data['user'];
$dsNhapTP = $data['dsNhapTP'] ?? [];
$dsXuatTP = $data['dsXuatTP'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Thủ Kho');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dashboard Thủ kho</title>
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
    /* === PHẦN 2: CSS NỘI DUNG CŨ (GIỮ NGUYÊN) === */
    /* ====================================================== */
    
    /* WELCOME */
    .welcome{
        background:#fff; border-radius:14px; padding:20px 25px;
        display:flex; align-items:center; gap:20px;
        box-shadow:0 10px 30px rgba(0,0,0,.1); margin-bottom:25px;
    }
    .avatar-icon{
        width:80px;height:80px; border-radius:50%; background:#1976D2;
        display:flex; align-items:center; justify-content:center;
        font-size:36px; color:#fff;
    }

    /* CARD STATS */
    .stats{ display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-top:20px; }
    .card-stat{ background:#fff; border-radius:14px; padding:25px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,.1); }
    .card-stat h2{ margin:0; color:#0D47A1; font-size: 2rem; }

    /* SECTION */
    .section{ background:#fff; border-radius:14px; padding:25px; margin-top:30px; box-shadow:0 10px 30px rgba(0,0,0,.1); }
    .section h3 { margin-top: 0; color: #333; }

    /* TABLE */
    table{ width:100%; border-collapse:collapse; margin-top:15px; }
    th,td{ padding:10px; border-bottom:1px solid #ddd; text-align:center; }
    th{ background:#f3f5f7; color: #555; font-weight: bold; }

    /* PIE nhỏ */
    .pie-wrapper{ max-width:380px; margin:20px auto; }
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
                <h2 class="page-title" style="margin:0 0 0 15px; font-size:1.3rem; color:#0f172a;">Dashboard Thủ Kho</h2>
            </div>
            <div style="color:#64748b; font-weight:500;">
                  <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">

            <div class="welcome">
                <div class="avatar-icon">
                    <?= ($user['gender'] ?? '')=='female' ? '👩' : '👤' ?>
                </div>
                <div>
                    <h2 style="margin:0;">Xin chào, <?= htmlspecialchars($user['username']) ?>!</h2>
                    <p style="margin:5px 0;">Vai trò: <b>Thủ kho</b></p>
                    <p style="margin:0;">Trạng thái:
                        <b style="color:<?= ($user['status'] ?? 'active')=='active'?'green':'red' ?>">
                            <?= ($user['status'] ?? 'active')=='active'?'🟢 Đang hoạt động':'🔴 Ngừng' ?>
                        </b>
                    </p>
                </div>
            </div>

            <div class="stats">
                <div class="card-stat"><h2><?= $data['tongNL'] ?? 0 ?></h2><p>Tồn kho nguyên liệu</p></div>
                <div class="card-stat"><h2><?= $data['tongTP'] ?? 0 ?></h2><p>Tồn kho thành phẩm</p></div>
                <div class="card-stat"><h2><?= $data['phieuNhap'] ?? 0 ?></h2><p>Phiếu nhập hôm nay</p></div>
                <div class="card-stat"><h2><?= $data['phieuXuat'] ?? 0 ?></h2><p>Phiếu xuất hôm nay</p></div>
                <div class="card-stat">
                    <h2><?= number_format($data['tongDoanhThu'] ?? 0) ?> ₫</h2>
                    <p>Tổng doanh thu</p>
                </div>
            </div>

            <div class="section">
                <h3>📥 Chi tiết nhập kho thành phẩm</h3>
                <table>
                    <tr>
                        <th>Ngày</th>
                        <th>Thành phẩm</th>
                        <th>Xưởng</th>
                        <th>Số lượng</th>
                        <th>QC</th>
                        <th>Người nhập</th>
                    </tr>
                    <?php if (empty($dsNhapTP)): ?>
                        <tr><td colspan="6">Chưa có dữ liệu</td></tr>
                    <?php else: foreach ($dsNhapTP as $n): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($n['ngay_nhap'])) ?></td>
                            <td><?= htmlspecialchars($n['ten_tp']) ?></td>
                            <td><?= htmlspecialchars($n['xuong']) ?></td>
                            <td><?= number_format($n['so_luong']) ?></td>
                            <td>
                                <?= isset($n['qc_ket_qua']) && $n['qc_ket_qua'] == 'dat'
                                    ? '<span style="color:green;font-weight:bold">Đạt</span>'
                                    : '<span style="color:red;font-weight:bold">Không đạt</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($n['username']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </table>
            </div>

            <div class="section">
                <h3>📤 Chi tiết xuất kho thành phẩm</h3>
                <table>
                    <tr>
                        <th>Ngày</th>
                        <th>Thành phẩm</th>
                        <th>Số lượng</th>
                        <th>Người xuất</th>
                    </tr>
                    <?php if (empty($dsXuatTP)): ?>
                        <tr><td colspan="4">Chưa có dữ liệu</td></tr>
                    <?php else: foreach ($dsXuatTP as $x): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($x['ngay_xuat'])) ?></td>
                            <td><?= htmlspecialchars($x['ten_tp']) ?></td>
                            <td><?= number_format($x['so_luong']) ?></td>
                            <td><?= htmlspecialchars($x['username']) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </table>
            </div>

            <div class="section">
                <h3>📈 Thống kê kho</h3>
                <select id="chonBieuDo" style="padding:10px;border-radius:8px; border:1px solid #ddd;">
                    <option value="xuat7">📤 Xuất kho & Doanh thu (7 ngày)</option>
                    <option value="xuat30">📤 Xuất kho & Doanh thu (30 ngày)</option>
                    <option value="ton">📦 Tồn kho hiện tại</option>
                </select>

                <div style="margin-top: 15px; height: 300px;">
                    <canvas id="chartXuat"></canvas>
                    <div class="pie-wrapper" style="display:none; height: 100%; display: flex; justify-content: center;">
                        <canvas id="chartTon"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Toggle Sidebar Script
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // Chart Data
    const dataXuat7  = <?= json_encode($data['bieuDoXuat7'] ?? []) ?>;
    const dataXuat30 = <?= json_encode($data['bieuDoXuat30'] ?? []) ?>;
    const dataTon    = <?= json_encode($data['bieuDoTon'] ?? []) ?>;

    let chartXuat;

    function renderXuat(data){
        if(chartXuat) chartXuat.destroy();
        chartXuat = new Chart(document.getElementById('chartXuat'), {
            type:'bar',
            data:{
                labels:data.map(i=>i.ngay),
                datasets:[
                    {
                        label:'Số lượng xuất',
                        data:data.map(i=>i.tong_sl),
                        yAxisID:'ySL',
                        backgroundColor: '#3b82f6', // Xanh (Modern)
                        barPercentage: 0.6
                    },
                    {
                        label:'Doanh thu (VNĐ)',
                        data:data.map(i=>i.doanh_thu),
                        yAxisID:'yDT',
                        backgroundColor: '#f59e0b', // Cam/Vàng (Modern)
                        barPercentage: 0.6
                    }
                ]
            },
            options:{
                responsive:true,
                maintainAspectRatio: false,
                scales:{
                    ySL:{
                        position:'left',
                        title:{display:true,text:'Số lượng'},
                        grid: { borderDash: [5, 5], color: '#e2e8f0' }
                    },
                    yDT:{
                        position:'right',
                        grid:{drawOnChartArea:false},
                        title:{display:true,text:'VNĐ'}
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    renderXuat(dataXuat7);

    const chartTon = new Chart(document.getElementById('chartTon'), {
        type:'pie',
        data:{
            labels:dataTon.map(i=>i.name),
            datasets:[{ data:dataTon.map(i=>i.ton_kho), backgroundColor:['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'] }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true
        }
    });

    document.getElementById('chonBieuDo').addEventListener('change',function(){
        if(this.value==='xuat7'){
            renderXuat(dataXuat7);
            document.getElementById('chartXuat').style.display='block';
            document.querySelector('.pie-wrapper').style.display='none';
        }
        if(this.value==='xuat30'){
            renderXuat(dataXuat30);
            document.getElementById('chartXuat').style.display='block';
            document.querySelector('.pie-wrapper').style.display='none';
        }
        if(this.value==='ton'){
            document.getElementById('chartXuat').style.display='none';
            document.querySelector('.pie-wrapper').style.display='flex';
        }
    });
</script>

</body>
</html>