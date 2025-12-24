<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thiết kế sản phẩm</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ===== RESET & GLOBAL ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    width: 100%;
    min-height: 100%;
    background: #0b0f14;
    font-family: "Segoe UI", sans-serif;
    overflow-x: hidden;
}

header {
    width: 100%;
    height: 64px;
    background: linear-gradient(90deg, #0f172a, #1e293b);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.35);
    position: relative;
    z-index: 1000;
    isolation: isolate; /* ⭐ QUAN TRỌNG */
}


/* LOGO */
header .logo img {
    height: 40px;
    object-fit: contain;
}

/* NAV CENTER */
.nav-center {
    display: flex;
    gap: 32px;
}

.nav-center a {
    color: #e5e7eb;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    position: relative;
    padding-bottom: 4px;
    transition: 0.3s;
}

/* gạch chân hover */
.nav-center a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0%;
    height: 2px;
    background: #38bdf8;
    transition: width 0.3s;
}

.nav-center a:hover {
    color: #ffffff;
}

.nav-center a:hover::after {
    width: 100%;
}

/* NAV RIGHT */
.nav-right {
    display: flex;
    align-items: center;
    gap: 18px;
    font-size: 14px;
}

.nav-right a {
    color: #e5e7eb;
    text-decoration: none;
    transition: 0.3s;
}

.nav-right a:hover {
    color: #ffffff;
}

/* HELLO USER */
.hello {
    color: #9ca3af;
    font-size: 14px;
}

/* LOGOUT BUTTON */
.logout-btn {
    padding: 6px 14px;
    border: 1px solid #38bdf8;
    border-radius: 4px;
    color: #38bdf8 !important;
    transition: 0.3s;
}

.logout-btn:hover {
    background: #38bdf8;
    color: #0f172a !important;
}

        footer {
    background: linear-gradient(180deg, #1f2937, #0b0f14);
    color: #e5e7eb;
    padding: 40px 20px 15px;
    font-family: "Segoe UI", sans-serif;
}

/* container bên trong footer */
footer .footer-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 40px;
    flex-wrap: wrap;
}

/* LOGO */
footer .footer-logo {
    display: flex;
    align-items: center;
    gap: 14px;
}

footer .logo-icon {
    width: 54px;
    height: 54px;
    border: 2px solid #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
}

footer .logo-name {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 2px;
    color: #ffffff;
}

footer .logo-sub {
    font-size: 11px;
    letter-spacing: 3px;
    color: #9ca3af;
}

/* THÔNG TIN */
footer .footer-info p {
    margin: 6px 0;
    font-size: 14px;
    line-height: 1.6;
    color: #d1d5db;
}

/* COPYRIGHT */
footer .footer-bottom {
    border-top: 1px solid #374151;
    margin-top: 30px;
    padding-top: 12px;
    text-align: center;
    font-size: 13px;
    letter-spacing: 1px;
    color: #9ca3af;
}

/* RESPONSIVE */
/* ===== FORM DARK CONTACT STYLE ===== */

/* nền khu vực form */
.design-wrapper {
    background: #0b0f14;
    padding: 80px 20px;
}

/* bỏ card trắng */
.design-card {
    background: transparent;
    max-width: 1100px;
    margin: auto;
}

/* tiêu đề */
.design-title {
    text-align: center;
    color: #ffffff;
    font-size: 42px;
    letter-spacing: 4px;
    font-weight: 700;
    margin-bottom: 50px;
    text-transform: uppercase;
}

.design-title::after {
    content: "";
    display: block;
    width: 60px;
    height: 1px;
    background: rgba(255,255,255,0.4);
    margin: 20px auto 0;
}

/* layout 2 cột */
.design-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

/* group */
.form-group {
    margin-bottom: 22px;
}

/* label */
.form-group label {
    color: #9ca3af;
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 8px;
    display: block;
}

/* input / select / textarea */
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.15);
    padding: 14px 16px;
    color: #ffffff;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

/* textarea */
.form-group textarea {
    min-height: 160px;
    resize: vertical;
}

/* focus */
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #ffffff;
}

/* select arrow trắng */
.form-group select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='white'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
}

/* file input */
.form-group input[type="file"] {
    padding: 10px;
    color: #9ca3af;
}

/* ===== FIX SELECT DROPDOWN DARK MODE ===== */

/* nền dropdown */
.form-group select option {
    background-color: #0f172a; /* nền tối */
    color: #ffffff;           /* chữ trắng */
}

/* option khi hover */
.form-group select option:hover {
    background-color: #1e293b;
}

/* option được chọn */
.form-group select option:checked {
    background-color: #38bdf8;
    color: #0f172a;
}

/* footer form */
.design-footer {
    grid-column: 1 / -1;
    margin-top: 30px;
}

/* button */
.btn-submit,
.btn-login {
    width: 100%;
    padding: 18px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.3);
    color: #ffffff;
    font-size: 13px;
    letter-spacing: 3px;
    text-transform: uppercase;
    cursor: pointer;
    transition: 0.3s;
    text-align: center;
    text-decoration: none;
}

/* hover */
.btn-submit:hover,
.btn-login:hover {
    background: #ffffff;
    color: #000000;
}

