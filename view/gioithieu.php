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
.about-page {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 20px;
    color: #e5e7eb;
}

.about-hero {
    text-align: center;
    margin-bottom: 40px;
}

.about-hero h1 {
    font-size: 34px;
    margin-bottom: 10px;
    color: #38bdf8;
}

.about-hero p {
    color: #9ca3af;
    font-size: 16px;
}

.about-content h2 {
    margin-top: 30px;
    margin-bottom: 12px;
    color: #ffffff;
}

.about-content p {
    line-height: 1.7;
    margin-bottom: 12px;
    color: #d1d5db;
}

.about-content ul {
    margin-left: 20px;
    line-height: 1.8;
}

.member-list li {
    list-style: square;
    margin-left: 20px;
}

.about-content a {
    color: #38bdf8;
    text-decoration: none;
}

.about-content a:hover {
    text-decoration: underline;
}

.banner-full {
    width: 100%;
}

.banner-full img {
    width: 100%;
    height: auto;
    display: block;
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

<main class="about-page">
    <section class="about-hero">
        <h1>GIỚI THIỆU</h1>
        <p>Hệ thống Quản lý xưởng sản xuất may mặc App Masters Group</p>
    </section>

    <section class="about-content">
        <h2>1. Giới thiệu chung</h2>
        <p>
            Đề tài <strong>“Quản lý xưởng sản xuất”</strong> được xây dựng nhằm mô phỏng và quản lý hoạt động 
            của một hệ thống sản xuất gồm nhiều xưởng may khác nhau. 
            Hệ thống hỗ trợ quản lý sản phẩm, theo dõi quy trình sản xuất, 
            và cung cấp giao diện trực quan phục vụ cho việc vận hành và quản lý xưởng hiệu quả hơn.
        </p>

        <p>
            Đây là đồ án thuộc môn học <strong>Phát triển ứng dụng</strong>, 
            được thực hiện với mục tiêu áp dụng kiến thức đã học về phân tích hệ thống, 
            thiết kế giao diện và xây dựng ứng dụng web vào một bài toán thực tế.
        </p>

        <h2>2. Mục tiêu của đề tài</h2>
        <ul>
            <li>Xây dựng hệ thống quản lý xưởng sản xuất có giao diện trực quan, dễ sử dụng.</li>
            <li>Quản lý thông tin sản phẩm, xưởng sản xuất và người dùng.</li>
            <li>Áp dụng kiến thức lập trình web vào bài toán quản lý thực tế.</li>
            <li>Nâng cao kỹ năng làm việc nhóm và phát triển phần mềm.</li>
        </ul>

        <h2>3. Giảng viên hướng dẫn</h2>
        <p>
            Đề tài được thực hiện dưới sự hướng dẫn của <strong>Cô Lê Thùy Trang</strong>.  
            Nhóm xin gửi lời cảm ơn chân thành đến cô vì đã tận tình hướng dẫn, 
            hỗ trợ và góp ý trong suốt quá trình thực hiện đồ án.
        </p>

        <h2>4. Thành viên thực hiện</h2>
        <ul class="member-list">
            <li>Vỹ Hào</li>
            <li>Văn Duy</li>
            <li>Ngọc Hiếu</li>
            <li>Quang Diễn</li>
            <li>Ngọc Hào</li>
        </ul>

        <h2>5. Nguồn tham khảo hình ảnh</h2>
        <p>
            Các hình ảnh sản phẩm được sử dụng trong hệ thống chỉ nhằm mục đích học tập, 
            tham khảo và minh họa cho đồ án, không phục vụ mục đích thương mại.
        </p>
        <ul>
            <li>
                <a href="https://dony.vn/thoi-trang/" target="_blank">
                    https://dony.vn/thoi-trang/
                </a>
            </li>
            <li>
                <a href="https://dirtycoins.vn/" target="_blank">
                    https://dirtycoins.vn/
                </a>
            </li>
        </ul>

        <h2>6. Lời cảm ơn</h2>
        <p>
            Nhóm xin chân thành cảm ơn <strong>Khoa</strong> và <strong>Giảng viên bộ môn</strong> 
            đã tạo điều kiện cho nhóm được học tập, nghiên cứu và thực hiện đề tài này.  
            Đặc biệt, nhóm xin gửi lời cảm ơn sâu sắc đến <strong>Cô Lê Thùy Trang</strong> 
            vì sự hướng dẫn tận tâm, giúp nhóm hoàn thiện đồ án một cách tốt nhất.
        </p>

        <p>
            Mặc dù đã cố gắng hoàn thiện đề tài trong khả năng cho phép, 
            nhưng không tránh khỏi những thiếu sót.  
            Nhóm rất mong nhận được sự đóng góp ý kiến từ giảng viên để đề tài được hoàn thiện hơn.
        </p>
    </section>
    <h2>7. Site map</h2>
    <br>
    <section class="banner-full">
    <img src="../assets/images/sitemap.jpg" alt="sitemap">
    </section>

</main>
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
