<?php
// FILE: view/ban_hang_form.php

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
<title>Lập Đơn Bán Hàng</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    /* === STYLE CHUNG (DARK SLATE) === */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
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
    
    .nav-link { display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px; border-radius: 8px; color: #94a3b8; font-weight: 500; transition: 0.2s; }
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
.nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
    .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
    .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }

    /* CONTENT */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
    .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 70px; }
    .page-title { margin: 0; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
    .content-body { flex: 1; overflow-y: auto; padding: 30px; }

    /* FORM & CARD */
    .card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    h3 { border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; color: #334155; font-size: 1.1rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 20px; }
    label { display: block; font-weight: 600; color: #475569; margin-bottom: 8px; font-size: 0.9rem; }
    input, select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; }
    input:focus, select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    input[readonly] { background-color: #f8fafc; color: #64748b; }

    /* TABLE */
    table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
    th { background: #f8fafc; color: #475569; padding: 12px; text-align: left; font-weight: 700; font-size: 0.85rem; border-bottom: 1px solid #e2e8f0; }
    td { padding: 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    /* BUTTONS */
    .btn-submit { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
        color: white; border: none; padding: 12px 30px; border-radius: 8px; 
        font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 1rem;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); float: right; margin-top: 20px;
        display: flex; align-items: center; gap: 10px;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3); }

    .btn-add { background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; margin-top: 10px; }
    .btn-add:hover { background: #2563eb; }
    .btn-remove { color: #ef4444; border: none; background: none; cursor: pointer; font-size: 1.1rem; }
    .btn-remove:hover { color: #dc2626; }
</style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
<div class="sidebar-brand"><i class="fas fa-cut"></i> <span class="text-hide">APPMASTERS</span></div>
        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">Kinh Doanh</div>
        </div>
        <nav style="flex:1; padding-top:10px">
            <a href="BanHangController.php" class="nav-link active"><i class="fas fa-file-invoice-dollar"></i> <span class="text-hide">Lập Đơn Bán Hàng</span></a>
            <a href="LapDonDatHangController.php" class="nav-link"><i class="fas fa-shopping-cart"></i> <span class="text-hide">Lập Đơn Nhập (NCC)</span></a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:15px">
                <i class="fas fa-bars" id="sidebarToggle" style="cursor:pointer; color:#64748b; font-size:1.2rem"></i>
                <h2 class="page-title">Lập Đơn Bán Hàng (Sales Order)</h2>
            </div>
            <div style="color:#64748b"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            <div class="card">
                <form action="BanHangController.php" method="POST" onsubmit="return confirm('Xác nhận tạo đơn hàng này?');">
                    <input type="hidden" name="btn_luu_ban" value="1">

                    <h3><i class="fas fa-user-tag" style="color:#3b82f6; margin-right:8px"></i> 1. Thông tin khách hàng</h3>
                    <div class="form-row">
                        <div><label>Tên khách hàng <span style="color:red">*</span></label><input type="text" name="ten_kh" required placeholder="VD: Công ty ABC..."></div>
                        <div><label>Mã khách hàng (nếu có)</label><input type="text" name="ma_kh" placeholder="VD: KH001"></div>
                    </div>
                    <div class="form-row">
                        <div><label>Người liên hệ</label><input type="text" name="nguoi_lien_he" placeholder="Họ tên người nhận..."></div>
                        <div><label>SĐT / Email</label><input type="text" name="sdt_email" placeholder="0909xxxxxx"></div>
                    </div>
                    <div style="margin-bottom:20px"><label>Địa chỉ</label><input type="text" name="dia_chi" placeholder="Địa chỉ xuất hóa đơn..."></div>

                    <h3><i class="fas fa-file-alt" style="color:#3b82f6; margin-right:8px"></i> 2. Thông tin đơn hàng</h3>
                    <div class="form-row">
                        <div><label>Số đơn hàng</label><input type="text" name="so_don_hang" value="SO-<?= date('YmdHis') ?>" readonly></div>
<div><label>Ngày lập</label><input type="date" name="ngay_lap" value="<?= date('Y-m-d') ?>"></div>
                    </div>
                    <div class="form-row">
                        <div><label>Ngày giao dự kiến</label><input type="date" name="ngay_giao" required></div>
                        <div>
                            <label>Điều khoản thanh toán</label>
                            <select name="dieu_khoan_tt">
                                <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                                <option value="CK">Chuyển khoản ngân hàng</option>
                                <option value="CongNo">Công nợ (30 ngày)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label>Phương thức vận chuyển</label>
                            <select name="phuong_thuc_vc">
                                <option value="XeTai">Xe tải công ty</option>
                                <option value="XeMay">Xe máy (Giao gấp)</option>
                                <option value="DichVu">Dịch vụ vận chuyển ngoài</option>
                            </select>
                        </div>
                        <div><label>Địa điểm giao hàng</label><input type="text" name="dia_diem_giao" placeholder="Nhập địa chỉ giao hàng..."></div>
                    </div>

                    <h3><i class="fas fa-box-open" style="color:#3b82f6; margin-right:8px"></i> 3. Chi tiết sản phẩm</h3>
                    <table id="tblSP">
                        <thead>
                            <tr>
                                <th>Tên Sản Phẩm / Dịch Vụ</th>
                                <th width="12%">Size</th> 
                                <th width="15%">Số Lượng</th>
                                <th width="20%">Đơn Giá (VNĐ)</th>
                                <th width="5%" style="text-align:center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="ten_sp[]" required placeholder="Nhập tên sản phẩm..."></td>
                                <td>
                                    <select name="size[]" style="width:100%">
                                        <option value="S">S</option><option value="M">M</option><option value="L">L</option>
                                        <option value="XL">XL</option><option value="XXL">XXL</option><option value="Free">Free</option>
                                    </select>
                                </td>
                                <td><input type="number" name="so_luong[]" value="1" min="1" style="text-align:center"></td>
<td><input type="number" name="don_gia[]" placeholder="0"></td>
                                <td style="text-align:center">
                                    <button type="button" class="btn-remove" onclick="this.closest('tr').remove()"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <button type="button" onclick="themDong()" class="btn-add"><i class="fas fa-plus"></i> Thêm dòng</button>

                    <div style="margin-top:30px; border-top:1px solid #e2e8f0; padding-top:20px; overflow:hidden">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> LƯU & TẠO ĐƠN HÀNG
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

    // Thêm dòng bảng
    function themDong(){
        var tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="ten_sp[]" placeholder="Nhập tên sản phẩm..." required></td>
            <td>
                <select name="size[]" style="width:100%">
                    <option value="S">S</option><option value="M">M</option><option value="L">L</option>
                    <option value="XL">XL</option><option value="XXL">XXL</option><option value="Free">Free</option>
                </select>
            </td>
            <td><input type="number" name="so_luong[]" value="1" min="1" style="text-align:center"></td>
            <td><input type="number" name="don_gia[]" placeholder="0"></td>
            <td style="text-align:center">
                <button type="button" class="btn-remove" onclick="this.closest('tr').remove()"><i class="fas fa-trash-alt"></i></button>
            </td>
        `;
        document.getElementById('tblSP').getElementsByTagName('tbody')[0].appendChild(tr);
    }
</script>

</body>
</html>
