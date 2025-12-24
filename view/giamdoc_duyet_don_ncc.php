<div style="background:#fff; padding:25px; border-radius:12px; border:1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px">
        <h3 style="margin:0; color:#0f172a;">
            📄 Danh Sách Đơn Đặt Hàng Chờ Phê Duyệt
        </h3>
        <button onclick="loadContent('../controllers/GiamDocController.php?action=duyet_don_ncc')" style="border:none; background:transparent; cursor:pointer; color:#3b82f6" title="Làm mới">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="background:#f8fafc; color:#64748b; text-align:left">
                <th style="padding:12px">Mã Đơn</th>
                <th style="padding:12px">Nhà Cung Cấp</th>
                <th style="padding:12px">Ngày Lập</th>
                <th style="padding:12px">Tổng Tiền</th>
                <th style="padding:12px">Người Lập</th>
                <th style="padding:12px; text-align:center; width:250px">Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($dsDonNCC)): foreach ($dsDonNCC as $row): ?>
                <tr style="border-bottom:1px solid #f1f5f9; transition: background 0.2s;">
                    <td style="padding:12px; font-weight:bold; color:#3b82f6"><?= $row['ma_don_hang'] ?></td>
                    <td style="padding:12px; font-weight:600"><?= htmlspecialchars($row['ten_ncc']) ?></td>
                    <td style="padding:12px"><?= date('d/m/Y', strtotime($row['ngay_lap'])) ?></td>
                    <td style="padding:12px; font-weight:bold; color:#10b981"><?= number_format($row['tong_tien']) ?> ₫</td>
                    <td style="padding:12px"><?= htmlspecialchars($row['nguoi_lap']) ?></td>
                    
                    <td style="padding:12px; text-align:center; display:flex; gap:8px; justify-content:center">
                        <button onclick="openModalChiTiet(<?= $row['id'] ?>, '<?= $row['ma_don_hang'] ?>')" 
                                title="Xem chi tiết"
                                style="background:#e0f2fe; color:#0369a1; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; transition:0.2s">
                            <i class="fas fa-eye"></i>
                        </button>

                        <button onclick="pheDuyet(<?= $row['id'] ?>)" 
                                title="Phê duyệt ngay"
                                style="background:#dcfce7; color:#166534; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; transition:0.2s">
                            <i class="fas fa-check"></i>
                        </button>

                        <button onclick="tuChoi(<?= $row['id'] ?>)" 
                                title="Từ chối đơn này"
                                style="background:#fee2e2; color:#991b1b; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; transition:0.2s">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="padding:40px; text-align:center; color:#94a3b8">
                        <i class="fas fa-check-circle" style="font-size:30px; margin-bottom:10px; display:block; color:#cbd5e1"></i>
                        Hiện không có đơn hàng nào cần phê duyệt.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>