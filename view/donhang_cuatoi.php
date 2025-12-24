<?php
session_start();
include "../config/database.php";

/* ==== KIỂM TRA ĐĂNG NHẬP ==== */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'khachhang') {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user']['id'];

/* ==== LẤY DANH SÁCH ĐƠN HÀNG ==== */
$sql = "
SELECT 
    d.id,
    d.size,
    d.quantity,
    d.price,
    d.status,
    d.created_at,
    p.name,
    p.image
FROM datmay d
JOIN products p ON d.product_id = p.id
WHERE d.user_id = $user_id
ORDER BY d.created_at DESC
";

$orders = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đơn hàng của tôi</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f7f7f7;
    margin: 0;
    padding-top: 80px; /* tránh header đè */
}

header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 64px;
    background: #111;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    z-index: 1000;
}

header a {
    color: #fff;
    text-decoration: none;
    margin-left: 15px;
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


h2 {
    text-align: center;
    margin-bottom: 30px;
}

.order-box {
    background: #fff;
    border-radius: 10px;
    padding: 16px;
    margin: 0 auto 15px;
    max-width: 800px;
    display: flex;
    gap: 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
/* LOGO */
header .logo img {
    height: 40px;
    object-fit: contain;
}

.order-img {
    width: 100px;
    height: auto;
    border-radius: 8px;
}

.order-info h4 {
    margin: 0 0 8px;
}

.order-info p {
    margin: 4px 0;
}

.total {
    color: #d40000;
    font-weight: bold;
}

.status {
    font-weight: bold;
}

.cancel {
    display: inline-block;
    margin-top: 8px;
    color: red;
    text-decoration: none;
}

.back {
    display: block;
    margin: 30px auto;
    text-align: center;
}

footer {
    width: 100%;
    background: linear-gradient(180deg, #1f2937, #0b0f14);
    color: #e5e7eb;
    padding: 40px 20px 15px;
    font-family: "Segoe UI", sans-serif;
    isolation: isolate; /* ⭐ QUAN TRỌNG */
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
@media (max-width: 768px) {
    footer .footer-container {
        flex-direction: column;
        text-align: center;
    }

    footer .footer-logo {
        justify-content: center;
    }
}
</style>
</head>

<body>

<!-- HEADER -->
 <header>
    <div class="logo">
        <img src="../assets/images/logo3.png" alt="Logo">
        
    </div>
    <nav class="nav-center">
    <a href="../index.php">Trang chủ</a>
    <a href="thietkesanpham.php">Thiết kế sản phẩm</a>
    <a href="donhang_cuatoi.php">Đơn hàng của tôi</a>
    <a href="gioithieu.php">Giới thiệu</a>
</nav>

    <nav class="nav-right">
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="login.php">Đăng nhập</a>
        <?php else: ?>
            <span class="hello">Xin chào, <?= $_SESSION['user']['username'] ?></span>
            <a href="../controllers/LogoutController.php" class="logout-btn">Đăng xuất</a>
        <?php endif; ?>
    </nav>
</header>

<h2>🧾 Đơn hàng đã đặt</h2>

<?php if ($orders->num_rows > 0): ?>
    <?php while ($o = $orders->fetch_assoc()): ?>
        <div class="order-box">
            <img src="../assets/images/<?= $o['image'] ?>" class="order-img" alt="">

            <div class="order-info">
                <h4><?= $o['name'] ?></h4>
                <p>Size: <?= $o['size'] ?></p>
                <p>Số lượng: <?= $o['quantity'] ?></p>
                <p>Giá: <?= number_format($o['price']) ?> đ</p>

                <p class="total">
                    Tổng tiền: <?= number_format($o['price'] * $o['quantity']) ?> đ
                </p>

                <p>Ngày đặt: <?= $o['created_at'] ?></p>

                <p class="status">
                    Trạng thái: <?= $o['status'] ?>
                </p>

                <?php if ($o['status'] === 'Đang xử lý'): ?>
                    <a class="cancel" href="huy_don.php?id=<?= $o['id'] ?>"
                       onclick="return confirm('Bạn có chắc muốn hủy đơn này?')">
                        Hủy đơn
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">Bạn chưa đặt đơn hàng nào.</p>
<?php endif; ?>

<a href="../index.php" class="back">⬅ Quay lại trang chủ</a>

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
                <span class="logo-name">XƯỞNG MAY APP MASTERS</span>
                
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
