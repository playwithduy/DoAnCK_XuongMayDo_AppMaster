<?php 
// FILE: view/bao_cao_su_co.php

if(!isset($data)) { header("Location: ../controllers/BaoCaoSuCoController.php"); exit; }

// Lấy thông tin user để hiển thị Sidebar
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user = $_SESSION['user'] ?? [];
$username = $user['full_name'] ?? ($user['username'] ?? 'Công Nhân');
$avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random&color=fff&size=128&bold=true";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"> <title>Báo Cáo Sự Cố</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    
    /* HEADER SIDEBAR */
    .sidebar-brand { 
        padding: 24px 20px; font-size: 1.25rem; font-weight: 800; color: #fff;
        text-transform: uppercase; letter-spacing: 1px; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        display: flex; align-items: center; gap: 10px;
    }
    .sidebar-brand i { color: #38bdf8; }

    /* USER PANEL */
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

    /* --- FORM STYLE --- */
    .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 25px; }
    h3 { color: #334155; font-size: 1.1rem; margin-top: 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; }
    
    .form-group { margin-bottom: 20px; }
    label { display: block; font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .form-control { 
        width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; 
        font-family: inherit; font-size: 0.95rem; transition: border-color 0.2s;
    }
    .form-control:focus { outline: none; border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
    
    .btn-submit { 
        padding: 12px 24px; background: #ef4444; color: white; border: none; border-radius: 8px; 
        cursor: pointer; font-weight: 600; transition: 0.2s; font-size: 0.95rem;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3); display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-submit:hover { background: #dc2626; transform: translateY(-1px); }
    
    /* --- TABLE STYLE --- */
    table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
    th { background: #f8fafc; padding: 15px; text-align: left; font-weight: 700; color: #64748b; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
    td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    tr:hover { background-color: #f8fafc; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .new { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; } 
    .done { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; } 
    .process { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    
    .alert-success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px; }
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
            <div style="font-size:0.75rem; color:#94a3b8">Công Nhân</div>
        </div>

        <nav style="flex:1; padding-top:10px">
            <div class="nav-section text-hide">Tổng Quan</div>
            <a href="../view/congnhan.php" class="nav-link"><i class="fas fa-home"></i> <span class="text-hide">Dashboard</span></a>
            
            <div class="nav-section text-hide">Chức Năng</div>
            <a href="../controllers/LichLamViecController.php" class="nav-link"><i class="fas fa-calendar-alt"></i> <span class="text-hide">Lịch Làm Việc</span></a>
            <a href="../controllers/BaoCaoSuCoController.php" class="nav-link active"><i class="fas fa-exclamation-triangle"></i> <span class="text-hide">Báo Cáo Sự Cố</span></a>
        </nav>
        <a href="../controllers/LogoutController.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> <span class="text-hide">Đăng Xuất</span></a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center;">
                <div class="toggle-btn" id="sidebarToggle"><i class="fas fa-bars"></i></div>
                <h2 class="page-title">Báo Cáo Sự Cố</h2>
            </div>
            <div style="color:#64748b"><?= date('d/m/Y') ?></div>
        </div>

        <div class="content-body">
            
            <?php if($data['msg']) echo "<div class='alert-success'><i class='fas fa-check-circle'></i> {$data['msg']}</div>"; ?>

            <div class="card" style="border-top: 4px solid #ef4444;">
                <h3><i class="fas fa-pen-nib" style="color: #ef4444; margin-right: 8px;"></i> Gửi báo cáo mới</h3>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Loại sự cố</label>
                        <select name="loai_su_co" class="form-control" required>
                            <option value="Máy hỏng">Máy hỏng</option>
                            <option value="Thiếu nguyên liệu">Thiếu nguyên liệu</option>
                            <option value="Tai nạn">Tai nạn / An toàn</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Vị trí (Máy/Chuyền)</label>
                        <input type="text" name="vi_tri" class="form-control" placeholder="VD: Máy may số 05 - Chuyền 1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mô tả chi tiết</label>
                        <textarea name="mo_ta" class="form-control" rows="3" placeholder="Mô tả tình trạng sự cố..." required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Ảnh đính kèm (nếu có)</label>
                        <input type="file" name="hinh_anh" class="form-control" style="padding: 8px;">
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> GỬI BÁO CÁO
                    </button>
                </form>
            </div>

            <div class="card">
                <h3><i class="fas fa-history" style="color: #3b82f6; margin-right: 8px;"></i> Lịch sử báo cáo</h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Loại</th>
                                <th>Vị trí</th>
                                <th>Mô tả</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($data['history'])): ?>
                            <tr><td colspan="5" style="text-align:center; padding: 30px; color:#94a3b8;">Chưa có báo cáo nào.</td></tr>
                        <?php else: foreach($data['history'] as $h): 
                            $cls = ($h['trang_thai']=='Đã xong')?'done':(($h['trang_thai']=='Đang xử lý')?'process':'new'); 
                        ?>
                            <tr>
                                <td><?= date('d/m H:i', strtotime($h['ngay_tao'])) ?></td>
                                <td style="color:#ef4444; font-weight:700"><?= $h['loai_su_co'] ?></td>
                                <td><?= $h['vi_tri'] ?></td>
                                <td><?= $h['mo_ta'] ?></td>
                                <td><span class="badge <?= $cls ?>"><?= $h['trang_thai'] ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>

</body>
</html>