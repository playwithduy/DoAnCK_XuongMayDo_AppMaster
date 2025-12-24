<?php
session_start();
require_once "../config/database.php";
require_once "../models/LichLamViecModel.php";

// Kiểm tra quyền
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'congnhan') {
    header("Location: ../login.php"); exit;
}

$model = new LichLamViecModel($conn);
$user = $_SESSION['user'];

// --- 1. XỬ LÝ THAM SỐ ---
// Mặc định là 'week' 
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'week';

// Nếu URL có biến date thì lấy, nếu không thì lấy ngày hôm nay
$date_ref = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$timestamp = strtotime($date_ref);

// --- 2. TÍNH TOÁN KHOẢNG THỜI GIAN ---
$start_date = '';
$end_date = '';
$title = '';
$prev_link = '';
$next_link = '';

switch ($mode) {
    case 'month':
        // Xem Tháng: Từ ngày 1 đến ngày cuối tháng
        $start_date = date('Y-m-01', $timestamp);
        $end_date = date('Y-m-t', $timestamp);
        $title = "Tháng " . date('m/Y', $timestamp);
        
        // Điều hướng: Nhảy +/- 1 tháng
        $prev_link = "?mode=month&date=" . date('Y-m-d', strtotime('-1 month', $timestamp));
        $next_link = "?mode=month&date=" . date('Y-m-d', strtotime('+1 month', $timestamp));
        break;

    case 'day':
        // Xem Ngày: Start = End = Ngày đang chọn
        $start_date = $date_ref;
        $end_date = $date_ref;
        
        // Format tiêu đề: Thứ..., Ngày...
        $day_names = ['Sunday'=>'Chủ Nhật','Monday'=>'Thứ 2','Tuesday'=>'Thứ 3','Wednesday'=>'Thứ 4','Thursday'=>'Thứ 5','Friday'=>'Thứ 6','Saturday'=>'Thứ 7'];
        $day_vn = $day_names[date('l', $timestamp)];
        $title = "$day_vn, " . date('d/m/Y', $timestamp);
        
        // Điều hướng: Nhảy +/- 1 ngày
        $prev_link = "?mode=day&date=" . date('Y-m-d', strtotime('-1 day', $timestamp));
        $next_link = "?mode=day&date=" . date('Y-m-d', strtotime('+1 day', $timestamp));
        break;

    case 'week':
    default:
        // Xem Tuần: Từ Thứ 2 đến Chủ Nhật
        $day_of_week = date('N', $timestamp); // 1(T2) -> 7(CN)
        
        // Tính ngày đầu tuần (T2) và cuối tuần (CN)
        $start_date = date('Y-m-d', strtotime('-' . ($day_of_week - 1) . ' days', $timestamp));
        $end_date = date('Y-m-d', strtotime('+' . (7 - $day_of_week) . ' days', $timestamp));
        
        $title = "Tuần: " . date('d/m', strtotime($start_date)) . " - " . date('d/m/Y', strtotime($end_date));
        
        // Điều hướng: Nhảy +/- 1 tuần
        $prev_link = "?mode=week&date=" . date('Y-m-d', strtotime('-1 week', $timestamp));
        $next_link = "?mode=week&date=" . date('Y-m-d', strtotime('+1 week', $timestamp));
        break;
}

// --- 3. LẤY DỮ LIỆU ---
$schedules = $model->getLichLam($user['id'], $start_date, $end_date);

// --- 4. GỬI DỮ LIỆU SANG VIEW ---
$data = [
    'user' => $user,
    'mode' => $mode,
    'title' => $title,
    'schedules' => $schedules,
    'date_ref' => $date_ref,
    'links' => ['prev' => $prev_link, 'next' => $next_link],
    'range' => ['start' => $start_date, 'end' => $end_date]
];

require "../view/cong_nhan_lich_lam.php";
?>