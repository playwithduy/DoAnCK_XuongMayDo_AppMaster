<?php
require "../config/database.php";
require "../models/NhapThanhPhamModel.php";

$model = new NhapThanhPhamModel($conn);
$p = $model->getPhieu((int)$_GET['id']);

header("Content-Type:text/html");
?>
<h2 style="text-align:center">PHIẾU NHẬP THÀNH PHẨM</h2>
<p>Ngày: <?= $p['ngay_nhap'] ?></p>
<p>Tên TP: <?= $p['ten_tp'] ?></p>
<p>Xưởng: <?= $p['xuong'] ?></p>
<p>Số lượng: <?= $p['so_luong'] ?></p>
<p>QC: <?= $p['qc_ket_qua']=='dat'?'Đạt':'Không đạt' ?></p>
<p>Ghi chú: <?= $p['note'] ?></p>

<br><br>
<div style="display:flex;justify-content:space-between">
<div>Người nhập<br><b><?= $p['nguoi_nhap'] ?></b></div>
<div>Ký xác nhận<br>_____________</div>
</div>

<script>window.print()</script>
