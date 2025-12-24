<?php
// FILE: view/product_detail.php
session_start();
require_once "../config/database.php";

/* =====================================================
   1. KHỞI TẠO BIẾN (Tránh lỗi Undefined variable)
   ===================================================== */
$error = "";
$success = "";
$review_error = "";
$review_success = "";

// Mảng thống kê đánh giá
$stats = [
    'avg_rating' => 0,
    'total'      => 0,
    'star5'      => 0,
    'star4'      => 0,
    'star3'      => 0,
    'star2'      => 0,
    'star1'      => 0
];

/* =====================================================
   2. LẤY SẢN PHẨM
   ===================================================== */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prod = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if (!$prod) {
    die("Sản phẩm không tồn tại. <a href='../index.php'>Quay lại trang chủ</a>");
}

/* =====================================================
   3. XỬ LÝ ĐẶT MAY
   ===================================================== */
if (isset($_POST['dat_may'])) {

    if (!isset($_SESSION['user'])) {
        $error = "Bạn cần đăng nhập để đặt hàng";
    }
    elseif ($_SESSION['user']['role'] !== 'khachhang') {
        $error = "Chỉ khách hàng mới được đặt may";
    }
    elseif (empty($_POST['size'])) {
        $error = "Bạn chưa chọn size";
    }
    elseif (empty($_POST['address'])) { // Kiểm tra địa chỉ
        $error = "Vui lòng nhập địa chỉ nhận hàng";
    }
    else {
        $user_id  = (int)$_SESSION['user']['id'];
        $size     = $conn->real_escape_string($_POST['size']);
        $quantity = max(1, (int)$_POST['quantity']);
        $price    = (int)$prod['price'];
        $tongTien = $quantity * $price;
        $soDon    = "ONLINE-" . time();
        $diaChi   = $conn->real_escape_string($_POST['address']);

        // Kiểm tra kho
        if ($prod['ton_kho'] < $quantity) {
            $error = "Số lượng trong kho không đủ (Còn: {$prod['ton_kho']})";
        } 
        else {
            // Trừ kho
            $conn->query("UPDATE products SET ton_kho = ton_kho - $quantity WHERE id = $id");

            // Tạo Đơn Hàng - SỬA LẠI TÊN CỘT dia_diem_giao_hang CHO ĐÚNG DATABASE
            $sqlHeader = "INSERT INTO don_hang_ban (so_don_hang, user_id, ngay_lap, ngay_giao_du_kien, dia_diem_giao_hang, tong_tien, trang_thai) 
                          VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), ?, ?, 'Moi')";
            
            $stmtHeader = $conn->prepare($sqlHeader);
            
            if ($stmtHeader) {
                // Bind: s=string, i=int, s=string, d=double
                $stmtHeader->bind_param("sisd", $soDon, $user_id, $diaChi, $tongTien);
                
                if ($stmtHeader->execute()) {
                    $donHangId = $stmtHeader->insert_id;

                    // Tạo Chi Tiết
                    $sqlDetail = "INSERT INTO chi_tiet_don_hang_ban (don_hang_id, ten_san_pham, size, so_luong, don_gia, thanh_tien) 
                                  VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtDetail = $conn->prepare($sqlDetail);
                    $stmtDetail->bind_param("issidd", $donHangId, $prod['name'], $size, $quantity, $price, $tongTien);
                    $stmtDetail->execute();

                    $success = "Đặt hàng thành công! Mã đơn: $soDon";
                    $prod['ton_kho'] -= $quantity; // Cập nhật hiển thị
                } else {
                    $error = "Lỗi thực thi: " . $stmtHeader->error;
                }
            } else {
                $error = "Lỗi chuẩn bị câu lệnh: " . $conn->error;
            }
        }
    }
}

/* =====================================================
   4. XỬ LÝ ĐÁNH GIÁ
   ===================================================== */
if (isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user'])) {
        $review_error = "Vui lòng đăng nhập để đánh giá.";
    } elseif (empty($_POST['rating'])) {
        $review_error = "Bạn chưa chọn số sao";
    } else {
        $name = $conn->real_escape_string($_POST['customer_name']);
        $rating = (int)$_POST['rating'];
        $comment = $conn->real_escape_string($_POST['comment']);

        $stmtReview = $conn->prepare("INSERT INTO product_reviews(product_id, customer_name, rating, comment) VALUES(?, ?, ?, ?)");
        $stmtReview->bind_param("isis", $id, $name, $rating, $comment);
        
        if ($stmtReview->execute()) {
            $review_success = "Gửi đánh giá thành công!";
        } else {
            $review_error = "Lỗi khi gửi đánh giá.";
        }
    }
}

/* =====================================================
   5. TÍNH TOÁN THỐNG KÊ (STATS)
   ===================================================== */
$sqlStats = "SELECT 
        AVG(rating) as avg_rating,
        COUNT(*) as total,
        SUM(rating=5) as star5,
        SUM(rating=4) as star4,
        SUM(rating=3) as star3,
        SUM(rating=2) as star2,
        SUM(rating=1) as star1
    FROM product_reviews WHERE product_id=$id";

