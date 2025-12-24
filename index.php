<?php 
session_start();
include "config/database.php";

/* ======================
   XỬ LÝ TÌM KIẾM & LỌC
====================== */
$where = [];

if (!empty($_GET['keyword'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $where[] = "name LIKE '%$keyword%'";
}

if (!empty($_GET['category'])) {
    $category = (int)$_GET['category'];
    $where[] = "category_id = $category";
}

$sql = "SELECT * FROM products";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style1.css">

<style>
/* --- LOGO STYLE --- */
.logo-container {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}
.logo-svg {
    width: 50px;
    height: 50px;
}
.logo-text-group {
    display: flex;
    flex-direction: column;
}
.logo-brand {
    font-size: 1.4rem;
    font-weight: 800;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.1;
}
.logo-sub {
    font-size: 0.75rem;
    color: #94a3b8;
    letter-spacing: 2px;
    text-transform: uppercase;
}

/* --- COMMIT SECTION --- */
.commit-section {
    background: #f5f5f5;
    padding: 60px 20px;
    margin-top: 40px;
}
.commit-container {
    max-width: 1200px;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    text-align: center;
}
.commit-item {
    background: linear-gradient(180deg, #0f172a, #020617);
    padding: 28px 20px;
    border-radius: 10px;
    transition: 0.3s;
}
.commit-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.35);
}
.commit-icon { font-size: 42px; margin-bottom: 14px; }
.commit-item h4 { color: #fff; margin-bottom: 10px; }
.commit-item p { color: #cbd5f5; font-size: 14px; }

@media(max-width:992px){
    .commit-container{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:576px){
    .commit-container{grid-template-columns:1fr;}
}

.process-section {
    padding: 60px 20px;
    background: #ffffff;
}

.process-title {
    text-align: center;
    font-size: 32px;
    color: #16a34a;
    margin-bottom: 10px;
}

.process-desc {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 40px;
    color: #475569;
}

.process-container {
    max-width: 1200px;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.process-item {
    background: #f8fafc;
    padding: 28px 20px;
    text-align: center;
    border-radius: 12px;
    transition: 0.3s;
}

.process-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

.process-icon {
    font-size: 42px;
    color: #16a34a;
    margin-bottom: 14px;
}

.process-item h4 {
    color: #16a34a;
    margin-bottom: 10px;
}

.process-item p {
    font-size: 14px;
    color: #475569;
}

/* Responsive */
@media(max-width: 992px){
    .process-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width: 576px){
    .process-container {
        grid-template-columns: 1fr;
    }
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

<header>
    <a href="index.php" class="logo-container">
        <svg class="logo-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 28L16 16L28 28L40 16L52 28V56H4V28Z" fill="#3b82f6" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
            <path d="M46 12V22" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <path d="M52 8V28" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <rect x="22" y="38" width="12" height="18" fill="#1e293b" stroke="#fff" stroke-width="2"/>
            <circle cx="28" cy="24" r="6" stroke="#fff" stroke-width="2" stroke-dasharray="2 2"/>
        </svg>
        
        <div class="logo-text-group">
<span class="logo-brand">App Masters</span>
            <span class="logo-sub">Xưởng May</span>
        </div>
    </a>

    <nav class="nav-center">
        <a href="index.php">Trang chủ</a>
        <a href="view/sanpham.php">Sản phẩm</a>
        <a href="view/thietkesanpham.php">Thiết kế sản phẩm</a>
        <a href="view/gioithieu.php">Giới thiệu</a>
        <a href="view/blog.php">Blog</a>
    </nav>

    <nav class="nav-right">
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="login.php">Đăng nhập</a>
        <?php else: ?>
            <span class="hello">Xin chào, <?= $_SESSION['user']['username'] ?></span>
            <a href="controllers/LogoutController.php" class="logout-btn">Đăng xuất</a>
        <?php endif; ?>
    </nav>
</header>

<section class="banner-full">
    <img src="assets/images/qn.png" alt="Quảng cáo App Master">
</section>

<div class="container">

    <aside class="sidebar">
        <h3>Tìm kiếm</h3>
        <form method="GET">
            <input 
                type="text" 
                name="keyword" 
                placeholder="Nhập tên sản phẩm..."
                value="<?= $_GET['keyword'] ?? '' ?>"
            >
        </form>

        <h3>Danh mục</h3>
        <ul>
            <li><a href="index.php">Tất cả</a></li>
            <li><a href="index.php?category=1">Áo</a></li>
            <li><a href="index.php?category=2">Quần</a></li>
            <li><a href="index.php?category=3">Đồng phục</a></li>
            <li><a href="index.php?category=4">Giày thể thao</a></li>
        </ul>
    </aside>

    <main class="product-list">
        <?php if ($result->num_rows == 0): ?>
            <p style="padding:20px;">Không tìm thấy sản phẩm phù hợp</p>
        <?php endif; ?>

        <?php while ($row = $result->fetch_assoc()): ?>
            <a class="product" href="view/product_detail.php?id=<?= $row['id'] ?>">
                <img src="assets/images/<?= $row['image'] ?>" alt="">
                <div class="name"><?= $row['name'] ?></div>
                <div class="price"><?= number_format($row['price']) ?>đ</div>

                <div class="product-buttons">
                    <button class="btn-buy">Đặt may ngay</button>
                    <button class="btn-cart">Thêm vào giỏ hàng</button>
                </div>
            </a>
        <?php endwhile; ?>
    </main>
</div>

<section class="commit-section">
    <div class="commit-container">
        <div class="commit-item">
            <div class="commit-icon">👕</div>
            <h4>Đa dạng mẫu mã</h4>
            <p>Kho mẫu theo trend, hỗ trợ thiết kế theo yêu cầu</p>
        </div>
        <div class="commit-item">
            <div class="commit-icon">🏅</div>
            <h4>Cam kết chất lượng</h4>
            <p>Chất liệu cao cấp, đường may chỉnh chu</p>
        </div>
        <div class="commit-item">
            <div class="commit-icon">💰</div>
            <h4>Giá gốc tận xưởng</h4>
            <p>Không qua trung gian – giá tốt nhất</p>
        </div>
        <div class="commit-item">
<div class="commit-icon">🚚</div>
            <h4>Giao hàng tận nơi</h4>
            <p>Hỗ trợ giao hàng toàn quốc</p>
        </div>
    </div>
</section>

<!-- QUY TRÌNH ĐẶT MAY -->
<section class="process-section">
    <h2 class="process-title">Quy trình đặt may</h2>
    <p class="process-desc">
        Để mang đến một sản phẩm chất lượng và có tính thẩm mỹ,
        Xưởng may App Master đảm bảo 6 bước sản xuất tiêu chuẩn sau
    </p>

    <div class="process-container">
        <div class="process-item">
            <div class="process-icon">📝</div>
            <h4>Tiếp nhận và xử lý đơn hàng</h4>
            <p>Tư vấn yêu cầu, hỗ trợ thiết kế và lên mẫu để khách duyệt</p>
        </div>

        <div class="process-item">
            <div class="process-icon">💻</div>
            <h4>Báo giá & thiết kế sơ bộ</h4>
            <p>Trao đổi mẫu mã, chất liệu, báo giá và thiết kế sơ bộ</p>
        </div>

        <div class="process-item">
            <div class="process-icon">✅</div>
            <h4>Duyệt mẫu & chuẩn bị sản xuất</h4>
            <p>Tạo rập, chọn vải và cắt vải theo thiết kế</p>
        </div>

        <div class="process-item">
            <div class="process-icon">👕</div>
            <h4>Sản xuất hàng loạt</h4>
            <p>Tiến hành may, gắn tag theo số lượng đặt</p>
        </div>

        <div class="process-item">
            <div class="process-icon">🔍</div>
            <h4>Kiểm tra chất lượng</h4>
            <p>Kiểm tra màu sắc, đường may và chất lượng in/thêu</p>
        </div>

        <div class="process-item">
            <div class="process-icon">🚚</div>
            <h4>Giao hàng tận nơi</h4>
            <p>Giao đủ số lượng, đúng kích cỡ và hoàn tất thanh toán</p>
        </div>
    </div>
</section>



<footer class="footer">
    <div class="footer-container">

        <div class="footer-logo">
            <div class="logo-icon">
                <svg width="42" height="42" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 28L16 16L28 28L40 16L52 28V56H4V28Z" fill="none" stroke="#fff" stroke-width="2"/>
                    <rect x="22" y="38" width="12" height="18" fill="#fff"/>
                </svg>
            </div>
            <div class="logo-text">
                <span class="logo-name">XƯỞNG MAY APP MASTERS</span>
            </div>
        </div>

        <div class="footer-info">
            <p><strong>Địa chỉ:</strong> Trường Đại học Công nghiệp TP.HCM (IUH),  
                12 Nguyễn Văn Bảo, P.4, Q.Gò Vấp, TP.HCM</p>
            <p><strong>Điện thoại:</strong> 0989 123 456</p>
            <p><strong>Email:</strong> xuongmay@iuh.edu.vn</p>
        </div>

    </div>

    <div class="footer-bottom">
        COPYRIGHT © 2025 – BẢN QUYỀN THUỘC VỀ NHÓM APP MASTERS
    </div>
</footer>

</body>
</html>
