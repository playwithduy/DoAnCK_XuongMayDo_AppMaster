<?php 
// FILE: view/lap_ke_hoach.php

// Kiểm tra dữ liệu
if(!isset($data)) { 
    header("Location: ../controllers/LapKeHoachController.php"); 
    exit; 
}
$orders = $data['orders'];
$selected = $data['selected'];
$lines = $data['lines'];
$msg = $data['msg'];

// User info
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Quản Đốc');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"> 
<title>Lập Kế Hoạch Sản Xuất</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    /* === FONT & RESET === */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
    * { box-sizing: border-box; outline: none; text-decoration: none; }
    
    .wrapper { display: flex; height: 100vh; overflow: hidden; }

    /* ====================================================== */
    /* === SIDEBAR HIỆN ĐẠI (DARK SLATE) === */
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
    /* === CSS NỘI DUNG === */
    /* ====================================================== */
    .card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e2e8f0; }
    
    /* TABLE */
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.95rem; }
    th { background: #f8fafc; padding: 12px; text-align: left; color: #64748b; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
    td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    /* BUTTONS */
    .btn { padding: 8px 16px; border-radius: 6px; font-weight: 500; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
    .btn-primary { background: #3b82f6; color: white; }
    .btn-primary:hover { background: #2563eb; }
    .btn-success { background: #10b981; color: white; width: 100%; justify-content: center; padding: 12px; }
    .btn-success:hover { background: #059669; }
    .btn-secondary { background: #e2e8f0; color: #334155; }
    .btn-secondary:hover { background: #cbd5e1; }
    
    /* FORM & GRID */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: 'Inter', sans-serif; }
    .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .grid-layout { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
    
    /* PRODUCT LIST */
    .prod-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px dashed #e2e8f0; }
    .prod-img { width: 45px; height: 45px; border-radius: 6px; object-fit: cover; border: 1px solid #ddd; }
    
    /* ALERT */
    .alert-success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; }
    .alert-info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bfdbfe; }
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
            <a href="../controllers/LapKeHoachController.php" class="nav-link active">
                <i class="fas fa-clipboard-list"></i> <span class="text-hide">Lập kế hoạch SX</span>
            </a>
            <a href="../controllers/PhanBoController.php" class="nav-link">
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
                <h2 class="page-title">Lập Kế Hoạch Sản Xuất</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;">
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">
            
            <?php if($msg): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> <?= $msg ?>
                </div>
            <?php endif; ?>

            <?php if(!$selected): ?>
                <div class="card">
                    <h3 style="margin-top:0; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:15px;">
                        <i class="fas fa-list-ul" style="color:#3b82f6; margin-right:8px;"></i> Danh sách đơn hàng chờ
                    </h3>
                    
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Người Đặt</th>
                                    <th>Địa Chỉ Giao</th>
                                    <th>Tổng SL</th>
                                    <th>Hạn Giao</th>
                                    <th style="text-align: center;">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($orders)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding: 40px; color:#94a3b8;">
                                            <i class="fas fa-box-open" style="font-size:2rem; display:block; margin-bottom:10px"></i>
                                            Hiện không có đơn hàng mới cần lập kế hoạch.
                                        </td>
                                    </tr>
                                <?php else: foreach($orders as $o): 
                                    $nguoiDat = !empty($o['ten_nguoi_dat']) ? $o['ten_nguoi_dat'] : 'Khách Lẻ';
                                    $diaChi = !empty($o['dia_diem_giao_hang']) ? $o['dia_diem_giao_hang'] : '<span style="color:#94a3b8">Chưa có</span>';
                                ?>
                                    <tr>
                                        <td style="font-weight:bold; color:#0f172a"><?= htmlspecialchars($o['so_don_hang']) ?></td>
                                        <td style="font-weight:500"><?= htmlspecialchars($nguoiDat) ?></td>
                                        <td style="font-size:0.9rem"><?= $diaChi ?></td>
                                        <td style="color:#ef4444; font-weight:bold"><?= number_format($o['tong_so_luong']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($o['ngay_giao_du_kien'])) ?></td>
                                        <td style="text-align: center;">
                                            <a href="?order_id=<?= $o['id'] ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> Lập KH
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="card">
                    <div style="margin-bottom: 25px;">
                        <a href="LapKeHoachController.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
                    </div>

                    <div class="grid-layout">
                        <div style="background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; height: fit-content;">
                            <h3 style="margin-top:0; color:#0f172a; margin-bottom: 20px; border-bottom:1px solid #e2e8f0; padding-bottom:10px">
                                📦 Thông tin đơn hàng
                            </h3>
                            
                            <?php 
                                $nguoiDat = !empty($selected['ten_nguoi_dat']) ? $selected['ten_nguoi_dat'] : "Khách Lẻ";
                                $diaChi = !empty($selected['dia_diem_giao_hang']) ? $selected['dia_diem_giao_hang'] : '---';
                            ?>

                            <p style="margin-bottom:10px;"><b>Mã ĐH:</b> <span style="color:#3b82f6; font-weight:bold;"><?= htmlspecialchars($selected['so_don_hang']) ?></span></p>
                            <p style="margin-bottom:10px;"><b>Người đặt:</b> <?= htmlspecialchars($nguoiDat) ?></p>
                            <p style="margin-bottom:10px;"><b>Địa chỉ giao:</b> <?= htmlspecialchars($diaChi) ?></p>
                            <p style="margin-bottom:10px;"><b>Hạn giao:</b> <span style="color:#ef4444; font-weight:bold"><?= date('d/m/Y', strtotime($selected['ngay_giao_du_kien'])) ?></span></p>
                            
                            <hr style="border:0; border-top:1px solid #e2e8f0; margin: 15px 0;">
                            
                            <h4 style="margin:0 0 15px 0; color:#475569; font-size:0.95rem">Chi tiết sản phẩm:</h4>
                            
                            <?php 
                                $tongSL = $selected['tong_so_luong'] ?? 0;
                                if(!empty($selected['chi_tiet'])): 
                                    foreach($selected['chi_tiet'] as $item): 
                            ?>
                                <div class="prod-item">
                                    <?php if(!empty($item['image'])): ?>
                                        <img src="../assets/images/<?= htmlspecialchars($item['image']) ?>" class="prod-img" alt="Product">
                                    <?php else: ?>
                                        <div class="prod-img" style="display:flex;align-items:center;justify-content:center;background:#eee;color:#999"><i class="fas fa-tshirt"></i></div>
                                    <?php endif; ?>

                                    <div style="flex:1">
                                        <b style="color:#334155; font-size:0.95rem"><?= htmlspecialchars($item['ten_san_pham']) ?></b>
                                        <div style="color:#64748b; font-size:0.85rem; margin-top:2px;">
                                            Size: <span style="background:#e2e8f0; padding:1px 6px; border-radius:4px; font-size:0.8rem"><?= htmlspecialchars($item['size']) ?></span> 
                                            | SL: <b style="color:#0f172a"><?= number_format($item['so_luong']) ?></b>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                            
                            <div style="margin-top:15px; padding-top:10px; border-top:1px solid #e2e8f0; font-weight:bold; text-align:right">
                                TỔNG SỐ LƯỢNG: <span style="color:#d97706; font-size:1.2rem"><?= number_format($tongSL) ?></span>
                            </div>
                        </div>

                        <div>
                            <form method="POST">
                                <input type="hidden" name="don_hang_id" value="<?= $selected['id'] ?>">
                                <input type="hidden" name="btn_save_plan" value="1">
                                
                                <h3 style="margin-top:0; color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom: 25px;">
                                    ⚙️ Thiết lập sản xuất
                                </h3>

                                <div class="form-group">
                                    <label>Dây chuyền sản xuất <span style="color:red">*</span></label>
                                    <select name="day_chuyen_id" class="form-control" required>
                                        <option value="">-- Chọn dây chuyền phù hợp --</option>
                                        <?php foreach($lines as $l): ?>
                                            <option value="<?= $l['id'] ?>">
                                                <?= htmlspecialchars($l['ten_chuyen']) ?> (Công suất: <?= number_format($l['cong_suat']) ?> sp/ngày)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div style="display:flex; gap:20px;">
                                    <div class="form-group" style="flex:1">
                                        <label>Ngày bắt đầu <span style="color:red">*</span></label>
                                        <input type="date" name="ngay_bat_dau" class="form-control" required>
                                    </div>
                                    <div class="form-group" style="flex:1">
                                        <label>Ngày kết thúc <span style="color:red">*</span></label>
                                        <input type="date" name="ngay_ket_thuc" class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Sản lượng mục tiêu / ngày <span style="color:red">*</span></label>
                                    <input type="number" name="san_luong_ngay" class="form-control" placeholder="Ví dụ: 500" required>
                                    <small style="color:#64748b; margin-top:5px; display:block">
                                        * Dựa trên tổng số lượng <?= number_format($tongSL) ?>, hệ thống sẽ tự tính tiến độ.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> GỬI DUYỆT KẾ HOẠCH
                                </button>
                            </form>
                        </div>
                    </div>
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