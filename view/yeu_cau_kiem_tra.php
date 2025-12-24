<?php
// FILE: view/yeu_cau_kiem_tra.php

if (!isset($lots)) {
    header("Location: ../controllers/YeuCauKiemTraController.php");
    exit;
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Xưởng Trưởng');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi Yêu Cầu Kiểm Tra (Final QC)</title>
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
        .content-body { flex: 1; overflow-y: auto; padding: 30px; }

        /* === FORM STYLE === */
        .card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-width: 850px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; color: #475569; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; transition: 0.2s; }
        .form-control:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1); }

        /* INFO BOX */
        .info-box { background: #f0f9ff; border: 1px solid #bae6fd; padding: 20px; border-radius: 8px; display: flex; gap: 20px; align-items: flex-start; margin-bottom: 25px; animation: fadeIn 0.3s; }
        .info-content { flex: 1; }
        .info-row { margin: 8px 0; color: #334155; font-size: 0.95rem; display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 5px; }
        .info-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; }
        
        /* BUTTONS */
        .btn-submit { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; width: 100%; display: flex; justify-content: center; align-items: center; gap: 10px; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 10px -1px rgba(16, 185, 129, 0.3); }
        .btn-submit:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none; }

        /* ALERTS */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 12px; margin-top: 20px; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
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
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Xưởng Trưởng</div>
        </div>
        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="XuongTruongDashboardController.php" class="nav-link"><i class="fas fa-home"></i> <span class="text-hide">Dashboard</span></a>
            
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="xulychamcong.php" class="nav-link"><i class="fas fa-user-check"></i> <span class="text-hide">Chấm Công</span></a>
            <a href="YeuCauNguyenLieuController.php" class="nav-link"><i class="fas fa-box-open"></i> <span class="text-hide">Yêu Cầu Nguyên Liệu</span></a>
            <a href="YeuCauKiemTraController.php" class="nav-link active"><i class="fas fa-tasks"></i> <span class="text-hide">Gửi QA Kiểm Tra</span></a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span>
        </a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:15px">
                <i class="fas fa-bars" id="sidebarToggle" style="cursor:pointer; color:#64748b; font-size:1.2rem"></i>
                <h2 class="page-title">Gửi Yêu Cầu Kiểm Tra (Final QC)</h2>
            </div>
            <div style="color:#64748b; font-weight:500"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            
            <?php if ($msg == 'success'): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> Đã gửi yêu cầu kiểm tra thành công!</div>
            <?php elseif ($msg == 'error'): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra, vui lòng thử lại.</div>
            <?php endif; ?>

            <div class="card">
                <form method="POST" action="YeuCauKiemTraController.php" onsubmit="return validateForm()">
                    
                    <div class="form-group">
                        <label>Chọn Lô Sản Phẩm (Đã SX / Khách đặt) <span style="color:red">*</span></label>
                        <select name="ke_hoach_id" id="ke_hoach_id" class="form-control" onchange="loadDetail()" required>
                            <option value="">-- Chọn lô hàng --</option>
                            <?php if ($lots): while ($l = $lots->fetch_assoc()): 
                                // Tính % hoàn thành
                                $slKhach = $l['sl_khach_dat'] > 0 ? $l['sl_khach_dat'] : 1;
                                $tongDaLam = $l['tong_da_lam'] ?? 0;
                                $percent = round(($tongDaLam / $slKhach) * 100);
                            ?>
                                <option value="<?= $l['id'] ?>">
                                    [<?= $l['ma_ke_hoach'] ?>] <?= $l['ten_san_pham'] ?> 
                                    (Đã làm: <?= number_format($tongDaLam) ?>/<?= number_format($slKhach) ?>) - <?= $percent ?>%
                                </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>

                    <div id="detail-wrapper" style="display:none; animation: fadeIn 0.3s ease;">
                        <div class="info-box">
                            <div style="flex-shrink:0;">
                                <img id="info-img" src="" class="info-img" style="display:none">
                                <div id="no-img" class="info-img" style="display:flex; align-items:center; justify-content:center; color:#94a3b8; background:#fff; border:1px solid #e2e8f0;">
                                    <i class="fas fa-image fa-2x"></i>
                                </div>
                            </div>
                            <div class="info-content">
                                <div class="info-row"><span>Sản phẩm:</span> <b id="info-sp" style="color:#0f172a"></b></div>
                                <div class="info-row"><span>Size:</span> <span id="info-size" style="background:#fff; padding:2px 8px; border-radius:4px; border:1px solid #cbd5e1; font-weight:bold"></span></div>
                                <div class="info-row"><span>Đơn hàng gốc:</span> <span id="info-dh"></span></div>
                                <div class="info-row"><span>Mã kế hoạch:</span> <span id="info-kh"></span></div>
                                <div class="info-row" style="border:none; margin-top:10px; background:#fff; padding:10px; border-radius:6px;">
                                    <span>Thực tế đã làm (Đóng gói):</span> 
                                    <span id="info-progress" style="color:#10b981; font-weight:bold; font-size:1.1rem">0 cái</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Số lượng gửi kiểm tra <span style="color:red">*</span></label>
                            <input type="number" name="so_luong" id="input-sl" class="form-control" min="1" required>
                            <small style="color:#64748b; margin-top:5px; display:block">
                                * Số lượng này được gợi ý từ hệ thống chấm công. Bạn có thể sửa nếu cần test các trường hợp đặc biệt.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Ghi chú (Tình trạng lô hàng)</label>
                            <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Ví dụ: Kiểm tra gấp để xuất hàng..."></textarea>
                        </div>

                        <button type="submit" name="save_request" id="btn-submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> GỬI YÊU CẦU NGHIỆM THU
                        </button>
                    </div>

                    <div id="empty-state" class="empty-state">
                        <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:15px; display:block; color:#cbd5e1"></i>
                        <p>Vui lòng chọn <b>Lô Sản Phẩm</b> ở trên để bắt đầu.</p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // Validate
    function validateForm() {
        const sl = document.getElementById('input-sl').value;
        if (sl <= 0) {
            alert("Số lượng gửi kiểm tra phải lớn hơn 0!");
            return false;
        }
        return confirm("Xác nhận gửi phiếu yêu cầu kiểm tra?");
    }

    // Load Data AJAX
    function loadDetail() {
        const id = document.getElementById('ke_hoach_id').value;
        const wrapper = document.getElementById('detail-wrapper');
        const emptyState = document.getElementById('empty-state');
        const btnSubmit = document.getElementById('btn-submit');

        if (!id) {
            wrapper.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        fetch(`YeuCauKiemTraController.php?action=get_detail&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data) {
                    emptyState.style.display = 'none';
                    wrapper.style.display = 'block';

                    document.getElementById('info-sp').innerText = data.ten_san_pham;
                    document.getElementById('info-size').innerText = data.size || 'Free';
                    document.getElementById('info-dh').innerText = data.so_don_hang;
                    document.getElementById('info-kh').innerText = data.ma_ke_hoach;
                    
                    const daLam = parseInt(data.da_san_xuat) || 0;
                    const khachDat = parseInt(data.sl_khach_dat) || 1;
                    document.getElementById('info-progress').innerText = `${daLam} / ${khachDat} cái`;

                    // Hình ảnh
                    const img = document.getElementById('info-img');
                    const noImg = document.getElementById('no-img');
                    if(data.image) {
                        img.src = '../assets/images/' + data.image;
                        img.style.display = 'block';
                        noImg.style.display = 'none';
                    } else {
                        img.style.display = 'none';
                        noImg.style.display = 'flex';
                    }

                    // Tự động điền số lượng
                    const inputSl = document.getElementById('input-sl');
                    inputSl.value = daLam;

                    // Logic Testcase:
                    // Nếu SL = 0 (chưa chấm công xong), vẫn cho hiện form nhưng cảnh báo nhẹ
                    // Để bạn có thể sửa số lượng > 0 và test chức năng gửi
                    if (daLam === 0) {
                        btnSubmit.innerHTML = "<i class='fas fa-exclamation-triangle'></i> GỬI (CẢNH BÁO: SL=0)";
                        btnSubmit.style.background = "#f59e0b"; // Màu cam cảnh báo
                    } else {
                        btnSubmit.innerHTML = "<i class='fas fa-paper-plane'></i> GỬI YÊU CẦU NGHIỆM THU";
                        btnSubmit.style.background = ""; // Reset
                    }
                }
            })
            .catch(err => console.error(err));
    }
</script>

</body>
</html>