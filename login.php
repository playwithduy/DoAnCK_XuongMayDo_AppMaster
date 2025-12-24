<?php
// FILE: login.php
session_start();
// Nếu đã đăng nhập rồi thì chuyển hướng luôn
if (isset($_SESSION['user'])) {
    // Logic chuyển hướng nhanh nếu user lỡ vào lại trang login
    // (Có thể bỏ qua hoặc customize thêm)
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Hệ Thống Xưởng May</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* === RESET & BASE === */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Be Vietnam Pro', sans-serif; }
        
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.85)), 
                        url('https://images.unsplash.com/photo-1556905055-8f358a7a47b2?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .login-wrapper { width: 100%; max-width: 420px; padding: 20px; animation: fadeIn 0.8s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }
        .login-box::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: linear-gradient(90deg, #2563eb, #7c3aed); }
        .brand-logo { font-size: 3rem; margin-bottom: 10px; background: -webkit-linear-gradient(#2563eb, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .login-title { color: #1e293b; font-size: 1.5rem; font-weight: 700; margin-bottom: 5px; }
        .login-subtitle { color: #64748b; font-size: 0.9rem; margin-bottom: 30px; }
        
        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; transition: 0.3s; }
        .input-field { width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.95rem; outline: none; transition: all 0.3s; background: #f8fafc; }
        .input-field:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .input-field:focus + i { color: #2563eb; }
        
        .login-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; border: none; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; margin-top: 10px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3); }
        .login-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4); }
        
        .remember-forgot { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; margin-bottom: 25px; }
        .login-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 20px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px; }
        .footer-text { margin-top: 25px; font-size: 0.9rem; color: #64748b; }
        .footer-text a { color: #2563eb; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <form method="POST" action="controllers/xulylogin.php" class="login-box">
        
        <div class="brand-logo"><i class="fas fa-cut"></i></div>
        <h2 class="login-title">AppMasters</h2>
        <p class="login-subtitle">Hệ thống quản lý xưởng may</p>

        <?php if(isset($_GET['error'])): ?>
            <div class="login-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php 
                    if($_GET['error'] == 'invalid') echo "Tài khoản hoặc mật khẩu sai!";
                    elseif($_GET['error'] == 'empty') echo "Vui lòng nhập đầy đủ thông tin!";
                    elseif($_GET['error'] == 'locked') echo "Tài khoản đã bị khóa!";
                    else echo "Đăng nhập thất bại!";
                ?>
            </div>
        <?php endif; ?>

        <div class="input-group">
            <label>Tài khoản</label>
            <div class="input-wrapper">
                <input type="text" name="taikhoan" class="input-field" placeholder="Username" required autofocus>
                <i class="fas fa-user"></i>
            </div>
        </div>

        <div class="input-group">
            <label>Mật khẩu</label>
            <div class="input-wrapper">
                <input type="password" name="matkhau" class="input-field" placeholder="••••••••" required>
                <i class="fas fa-lock"></i>
            </div>
        </div>

        <div class="remember-forgot">
            <label class="remember">
                <input type="checkbox" name="remember"> Ghi nhớ
            </label>
            <a href="#" class="forgot">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="login-btn">ĐĂNG NHẬP <i class="fas fa-sign-in-alt" style="margin-left: 5px;"></i></button>

        <div class="footer-text">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </form>
</div>

</body>
</html>