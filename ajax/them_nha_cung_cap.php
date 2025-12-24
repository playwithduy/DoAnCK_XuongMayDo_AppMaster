<?php
require_once "../config/database.php";

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($name === '') {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Tên nhà cung cấp không được để trống'
    ]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO suppliers(name, phone, address) VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $name, $phone, $address);
$stmt->execute();

echo json_encode([
    'status' => 'ok',
    'id'   => $stmt->insert_id,
    'name' => $name
]);
