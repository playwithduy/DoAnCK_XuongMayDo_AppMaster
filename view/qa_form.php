<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lập biên bản kiểm tra</title>
    <style>
        body{margin:0;font-family:Segoe UI,sans-serif;background:#f4f6f8}
        .container{max-width:900px;margin:30px auto;background:#fff;padding:30px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        h2{color:#0D47A1;border-bottom:2px solid #eee;padding-bottom:10px}
        
        .form-group{margin-bottom:15px}
        label{display:block;font-weight:bold;margin-bottom:5px;color:#333}
        input, select, textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:5px;box-sizing:border-box}
        input[readonly]{background:#f9f9f9;color:#666}
        
        /* Bảng tiêu chí */
        .criteria-table{width:100%;margin-top:20px;border-collapse:collapse}
        .criteria-table th, .criteria-table td{border:1px solid #ddd;padding:8px;text-align:left}
        .criteria-table th{background:#f1f1f1}
        
        .btn-submit{background:#28a745;color:#fff;border:none;padding:12px 25px;font-size:16px;border-radius:5px;cursor:pointer;margin-top:20px}
        .btn-submit:hover{background:#218838}
        .btn-back{background:#6c757d;color:#fff;text-decoration:none;padding:10px 20px;border-radius:5px;margin-right:10px}
    </style>
</head>
<body>

<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <h2>📝 LẬP BIÊN BẢN KIỂM TRA CHẤT LƯỢNG (QA)</h2>
        <a href="QAController.php" class="btn-back">Quay lại</a>
    </div>

    <form action="QAController.php" method="POST">
        <input type="hidden" name="save_report" value="1">
        <input type="hidden" name="phieu_yc_id" value="<?= $request_data['id'] ?>">

        <div style="display:grid;grid-template-columns: 1fr 1fr; gap:20px">
            <div class="form-group">
                <label>Sản phẩm:</label>
                <input type="text" name="ten_sp" value="<?= htmlspecialchars($request_data['ten_san_pham']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Mã sản phẩm:</label>
                <input type="text" name="ma_sp" value="<?= htmlspecialchars($request_data['ma_san_pham']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Lô sản xuất:</label>
                <input type="text" name="lo_sx" value="<?= htmlspecialchars($request_data['lo_san_xuat']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Ngày sản xuất:</label>
                <input type="date" name="ngay_sx" required>
            </div>
            <div class="form-group">
                <label>Người kiểm tra (QA):</label>
                <input type="text" name="ten_qa" value="<?= $_SESSION['user']['fullname'] ?? 'Admin' ?>" readonly>
            </div>
            <div class="form-group">
                <label>Ngày kiểm tra:</label>
                <input type="datetime-local" name="ngay_kt" value="<?= date('Y-m-d\TH:i') ?>">
            </div>
        </div>

        <h3 style="margin-top:30px;color:#0D47A1">Chi tiết tiêu chí kiểm tra</h3>
        <table class="criteria-table" id="tableCriteria">
            <thead>
                <tr>
                    <th width="30%">Tiêu chí kiểm tra</th>
                    <th width="25%">Tiêu chuẩn yêu cầu</th>
                    <th width="15%">Kết quả</th>
                    <th>Ghi chú lỗi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="tieu_chi[]" value="Kích thước (Dài x Rộng)" placeholder="Nhập tên tiêu chí..."></td>
                    <td><input type="text" name="tieu_chuan[]" value="Dung sai +/- 2cm"></td>
                    <td>
                        <select name="ket_qua_ct[]">
                            <option value="Dat">Đạt</option>
                            <option value="KhongDat">Không đạt</option>
                        </select>
                    </td>
                    <td><input type="text" name="ghi_chu[]"></td>
                </tr>
                <tr>
                    <td><input type="text" name="tieu_chi[]" value="Ngoại quan / Màu sắc"></td>
                    <td><input type="text" name="tieu_chuan[]" value="Không lệch màu, không bẩn"></td>
                    <td>
                        <select name="ket_qua_ct[]">
                            <option value="Dat">Đạt</option>
                            <option value="KhongDat">Không đạt</option>
                        </select>
                    </td>
                    <td><input type="text" name="ghi_chu[]"></td>
                </tr>
                 <tr>
                    <td><input type="text" name="tieu_chi[]" value="Đường may / Mối nối"></td>
                    <td><input type="text" name="tieu_chuan[]" value="Chắc chắn, không bung chỉ"></td>
                    <td>
                        <select name="ket_qua_ct[]">
                            <option value="Dat">Đạt</option>
                            <option value="KhongDat">Không đạt</option>
                        </select>
                    </td>
                    <td><input type="text" name="ghi_chu[]"></td>
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="addRow()" style="margin-top:10px;padding:5px 10px;cursor:pointer">+ Thêm dòng tiêu chí</button>

        <h3 style="margin-top:30px;color:#0D47A1">Kết luận cuối cùng</h3>
        <div class="form-group">
            <label>Kết quả chung:</label>
            <select name="ket_qua_chung" style="font-weight:bold; color:#0D47A1">
                <option value="Dat">✅ ĐẠT - Cho phép xuất kho</option>
                <option value="KhongDat">❌ KHÔNG ĐẠT - Cần xử lý lại</option>
            </select>
        </div>

        <div class="form-group">
            <label>Khuyến nghị / Ghi chú:</label>
            <textarea name="khuyen_nghi" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <label>Hướng dẫn khắc phục (nếu không đạt):</label>
            <textarea name="huong_dan" rows="3"></textarea>
        </div>

        <div style="text-align:center">
            <button type="submit" class="btn-submit">💾 LƯU BIÊN BẢN</button>
        </div>
    </form>
</div>

<script>
    // Hàm thêm dòng mới cho bảng tiêu chí
    function addRow() {
        var table = document.getElementById("tableCriteria");
        var row = table.insertRow(-1); // Thêm vào cuối bảng
        
        row.innerHTML = `
            <td><input type="text" name="tieu_chi[]" placeholder="Nhập tiêu chí..."></td>
            <td><input type="text" name="tieu_chuan[]"></td>
            <td>
                <select name="ket_qua_ct[]">
                    <option value="Dat">Đạt</option>
                    <option value="KhongDat">Không đạt</option>
                </select>
            </td>
            <td><input type="text" name="ghi_chu[]"></td>
        `;
    }
</script>

</body>
</html>