<?php
// FILE: view/sales_po_form.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Lấy thông tin user
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Nhân Viên KD');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lập Đơn Nhập Hàng (NCC)</title>
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
        text-transform: uppercase; letter-spacing: 1px; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        display: flex; align-items: center; gap: 10px;
    }
    .sidebar-brand i { color: #38bdf8; }

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

    /* CARD & FORM STYLE */
    .card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    
    h3 { border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; color: #334155; font-size: 1.1rem; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; }
    
    label { display: block; font-weight: 600; color: #475569; margin-bottom: 8px; font-size: 0.9rem; }
    input, select { 
        width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; 
        font-family: inherit; font-size: 0.95rem; transition: border-color 0.2s;
    }
    input:focus, select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    input[readonly] { background-color: #f8fafc; color: #64748b; }

    /* TABLE STYLE */
    .table-products { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
    .table-products th { background: #f8fafc; color: #475569; padding: 12px; text-align: left; font-weight: 700; font-size: 0.85rem; border-bottom: 1px solid #e2e8f0; }
    .table-products td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    /* BUTTONS */
    .btn-submit { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
        color: white; border: none; padding: 12px 30px; border-radius: 8px; 
        font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 1rem; margin-top: 20px;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 10px rgba(16, 185, 129, 0.3); }

    .btn-add {
        background: #3b82f6; color: white; border: none; padding: 8px 16px; 
        border-radius: 6px; cursor: pointer; font-size: 0.9rem; font-weight: 500;
    }
    .btn-add:hover { background: #2563eb; }

    .btn-remove { color: #ef4444; border: none; background: none; cursor: pointer; font-size: 1.1rem; }
    .btn-remove:hover { color: #dc2626; }
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
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Kinh Doanh</div>
        </div>

        <nav style="flex: 1; padding-top: 10px;">
            <a href="BanHangController.php" class="nav-link">
                <i class="fas fa-file-invoice-dollar"></i> <span class="text-hide">Lập Đơn Bán Hàng (KH)</span>
            </a>
            
            <a href="LapDonDatHangController.php" class="nav-link active">
                <i class="fas fa-shopping-cart"></i> <span class="text-hide">Lập Đơn Nhập (NCC)</span>
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
                <h2 class="page-title">Lập Đơn Nhập Hàng (NCC)</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;">
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <h2 style="color:#0f172a; margin-top:0; font-size:1.5rem; margin-bottom:20px">📦 Lập Đơn Đặt Hàng Nhà Cung Cấp (PO)</h2>
                
                <form action="LapDonDatHangController.php" method="POST">
                    <input type="hidden" name="btn_luu_don" value="1">

                    <h3><i class="fas fa-info-circle" style="color:#3b82f6; margin-right:8px"></i> 1. Thông tin chung</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nhà Cung Cấp:</label>
                            <select name="nha_cung_cap_id" required>
                                <option value="">-- Chọn NCC --</option>
                                <?php if(!empty($ds_ncc)): foreach($ds_ncc as $ncc): ?>
                                    <option value="<?= $ncc['id'] ?>">
                                        <?= htmlspecialchars($ncc['ten_ncc']) ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mã đơn hàng (PO):</label>
                            <input type="text" name="ma_don_hang" value="PO-<?= date('YmdHis') ?>" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Ngày lập đơn:</label>
                            <input type="date" name="ngay_lap" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label>Ngày nhận dự kiến:</label>
                            <input type="date" name="ngay_nhan_du_kien" required>
                        </div>
                    </div>

                    <h3><i class="fas fa-boxes" style="color:#3b82f6; margin-right:8px"></i> 2. Chi tiết hàng hóa nhập</h3>
                    <table class="table-products" id="tblPO">
                        <thead>
                            <tr>
                                <th>Tên Vật Tư / Hàng Hóa</th>
                                <th width="15%">Số Lượng</th>
                                <th width="20%">Đơn Giá Nhập</th>
                                <th width="5%" style="text-align:center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="ten_sp[]" placeholder="Nhập tên hàng..." required></td>
                                <td><input type="number" name="so_luong[]" value="1" min="1"></td>
                                <td><input type="number" name="don_gia[]" value="0" min="0"></td>
                                <td style="text-align:center">
                                    <button type="button" class="btn-remove" onclick="this.closest('tr').remove()"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <button type="button" class="btn-add" onclick="addRow()"><i class="fas fa-plus"></i> Thêm dòng</button>

                    <div style="text-align:center; margin-top:30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> LƯU ĐƠN NHẬP</button>
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

    // Thêm dòng bảng
    function addRow() {
        var table = document.getElementById("tblPO").getElementsByTagName('tbody')[0];
        var row = table.insertRow(table.rows.length);
        row.innerHTML = `
            <td><input type="text" name="ten_sp[]" placeholder="Nhập tên hàng..."></td>
            <td><input type="number" name="so_luong[]" value="1" min="1"></td>
            <td><input type="number" name="don_gia[]" value="0" min="0"></td>
            <td style="text-align:center">
                <button type="button" class="btn-remove" onclick="this.closest('tr').remove()"><i class="fas fa-trash-alt"></i></button>
            </td>
        `;
    }
</script>

</body>
</html>