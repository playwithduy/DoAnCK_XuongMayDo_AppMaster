<?php
// Dữ liệu được truyền từ Controller: $tab, $dataReport
?>
<style>
    /* ... (Giữ nguyên CSS của bạn) ... */
    .tk-container { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    .tk-header { margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    .tk-title { margin: 0; color: #0f172a; font-size: 1.5rem; }
    
    .tk-tabs { display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
    .tk-tab-btn {
        padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.95rem;
        transition: all 0.2s; background: #f1f5f9; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .tk-tab-btn:hover { background: #e2e8f0; color: #334155; }
    .tk-tab-btn.active { background: #0D47A1; color: #fff; box-shadow: 0 4px 6px rgba(13, 71, 161, 0.3); }

    .tk-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .tk-table th { background: #f8fafc; color: #475569; padding: 12px; text-align: left; font-weight: 700; border-bottom: 2px solid #e2e8f0; font-size: 0.9rem; }
    .tk-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    .tk-table tr:hover { background-color: #f8fafc; }

    .badge-warn { background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .badge-ok { background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    
    .mini-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .mini-card { background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; text-align: center; }
    .mini-val { font-size: 1.8rem; font-weight: 800; color: #0D47A1; margin: 10px 0; }
    .mini-label { color: #64748b; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; }
</style>

<div class="tk-container">
    <div class="tk-header">
        <h2 class="tk-title">📊 Trung Tâm Báo Cáo & Thống Kê</h2>
    </div>

    <div class="tk-tabs">
        <button onclick="loadContent('../controllers/GiamDocController.php?action=thong_ke&tab=dashboard')" class="tk-tab-btn <?= $tab=='dashboard'?'active':'' ?>">
            <i class="fas fa-chart-pie"></i> Tổng Quan
        </button>
        <button onclick="loadContent('../controllers/GiamDocController.php?action=thong_ke&tab=kho_tp')" class="tk-tab-btn <?= $tab=='kho_tp'?'active':'' ?>">
            <i class="fas fa-tshirt"></i> Tồn Kho Thành Phẩm
        </button>
        <button onclick="loadContent('../controllers/GiamDocController.php?action=thong_ke&tab=kho_nl')" class="tk-tab-btn <?= $tab=='kho_nl'?'active':'' ?>">
            <i class="fas fa-layer-group"></i> Tồn Kho Nguyên Liệu
        </button>
        <button onclick="loadContent('../controllers/GiamDocController.php?action=thong_ke&tab=xuat_nhap')" class="tk-tab-btn <?= $tab=='xuat_nhap'?'active':'' ?>">
            <i class="fas fa-history"></i> Lịch Sử Xuất/Nhập
        </button>
    </div>

    <div class="tk-content">
        
        <?php if ($tab == 'dashboard'): ?>
            <div class="mini-grid">
                <div class="mini-card">
                    <div class="mini-label">Tổng SP Tồn Kho</div>
                    <div class="mini-val"><?= number_format($dataReport['tong_tp'] ?? 0) ?></div>
                </div>
                <div class="mini-card">
                    <div class="mini-label">Giá Trị Tồn Kho (TP)</div>
                    <div class="mini-val" style="color:#10b981"><?= number_format($dataReport['gia_tri_tp'] ?? 0) ?> ₫</div>
                </div>
                <div class="mini-card">
                    <div class="mini-label">Tổng Nguyên Liệu</div>
                    <div class="mini-val" style="color:#f59e0b"><?= number_format($dataReport['tong_nl'] ?? 0) ?></div>
                </div>
            </div>
            <p style="text-align:center; color:#64748b">Chọn các tab phía trên để xem báo cáo chi tiết.</p>

        <?php elseif ($tab == 'kho_tp'): ?>
            <h3 style="color:#0D47A1; margin-top:0">📦 Báo Cáo Tồn Kho Thành Phẩm</h3>
            <table class="tk-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Đơn Vị</th>
                        <th>Tồn Kho</th>
                        <th>Đơn Giá</th>
                        <th>Tổng Giá Trị</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($dataReport)): ?>
                        <tr><td colspan="7" style="text-align:center; padding:20px">Kho trống</td></tr>
                    <?php else: foreach ($dataReport as $item): ?>
                        <tr>
                            <td>#<?= $item['id'] ?></td>
                            <td style="font-weight:600"><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td style="font-weight:bold; font-size:1.1em"><?= $item['ton_kho'] ?></td>
                            <td><?= number_format($item['price']) ?> ₫</td>
                            <td><?= number_format($item['gia_tri_ton']) ?> ₫</td>
                            <td>
                                <?php if ($item['ton_kho'] < 20): ?>
                                    <span class="badge-warn">⚠ Sắp hết</span>
                                <?php else: ?>
                                    <span class="badge-ok">✔ Ổn định</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

        <?php elseif ($tab == 'kho_nl'): ?>
            <h3 style="color:#0D47A1; margin-top:0">🧵 Báo Cáo Tồn Kho Nguyên Liệu</h3>
            <table class="tk-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Nguyên Liệu</th>
                        <th>Đơn Vị</th>
                        <th>Tồn Kho</th>
                        <th>Cảnh Báo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($dataReport)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:20px">Kho trống</td></tr>
                    <?php else: foreach ($dataReport as $item): ?>
                        <tr>
                            <td>#<?= $item['id'] ?></td>
                            <td style="font-weight:600"><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td style="font-weight:bold; font-size:1.1em"><?= $item['ton_kho'] ?></td>
                            <td>
                                <?php if ($item['ton_kho'] < 100): ?>
                                    <span class="badge-warn">⚠ Cần nhập</span>
                                <?php else: ?>
                                    <span class="badge-ok">✔ Đủ dùng</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

        <?php elseif ($tab == 'xuat_nhap'): ?>
            <h3 style="color:#0D47A1; margin-top:0">🔄 Nhật Ký Giao Dịch Kho (50 gần nhất)</h3>
            <table class="tk-table">
                <thead>
                    <tr>
                        <th>Loại Giao Dịch</th>
                        <th>Mã Phiếu</th>
                        <th>Ngày Giờ</th>
                        <th>Ghi Chú / Lý Do</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($dataReport)): ?>
                        <tr><td colspan="4" style="text-align:center; padding:20px">Chưa có giao dịch</td></tr>
                    <?php else: foreach ($dataReport as $item): ?>
                        <tr>
                            <td>
                                <?php if (strpos($item['loai'], 'Nhập') !== false): ?>
                                    <span style="color:#166534; font-weight:bold"><i class="fas fa-arrow-down"></i> <?= $item['loai'] ?></span>
                                <?php else: ?>
                                    <span style="color:#b45309; font-weight:bold"><i class="fas fa-arrow-up"></i> <?= $item['loai'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight:bold">#<?= $item['ma_phieu'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($item['ngay'])) ?></td>
                            <td style="color:#64748b"><?= htmlspecialchars($item['ghi_chu'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>