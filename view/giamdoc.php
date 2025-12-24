<?php
// FILE: view/giamdoc.php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'giamdoc') {
    // header("Location: ../login.php"); 
}

$user = $_SESSION['user'];
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($user['full_name'] ?? $user['username']) . "&background=random&color=fff&size=128";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Giám Đốc - AppMasters</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* === RESET & GLOBAL === */
    body { margin: 0; font-family: 'Inter', sans-serif; background: #f1f5f9; color: #334155; }
    * { box-sizing: border-box; outline: none; text-decoration: none; }
    
    .wrapper { display: flex; height: 100vh; overflow: hidden; }

    /* === SIDEBAR === */
    .sidebar {
        width: 260px;
        background: #0f172a; /* Dark Slate */
        color: #94a3b8;
        display: flex; flex-direction: column;
        flex-shrink: 0;
        transition: all 0.3s ease-in-out;
        border-right: 1px solid #1e293b;
        white-space: nowrap; /* Ngăn xuống dòng khi thu nhỏ */
    }
    
    /* Trạng thái thu gọn */
    .sidebar.collapsed { width: 80px; }
    .sidebar.collapsed .sidebar-brand span, 
    .sidebar.collapsed .user-name, 
    .sidebar.collapsed .user-role, 
    .sidebar.collapsed .nav-label, 
    .sidebar.collapsed .nav-link span,
    .sidebar.collapsed .logout-btn span { display: none; }
    
    .sidebar.collapsed .sidebar-brand { justify-content: center; padding: 24px 0; }
    .sidebar.collapsed .user-panel { padding: 15px 5px; }
    .sidebar.collapsed .user-avatar { width: 40px; height: 40px; }
    .sidebar.collapsed .nav-link { justify-content: center; padding: 15px; }
    .sidebar.collapsed .nav-link i { margin: 0; font-size: 1.4rem; }
    .sidebar.collapsed .logout-btn { justify-content: center; padding: 12px; }
    
    /* Brand & User */
    .sidebar-brand { 
            padding: 24px 20px; font-size: 1.25rem; font-weight: 800; color: #fff;
            text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #1e293b;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand i { color: #38bdf8; }
    
    .user-panel {
        padding: 20px; text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        background: rgba(255,255,255,0.02); overflow: hidden;
    }
    .user-avatar { width: 60px; height: 60px; border-radius: 50%; border: 2px solid #3b82f6; padding: 2px; background: #0f172a; transition: 0.3s; }
    .user-name { color: #fff; font-weight: 600; margin-top: 10px; font-size: 0.95rem; }
    .user-role { font-size: 0.8rem; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Menu */
    .nav-menu { flex: 1; padding: 15px 10px; overflow-y: auto; overflow-x: hidden; }
    .nav-label { font-size: 0.75rem; text-transform: uppercase; color: #475569; font-weight: 700; margin: 15px 10px 5px; }
    
    .nav-link {
        display: flex; align-items: center;
        padding: 12px 15px; margin-bottom: 4px;
        border-radius: 8px; color: #94a3b8; font-weight: 500; cursor: pointer; transition: all 0.2s;
    }
    .nav-link i { width: 24px; margin-right: 10px; font-size: 1.1rem; text-align: center; transition: 0.3s; }
    .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(3px); }
    .nav-link.active { background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%); color: #fff; box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.2); }
    
    .logout-btn { margin: 15px; padding: 12px; border: 1px solid #334155; border-radius: 8px; color: #94a3b8; text-align: center; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; text-decoration: none; }
    .logout-btn:hover { background: #ef4444; border-color: #ef4444; color: #fff; }

    /* === MAIN CONTENT === */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; transition: all 0.3s; }
    
    .topbar {
        height: 70px; background: #fff;
        border-bottom: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 30px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    
    .toggle-btn {
        font-size: 1.2rem; color: #64748b; cursor: pointer; padding: 8px; border-radius: 5px; transition: 0.2s;
        margin-right: 15px;
    }
    .toggle-btn:hover { background: #f1f5f9; color: #0f172a; }

    .content-body { flex: 1; overflow-y: auto; padding: 30px; }

    /* === MODAL STYLE === */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.6); 
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none; align-items: center; justify-content: center;
    }
    .modal-box {
        background: #fff; width: 95%; max-width: 800px;
        border-radius: 16px; padding: 0;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: slideUp 0.3s ease-out;
        display: flex; flex-direction: column;
        max-height: 85vh;
    }
    @keyframes slideUp { from {transform: translateY(20px); opacity:0;} to {transform: translateY(0); opacity:1;} }
    
    .modal-header { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-radius: 16px 16px 0 0; }
    .modal-title { margin: 0; font-size: 1.1rem; font-weight: 700; color: #0f172a; }
    .close-modal { cursor: pointer; color: #64748b; font-size: 1.5rem; transition: 0.2s; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .close-modal:hover { background: #fee2e2; color: #ef4444; }
    
    .modal-body { padding: 25px; overflow-y: auto; }
    .modal-footer { padding: 15px 25px; border-top: 1px solid #f1f5f9; text-align: right; background: #fff; border-radius: 0 0 16px 16px; }

    /* === DASHBOARD CARDS === */
    .stat-card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-5px); border-color: #3b82f6; }
    .stat-label { color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 2.2rem; font-weight: 800; color: #0f172a; margin-top: 10px; }
</style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cut" style="color:#38bdf8"></i> <span>AppMasters</span>
        </div>
        
        <div class="user-panel">
            <img src="<?= $avatarUrl ?>" class="user-avatar">
            <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
            <div class="user-role">Giám Đốc Điều Hành</div>
        </div>

        <div class="nav-menu">
            <div class="nav-label">Tổng quan</div>
            <a href="GiamDocController.php" class="nav-link active" onclick="setActive(this)">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>
            <div class="nav-label">Chức năng</div>

            <a onclick="loadContent('../controllers/GiamDocController.php?action=duyet_don_ncc'); setActive(this)" class="nav-link">
                <i class="fas fa-file-signature"></i> <span>Duyệt Đơn NCC</span>
            </a>
            <a onclick="loadContent('../controllers/GiamDocController.php?action=duyet_kehoach_sx'); setActive(this)" class="nav-link">
                <i class="fas fa-industry"></i> <span>Duyệt Kế Hoạch SX</span>
            </a>

            <a onclick="loadContent('../controllers/GiamDocController.php?action=thong_ke'); setActive(this)" class="nav-link">
                <i class="fas fa-chart-pie"></i> <span>Thống kê & Kho</span>
            </a>
        </div>

        <a href="../controllers/LogoutController.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
        </a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex; align-items:center">
                <div class="toggle-btn" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </div>
                <h2 style="margin:0; font-size:1.25rem; font-weight:700; color:#0f172a">Dashboard Điều Hành</h2>
            </div>
            
            <div style="font-weight:500; color:#64748b; background:#f8fafc; padding:8px 15px; border-radius:20px; border:1px solid #e2e8f0">
                <i class="far fa-calendar-alt"></i> <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="content-body" id="mainContent">
            <?php if(isset($data)): ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:25px; margin-bottom:30px">
                <div class="stat-card" style="border-left: 4px solid #ef4444;">
                    <div style="display:flex; justify-content:space-between;">
                        <div class="stat-label">Đơn NCC Chờ Duyệt</div>
                        <i class="fas fa-exclamation-circle" style="color:#ef4444; font-size:1.5rem; opacity:0.2"></i>
                    </div>
                    <div class="stat-value"><?= $data['donNCCChoDuyet'] ?></div>
                </div>

                <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                    <div style="display:flex; justify-content:space-between;">
                        <div class="stat-label">Kế Hoạch SX Chờ Duyệt</div>
                        <i class="fas fa-clock" style="color:#f59e0b; font-size:1.5rem; opacity:0.2"></i>
                    </div>
                    <div class="stat-value"><?= $data['keHoachSXChoDuyet'] ?></div>
                </div>

                <div class="stat-card" style="border-left: 4px solid #3b82f6;">
                    <div style="display:flex; justify-content:space-between;">
                        <div class="stat-label">Tổng Nhập Nguyên Liệu</div>
                        <i class="fas fa-cubes" style="color:#3b82f6; font-size:1.5rem; opacity:0.2"></i>
                    </div>
                    <div class="stat-value" style="font-size:1.8rem"><?= number_format($data['tongNhapNL']) ?></div>
                </div>
            </div>

            <div style="background:#fff; padding:30px; border-radius:12px; border:1px solid #e2e8f0; text-align:center; color:#64748b">
                <img src="https://cdn-icons-png.flaticon.com/512/2082/2082156.png" style="width:100px; opacity:0.5; margin-bottom:15px">
                <p>Chào mừng <strong><?= htmlspecialchars($user['username']) ?></strong> quay trở lại hệ thống.</p>
                <p>Vui lòng chọn chức năng từ menu bên trái để bắt đầu làm việc.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="modalChiTiet" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title">📦 Chi tiết: <span id="modalMaDon" style="color:#2563eb"></span></h3>
            <div class="close-modal" onclick="closeModal()">&times;</div>
        </div>
        
        <div class="modal-body" id="modalContent">
            <div style="text-align:center; padding:30px"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>
        </div>

        <div class="modal-footer">
            <button onclick="closeModal()" style="padding:10px 20px; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; font-weight:600; color:#475569">Đóng cửa sổ</button>
        </div>
    </div>
</div>

<script>
    // --- Toggle Sidebar ---
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });

    // --- Set Active Menu ---
    function setActive(element) {
        document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
        if(element) element.classList.add('active');
    }

    // --- Load Content via AJAX ---
    function loadContent(url) {
        const contentDiv = document.getElementById("mainContent");
        contentDiv.innerHTML = '<div style="text-align:center; padding:50px"><i class="fas fa-circle-notch fa-spin fa-3x" style="color:#3b82f6"></i><p style="margin-top:15px; color:#64748b">Đang tải dữ liệu...</p></div>';
        
        fetch(url)
            .then(res => res.text())
            .then(html => {
                contentDiv.innerHTML = html;
            })
            .catch(err => {
                contentDiv.innerHTML = '<div style="text-align:center; color:red; padding:30px"><i class="fas fa-exclamation-triangle fa-2x"></i><p>Không thể kết nối đến máy chủ.</p></div>';
                console.error(err);
            });
    }

    // --- Alias cho Load Báo Cáo ---
    function loadBaoCao(params) {
        loadContent('../controllers/GiamDocController.php?action=' + params);
    }

    // --- Modal Logic (Dùng chung cho Đơn hàng & Kế hoạch) ---
    function closeModal() {
        document.getElementById('modalChiTiet').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('modalChiTiet');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // --- 1. Xử lý ĐƠN HÀNG NCC ---
    function openModalChiTiet(id, maDon) {
        setupModal("Đơn hàng " + maDon);
        fetch('../controllers/GiamDocController.php?action=get_chi_tiet_don&id=' + id)
            .then(res => res.text()).then(html => { document.getElementById('modalContent').innerHTML = html; });
    }

    function pheDuyet(id) {
        if(!confirm("Xác nhận PHÊ DUYỆT đơn hàng này?")) return;
        postAction('phe_duyet', id, 'duyet_don_ncc');
    }

    function tuChoi(id) {
        handleReject(id, 'tu_choi', 'duyet_don_ncc');
    }

    // --- 2. Xử lý KẾ HOẠCH SẢN XUẤT ---
    function openModalKeHoach(id, maDon) {
        setupModal("Kế Hoạch SX - Đơn " + maDon);
        fetch('../controllers/GiamDocController.php?action=get_chi_tiet_kehoach&id=' + id)
            .then(res => res.text()).then(html => { document.getElementById('modalContent').innerHTML = html; });
    }

    function pheDuyetKeHoach(id) {
        if(!confirm("Xác nhận PHÊ DUYỆT kế hoạch này?")) return;
        postAction('phe_duyet_kehoach', id, 'duyet_kehoach_sx');
    }

    function tuChoiKeHoach(id) {
        handleReject(id, 'tu_choi_kehoach', 'duyet_kehoach_sx');
    }

    // --- Helper Functions ---
    function setupModal(titleText) {
        document.getElementById('modalChiTiet').style.display = 'flex';
        document.getElementById('modalMaDon').innerText = titleText;
        document.getElementById('modalContent').innerHTML = '<div style="text-align:center; padding:40px"><i class="fas fa-circle-notch fa-spin fa-2x" style="color:#3b82f6"></i></div>';
    }

    function handleReject(id, actionName, reloadAction) {
        let lyDo = prompt("Vui lòng nhập lý do từ chối:");
        if(lyDo === null) return;
        if(lyDo.trim() === "") { alert("Bạn phải nhập lý do!"); return; }

        const formData = new FormData();
        formData.append('action', actionName);
        formData.append('id', id);
        formData.append('ly_do', lyDo);
        
        sendPost(formData, reloadAction);
    }

    function postAction(actionName, id, reloadAction) {
        const formData = new FormData();
        formData.append('action', actionName);
        formData.append('id', id);
        sendPost(formData, reloadAction);
    }

    function sendPost(formData, reloadAction) {
        fetch('../controllers/GiamDocController.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === "OK") {
                alert("Thao tác thành công!");
                loadContent('../controllers/GiamDocController.php?action=' + reloadAction);
            } else {
                alert("Lỗi: " + data);
            }
        })
        .catch(err => { alert("Lỗi kết nối server!"); console.error(err); });
    }
</script>

</body>
</html>