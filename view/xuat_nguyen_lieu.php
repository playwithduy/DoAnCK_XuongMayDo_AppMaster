<?php
// FILE: view/xuat_nguyen_lieu.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'thukho') {
    // header("Location: ../login.php"); 
}

// Data from Controller
$requests = $requests ?? null; 
$msg = $msg ?? '';
$err = $err ?? '';

$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Thủ Kho');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xuất Kho Nguyên Liệu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* === FONT & RESET === */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f8; color: #334155; }
        * { box-sizing: border-box; outline: none; text-decoration: none; }
        .wrapper { display: flex; height: 100vh; overflow: hidden; }

        /* === SIDEBAR (Modern Dark Slate) === */
        .sidebar { width: 260px; background: #0f172a; color: #94a3b8; display: flex; flex-direction: column; transition: 0.3s; flex-shrink: 0; border-right: 1px solid #1e293b; }
        .sidebar-brand { padding: 24px 20px; font-size: 1.25rem; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 10px; }
        .sidebar-brand i { color: #38bdf8; }
        .user-panel { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 10px; }
        .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 3px solid #1e293b; margin-bottom: 8px; }
        .nav-section { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #475569; margin: 15px 20px 5px; }
        .nav-link { display: flex; align-items: center; padding: 12px 20px; margin: 4px 12px; border-radius: 8px; color: #94a3b8; font-weight: 500; transition: 0.2s; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
        .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
        .nav-link i { width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem; }
        .logout-btn { margin-top: auto; margin-bottom: 20px; border: 1px solid #334155; background: transparent; }
        .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: white; }
        
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed .text-hide, .sidebar.collapsed .sidebar-brand span { display: none; }
        .sidebar.collapsed .nav-link { justify-content: center; padding: 12px 0; }
        .sidebar.collapsed .nav-link i { margin: 0; }

        /* === MAIN CONTENT === */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #f4f6f8; }
        .topbar { background: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.05); height: 70px; z-index: 10; }
        .toggle-btn { cursor: pointer; font-size: 1.2rem; color: #64748b; margin-right: 15px; }
        .page-title { margin: 0; font-size: 1.25rem; color: #0f172a; font-weight: 700; }
        .content-body { flex: 1; overflow-y: auto; padding: 30px; }

        /* === CUSTOM CONTENT === */
        .card { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8fafc; padding: 12px; text-align: left; font-weight: 700; color: #64748b; font-size: 0.85rem; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
        
        .btn-action { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; font-size: 0.85rem; transition: 0.2s; }
        .btn-view { background: #e0f2fe; color: #0369a1; }
        .btn-view:hover { background: #bae6fd; }

        /* MODAL */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: #fff; width: 600px; max-width: 90%; border-radius: 12px; padding: 25px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .modal-header { display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 15px; }
        .modal-title { margin: 0; font-size: 1.2rem; color: #0f172a; }
        .close-btn { cursor: pointer; font-size: 1.5rem; color: #94a3b8; }
        
        .detail-table th { background: #f1f5f9; }
        .stock-ok { color: #10b981; font-weight: 600; }
        .stock-low { color: #ef4444; font-weight: 600; }

        .modal-footer { display: flex; justify-content: space-between; margin-top: 25px; pt-3; border-top: 1px solid #f1f5f9; }
        .btn-export { background: #10b981; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .btn-reject { background: #ef4444; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        
        /* ALERTS */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>
<?php if (!empty($alertMessage)): ?>
    <div class="alert <?= $alertClass ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px;">
        <?= $alertMessage ?>
    </div>
<?php endif; ?>

<style>
    .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
</style>
<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cut"></i> <span class="text-hide">AppMasters</span>
        </div>
        <div class="user-panel text-hide">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($username) ?></div>
            <div style="font-size:0.75rem; color:#94a3b8">Thủ Kho</div>
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
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex;align-items:center">
                <div class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
                <h2 class="page-title">Xuất Kho Nguyên Liệu</h2>
            </div>
            <div style="color:#64748b"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            <?php if ($msg == 'success'): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> Xuất kho thành công! Đã trừ tồn kho.</div>
            <?php elseif ($msg == 'rejected'): ?>
                <div class="alert alert-danger"><i class="fas fa-times-circle"></i> Đã từ chối phiếu yêu cầu.</div>
            <?php elseif ($msg == 'error'): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Lỗi: <?= htmlspecialchars($err) ?></div>
            <?php endif; ?>

            <div class="card">
                <h3 style="margin-top:0; color:#0f172a; font-size:1.1rem; border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:20px;">
                    📋 Danh sách phiếu yêu cầu đang chờ
                </h3>

                <?php if ($requests && $requests->num_rows > 0): ?>
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <th>Mã Phiếu</th>
                                <th>Người Yêu Cầu</th>
                                <th>Ngày Lập</th>
                                <th>Ghi Chú</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight:700; color:#3b82f6"><?= $r['ma_phieu'] ?></td>
                                    <td><?= $r['nguoi_lap'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($r['ngay_lap'])) ?></td>
                                    <td style="color:#64748b; font-style:italic"><?= $r['ghi_chu'] ?></td>
                                    <td>
                                        <button class="btn-action btn-view" onclick="openModal(<?= $r['id'] ?>, '<?= $r['ma_phieu'] ?>')">
                                            <i class="fas fa-eye"></i> Xem & Xuất
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align:center; padding:40px; color:#94a3b8;">
                        <i class="fas fa-inbox fa-3x" style="margin-bottom:15px; opacity:0.5"></i>
                        <p>Không có phiếu yêu cầu nào đang chờ.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Chi Tiết Phiếu: <span id="modal-maphieu" style="color:#3b82f6"></span></h3>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        
        <form method="POST" action="XuatNguyenLieuController.php" id="exportForm">
            <input type="hidden" name="phieu_id" id="modal-phieu-id">
            
            <div style="max-height:300px; overflow-y:auto;">
                <table class="detail-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nguyên Liệu</th>
                            <th>ĐVT</th>
                            <th>Yêu Cầu</th>
                            <th>Tồn Kho</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody id="modal-body">
                        </tbody>
                </table>
            </div>

            <div id="reject-area" style="display:none; margin-top:15px; padding-top:15px; border-top:1px solid #eee;">
                <label style="display:block; margin-bottom:5px; font-weight:600">Lý do từ chối:</label>
                <textarea name="ly_do_tu_choi" class="form-control" style="width:100%; border:1px solid #ccc; padding:10px; border-radius:6px;" rows="2"></textarea>
                <button type="submit" name="btn_reject" class="btn-reject" style="margin-top:10px; width:100%">Xác Nhận Từ Chối</button>
                <button type="button" onclick="toggleReject(false)" style="margin-top:5px; background:transparent; border:none; color:#64748b; cursor:pointer; width:100%">Hủy</button>
            </div>

            <div class="modal-footer" id="main-footer">
                <button type="button" class="btn-reject" style="background:#fee2e2; color:#991b1b" onclick="toggleReject(true)">Từ Chối Xuất</button>
                <button type="submit" name="btn_export" id="btn-confirm-export" class="btn-export">
                    <i class="fas fa-check"></i> Xác Nhận Xuất Kho
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Sidebar Toggle
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // Modal Logic
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('modal-body');
    const rejectArea = document.getElementById('reject-area');
    const mainFooter = document.getElementById('main-footer');
    const btnExport = document.getElementById('btn-confirm-export');

    function openModal(id, maPhieu) {
        document.getElementById('modal-maphieu').innerText = maPhieu;
        document.getElementById('modal-phieu-id').value = id;
        
        // Reset View
        toggleReject(false);
        modalBody.innerHTML = '<tr><td colspan="5" style="text-align:center">Đang tải dữ liệu...</td></tr>';
        modal.style.display = 'flex';

        // AJAX Fetch Detail
        fetch(`XuatNguyenLieuController.php?action=get_detail&id=${id}`)
            .then(res => res.json())
            .then(data => {
                let html = '';
                let canExport = true;

                data.forEach(item => {
                    const req = parseFloat(item.so_luong_yeu_cau);
                    const stock = parseFloat(item.ton_kho);
                    const enough = stock >= req;
                    if (!enough) canExport = false;

                    html += `
                        <tr>
                            <td>${item.ten_nl}</td>
                            <td>${item.unit}</td>
                            <td style="font-weight:bold">${req}</td>
                            <td>${stock}</td>
                            <td class="${enough ? 'stock-ok' : 'stock-low'}">
                                ${enough ? '<i class="fas fa-check"></i> Đủ hàng' : '<i class="fas fa-times"></i> Thiếu hàng'}
                            </td>
                        </tr>
                    `;
                });
                modalBody.innerHTML = html;

                // Disable Export button if not enough stock
                if (!canExport) {
                    btnExport.disabled = true;
                    btnExport.style.opacity = '0.5';
                    btnExport.style.cursor = 'not-allowed';
                    btnExport.title = "Không đủ tồn kho để xuất";
                } else {
                    btnExport.disabled = false;
                    btnExport.style.opacity = '1';
                    btnExport.style.cursor = 'pointer';
                    btnExport.title = "";
                }
            });
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function toggleReject(show) {
        if (show) {
            rejectArea.style.display = 'block';
            mainFooter.style.display = 'none';
        } else {
            rejectArea.style.display = 'none';
            mainFooter.style.display = 'flex';
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

</body>
</html>