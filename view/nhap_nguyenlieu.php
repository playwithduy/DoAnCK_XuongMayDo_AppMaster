<?php
// FILE: view/nhap_nguyenlieu.php

if (!isset($data) || !is_array($data)) {
    header("Location: ../controllers/NhapNguyenLieuController.php");
    exit;
}

$user      = $data['user'] ?? ['username' => ''];
$suppliers = $data['suppliers'] ?? [];
$history   = $data['history'] ?? [];
$username  = $user['full_name'] ?? ($user['username'] ?? 'Thủ Kho');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập Kho Nguyên Liệu</title>
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
        .grid{display:grid;grid-template-columns:3fr 2fr;gap:25px}
        @media(max-width:1024px){.grid{grid-template-columns:1fr}}

        .card{background:#fff;border-radius:14px;padding:25px;box-shadow:0 8px 25px rgba(0,0,0,.08)}
        
        label{font-weight:600;margin-top:14px;display:block;color:#333}
        input,select,textarea{width:100%;padding:12px;border-radius:8px;border:1px solid #ccc;margin-top:6px;font-family:inherit}
        
        .btn{padding:12px 22px;border:none;border-radius:8px;font-weight:600;cursor:pointer; transition:0.2s}
        .save{background:#fbc02d; color:#333}
        .save:hover{background:#f9a825}
        .add{background:#1976D2;color:#fff;margin-top:8px; font-size:0.9rem}
        .add:hover{background:#1565C0}
        
        table{width:100%;border-collapse:collapse; margin-top:15px}
        th,td{padding:10px;border-bottom:1px solid #ddd; text-align:left; font-size:0.95rem}
        th{background:#f3f5f7; color:#555}

        /* MODAL */
        .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);align-items:center;justify-content:center;z-index:1000}
        .modal-box{background:#fff;padding:25px;border-radius:14px;width:420px; box-shadow:0 10px 40px rgba(0,0,0,0.2)}
        .modal-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:20px}
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
                <h2 class="page-title">Nhập Kho Nguyên Liệu</h2>
            </div>
            <div style="color: #64748b; font-weight: 500;">
                <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body">
            <div class="grid">

                <div class="card">
                    <form method="post" action="../controllers/NhapNguyenLieuController.php" id="nhapForm">

                        <label>Nhà cung cấp</label>
                        <select name="supplier" id="supplierSelect" required>
                            <?php foreach ($suppliers as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach ?>
                        </select>

                        <button type="button" class="btn add" onclick="openSupplierModal()">
    ➕ Thêm nhà cung cấp
</button>

                        <label>Tên nguyên liệu</label>
                        <input name="ten_nl" id="ten_nl" required>

                        <label>Loại nguyên liệu</label>
                        <input name="loai_nl" id="loai_nl" required>

                        <label>Số lượng</label>
                        <input type="number" name="soluong" id="soluong" min="1" required>

                        <label>Đơn giá</label>
                        <input type="number" name="dongia" id="dongia" min="1" required>

                        <label>Ghi chú</label>
                        <textarea name="note"></textarea>

                        <button type="button" class="btn save" onclick="openConfirmModal()" style="margin-top:20px; width:100%">💾 Lưu Phiếu Nhập</button>
                        <input type="hidden" name="confirm_nhap" value="1">
                    </form>
                </div>

                <div class="card">
                    <h3 style="margin-top:0; color:#333">Lịch Sử Nhập Gần Đây</h3>
                    <table>
                        <tr>
                            <th>Ngày</th><th>NCC</th><th>Tên</th><th>Loại</th>
                            <th>SL</th><th>Người nhập</th>
                        </tr>

                        <?php if (empty($history)): ?>
                            <tr><td colspan="6" style="text-align:center;color:#777">Chưa có dữ liệu</td></tr>
                        <?php else: foreach ($history as $h): ?>
                            <tr>
                                <td><?= date('d/m', strtotime($h['ngay_nhap'])) ?></td>
                                <td><?= htmlspecialchars($h['supplier']) ?></td>
                                <td><?= htmlspecialchars($h['name']) ?></td>
                                <td><?= htmlspecialchars($h['loai']) ?></td>
                                <td style="font-weight:bold"><?= $h['so_luong'] ?></td>
                                <td><?= htmlspecialchars($h['username']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal" id="supplierModal">
    <div class="modal-box">
        <h3 style="margin-top:0">➕ Thêm Nhà Cung Cấp</h3>

        <label>Tên nhà cung cấp</label>
        <input type="text" id="supplierName">

        <label>Số điện thoại</label>
        <input type="text" id="supplierPhone">

        <label>Địa chỉ</label>
        <textarea id="supplierAddress"></textarea>

        <div class="modal-actions">
            <button class="btn" onclick="closeSupplierModal()" style="background:#ddd">Hủy</button>
            <button class="btn save" onclick="saveSupplier()">Lưu</button>
        </div>
    </div>
</div>
<script>
const supplierModal = document.getElementById("supplierModal");

function openSupplierModal(){
    supplierModal.style.display = "flex";
}

function closeSupplierModal(){
    supplierModal.style.display = "none";
}

// Demo lưu NCC (AJAX)
function saveSupplier(){
    const name = document.getElementById("supplierName").value.trim();
    const phone = document.getElementById("supplierPhone").value.trim();
    const address = document.getElementById("supplierAddress").value.trim();

    if(!name){
        alert("Vui lòng nhập tên nhà cung cấp");
        return;
    }

    fetch("../controllers/SupplierController.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, phone, address })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            // thêm NCC mới vào select
            const select = document.getElementById("supplierSelect");
            const opt = document.createElement("option");
            opt.value = data.id;
            opt.textContent = name;
            opt.selected = true;
            select.appendChild(opt);

            closeSupplierModal();
        } else {
            alert("Lỗi khi lưu nhà cung cấp");
        }
    });
}
</script>


<div class="modal" id="confirmModal">
    <div class="modal-box">
        <h3 style="margin-top:0">✅ Xác nhận nhập kho</h3>
        <p>Bạn có chắc chắn muốn lưu phiếu nhập này không?</p>
        <div class="modal-actions">
            <button class="btn" onclick="closeConfirmModal()" style="background:#ddd">Hủy</button>
            <button class="btn save" onclick="submitForm()">Xác nhận</button>
        </div>
    </div>
</div>

<script>
    const confirmModal = document.getElementById("confirmModal");

    function openConfirmModal() {
        const tenNL   = document.getElementById("ten_nl").value.trim();
        const loaiNL  = document.getElementById("loai_nl").value.trim();
        const soLuong = document.getElementById("soluong").value;
        const donGia  = document.getElementById("dongia").value;

        if (!tenNL || !loaiNL || !soLuong || !donGia) {
            alert("Bạn cần nhập đầy đủ thông tin để lưu phiếu nhập");
            return;
        }

        if (soLuong <= 0 || donGia <= 0) {
            alert("Số lượng và đơn giá phải lớn hơn 0");
            return;
        }

        // Hợp lệ → mở modal
        confirmModal.style.display = "flex";
    }

    function closeConfirmModal(){ 
        confirmModal.style.display="none"; 
    }

    function submitForm(){ 
        document.getElementById("nhapForm").submit(); 
    }
</script>

</body>
</html>