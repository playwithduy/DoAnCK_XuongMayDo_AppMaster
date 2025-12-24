<?php
session_start();
require_once "../config/database.php";
require_once "../models/XuatThanhPhamModel.php";

// Kiểm tra quyền
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='thukho'){
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$model = new XuatThanhPhamModel($conn);

// Xử lý submit form
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['confirm_xuat'])){
    $product_id = (int)($_POST['product_id'] ?? 0);
    $so_luong   = (int)($_POST['so_luong'] ?? 0);
    $don_gia    = (float)($_POST['don_gia'] ?? 0);
    $ly_do      = trim($_POST['ly_do'] ?? '');

    if($product_id && $so_luong>0 && $don_gia>0 && $ly_do){
        $products = $model->getProducts();
        foreach($products as $p){
            if($p['id']==$product_id && $so_luong <= $p['ton_kho']){
                $model->createXuat(
                    $user['id'],
                    $product_id,
                    $so_luong,
                    $don_gia,
                    $ly_do
                );
                break;
            }
        }
        header("Location: ../controllers/XuatThanhPhamController.php");
        exit;
    }
}

// Lấy danh sách sản phẩm còn tồn kho
$products = $model->getProducts();
// Lấy lịch sử phiếu xuất
$history  = $model->getHistory();

$data = [
    'user'     => $user,
    'products' => $products,
    'history'  => $history
];

require "../view/xuat_thanhpham.php";
