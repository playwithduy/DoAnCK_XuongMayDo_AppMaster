<?php 
// FILE: view/cham_cong.php

if (!isset($data)) {
    header("Location: ../controllers/xulychamcong.php");
    exit;
}

$user = $data['user'];
$username = $user['username'] ?? 'Xưởng Trưởng';
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Chấm Công</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        
        .user-panel { padding: 20px; text-align: center; border-bottom: 1px solid #1e293b; }
        .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #3b82f6; margin-bottom: 8px; }
        .user-name { font-weight: 600; color: #fff; font-size: 0.95rem; }
        
        .nav-section { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 15px 20px 5px; letter-spacing: 0.5px; }
        .nav-link { display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px; border-radius: 8px; color: #94a3b8; font-weight: 500; transition: 0.2s; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
        .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
        .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
        .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
        .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }

        /* === MAIN CONTENT === */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f8fafc; }
        .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 70px; }
        .page-title { margin: 0; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
        .content-body { flex: 1; overflow-y: auto; padding: 25px; }

        /* === CARD & FILTER === */
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; align-items: end; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px; }
        .form-control { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.9rem; transition: 0.2s; }
        .form-control:focus { border-color: #0ea5e9; outline: none; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1); }
        
        /* === SPLIT LAYOUT === */
        .split-layout { display: grid; grid-template-columns: 320px 1fr; gap: 25px; height: calc(100vh - 280px); }
        .col-left { overflow-y: auto; border-right: 1px solid #e2e8f0; padding-right: 20px; }
        .col-right { overflow-y: auto; }

        /* === STAGE LIST === */
        .stage-item { display: block; padding: 15px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; transition: 0.2s; cursor: pointer; color: inherit; text-decoration: none; }
        .stage-item:hover { border-color: #0ea5e9; background: #f0f9ff; }
        .stage-item.active { border-color: #0ea5e9; background: #eff6ff; border-left: 4px solid #0ea5e9; }
        .progress-bar { height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: 8px; }
        .progress-fill { height: 100%; background: #10b981; transition: width 0.3s; }

        /* === TABLE === */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 12px; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; }
        td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .input-qty { width: 80px; text-align: center; font-weight: bold; }
        
        .btn-primary { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); transition: 0.2s; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 10px -1px rgba(16, 185, 129, 0.3); }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
        .empty-state i { font-size: 3rem; margin-bottom: 15px; color: #cbd5e1; }
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
            <a href="../controllers/XuongTruongDashboardController.php" class="nav-link">
                <i class="fas fa-home"></i> <span class="text-hide">Dashboard</span>
            </a>
            
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="xulychamcong.php" class="nav-link active">
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
            <div style="display:flex; align-items:center; gap:15px">
                <i class="fas fa-bars" id="sidebarToggle" style="cursor:pointer; color:#64748b; font-size:1.2rem"></i>
                <h2 class="page-title">Quản Lý Chấm Công</h2>
            </div>
            <div style="color:#64748b; font-weight:500"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            
            <?php if ($data['msg']): ?>
                <div class="alert alert-<?= $data['msgType'] == 'success' ? 'success' : 'error' ?>">
                    <i class="fas fa-<?= $data['msgType'] == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($data['msg']) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form method="GET" action="../controllers/xulychamcong.php" id="filterForm">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label>Chọn Lô / Lệnh Sản Xuất</label>
                            <select name="id_ke_hoach" id="selectKeHoach" class="form-control" onchange="resetAndSubmit()">
                                <option value="0">-- Chọn kế hoạch của bạn --</option>
                                <?php if(empty($data['danhSachKeHoach'])): ?>
                                    <option value="0" disabled>Không có kế hoạch nào được phân bổ</option>
                                <?php else: ?>
                                    <?php foreach ($data['danhSachKeHoach'] as $kh): ?>
                                        <option value="<?= $kh['id'] ?>" <?= $kh['id'] == $data['selectedKeHoach'] ? 'selected' : '' ?>>
                                            Lệnh: <?= $kh['ma_lenh'] ?> | KH: <?= $kh['ma_ke_hoach'] ?> - <?= $kh['ten_san_pham'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ngày Chấm Công</label>
                            <input type="date" name="ngay_cham_cong" class="form-control" value="<?= $data['selectedDate'] ?>" onchange="submitForm()">
                        </div>
                        <div class="form-group">
                            <label>Ca Làm Việc</label>
                            <select name="id_ca" class="form-control" onchange="submitForm()">
                                <option value="0">-- Chọn Ca --</option>
                                <?php foreach ($data['caList'] as $ca): ?>
                                    <option value="<?= $ca['id'] ?>" <?= $ca['id'] == $data['selectedCa'] ? 'selected' : '' ?>>
                                        <?= $ca['ten_ca'] ?> (<?= substr($ca['gio_bat_dau'],0,5) ?> - <?= substr($ca['gio_ket_thuc'],0,5) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="id_cong_doan" id="selectedCongDoan" value="<?= $data['selectedCongDoan'] ?>">
                    </div>
                </form>
            </div>

            <div class="split-layout">
                
                <div class="col-left">
                    <h4 style="margin-top:0; color:#475569; margin-bottom:15px; font-size:0.85rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                        <i class="fas fa-list-ol" style="margin-right:5px"></i> Các công đoạn
                    </h4>
                    
                    <?php if (empty($data['congDoanList'])): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Chọn Kế hoạch để xem công đoạn.</p>
                            <?php if($data['selectedKeHoach'] > 0): ?>
                                <small style="color:#ef4444; font-weight:500;">Kế hoạch này chưa được phân bổ công đoạn!</small>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data['congDoanList'] as $cd): 
                            $percent = ($cd['chi_tieu'] > 0) ? round(($cd['da_san_xuat'] / $cd['chi_tieu']) * 100) : 0;
                            $active = ($cd['id'] == $data['selectedCongDoan']) ? 'active' : '';
                            // Link chọn công đoạn
                            $link = "../controllers/xulychamcong.php?id_ke_hoach={$data['selectedKeHoach']}&id_ca={$data['selectedCa']}&ngay_cham_cong={$data['selectedDate']}&id_cong_doan={$cd['id']}";
                        ?>
                            <a href="<?= $link ?>" class="stage-item <?= $active ?>">
                                <div style="display:flex; justify-content:space-between; font-weight:600; font-size:0.95rem; color:#334155;">
                                    <span><?= htmlspecialchars($cd['ten_cong_doan']) ?></span>
                                    <span style="font-size:0.8rem; background:#f1f5f9; padding:2px 8px; border-radius:4px; border:1px solid #cbd5e1;">
                                        <?= $cd['da_san_xuat'] ?> / <?= $cd['chi_tieu'] ?>
                                    </span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percent ?>%"></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="col-right">
                    <?php if ($data['currentStage']): ?>
                        <div class="card" style="margin-bottom:0; height:100%; display:flex; flex-direction:column">
                            <div style="border-bottom:1px solid #e2e8f0; padding-bottom:15px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center">
                                <div>
                                    <h3 style="margin:0; color:#0f172a; font-size:1.1rem; font-weight:700;">
                                        <i class="fas fa-edit" style="color:#3b82f6; margin-right:5px"></i> 
                                        <?= htmlspecialchars($data['currentStage']['ten_cong_doan']) ?>
                                    </h3>
                                    <span style="color:#64748b; font-size:0.85rem">Nhập sản lượng cho công nhân</span>
                                </div>
                                <button type="submit" form="mainForm" class="btn-primary">
                                    <i class="fas fa-save"></i> LƯU KẾT QUẢ
                                </button>
                            </div>

                            <form id="mainForm" method="POST" action="../controllers/xulychamcong.php" style="flex:1; overflow-y:auto">
                                <input type="hidden" name="id_ke_hoach" value="<?= $data['selectedKeHoach'] ?>">
                                <input type="hidden" name="id_cong_doan" value="<?= $data['selectedCongDoan'] ?>">
                                <input type="hidden" name="id_ca" value="<?= $data['selectedCa'] ?>">
                                <input type="hidden" name="ngay_cham_cong" value="<?= $data['selectedDate'] ?>">

                                <?php if ($data['selectedCa'] == 0): ?>
                                    <div class="alert alert-error">Vui lòng chọn <b>Ca làm việc</b> trước khi nhập liệu!</div>
                                <?php else: ?>
                                    <table class="table-custom">
                                        <thead>
                                            <tr>
                                                <th style="width:50px; text-align:center">STT</th>
                                                <th>Họ Tên Công Nhân</th>
                                                <th style="width:140px; text-align:center">Sản Lượng</th>
                                                <th>Ghi Chú</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stt=1; foreach ($data['allWorkers'] as $w): 
                                                $val = $data['existingData'][$w['id']]['san_luong'] ?? 0;
                                                $note = $data['existingData'][$w['id']]['ghi_chu'] ?? '';
                                            ?>
                                                <tr>
                                                    <td style="text-align:center; color:#94a3b8"><?= $stt++ ?></td>
                                                    <td style="font-weight:500; color:#334155"><?= htmlspecialchars($w['ho_ten']) ?></td>
                                                    <td style="text-align:center">
                                                        <input type="number" name="san_luong[<?= $w['id'] ?>]" value="<?= $val ?>" min="0" class="form-control input-qty">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="ghi_chu[<?= $w['id'] ?>]" value="<?= htmlspecialchars($note) ?>" class="form-control" placeholder="...">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="empty-state" style="background:#fff; border-radius:12px; height:100%; display:flex; flex-direction:column; justify-content:center; align-items:center; border:1px solid #e2e8f0">
                            <i class="fas fa-arrow-left" style="font-size:3rem; margin-bottom:20px; color:#e2e8f0"></i>
                            <h3 style="margin:0 0 10px 0; color:#334155; font-size:1.2rem">Chưa chọn công đoạn</h3>
                            <p style="color:#64748b">Vui lòng chọn Lệnh Sản Xuất và Công đoạn bên trái.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // Khi chọn Kế hoạch -> Reset công đoạn về 0 và submit form
    function resetAndSubmit() {
        document.getElementById('selectedCongDoan').value = 0; 
        document.getElementById('filterForm').submit();
    }

    // Khi chọn Ngày hoặc Ca -> Chỉ submit form
    function submitForm() {
        var kh = document.getElementById('selectKeHoach').value;
        if(kh > 0) {
            document.getElementById('filterForm').submit();
        }
    }
</script>

</body>
</html>