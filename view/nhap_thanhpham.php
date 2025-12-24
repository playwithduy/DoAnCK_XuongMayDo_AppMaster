<?php
// FILE: view/nhap_thanhpham.php

if (!isset($data) || !is_array($data)) {
    header("Location: ../controllers/NhapThanhPhamController.php");
    exit;
}

$user    = $data['user'] ?? ['username' => ''];
$history = $data['history'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Thủ Kho');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nhập Kho Thành Phẩm</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    /* === FONT & RESET === */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
    * { box-sizing: border-box; outline: none; text-decoration: none; }
    
    .wrapper { display: flex; height: 100vh; overflow: hidden; }

    /* ====================================================== */
    /* === PHẦN 1: SIDEBAR HIỆN ĐẠI (DARK SLATE) === */
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
    /* === PHẦN 2: CSS NỘI DUNG CŨ (GIỮ NGUYÊN) === */
    /* ====================================================== */
    .grid{display:grid;grid-template-columns:3fr 2fr;gap:25px}
    @media(max-width:1024px){.grid{grid-template-columns:1fr}}

    .card{ background:#fff;border-radius:14px;padding:25px; box-shadow:0 8px 25px rgba(0,0,0,.08); margin-bottom: 20px; }
    .card h2, .card h3 { text-align:center; margin-top:0; color:#333; font-size: 1.2rem; }

    label{font-weight:600;margin-top:14px;display:block;color:#333}
    input,select,textarea{ width:100%;padding:12px;border-radius:8px; border:1px solid #ccc;margin-top:6px; font-family: inherit; }
    
    .btn{ padding:8px 14px;border:none;border-radius:8px; font-weight:600;cursor:pointer; transition:0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 5px; }
    .btn:hover { transform: translateY(-1px); }
    
    .save{background:#10b981;color:#fff; width: 100%; padding: 12px; margin-top: 20px; font-size: 1rem;}
    .save:hover{background:#059669;}
    
    .btn-icon { background: #f1f5f9; color: #334155; padding: 6px 10px; }
    .btn-icon:hover { background: #e2e8f0; }
    
    table{width:100%;border-collapse:collapse; margin-top: 15px;}
    th,td{padding:12px 10px;border-bottom:1px solid #ddd;text-align:left; font-size: 0.95rem;}
    th{background:#f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.8rem;}

    /* MODAL */
    .modal{ display:none;position:fixed;inset:0; background:rgba(0,0,0,.45); align-items:center;justify-content:center; z-index:1000; }
    .modal-box{ background:#fff;padding:25px; border-radius:14px;width:480px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    .modal-actions{ display:flex;justify-content:flex-end; gap:12px;margin-top:20px; }
    .modal h3 { margin-top: 0; color: #333; }
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
                <h2 class="page-title">Nhập Kho Thành Phẩm</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;">
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">
            <div class="grid">

                <div class="card">
                    <h2 style="text-align: left; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <i class="fas fa-edit" style="color: #3b82f6;"></i> Tạo Phiếu Nhập
                    </h2>

                    <form method="post" action="../controllers/NhapThanhPhamController.php" id="nhapForm">

                        <label>Tên thành phẩm</label>
                        <input name="ten_tp" placeholder="VD: Áo thun cổ tròn" required>

                        <label>Nhập từ xưởng</label>
                        <select name="xuong" required>
                            <option value="">-- Chọn xưởng --</option>
                            <?php for($i=1;$i<=5;$i++): ?>
                                <option value="Xưởng <?= $i ?>">Xưởng <?= $i ?></option>
                            <?php endfor ?>
                        </select>

                        <label>Số lượng nhập kho</label>
                        <input type="number" name="so_luong" min="1" required>

                        <label>Ghi chú (Kiểm tra chất lượng)</label>
                        <textarea name="note" rows="3" placeholder="Ghi chú về lô hàng..."></textarea>

                        <button type="button" class="btn save" onclick="validateAndConfirm()">
    <i class="fas fa-save"></i> Lưu Phiếu Nhập
</button>


                        <input type="hidden" name="confirm_nhap" value="1">
                    </form>
                </div>

                <div class="card">
                    <h3 style="text-align: left; font-size: 1.1rem; color: #333;">
                        <i class="fas fa-history" style="color: #f59e0b;"></i> Lịch Sử Nhập Gần Đây
                    </h3>

                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Tên TP</th>
                                    <th>Xưởng</th>
                                    <th>SL</th>
                                    <th>Người nhập</th>
                                    <th style="text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($history)): ?>
                                    <tr>
                                        <td colspan="6" style="color:#777; text-align: center; padding: 20px;">Chưa có dữ liệu</td>
                                    </tr>
                                <?php else: foreach ($history as $h): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($h['ngay_nhap'])) ?></td>
                                        <td style="font-weight: 600; color: #334155;"><?= htmlspecialchars($h['ten_tp']) ?></td>
                                        <td><?= htmlspecialchars($h['xuong']) ?></td>
                                        <td style="font-weight: 700; color: #10b981;"><?= number_format($h['so_luong']) ?></td>
                                        <td><?= htmlspecialchars($h['username']) ?></td>
                                        <td style="text-align: center;">
                                            <button class="btn btn-icon" title="Xem chi tiết" onclick="viewPhieu(
                                                '<?= htmlspecialchars($h['ten_tp']) ?>',
                                                '<?= htmlspecialchars($h['xuong']) ?>',
                                                '<?= $h['so_luong'] ?>',
                                                '<?= htmlspecialchars($h['note']) ?>'
                                            )"><i class="fas fa-search"></i></button>

                                            <a class="btn btn-icon" title="In phiếu" href="../controllers/InPhieuNhapTP.php?id=<?= $h['id'] ?>"><i class="fas fa-print"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal" id="confirmModal">
    <div class="modal-box">
        <h3>✅ Xác nhận nhập kho</h3>
        <p>Bạn có chắc chắn muốn lưu phiếu nhập thành phẩm này không?</p>
        <div class="modal-actions">
            <button class="btn" onclick="closeConfirmModal()" style="background: #e2e8f0; color: #333;">Hủy</button>
            <button class="btn" style="background: #10b981; color: white;" onclick="submitForm()">Xác nhận</button>
        </div>
    </div>
</div>

<div class="modal" id="detailModal">
    <div class="modal-box">
        <h3>📄 Chi tiết phiếu nhập</h3>
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <p><b>Thành phẩm:</b> <span id="d_tp" style="color: #3b82f6; font-weight: bold;"></span></p>
            <p><b>Xưởng:</b> <span id="d_xuong"></span></p>
            <p><b>Số lượng:</b> <span id="d_sl" style="font-weight: bold;"></span></p>
            <p><b>Ghi chú:</b> <span id="d_note" style="font-style: italic; color: #666;"></span></p>
        </div>
        <p><b>Người nhập kho:</b> ✍️ <?= htmlspecialchars($user['username']) ?></p>

        <div class="modal-actions">
            <button class="btn" onclick="closeDetail()" style="background: #3b82f6; color: white; width: 100%;">Đóng</button>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    const confirmModal = document.getElementById("confirmModal");
    const detailModal = document.getElementById("detailModal");

    function openConfirmModal(){ confirmModal.style.display="flex"; }
    function closeConfirmModal(){ confirmModal.style.display="none"; }
    function submitForm(){ document.getElementById("nhapForm").submit(); }

    function viewPhieu(tp,xuong,sl,note){
        document.getElementById('d_tp').innerText = tp;
        document.getElementById('d_xuong').innerText = xuong;
        document.getElementById('d_sl').innerText = sl;
        document.getElementById('d_note').innerText = note || 'Không có ghi chú';
        detailModal.style.display="flex";
    }
    function closeDetail(){ detailModal.style.display="none"; }
</script>
<script>
function validateAndConfirm() {
    const tenTP   = document.querySelector('input[name="ten_tp"]').value.trim();
    const xuong   = document.querySelector('select[name="xuong"]').value;
    const soLuong = document.querySelector('input[name="so_luong"]').value;

    if (tenTP === "" || xuong === "" || soLuong === "") {
        alert("Vui lòng nhập đầy đủ thông tin!");
        return;
    }

    if (!Number.isInteger(Number(soLuong)) || Number(soLuong) < 1) {
        alert("Số lượng phải là số nguyên và lớn hơn 0!");
        return;
    }

    openConfirmModal();
}
</script>


</body>
</html>