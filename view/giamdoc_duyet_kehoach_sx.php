<?php
// FILE: view/giamdoc_duyet_kehoach_sx.php
// View con - được load vào mainContent qua AJAX

// Kiểm tra dữ liệu
if (!isset($dsKeHoach)) {
    echo '<p style="color:red; text-align:center">Lỗi: Không có dữ liệu kế hoạch!</p>';
    exit;
}
?>

<div style="background:#fff; padding:25px; border-radius:12px; border:1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px">
        <h3 style="margin:0; color:#0f172a;">
            🏭 Duyệt Kế Hoạch Sản Xuất
        </h3>
        <button onclick="loadContent('../controllers/GiamDocController.php?action=duyet_kehoach_sx')" 
                style="border:none; background:transparent; cursor:pointer; color:#3b82f6; font-size:1.1rem" 
                title="Làm mới">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; color:#64748b; text-align:left">
                    <th style="padding:12px; border-bottom:2px solid #e2e8f0">Mã KH</th>
                    <th style="padding:12px; border-bottom:2px solid #e2e8f0">Đơn Hàng</th>
                    <th style="padding:12px; border-bottom:2px solid #e2e8f0">Sản Phẩm</th>
                    <th style="padding:12px; border-bottom:2px solid #e2e8f0">Chuyền</th>
                    <th style="padding:12px; border-bottom:2px solid #e2e8f0; text-align:center">SL / Ngày</th>
                    <th style="padding:12px; border-bottom:2px solid #e2e8f0; text-align:center">Thời Gian</th>
                    <th style="padding:12px; border-bottom:2px solid #e2e8f0; text-align:center; width:180px">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$dsKeHoach || $dsKeHoach->num_rows == 0): ?>
                    <tr>
                        <td colspan="7" style="padding:40px; text-align:center; color:#94a3b8">
                            <i class="fas fa-check-circle" style="font-size:2.5rem; margin-bottom:10px; display:block; color:#cbd5e1"></i>
                            <strong style="font-size:1.1rem; display:block; margin-bottom:5px">Không có kế hoạch nào cần duyệt</strong>
                            <span style="font-size:0.9rem">Tất cả kế hoạch sản xuất đã được xử lý</span>
                        </td>
                    </tr>
                <?php else: 
                    while ($row = $dsKeHoach->fetch_assoc()): 
                        // Xử lý dữ liệu an toàn
                        $maKH = htmlspecialchars($row['ma_ke_hoach'] ?? '---');
                        $maDH = htmlspecialchars($row['ma_don_kh'] ?? '---');
                        $tenSP = htmlspecialchars($row['ten_san_pham'] ?? 'Chưa xác định');
                        $tenChuyen = htmlspecialchars($row['ten_chuyen'] ?? 'Chưa phân');
                        $soLuong = number_format($row['so_luong'] ?? 0);
                        $slNgay = number_format($row['san_luong_ngay'] ?? 0);
                        $ngayBD = date('d/m/Y', strtotime($row['ngay_bat_dau']));
                        $ngayKT = date('d/m/Y', strtotime($row['ngay_ket_thuc']));
                ?>
                    <tr style="border-bottom:1px solid #f1f5f9; transition: background 0.2s;" 
                        onmouseover="this.style.background='#f8fafc'" 
                        onmouseout="this.style.background='transparent'">
                        <td style="padding:12px; font-weight:bold; color:#3b82f6; font-size:0.9rem">
                            <?= $maKH ?>
                        </td>
                        <td style="padding:12px; font-weight:600; color:#0f172a">
                            <?= $maDH ?>
                        </td>
                        <td style="padding:12px; font-size:0.95rem">
                            <?= $tenSP ?>
                            <div style="font-size:0.8rem; color:#64748b; margin-top:2px">
                                Tổng: <strong><?= $soLuong ?></strong> sản phẩm
                            </div>
                        </td>
                        <td style="padding:12px; font-size:0.95rem">
                            <?= $tenChuyen ?>
                        </td>
                        <td style="padding:12px; text-align:center; font-weight:bold; color:#059669">
                            <?= $slNgay ?> sp
                        </td>
                        <td style="padding:12px; text-align:center; font-size:0.9rem; color:#64748b">
                            <div><?= $ngayBD ?></div>
                            <div style="color:#cbd5e1">↓</div>
                            <div><?= $ngayKT ?></div>
                        </td>
                        
                        <td style="padding:12px; text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center">
                                <button onclick="openModalKeHoach(<?= $row['id'] ?>, '<?= $maKH ?>')" 
                                        title="Xem chi tiết"
                                        style="background:#e0f2fe; color:#0369a1; border:none; padding:8px 10px; border-radius:6px; cursor:pointer; font-size:0.9rem; transition:0.2s">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button onclick="pheDuyetKeHoach(<?= $row['id'] ?>)" 
                                        title="Phê duyệt"
                                        style="background:#dcfce7; color:#166534; border:none; padding:8px 10px; border-radius:6px; cursor:pointer; font-size:0.9rem; transition:0.2s">
                                    <i class="fas fa-check"></i>
                                </button>

                                <button onclick="tuChoiKeHoach(<?= $row['id'] ?>)" 
                                        title="Từ chối"
                                        style="background:#fee2e2; color:#991b1b; border:none; padding:8px 10px; border-radius:6px; cursor:pointer; font-size:0.9rem; transition:0.2s">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Hover effects cho buttons */
    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>