/* warning */
.warning-msg {
    color: #fbbf24;
    text-align: center;
    letter-spacing: 1px;
}
.design-wrapper {
    background: linear-gradient(
        180deg,
        #0f172a 0%,
        #0b0f14 120px
    );
    padding: 80px 20px;
}


/* responsive */
@media (max-width: 900px) {
    .design-grid {
        grid-template-columns: 1fr;
    }

    .design-title {
        font-size: 32px;
    }
}

</style>
</head>

<body>

<!-- HEADER -->
<header class="header">
    <div class="logo">
        <img src="../assets/images/logo3.png" alt="Logo">
    </div>

       <nav class="nav-center">
        <a href="../index.php">Trang chủ</a>
        <a href="sanpham.php">Sản phẩm</a>
        <a href="thietkesanpham.php">Thiết kế sản phẩm</a>
        <a href="gioithieu.php">Giới thiệu</a>
        <a href="blog.php">Blog</a>
    </nav>

    <nav class="nav-right">
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="../login.php">Đăng nhập</a>
        <?php else: ?>
            <span class="hello">Xin chào, <?= $_SESSION['user']['username'] ?></span>
        <?php endif; ?>
    </nav>
</header>


<!-- FORM WRAPPER -->
<div class="design-wrapper">
    <div class="design-card">

        <h2 class="design-title">Thiết kế sản phẩm theo yêu cầu</h2>

        <form action="xulydatthietke.php" method="POST" enctype="multipart/form-data" class="design-grid">

            <!-- LEFT -->
            <div class="design-left">

                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" name="ten_sp" required>
                </div>

                <div class="form-group">
                    <label>Loại sản phẩm</label>
                    <select name="loai_sp">
                        <option value="áo">Áo</option>
                        <option value="Quần">Quần</option>
                        <option value="Giay">Giay</option>
                        <option value="túi canvas">Túi canvas</option>
                        <option value="tranh treo tường">Tranh treo tường</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" min="1" name="so_luong" required>
                </div>

                <div class="form-group">
                    <label>Màu sắc mong muốn</label>
                    <input type="text" name="mau_sac">
                </div>
            </div>

            <!-- RIGHT -->
            <div class="design-right">

                <div class="form-group">
                    <label>Mô tả yêu cầu</label>
                    <textarea name="mo_ta" required></textarea>
                </div>

                <div class="form-group">
                    <label>Tệp mẫu (tùy chọn)</label>
                    <input type="file" name="file_mau">
                </div>

                <div class="form-group">
                    <label>Thời gian mong muốn</label>
                    <select name="thoi_gian">
                        <option value="3 ngày">3 ngày</option>
                        <option value="5 ngày">5 ngày</option>
                        <option value="7 ngày">7 ngày</option>
                        <option value="gấp">Gấp (Phụ thu)</option>
                    </select>
                </div>

            </div>
            <?php if (isset($_GET['success'])): ?>
    <p class="warning-msg" style="color:#22c55e; margin-bottom:20px;">
        ✔ Đặt thiết kế thành công! Chúng tôi sẽ liên hệ sớm.
    </p>
<?php endif; ?>


            <!-- FOOTER -->
            <div class="design-footer">
                <?php if (!isset($_SESSION['user'])): ?>
                    <a href="../login.php" class="btn-login">Đăng nhập để đặt thiết kế</a>

                <?php elseif ($_SESSION['user']['role'] !== 'khachhang'): ?>
                    <p class="warning-msg">⚠ Chỉ khách hàng mới được đặt thiết kế!</p>

                <?php else: ?>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-paper-plane"></i> Gửi yêu cầu thiết kế
                    </button>
                <?php endif; ?>
            </div>

        </form>

    </div>
</div>


<!-- FOOTER -->
<!-- FOOTER -->
<footer class="footer">
    <div class="footer-container">

        <!-- LOGO -->
        <div class="footer-logo">
            <div class="logo-icon">
                <!-- Icon máy may (SVG) -->
                <svg width="42" height="42" viewBox="0 0 64 64" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 42h44v6H10z" fill="#fff"/>
                    <path d="M18 36V18h10c8 0 14 6 14 14v4H18z" stroke="#fff" stroke-width="3"/>
                    <circle cx="38" cy="24" r="3" fill="#fff"/>
                </svg>
            </div>
            <div class="logo-text">
                <span class="logo-name">XƯỞNG MAY IUH</span>
                <span class="logo-sub">GARMENT FACTORY</span>
            </div>
        </div>

        <!-- INFO -->
        <div class="footer-info">
            <p><strong>Địa chỉ:</strong> Trường Đại học Công nghiệp TP.HCM (IUH),  
                12 Nguyễn Văn Bảo, P.4, Q.Gò Vấp, TP.HCM</p>
            <p><strong>Điện thoại:</strong> 0989 123 456</p>
            <p><strong>Email:</strong> xuongmay@iuh.edu.vn</p>
        </div>

    </div>

    <!-- COPYRIGHT -->
    <div class="footer-bottom">
        COPYRIGHT © 2025 – BẢN QUYỀN THUỘC VỀ XƯỞNG MAY IUH
    </div>
</footer>

</body>
</html>
