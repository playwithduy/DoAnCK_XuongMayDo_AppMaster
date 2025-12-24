<?php
// FILE: view/yeu_cau_nguyen_lieu.php

// 1. CHECK AUTH & DATA
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'xuongtruong') {
    // header("Location: ../login.php"); 
}

// Data from Controller
$plans = $data['plans'] ?? null; 
$msg = $data['msg'] ?? $_GET['msg'] ?? '';

$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Xưởng Trưởng');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lập Phiếu Yêu Cầu Nguyên Liệu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        }
        .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; padding: 5px; }
        .page-title { margin: 0 0 0 15px; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
        
        .content-body { flex: 1; overflow-y: auto; padding: 30px; }

        /* CARD STYLE */
        .card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        
        /* FORM ELEMENTS */
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; color: #475569; margin-bottom: 8px; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; }
        .form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        
        /* TABLE STYLE */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f8fafc; padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
        
        /* ALERTS & BUTTONS */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .stock-warning { color: #ef4444; font-size: 0.85rem; display: none; margin-top: 4px; font-weight: 600; }
        
        .btn-submit { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; border: none; padding: 12px 24px; border-radius: 8px; 
            font-weight: 600; cursor: pointer; float: right; margin-top: 20px;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); transition: 0.2s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 10px -1px rgba(16, 185, 129, 0.3); }
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
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Xưởng Trưởng</div>
        </div>

        <nav style="flex: 1; padding-top: 10px;">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../controllers/XuongTruongDashboardController.php" class="nav-link">
                <i class="fas fa-home"></i> <span class="text-hide">Dashboard</span>
            </a>
            
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="../controllers/xulychamcong.php" class="nav-link">
                <i class="fas fa-user-check"></i> <span class="text-hide">Chấm Công</span>
            </a>
            
            <a href="../controllers/YeuCauNguyenLieuController.php" class="nav-link active">
                <i class="fas fa-box-open"></i> <span class="text-hide">Yêu Cầu Nguyên Liệu</span>
            </a>
            
            <a href="../controllers/YeuCauKiemTraController.php" class="nav-link">
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
                <div class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
                <h2 class="page-title">Lập Phiếu Yêu Cầu Nguyên Liệu</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;">
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">
            
            <?php if ($msg == 'success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Lập phiếu yêu cầu thành công! Đang chờ duyệt.
                </div>
            <?php elseif ($msg == 'error'): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra, vui lòng thử lại.
                </div>
            <?php endif; ?>

            <div class="card">
                <form method="POST" action="../controllers/YeuCauNguyenLieuController.php">
                    <div class="form-group">
                        <label>Chọn Kế Hoạch Sản Xuất</label>
                        <select name="ke_hoach_id" id="ke_hoach_id" class="form-control" required onchange="loadMaterials()">
                            <option value="">-- Chọn kế hoạch cần nguyên liệu --</option>
                            <?php if ($plans): 
                                while ($p = $plans->fetch_assoc()): 
                                    $displayText = $p['ma_ke_hoach'];
                                    if (!empty($p['ten_san_pham'])) {
                                        $displayText .= " - " . htmlspecialchars($p['ten_san_pham']);
                                    }
                                    if (!empty($p['ma_dh'])) {
                                        $displayText .= " (" . htmlspecialchars($p['ma_dh']) . ")";
                                    }
                            ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= $displayText ?> (Bắt đầu: <?= date('d/m/Y', strtotime($p['ngay_bat_dau'])) ?>)
                                </option>
                            <?php 
                                endwhile; 
                            endif; ?>
                        </select>
                    </div>

                    <div id="material-section" style="display:none">
                        <h3 style="margin-bottom:15px; color:#334155; border-bottom:1px solid #f1f5f9; padding-bottom:10px; font-size:1.1rem;">
                            <i class="fas fa-list-ul" style="color:#3b82f6; margin-right:8px"></i> Danh sách nguyên liệu cần thiết
                        </h3>
                        
                        <div style="overflow-x:auto;">
                            <table id="material-table">
                                <thead>
                                    <tr>
                                        <th>Tên Nguyên Liệu</th>
                                        <th>Đơn vị</th>
                                        <th>Tồn kho hiện tại</th>
                                        <th>SL Cần (Dự kiến)</th>
                                        <th style="width: 180px;">SL Yêu cầu thực tế</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </tbody>
                            </table>
                        </div>

                        <div class="form-group" style="margin-top:20px">
                            <label>Ghi chú thêm</label>
                            <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Ví dụ: Cần gấp trong sáng mai..."></textarea>
                        </div>

                        <button type="submit" name="save_request" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Gửi Phiếu Yêu Cầu
                        </button>
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

    // Load Materials Logic
    function loadMaterials() {
        const id = document.getElementById('ke_hoach_id').value;
        const section = document.getElementById('material-section');
        const tbody = document.querySelector('#material-table tbody');

        if (!id) {
            section.style.display = 'none';
            return;
        }

        // Gọi AJAX (Đường dẫn tương đối tới Controller)
        fetch(`../controllers/YeuCauNguyenLieuController.php?action=get_materials&id=${id}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                
                // Kiểm tra nếu có lỗi
                if (data.error) {
                    let errorMsg = data.error;
                    if (data.product_name) {
                        errorMsg += `\n\nSản phẩm: ${data.product_name}`;
                        if (data.product_id) {
                            errorMsg += `\n\nGiải pháp: Chạy script SQL để thêm định mức:\nFile: database/add_dinh_muc_product_${data.product_id}.sql`;
                        }
                    }
                    alert(errorMsg);
                    section.style.display = 'none';
                    return;
                }
                
                // Kiểm tra nếu là mảng rỗng
                if (!Array.isArray(data) || data.length === 0) {
                    alert('Kế hoạch này không có dữ liệu sản phẩm hoặc định mức nguyên liệu!\n\nVui lòng liên hệ Quản đốc để thiết lập định mức nguyên liệu cho sản phẩm.');
                    section.style.display = 'none';
                    return;
                }
                
                // Hiển thị danh sách nguyên liệu
                data.forEach(item => {
                    const required = parseFloat(item.so_luong_can_thiet || 0).toFixed(2);
                    const stock = parseFloat(item.ton_kho || 0);
                    
                    // Kiểm tra tồn kho (Logic Alternate Flow 5.1)
                    const isLowStock = required > stock;
                    
                    const row = `
                        <tr>
                            <td style="font-weight:600; color:#334155">${item.name || 'N/A'}</td>
                            <td>${item.unit || 'N/A'}</td>
                            <td style="color:${isLowStock ? '#ef4444' : '#10b981'}; font-weight:bold">${stock}</td>
                            <td style="color:#64748b">${required}</td>
                            <td>
                                <input type="number" step="0.01" name="qty[${item.id}]" 
                                       value="${required}" class="form-control" 
                                       style="font-weight:bold; color:#0f172a;"
                                       oninput="checkStock(this, ${stock})" min="0">
                                <div class="stock-warning" ${isLowStock ? 'style="display:block"' : ''}>
                                    <i class="fas fa-exclamation-triangle"></i> Vượt quá tồn kho
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
                section.style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối server! Vui lòng thử lại.');
                section.style.display = 'none';
            });
    }

    // Hàm kiểm tra khi người dùng nhập tay
    function checkStock(input, stock) {
        const val = parseFloat(input.value);
        const warning = input.nextElementSibling;
        if (val > stock) {
            warning.style.display = 'block';
            input.style.borderColor = '#ef4444';
            input.style.color = '#ef4444';
        } else {
            warning.style.display = 'none';
            input.style.borderColor = '#cbd5e1';
            input.style.color = '#0f172a';
        }
    }
</script>

</body>
</html>