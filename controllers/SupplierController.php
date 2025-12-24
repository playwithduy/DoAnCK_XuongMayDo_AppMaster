<?php
session_start();
require_once "../config/database.php";
require_once "../models/SupplierModel.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'thukho') {
    echo json_encode(["success" => false]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$name    = trim($data['name'] ?? '');
$phone   = trim($data['phone'] ?? '');
$address = trim($data['address'] ?? '');

if ($name === '') {
    echo json_encode(["success" => false, "message" => "Tên NCC rỗng"]);
    exit;
}

$model = new SupplierModel($conn);
$id = $model->insert($name, $phone, $address);

echo json_encode([
    "success" => true,
    "id" => $id
]);
