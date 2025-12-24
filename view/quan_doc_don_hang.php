<?php 
// FILE: view/quan_doc_don_hang.php

if(!isset($data)) { 
    header("Location: ../controllers/DonHangController.php"); 
    exit; 
}
$orders = $data['orders'];
$selected = $data['selected'];

$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Quản Đốc');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"> 
<title>Danh Sách Đơn Hàng</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* CSS GIỮ NGUYÊN NHƯ CŨ */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
    * { box-sizing: border-box; outline: none; text-decoration: none; }
    .wrapper { display: flex; height: 100vh; overflow: hidden; }
    
    .sidebar { width: 260px; background: #0f172a; color: #94a3b8; display: flex; flex-direction: column; transition: all 0.3s ease; flex-shrink: 0; overflow-y: auto; z-index: 100; border-right: 1px solid #1e293b; }
    .sidebar.collapsed { width: 80px; }
    .sidebar.collapsed .text-hide { display: none !important; }
    .sidebar.collapsed .sidebar-brand span { display: none; }
    .sidebar.collapsed .nav-link { justify-content: center; padding: 12px 0; margin: 5px 10px; }
    .sidebar.collapsed .nav-link i { margin-right: 0; font-size: 1.4rem; }
    .sidebar.collapsed .user-panel { padding: 10px 5px; }
    .sidebar-brand { padding: 24px 20px; font-size: 1.25rem; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #1e293b; display: flex; align-items: center; gap: 10px; }
    .sidebar-brand i { color: #38bdf8; }
    .user-panel { padding: 20px; text-align: center; border-bottom: 1px solid #1e293b; margin-bottom: 10px; }
    .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #1e293b; margin-bottom: 8px; }
    .user-name { font-weight: 600; color: #fff; font-size: 0.95rem; }
    .nav-section { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 15px 20px 5px; letter-spacing: 0.5px; }
    .nav-link { display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px; border-radius: 8px; color: #94a3b8; font-weight: 500; transition: all 0.2s ease-in-out; }
    .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
    .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
    .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
    .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }
    
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
    .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); z-index: 10; }
    .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; padding: 5px; }
    .page-title { margin: 0 0 0 15px; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
    .content-body { flex: 1; overflow-y: auto; padding: 30px; }
    .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e2e8f0; }
    
    /* TABLE */
    table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.95rem; }
    th { background: #f8fafc; padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.8rem; }
    td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
    tr:hover { background-color: #f8fafc; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .st-sx { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .btn-view { background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px; }
    .btn-view:hover { background: #e2e8f0; color: #0f172a; }
    .btn-plan { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); transition: 0.2s; }
    .btn-plan:hover { transform: translateY(-2px); box-shadow: 0 6px 10px -1px rgba(16, 185, 129, 0.3); }

    /* DETAIL */
    .detail-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px; }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; }
    .info-title { font-weight: bold; color: #0f172a; margin-bottom: 10px; display: block; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
    .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95rem; }
    .info-label { color: #64748b; }
    .prod-table th { background: #0f172a; color: #fff; }
    .prod-table td { border-bottom: 1px solid #e2e8f0; }
</style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-cut"></i> <span class="text-hide">AppMasters</span></div>
        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Quản Đốc</div>
        </div>
        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../controllers/QuanDocController.php" class="nav-link"><i class="fas fa-home"></i> <span class="text-hide">Dashboard</span></a>
            <div class="nav-section text-hide">Quản Lý Sản Xuất</div>
            <a href="../controllers/DonHangController.php" class="nav-link active"><i class="fas fa-file-invoice-dollar"></i> <span class="text-hide">Danh sách đơn hàng</span></a>
            <a href="../controllers/LapKeHoachController.php" class="nav-link"><i class="fas fa-clipboard-list"></i> <span class="text-hide">Lập kế hoạch SX</span></a>
            <a href="../controllers/PhanBoController.php" class="nav-link"><i class="fas fa-dolly-flatbed"></i> <span class="text-hide">Phân bổ sản xuất</span></a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center;">
                <div class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
                <h2 class="page-title">Quản Lý Đơn Hàng</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            
            <?php if ($selected): ?>
                <?php 
                    // Xử lý dữ liệu fallback để tránh lỗi Undefined
                    $khachHang = $selected['ten_khach_hang'] ?? ($selected['ten_user_online'] . " (Online)") ?? "Khách vãng lai";
                    $sdtEmail = $selected['sdt_email'] ?? "---";
                    $nguoiLienHe = $selected['nguoi_lien_he'] ?? $khachHang;
                    $diaChi = $selected['dia_diem_giao_hang'] ?? $selected['dia_chi_giao_hang'] ?? $selected['dia_diem_giao_hang_cu_the'] ?? "---";
                ?>
                <div class="card">
                    <div class="detail-header">
                        <h2 style="color:#0f172a; margin:0; font-size: 1.25rem;">
                            <i class="fas fa-file-alt" style="color: #3b82f6;"></i> Chi tiết: <span style="color:#3b82f6"><?= $selected['so_don_hang'] ?></span>
                        </h2>
                        <a href="DonHangController.php" style="color:#64748b; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                    <div class="info-grid">
                        <div class="info-box">
                            <span class="info-title"><i class="fas fa-user"></i> Khách Hàng</span>
                            <div class="info-row"><span class="info-label">Tên khách:</span> <b><?= $khachHang ?></b></div>
                            <div class="info-row"><span class="info-label">SĐT/Email:</span> <span><?= $sdtEmail ?></span></div>
                            <div class="info-row"><span class="info-label">Người liên hệ:</span> <span><?= $nguoiLienHe ?></span></div>
                        </div>
                        <div class="info-box">
                            <span class="info-title"><i class="fas fa-shipping-fast"></i> Giao Hàng</span>
                            <div class="info-row"><span class="info-label">Ngày đặt:</span> <span><?= date('d/m/Y', strtotime($selected['ngay_lap'])) ?></span></div>
                            <div class="info-row"><span class="info-label">Hạn giao:</span> <span style="color:#ef4444; font-weight:bold"><?= date('d/m/Y', strtotime($selected['ngay_giao_du_kien'])) ?></span></div>
                            <div class="info-row"><span class="info-label">Địa chỉ:</span> <span><?= $diaChi ?></span></div>
                        </div>
                    </div>

                    <h3 style="margin-top:20px; font-size:1.1rem; color:#334155;">📦 Danh Sách Sản Phẩm</h3>
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên Sản Phẩm</th>
                                <th style="text-align:center">Size</th>
                                <th style="text-align:center">Số Lượng</th>
                                <th style="text-align:right">Đơn Giá</th>
                                <th style="text-align:right">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stt = 1; $tongSL = 0;
                            if (!empty($selected['chi_tiet'])): 
                                foreach ($selected['chi_tiet'] as $sp): 
                                    $tongSL += $sp['so_luong'];
                            ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
                                    <td><b style="color:#0f172a"><?= htmlspecialchars($sp['ten_san_pham']) ?></b></td>
                                    <td style="text-align:center"><span class="badge st-sx"><?= $sp['size'] ?></span></td>
                                    <td style="text-align:center; font-weight:bold"><?= $sp['so_luong'] ?></td>
                                    <td style="text-align:right"><?= number_format($sp['don_gia']) ?> đ</td>
                                    <td style="text-align:right; font-weight:bold"><?= number_format($sp['thanh_tien']) ?> đ</td>
                                </tr>
                            <?php endforeach; endif; ?>
                            <tr style="background:#f1f5f9; font-weight:bold">
                                <td colspan="3" style="text-align:right">TỔNG:</td>
                                <td style="text-align:center"><?= number_format($tongSL) ?></td>
                                <td></td>
                                <td style="text-align:right; color:#ef4444; font-size:1.1rem"><?= number_format($selected['tong_tien']) ?> đ</td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if($selected['trang_thai'] == 'DaDuyet'): ?>
                        <div style="margin-top:25px; text-align:right">
                            <a href="LapKeHoachController.php?order_id=<?= $selected['id'] ?>" class="btn-plan"><i class="fas fa-calendar-plus"></i> Lập kế hoạch sản xuất</a>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="card">
                    <h2 style="color:#0f172a; border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-top:0; font-size: 1.25rem;">
                        Danh Sách Đơn Hàng (<?= count($orders) ?>)
                    </h2>
                    
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Hình ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th style="text-align:center">Size</th>
                                    <th style="text-align:center">Số lượng</th>
                                    <th>Khách Hàng</th>
                                    <th>Ngày Giao</th>
                                    <th>Tổng Tiền</th>
                                    <th>Tác Vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($orders)): ?>
                                    <tr><td colspan="9" style="text-align:center; padding:30px; color:#94a3b8;">Chưa có đơn hàng nào.</td></tr>
                                <?php else: foreach($orders as $o): 
                                    // Xử lý tên khách hàng fallback
                                    $tenKhach = $o['ten_khach_hang'] ?? ($o['ten_user_online'] . " (Online)") ?? "Khách vãng lai";
                                ?>
                                    <tr>
                                        <td><b style="color: #3b82f6;"><?= $o['so_don_hang'] ?></b></td>
                                        
                                        <td>
                                            <?php if(isset($o['image']) && $o['image']): ?>
                                                <img src="../assets/images/<?= $o['image'] ?>" width="40" height="40" style="object-fit:cover; border-radius:4px; border:1px solid #ddd">
                                            <?php else: ?>
                                                <span style="color:#ccc; font-size:0.8rem"><i class="fas fa-image"></i></span>
                                            <?php endif; ?>
                                        </td>

                                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap" title="<?= $o['ten_san_pham'] ?>">
                                            <?= $o['ten_san_pham'] ?? 'N/A' ?>
                                        </td>

                                        <td style="text-align:center">
                                            <span class="badge st-sx"><?= $o['size'] ?? '-' ?></span>
                                        </td>

                                        <td style="text-align:center; font-weight:bold">
                                            <?= number_format($o['sl_san_pham'] ?? 0) ?>
                                        </td>

                                        <td style="font-weight: 500;"><?= $tenKhach ?></td>
                                        <td style="color:#ef4444"><?= date('d/m/Y', strtotime($o['ngay_giao_du_kien'])) ?></td>
                                        <td style="font-weight:bold"><?= number_format($o['tong_tien']) ?> đ</td>
                                        
                                        <td>
                                            <a href="?id=<?= $o['id'] ?>" class="btn-view"><i class="fas fa-eye"></i> Xem</a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
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