$resultStats = $conn->query($sqlStats);

if ($resultStats && $resultStats->num_rows > 0) {
    $row = $resultStats->fetch_assoc();
    if ($row['total'] > 0) {
        $stats['avg_rating'] = round((float)$row['avg_rating'], 1);
        $stats['total']      = (int)$row['total'];
        $stats['star5']      = (int)$row['star5'];
        $stats['star4']      = (int)$row['star4'];
        $stats['star3']      = (int)$row['star3'];
        $stats['star2']      = (int)$row['star2'];
        $stats['star1']      = (int)$row['star1'];
    }
}

// Lấy danh sách review
$filter = isset($_GET['star']) ? (int)$_GET['star'] : 0;
$where = $filter ? "AND rating=$filter" : "";
$reviews = $conn->query("SELECT * FROM product_reviews WHERE product_id=$id $where ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($prod['name']) ?></title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
    body { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); padding-bottom: 50px; }
    
    header { width: 100%; height: 64px; background: linear-gradient(90deg, #0f172a, #1e293b); display: flex; align-items: center; justify-content: space-between; padding: 0 30px; color: #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.35); position: relative; z-index: 1000; }
    header .logo img { height: 40px; object-fit: contain; }
    .nav-center { display: flex; gap: 32px; }
    .nav-center a { color: #e5e7eb; text-decoration: none; font-size: 15px; font-weight: 500; padding-bottom: 4px; transition: 0.3s; }
    .nav-center a:hover { color: #ffffff; border-bottom: 2px solid #38bdf8; }
    .nav-right { display: flex; align-items: center; gap: 18px; font-size: 14px; }
    .nav-right a { color: #e5e7eb; text-decoration: none; }
    .logout-btn { padding: 6px 14px; border: 1px solid #38bdf8; border-radius: 4px; color: #38bdf8 !important; }
    .logout-btn:hover { background: #38bdf8; color: #0f172a !important; }

    .container { width: 90%; max-width: 1200px; margin: 30px auto; }
    .detail-box { background: white; padding: 25px; border-radius: 14px; box-shadow: 0 5px 15px #ddd; margin-bottom: 25px; }
    .detail-flex { display: flex; gap: 40px; }
    .detail-img { width: 400px; height: 400px; object-fit: cover; border-radius: 12px; border: 1px solid #eee; }
    
    .price { color: #d32f2f; font-size: 28px; font-weight: bold; margin: 15px 0; }
    .stock-info { margin: 10px 0; font-size: 15px; color: #555; }
    .stock-info strong { color: #007bff; }
    
    .msg-error { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin: 15px 0; }
    .msg-success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin: 15px 0; }

    /* SIZE OPTIONS */
    .size-options { display: flex; gap: 10px; margin: 15px 0; }
    .size-options input { display: none; }
    .size-box { border: 1px solid #ddd; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; color: #555; transition: 0.2s; }
    .size-options input:checked + .size-box { border-color: #ff6600; background: #fff3eb; color: #ff6600; }
    .size-box:hover { border-color: #ff6600; }

    /* FORM INPUTS */
    input[type="number"], textarea, input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; margin-top: 5px; font-size: 14px; }
    textarea { resize: vertical; min-height: 80px; }
    
    .btn-submit { background: linear-gradient(135deg, #ff6600, #ff4500); color: white; border: none; padding: 14px 30px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.2s; margin-top: 20px; width: 100%; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(255, 69, 0, 0.3); }

    /* RATING */
    .rating-summary { background: #fff7f5; display: flex; align-items: center; gap: 30px; }
    .big-score { font-size: 48px; color: #ff5722; font-weight: bold; }
    .rating-buttons button { background: white; border: 1px solid #ddd; padding: 6px 12px; margin: 4px; border-radius: 4px; cursor: pointer; }
    
    .star-rating { font-size: 30px; color: #ddd; cursor: pointer; }
    .star-rating span.active { color: #ffcc00; }
    
    .footer { background: #1f2937; color: #fff; padding: 30px 0; margin-top: 50px; text-align: center; }
</style>
</head>
<body>

<header>
    <div class="logo">
        <span style="font-weight:bold; font-size:18px;">XƯỞNG MAY</span>
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
            <?php if ($_SESSION['user']['role'] === 'khachhang'): ?>
                <a href="donhang_cuatoi.php">Đơn hàng</a>
            <?php endif; ?>
            <span>Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
            <a href="../controllers/LogoutController.php" class="logout-btn">Thoát</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">

    <div class="detail-box detail-flex">
        <img class="detail-img" src="../assets/images/<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">

        <div style="flex:1">
            <h2><?= htmlspecialchars($prod['name']) ?></h2>
            <div class="price"><?= number_format($prod['price']) ?> đ</div>
            
            <p class="stock-info">Tồn kho: <strong><?= $prod['ton_kho'] ?> <?= $prod['unit'] ?></strong></p>
            <p style="color:#666; line-height:1.6; margin-bottom:20px"><?= nl2br(htmlspecialchars($prod['description'])) ?></p>

            <?php if ($error): ?> <div class="msg-error"><?= $error ?></div> <?php endif; ?>
            <?php if ($success): ?> <div class="msg-success"><?= $success ?></div> <?php endif; ?>

            <form method="POST">
                <p><b>Chọn Size:</b></p>
                <div class="size-options">
                    <?php foreach (['S','M','L','XL','XXL'] as $s): ?>
                        <label>
                            <input type="radio" name="size" value="<?= $s ?>">
                            <div class="size-box"><?= $s ?></div>
                        </label>
                    <?php endforeach; ?>
                </div>

                <p><b>Số lượng:</b></p>
                <input type="number" name="quantity" value="1" min="1" max="<?= $prod['ton_kho'] ?>" style="width:100px">
                
                <br><br>
                <p><b>Địa chỉ nhận hàng:</b></p>
                <textarea name="address" placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện..." required></textarea>

                <?php if ($prod['ton_kho'] > 0): ?>
                    <button type="submit" name="dat_may" class="btn-submit">ĐẶT MAY NGAY</button>
                <?php else: ?>
                    <button type="button" class="btn-submit" style="background:#ccc; cursor:not-allowed">HẾT HÀNG</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="detail-box rating-summary">
        <div style="text-align:center; padding-right:30px; border-right:1px solid #ddd">
            <div class="big-score"><?= $stats['avg_rating'] ?></div>
            <div style="color:#f4c150; font-size:20px"><?= str_repeat("★", round($stats['avg_rating'])) ?></div>
        </div>
        <div class="rating-buttons">
            <button onclick="location.href='?id=<?= $id ?>'">Tất cả (<?= $stats['total'] ?>)</button>
            <button onclick="location.href='?id=<?= $id ?>&star=5'">5 Sao (<?= $stats['star5'] ?>)</button>
            <button onclick="location.href='?id=<?= $id ?>&star=4'">4 Sao (<?= $stats['star4'] ?>)</button>
            <button onclick="location.href='?id=<?= $id ?>&star=3'">3 Sao (<?= $stats['star3'] ?>)</button>
            <button onclick="location.href='?id=<?= $id ?>&star=2'">2 Sao (<?= $stats['star2'] ?>)</button>
            <button onclick="location.href='?id=<?= $id ?>&star=1'">1 Sao (<?= $stats['star1'] ?>)</button>
        </div>
    </div>

    <div class="detail-box">
        <h3>Viết đánh giá</h3>
        <?php if($review_error): ?><div class="msg-error"><?= $review_error ?></div><?php endif; ?>
        <?php if($review_success): ?><div class="msg-success"><?= $review_success ?></div><?php endif; ?>

        <form method="POST">
            <div class="star-rating">
                <input type="hidden" name="rating" id="rating-value" value="5">
                <span data-star="1">★</span><span data-star="2">★</span><span data-star="3">★</span><span data-star="4">★</span><span data-star="5" class="active">★</span>
            </div>
            <input type="text" name="customer_name" placeholder="Họ tên của bạn" required 
                   value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['username']) : '' ?>">
            <textarea name="comment" placeholder="Chia sẻ cảm nhận về sản phẩm..." required></textarea>
            <button type="submit" name="submit_review" class="btn-submit" style="width:auto; margin-top:10px; padding:10px 25px;">Gửi Đánh Giá</button>
        </form>
    </div>

    <div class="detail-box">
        <h3>Khách hàng nhận xét</h3>
        <?php if($reviews->num_rows): while($r=$reviews->fetch_assoc()): ?>
            <div style="border-bottom:1px solid #eee; padding:15px 0">
                <div style="display:flex; justify-content:space-between">
                    <strong><?= htmlspecialchars($r['customer_name']) ?></strong>
                    <span style="color:#999; font-size:12px"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                </div>
                <div style="color:#f4c150"><?= str_repeat("★", $r['rating']) ?></div>
                <p style="margin-top:5px; color:#444"><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
            </div>
        <?php endwhile; else: ?>
            <p style="color:#888; text-align:center">Chưa có đánh giá nào.</p>
        <?php endif; ?>
    </div>

</div>

<footer class="footer">
    <p>COPYRIGHT © 2025 – BẢN QUYỀN THUỘC VỀ XƯỞNG MAY APP MASTERS</p>
</footer>

<script>
    // Script xử lý chọn sao
    const stars = document.querySelectorAll(".star-rating span");
    const input = document.getElementById("rating-value");
    stars.forEach((s, idx) => {
        s.onclick = () => {
            const val = s.dataset.star;
            input.value = val;
            stars.forEach((x, i) => {
                x.classList.toggle("active", i < val);
            });
        }
    });
</script>

</body>
</html>