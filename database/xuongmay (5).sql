-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2025 at 07:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xuongmay`
--

-- --------------------------------------------------------

--
-- Table structure for table `baocaosuco`
--

CREATE TABLE `baocaosuco` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Người báo cáo',
  `loai_su_co` varchar(100) NOT NULL COMMENT 'Máy hỏng, Thiếu nguyên liệu...',
  `mo_ta` text NOT NULL COMMENT 'Mô tả chi tiết',
  `vi_tri` varchar(100) NOT NULL COMMENT 'Ví dụ: Chuyền 1, Máy 05...',
  `hinh_anh` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh đính kèm',
  `trang_thai` varchar(50) DEFAULT 'Chờ xử lý' COMMENT 'Chờ xử lý, Đang sửa, Đã xong',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ca_lam_viec`
--

CREATE TABLE `ca_lam_viec` (
  `id` int(11) NOT NULL,
  `ten_ca` varchar(50) NOT NULL,
  `gio_bat_dau` time DEFAULT NULL,
  `gio_ket_thuc` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ca_lam_viec`
--

INSERT INTO `ca_lam_viec` (`id`, `ten_ca`, `gio_bat_dau`, `gio_ket_thuc`) VALUES
(1, 'Ca Sáng', '07:30:00', '11:30:00'),
(2, 'Ca Chiều', '13:00:00', '17:00:00'),
(3, 'Ca Tối (Tăng ca)', '18:00:00', '22:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `cham_cong`
--

CREATE TABLE `cham_cong` (
  `id` int(11) NOT NULL,
  `id_ke_hoach` int(11) NOT NULL,
  `id_cong_doan` int(11) NOT NULL,
  `id_cong_nhan` int(11) NOT NULL COMMENT 'ID lấy từ bảng users',
  `id_ca` int(11) NOT NULL,
  `ngay_cham_cong` date NOT NULL,
  `san_luong` int(11) NOT NULL DEFAULT 0,
  `ghi_chu` text DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cham_cong`
--

INSERT INTO `cham_cong` (`id`, `id_ke_hoach`, `id_cong_doan`, `id_cong_nhan`, `id_ca`, `ngay_cham_cong`, `san_luong`, `ghi_chu`, `ngay_tao`) VALUES
(1, 3, 1, 3, 1, '2025-12-24', 200, '', '2025-12-24 01:49:40'),
(2, 3, 1, 5, 1, '2025-12-24', 200, '', '2025-12-24 01:49:40'),
(3, 3, 2, 3, 1, '2025-12-24', 500, '', '2025-12-24 03:28:33'),
(4, 3, 2, 5, 1, '2025-12-24', 500, '', '2025-12-24 03:28:33'),
(5, 3, 3, 3, 1, '2025-12-24', 200, '', '2025-12-24 03:28:39'),
(6, 3, 3, 5, 1, '2025-12-24', 200, '', '2025-12-24 03:28:39'),
(7, 3, 4, 3, 1, '2025-12-24', 200, '', '2025-12-24 03:28:46'),
(8, 5, 5, 3, 1, '2025-12-24', 8, '', '2025-12-24 04:01:27'),
(9, 5, 6, 3, 1, '2025-12-24', 20, '', '2025-12-24 04:01:31'),
(10, 5, 7, 3, 1, '2025-12-24', 8, '', '2025-12-24 04:01:36'),
(11, 5, 8, 3, 1, '2025-12-24', 4, '', '2025-12-24 04:01:40'),
(12, 4, 9, 3, 1, '2025-12-24', 10, '', '2025-12-24 04:08:34'),
(13, 4, 10, 3, 1, '2025-12-24', 10, '', '2025-12-24 04:08:37'),
(14, 4, 11, 3, 1, '2025-12-24', 10, '', '2025-12-24 04:08:40'),
(15, 4, 12, 3, 1, '2025-12-24', 10, '', '2025-12-24 04:08:42');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang_ban`
--

CREATE TABLE `chi_tiet_don_hang_ban` (
  `id` int(11) NOT NULL,
  `don_hang_id` int(11) DEFAULT NULL,
  `ten_san_pham` varchar(255) DEFAULT NULL,
  `size` varchar(10) DEFAULT 'Free',
  `so_luong` int(11) DEFAULT NULL,
  `don_gia` decimal(15,2) DEFAULT NULL,
  `thanh_tien` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chi_tiet_don_hang_ban`
--

INSERT INTO `chi_tiet_don_hang_ban` (`id`, `don_hang_id`, `ten_san_pham`, `size`, `so_luong`, `don_gia`, `thanh_tien`) VALUES
(1, 1, 'DCxRS Flame Raw Denim Jacket', 'XL', 5, 680000.00, 3400000.00),
(2, 2, 'Metal Label Wide Trouser Pants - Black', 'XXL', 10, 480000.00, 4800000.00),
(3, 3, 'Drawstring Camo Denim Cargo Pants', 'XL', 1, 520000.00, 520000.00),
(4, 4, 'DCxRS Camo Fur Hooded Bomber Jacket - Grey', 'XL', 10, 750000.00, 7500000.00),
(5, 5, 'Metal Label Wide Trouser Pants - Black', 'M', 10, 480000.00, 4800000.00),
(6, 6, 'DCxRS Western Rivet Flannel Brown', 'XXL', 1, 420000.00, 420000.00),
(7, 7, 'DCxRS Western Rivet Flannel Brown', 'S', 1, 420000.00, 420000.00),
(8, 8, 'áo jean', 'S', 1, 10000000.00, 10000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang_mua`
--

CREATE TABLE `chi_tiet_don_hang_mua` (
  `id` int(11) NOT NULL,
  `don_hang_id` int(11) DEFAULT NULL,
  `ten_san_pham` varchar(255) DEFAULT NULL,
  `so_luong` int(11) DEFAULT NULL,
  `don_gia` decimal(15,2) DEFAULT NULL,
  `thanh_tien` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `congdoan`
-- (See below for the actual view)
--
CREATE TABLE `congdoan` (
`id` int(11)
,`id_ke_hoach` int(11)
,`ten_cong_doan` varchar(100)
,`chi_tieu` int(11)
,`da_san_xuat` int(11)
,`trang_thai` enum('cho','dang_thuc_hien','hoan_thanh')
,`thu_tu` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `congnhan`
-- (See below for the actual view)
--
CREATE TABLE `congnhan` (
`id` int(11)
,`ho_ten` varchar(50)
,`to_doi` varchar(11)
,`ngay_tao` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `cong_doan`
--

CREATE TABLE `cong_doan` (
  `id` int(11) NOT NULL,
  `id_ke_hoach` int(11) NOT NULL,
  `ten_cong_doan` varchar(100) NOT NULL,
  `chi_tieu` int(11) NOT NULL DEFAULT 0,
  `da_san_xuat` int(11) DEFAULT 0,
  `trang_thai` enum('cho','dang_thuc_hien','hoan_thanh') DEFAULT 'cho',
  `thu_tu` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cong_doan`
--

INSERT INTO `cong_doan` (`id`, `id_ke_hoach`, `ten_cong_doan`, `chi_tieu`, `da_san_xuat`, `trang_thai`, `thu_tu`) VALUES
(1, 3, 'Cắt vải', 400, 400, 'hoan_thanh', 1),
(2, 3, 'May', 1000, 1000, 'hoan_thanh', 2),
(3, 3, 'Ủi', 400, 400, 'hoan_thanh', 3),
(4, 3, 'Đóng gói', 200, 200, 'hoan_thanh', 4),
(5, 5, 'Cắt vải', 8, 8, 'hoan_thanh', 1),
(6, 5, 'May', 20, 20, 'hoan_thanh', 2),
(7, 5, 'Ủi', 8, 8, 'hoan_thanh', 3),
(8, 5, 'Đóng gói', 4, 4, 'hoan_thanh', 4),
(9, 4, 'Cắt vải', 10, 10, 'hoan_thanh', 1),
(10, 4, 'May', 10, 10, 'hoan_thanh', 2),
(11, 4, 'Ủi', 10, 10, 'hoan_thanh', 3),
(12, 4, 'Đóng gói', 10, 10, 'hoan_thanh', 4);

-- --------------------------------------------------------

--
-- Table structure for table `ct_nhap_nguyen_lieu`
--

CREATE TABLE `ct_nhap_nguyen_lieu` (
  `id` int(11) NOT NULL,
  `phieu_nhap_id` int(11) DEFAULT NULL,
  `nguyen_lieu_id` int(11) DEFAULT NULL,
  `so_luong` int(11) DEFAULT NULL,
  `don_gia` int(11) NOT NULL,
  `thanh_tien` int(11) GENERATED ALWAYS AS (`so_luong` * `don_gia`) STORED,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_nhap_nguyen_lieu`
--

INSERT INTO `ct_nhap_nguyen_lieu` (`id`, `phieu_nhap_id`, `nguyen_lieu_id`, `so_luong`, `don_gia`, `ghi_chu`) VALUES
(1, 2, 1, 120, 400000000, 'kh co'),
(2, 3, 2, 1200, 600000000, 'khong co'),
(3, 4, 3, 1200, 2147483647, 'khong'),
(4, 5, 4, 15000, 2147483647, 'khong'),
(5, 6, 5, 150, 1000000000, 'ok'),
(6, 7, 6, 12, 122, 'ok'),
(7, 8, 7, 400, 2000000, 'Đạt'),
(8, 9, 8, 100, 1000000, 'nhap'),
(9, 10, 8, 100, 40000000, 'ok'),
(10, 11, 8, 60, 60000000, 'pl');

-- --------------------------------------------------------

--
-- Table structure for table `ct_nhap_thanh_pham`
--

CREATE TABLE `ct_nhap_thanh_pham` (
  `id` int(11) NOT NULL,
  `phieu_nhap_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `so_luong` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_nhap_thanh_pham`
--

INSERT INTO `ct_nhap_thanh_pham` (`id`, `phieu_nhap_id`, `product_id`, `so_luong`) VALUES
(8, 8, 2, 23),
(9, 9, 5, 150),
(10, 10, 6, 1000),
(14, 14, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ct_xuat_thanh_pham`
--

CREATE TABLE `ct_xuat_thanh_pham` (
  `id` int(11) NOT NULL,
  `phieu_xuat_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `don_gia` int(11) NOT NULL,
  `thanh_tien` int(11) GENERATED ALWAYS AS (`so_luong` * `don_gia`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_xuat_thanh_pham`
--

INSERT INTO `ct_xuat_thanh_pham` (`id`, `phieu_xuat_id`, `product_id`, `so_luong`, `don_gia`) VALUES
(4, 4, 5, 10, 1000000),
(5, 5, 6, 50, 500000),
(6, 6, 5, 20, 100000),
(7, 7, 5, 20, 20000000),
(8, 8, 6, 50, 20000000),
(9, 9, 6, 100, 1000000),
(10, 10, 6, 50, 400000);

--
-- Triggers `ct_xuat_thanh_pham`
--
DELIMITER $$
CREATE TRIGGER `trg_xuat_thanh_pham` AFTER INSERT ON `ct_xuat_thanh_pham` FOR EACH ROW BEGIN
    UPDATE products
    SET ton_kho = ton_kho - NEW.so_luong
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ct_yeu_cau_nguyen_lieu`
--

CREATE TABLE `ct_yeu_cau_nguyen_lieu` (
  `id` int(11) NOT NULL,
  `phieu_id` int(11) NOT NULL,
  `nguyen_lieu_id` int(11) NOT NULL,
  `so_luong_yeu_cau` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_yeu_cau_nguyen_lieu`
--

INSERT INTO `ct_yeu_cau_nguyen_lieu` (`id`, `phieu_id`, `nguyen_lieu_id`, `so_luong_yeu_cau`) VALUES
(1, 1, 2, 12),
(2, 2, 1, 18),
(3, 2, 2, 5),
(4, 3, 1, 18),
(5, 3, 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `datmay`
--

CREATE TABLE `datmay` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Đang xử lý'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `datmay`
--

INSERT INTO `datmay` (`id`, `user_id`, `product_id`, `size`, `quantity`, `price`, `created_at`, `status`) VALUES
(1, 4, 5, 'L', 1, 420000, '2025-12-23 14:55:23', 'Đang xử lý'),
(2, 4, 5, 'XL', 36, 420000, '2025-12-23 15:21:39', 'Đang xử lý');

-- --------------------------------------------------------

--
-- Table structure for table `daychuyen`
--

CREATE TABLE `daychuyen` (
  `id` int(11) NOT NULL,
  `ten_chuyen` varchar(100) NOT NULL COMMENT 'Ví dụ: Chuyền May 1',
  `cong_suat` int(11) DEFAULT 0 COMMENT 'Năng suất tối đa sp/ngày',
  `trang_thai` varchar(50) DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daychuyen`
--

INSERT INTO `daychuyen` (`id`, `ten_chuyen`, `cong_suat`, `trang_thai`) VALUES
(1, 'Chuyền May 1 (Áo thun)', 500, 'Hoạt động'),
(2, 'Chuyền May 2 (Sơ mi)', 400, 'Hoạt động'),
(3, 'Chuyền Jean', 300, 'Hoạt động');

-- --------------------------------------------------------

--
-- Table structure for table `dinh_muc_san_pham`
--

CREATE TABLE `dinh_muc_san_pham` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `nguyen_lieu_id` int(11) NOT NULL,
  `so_luong_can` float NOT NULL COMMENT 'Số lượng NL cần cho 1 SP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dinh_muc_san_pham`
--

INSERT INTO `dinh_muc_san_pham` (`id`, `product_id`, `nguyen_lieu_id`, `so_luong_can`) VALUES
(1, 5, 1, 1.5),
(2, 5, 2, 0.5),
(3, 6, 4, 2),
(4, 6, 2, 0.3),
(5, 7, 1, 1.8),
(6, 7, 2, 0.5),
(7, 8, 4, 1.5),
(8, 8, 2, 0.2),
(9, 9, 4, 0.8),
(10, 9, 2, 0.15),
(11, 10, 2, 1.2);

-- --------------------------------------------------------

--
-- Table structure for table `donhangkh`
--

CREATE TABLE `donhangkh` (
  `id` int(11) NOT NULL,
  `ma_dh` varchar(20) NOT NULL,
  `khach_hang` varchar(100) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `ngay_dat` date DEFAULT curdate(),
  `han_giao` date NOT NULL,
  `trang_thai` varchar(50) DEFAULT 'Đã duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donhangkh`
--

INSERT INTO `donhangkh` (`id`, `ma_dh`, `khach_hang`, `san_pham_id`, `so_luong`, `ngay_dat`, `han_giao`, `trang_thai`) VALUES
(1, 'DH001', 'Công ty ABC', 5, 1000, '2025-12-23', '2025-12-20', 'Đã có KH'),
(2, 'DH002', 'Shop Thời Trang X', 2, 500, '2025-12-23', '2025-12-25', 'Đã duyệt'),
(3, 'DH003', 'Đồng phục Y', 4, 2000, '2025-12-23', '2025-12-30', 'Đã duyệt');

-- --------------------------------------------------------

--
-- Table structure for table `don_hang_ban`
--

CREATE TABLE `don_hang_ban` (
  `id` int(11) NOT NULL,
  `so_don_hang` varchar(50) NOT NULL,
  `khach_hang_id` int(11) DEFAULT NULL,
  `ngay_lap` date DEFAULT NULL,
  `ngay_giao_du_kien` date DEFAULT NULL,
  `dieu_khoan_thanh_toan` varchar(255) DEFAULT NULL,
  `phuong_thuc_van_chuyen` varchar(255) DEFAULT NULL,
  `dia_diem_giao_hang` text DEFAULT NULL,
  `tong_tien` decimal(15,2) DEFAULT 0.00,
  `trang_thai` varchar(50) DEFAULT 'Moi',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `don_hang_ban`
--

INSERT INTO `don_hang_ban` (`id`, `so_don_hang`, `khach_hang_id`, `ngay_lap`, `ngay_giao_du_kien`, `dieu_khoan_thanh_toan`, `phuong_thuc_van_chuyen`, `dia_diem_giao_hang`, `tong_tien`, `trang_thai`, `user_id`) VALUES
(1, 'ONLINE-1766536197', NULL, '2025-12-24', '2025-12-31', NULL, NULL, '30/2', 3400000.00, 'Moi', 4),
(2, 'ONLINE-1766538067', NULL, '2025-12-24', '2025-12-31', NULL, NULL, '12', 4800000.00, 'Moi', 4),
(3, 'ONLINE-1766540829', NULL, '2025-12-24', '2025-12-31', NULL, NULL, 'aaa', 520000.00, 'Moi', 4),
(4, 'ONLINE-1766547617', NULL, '2025-12-24', '2025-12-31', NULL, NULL, '1231', 7500000.00, 'Moi', 4),
(5, 'ONLINE-1766548037', NULL, '2025-12-24', '2025-12-31', NULL, NULL, '123123123', 4800000.00, 'Moi', 4),
(6, 'ONLINE-1766548477', NULL, '2025-12-24', '2025-12-31', NULL, NULL, '123123', 420000.00, 'Moi', 4),
(7, 'ONLINE-1766551879', NULL, '2025-12-24', '2025-12-31', NULL, NULL, 'H', 420000.00, 'Moi', 4),
(8, 'SO-20251224062329', 1, '2025-12-24', '2025-12-27', 'COD', 'XeTai', '32', 10000000.00, 'Moi', 9);

-- --------------------------------------------------------

--
-- Table structure for table `don_hang_mua`
--

CREATE TABLE `don_hang_mua` (
  `id` int(11) NOT NULL,
  `ma_don_hang` varchar(50) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `ngay_lap` date DEFAULT NULL,
  `ngay_nhan_du_kien` date DEFAULT NULL,
  `trang_thai` varchar(50) DEFAULT 'ChoDuyet',
  `ly_do_tu_choi` text DEFAULT NULL,
  `tong_tien` decimal(15,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kehoachsanxuat`
--

CREATE TABLE `kehoachsanxuat` (
  `id` int(11) NOT NULL,
  `ma_ke_hoach` varchar(20) NOT NULL,
  `don_hang_id` int(11) NOT NULL,
  `day_chuyen_id` int(11) NOT NULL,
  `ngay_bat_dau` date NOT NULL,
  `ngay_ket_thuc` date NOT NULL,
  `san_luong_ngay` int(11) NOT NULL,
  `trang_thai` varchar(50) DEFAULT 'Chờ duyệt' COMMENT 'Chờ duyệt, Đã duyệt, Từ chối',
  `ly_do_tu_choi` text DEFAULT NULL,
  `nguoi_lap_id` int(11) DEFAULT NULL COMMENT 'ID của Quản đốc',
  `ngay_lap` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kehoachsanxuat`
--

INSERT INTO `kehoachsanxuat` (`id`, `ma_ke_hoach`, `don_hang_id`, `day_chuyen_id`, `ngay_bat_dau`, `ngay_ket_thuc`, `san_luong_ngay`, `trang_thai`, `ly_do_tu_choi`, `nguoi_lap_id`, `ngay_lap`) VALUES
(1, 'KH-1766504125', 1, 1, '2025-12-23', '2025-12-27', 300, 'Đã phân bổ', NULL, 6, '2025-12-23 15:35:25'),
(2, 'KH-1766538825', 2, 1, '2025-12-24', '2025-12-27', 500, 'Đã phân bổ', NULL, 6, '2025-12-24 01:13:45'),
(3, 'KH-1766540850', 3, 1, '2025-12-24', '2025-12-27', 500, 'Đã phân bổ', NULL, 6, '2025-12-24 01:47:30'),
(4, 'KH-1766547663', 4, 1, '2025-12-27', '2025-12-30', 5, 'Đã phân bổ', NULL, 6, '2025-12-24 03:41:03'),
(5, 'KH-1766548063', 5, 1, '2025-12-24', '2025-12-31', 5, 'Đã phân bổ', NULL, 6, '2025-12-24 03:47:43'),
(6, 'KH-1766548498', 6, 1, '2025-12-24', '2025-12-30', 1, 'Đã duyệt', NULL, 6, '2025-12-24 03:54:58');

-- --------------------------------------------------------

--
-- Table structure for table `khach_hang`
--

CREATE TABLE `khach_hang` (
  `id` int(11) NOT NULL,
  `ma_kh` varchar(50) NOT NULL,
  `ten_kh` varchar(255) NOT NULL,
  `dia_chi_giao_hd` text DEFAULT NULL,
  `sdt_email` varchar(100) DEFAULT NULL,
  `nguoi_lien_he` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khach_hang`
--

INSERT INTO `khach_hang` (`id`, `ma_kh`, `ten_kh`, `dia_chi_giao_hd`, `sdt_email`, `nguoi_lien_he`) VALUES
(1, 'kh001', 'Công ty Tư nhân IUH ', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `kho_nguyen_lieu`
--

CREATE TABLE `kho_nguyen_lieu` (
  `id` int(11) NOT NULL,
  `nguyen_lieu_id` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kho_thanh_pham`
--

CREATE TABLE `kho_thanh_pham` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lenhsanxuat`
--

CREATE TABLE `lenhsanxuat` (
  `id` int(11) NOT NULL,
  `ma_lenh` varchar(20) NOT NULL,
  `ke_hoach_id` int(11) NOT NULL,
  `xuong_truong_id` int(11) NOT NULL,
  `ngay_tao` date DEFAULT curdate(),
  `trang_thai` varchar(50) DEFAULT 'Mới' COMMENT 'Mới, Đang thực hiện, Hoàn thành',
  `ghi_chu_phan_bo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lenhsanxuat`
--

INSERT INTO `lenhsanxuat` (`id`, `ma_lenh`, `ke_hoach_id`, `xuong_truong_id`, `ngay_tao`, `trang_thai`, `ghi_chu_phan_bo`) VALUES
(1, 'LSX-1766504136', 1, 7, '2025-12-23', 'Mới', NULL),
(2, 'LSX-1766539728', 2, 7, '2025-12-24', 'Mới', 'cẩn thận '),
(3, 'LSX-1766540887', 3, 7, '2025-12-24', 'Mới', 'a'),
(4, 'LSX-1766548855', 5, 7, '2025-12-24', 'Mới', ''),
(5, 'LSX-1766549290', 4, 7, '2025-12-24', 'Mới', '');

-- --------------------------------------------------------

--
-- Stand-in structure for view `lichlamviec`
-- (See below for the actual view)
--
CREATE TABLE `lichlamviec` (
`id` int(11)
,`ten_ca` varchar(50)
,`gio_bat_dau` time
,`gio_ket_thuc` time
);

-- --------------------------------------------------------

--
-- Table structure for table `lich_lam_viec`
--

CREATE TABLE `lich_lam_viec` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Liên kết với bảng users',
  `ngay_lam` date NOT NULL,
  `ca_lam` varchar(50) NOT NULL COMMENT 'Ca 1, Ca 2, Hành chính...',
  `xuong` varchar(100) NOT NULL COMMENT 'Xưởng A, Xưởng B...',
  `chuyen` varchar(100) NOT NULL COMMENT 'Chuyền 1, Chuyền May...',
  `ma_don_hang` varchar(50) DEFAULT NULL COMMENT 'Làm cho đơn hàng nào (nếu có)',
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lich_lam_viec`
--

INSERT INTO `lich_lam_viec` (`id`, `user_id`, `ngay_lam`, `ca_lam`, `xuong`, `chuyen`, `ma_don_hang`, `ghi_chu`, `created_at`) VALUES
(1, 5, '2025-12-23', 'Ca 1 (07:30 - 11:30)', 'Xưởng A', 'Chuyền May 1', 'DH2025-001', 'May tay áo', '2025-12-22 19:36:33'),
(2, 5, '2025-12-24', 'Ca 1 (07:30 - 11:30)', 'Xưởng A', 'Chuyền May 1', 'DH2025-001', 'Ráp thân sau', '2025-12-22 19:36:33'),
(3, 5, '2025-12-25', 'Ca 2 (13:00 - 17:00)', 'Xưởng B', 'Chuyền Ủi', 'DH2025-002', 'Hỗ trợ đóng gói', '2025-12-22 19:36:33'),
(4, 5, '2025-12-20', 'Ca 1 (07:30 - 16:30)', 'Xưởng A', 'Chuyền May 1', 'DH-NOEL-01', 'Gấp rút hoàn thành', '2025-12-22 19:36:33'),
(5, 5, '2025-12-21', 'Ca 1 (07:30 - 16:30)', 'Xưởng A', 'Chuyền May 1', 'DH-NOEL-01', 'Tăng ca chủ nhật', '2025-12-22 19:36:33'),
(6, 5, '2025-12-22', 'Ca 2 (13:30 - 21:30)', 'Xưởng B', 'Chuyền Đóng Gói', 'DH-TET-02', '', '2025-12-22 19:36:33'),
(7, 5, '2025-12-23', 'Ca 2 (13:30 - 21:30)', 'Xưởng B', 'Chuyền Đóng Gói', 'DH-TET-02', '', '2025-12-22 19:36:33'),
(8, 5, '2025-12-24', 'Ca 1 (07:30 - 16:30)', 'Xưởng A', 'Chuyền May 2', 'DH-XUAN-25', 'Nghỉ sớm 30p (Giáng sinh)', '2025-12-22 19:36:33'),
(9, 5, '2025-12-25', 'Ca 1 (07:30 - 16:30)', 'Xưởng A', 'Chuyền May 2', 'DH-XUAN-25', '', '2025-12-22 19:36:33'),
(10, 5, '2025-12-26', 'Ca 1 (07:30 - 16:30)', 'Xưởng A', 'Chuyền May 2', 'DH-XUAN-25', '', '2025-12-22 19:36:33'),
(11, 5, '2025-12-27', 'Ca 3 (22:00 - 06:00)', 'Xưởng C', 'Chuyền Ủi', 'DH-GAP-03', 'Ca đêm', '2025-12-22 19:36:33'),
(12, 5, '2025-12-28', 'Ca 3 (22:00 - 06:00)', 'Xưởng C', 'Chuyền Ủi', 'DH-GAP-03', 'Ca đêm', '2025-12-22 19:36:33'),
(13, 5, '2025-12-29', 'Hành chính', 'Xưởng A', 'Chuyền Kiểm Hàng', 'DH-FINAL', 'Tổng kiểm kê', '2025-12-22 19:36:33'),
(14, 5, '2025-12-30', 'Hành chính', 'Xưởng A', 'Chuyền Kiểm Hàng', 'DH-FINAL', 'Tổng kiểm kê', '2025-12-22 19:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `nguyen_lieu`
--

CREATE TABLE `nguyen_lieu` (
  `id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `loai` varchar(100) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `ton_kho` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `don_gia` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguyen_lieu`
--

INSERT INTO `nguyen_lieu` (`id`, `code`, `name`, `loai`, `unit`, `ton_kho`, `created_at`, `don_gia`) VALUES
(1, NULL, 'Vải trắng cao cấp', 'Vải', NULL, 222, '2025-12-17 08:06:42', 0),
(2, NULL, 'Vải đen cao cấp', 'Vải', NULL, 1195, '2025-12-17 08:14:05', 0),
(3, NULL, 'Vải đỏ cao cấp', 'Vải', NULL, 1200, '2025-12-17 08:15:59', 0),
(4, NULL, 'Vải xanh cao cấp', 'Vải', NULL, 15000, '2025-12-18 13:30:32', 0),
(5, NULL, 'Vải trắng cao cấp 2', 'Vải cao cấp', NULL, 150, '2025-12-23 15:07:20', 0),
(6, NULL, 'Vải cotten', 'Vải', NULL, 12, '2025-12-23 15:51:13', 0),
(7, NULL, 'Vải tơ đỏ', 'Vải cao cấp', NULL, 400, '2025-12-23 17:59:46', 0),
(8, NULL, 'Vải trắng cao cấp', 'Vải cao cấp', NULL, 260, '2025-12-24 04:56:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `phieu_kiem_tra`
--

CREATE TABLE `phieu_kiem_tra` (
  `id` int(11) NOT NULL,
  `thanh_pham_id` int(11) DEFAULT NULL,
  `so_luong` int(11) DEFAULT NULL,
  `ket_qua` enum('dat','khong_dat') DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ngay_kiem_tra` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap_nguyen_lieu`
--

CREATE TABLE `phieu_nhap_nguyen_lieu` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ngay_nhap` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_nhap_nguyen_lieu`
--

INSERT INTO `phieu_nhap_nguyen_lieu` (`id`, `supplier_id`, `user_id`, `ngay_nhap`, `note`) VALUES
(1, 1, 2, '2025-12-17 15:06:42', NULL),
(2, 1, 2, '2025-12-17 15:07:48', NULL),
(3, 1, 2, '2025-12-17 15:14:05', NULL),
(4, 1, 2, '2025-12-17 15:15:59', NULL),
(5, 1, 2, '2025-12-18 20:30:32', NULL),
(6, 1, 2, '2025-12-23 22:07:20', NULL),
(7, 1, 2, '2025-12-23 22:51:13', NULL),
(8, 2, 2, '2025-12-24 00:59:46', NULL),
(9, 4, 2, '2025-12-24 11:56:06', NULL),
(10, 3, 2, '2025-12-24 12:39:50', NULL),
(11, 1, 2, '2025-12-24 12:40:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap_thanh_pham`
--

CREATE TABLE `phieu_nhap_thanh_pham` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `xuong` varchar(50) DEFAULT NULL,
  `so_luong` int(11) DEFAULT NULL,
  `qc_ket_qua` enum('dat','khong_dat') DEFAULT 'dat',
  `nguoi_nhap` varchar(50) DEFAULT NULL,
  `ngay_nhap` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_nhap_thanh_pham`
--

INSERT INTO `phieu_nhap_thanh_pham` (`id`, `user_id`, `xuong`, `so_luong`, `qc_ket_qua`, `nguoi_nhap`, `ngay_nhap`, `note`) VALUES
(8, 2, 'Xưởng 2', 23, 'dat', 'kho1', '2025-12-23 22:05:42', 'đạt'),
(9, 2, 'Xưởng 1', 150, 'dat', 'kho1', '2025-12-23 22:06:47', 'đạt'),
(10, 2, 'Xưởng 1', 1000, 'dat', 'kho1', '2025-12-24 01:01:05', 'đạt'),
(11, 2, '', 0, 'dat', 'kho1', '2025-12-24 12:42:14', ''),
(12, 2, '', 0, 'dat', 'kho1', '2025-12-24 12:43:39', ''),
(13, 2, 'Xưởng 1', 0, 'dat', 'kho1', '2025-12-24 12:47:21', 'f'),
(14, 2, 'Xưởng 1', 1, 'dat', 'kho1', '2025-12-24 12:51:42', 'd');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_xuat_kho`
--

CREATE TABLE `phieu_xuat_kho` (
  `id` int(11) NOT NULL,
  `ma_phieu_xuat` varchar(20) NOT NULL,
  `phieu_yeu_cau_id` int(11) NOT NULL,
  `nguoi_xuat_id` int(11) NOT NULL,
  `ngay_xuat` datetime DEFAULT current_timestamp(),
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_xuat_kho`
--

INSERT INTO `phieu_xuat_kho` (`id`, `ma_phieu_xuat`, `phieu_yeu_cau_id`, `nguoi_xuat_id`, `ngay_xuat`, `ghi_chu`) VALUES
(1, 'PX20251224054618', 3, 2, '2025-12-24 11:46:18', 'Xuất kho theo yêu cầu');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_xuat_thanh_pham`
--

CREATE TABLE `phieu_xuat_thanh_pham` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ngay_xuat` datetime DEFAULT current_timestamp(),
  `ly_do` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_xuat_thanh_pham`
--

INSERT INTO `phieu_xuat_thanh_pham` (`id`, `user_id`, `ngay_xuat`, `ly_do`) VALUES
(1, 2, '2025-12-17 23:13:25', 'ban'),
(2, 2, '2025-12-18 20:32:49', 'ban cho ben a'),
(3, 2, '2025-12-20 01:53:37', 'xuat kho'),
(4, 2, '2025-12-20 02:15:25', 'xuat cho a'),
(5, 2, '2025-12-20 02:27:51', 'sll'),
(6, 2, '2025-12-24 12:12:47', 'ok'),
(7, 2, '2025-12-24 12:13:07', 'ok'),
(8, 2, '2025-12-24 12:14:19', 'ban'),
(9, 2, '2025-12-24 12:22:01', 'kh'),
(10, 2, '2025-12-24 12:58:22', 'ok');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_yeu_cau_kiem_tra`
--

CREATE TABLE `phieu_yeu_cau_kiem_tra` (
  `id` int(11) NOT NULL,
  `ma_phieu` varchar(20) NOT NULL,
  `ke_hoach_id` int(11) DEFAULT NULL,
  `cong_doan_id` int(11) DEFAULT NULL,
  `nguoi_lap_id` int(11) NOT NULL,
  `so_luong_yeu_cau` int(11) NOT NULL,
  `ngay_lap` datetime DEFAULT current_timestamp(),
  `trang_thai` enum('cho_duyet','dang_kiem_tra','hoan_thanh','tu_choi') DEFAULT 'cho_duyet',
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_yeu_cau_kiem_tra`
--

INSERT INTO `phieu_yeu_cau_kiem_tra` (`id`, `ma_phieu`, `ke_hoach_id`, `cong_doan_id`, `nguoi_lap_id`, `so_luong_yeu_cau`, `ngay_lap`, `trang_thai`, `ghi_chu`) VALUES
(3, 'QC-24122025-96', 4, NULL, 7, 10, '2025-12-24 11:08:50', 'hoan_thanh', ''),
(4, 'QC-24122025-21', 5, NULL, 7, 4, '2025-12-24 11:11:47', 'cho_duyet', '');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_yeu_cau_nguyen_lieu`
--

CREATE TABLE `phieu_yeu_cau_nguyen_lieu` (
  `id` int(11) NOT NULL,
  `ma_phieu` varchar(20) NOT NULL,
  `ke_hoach_id` int(11) NOT NULL,
  `nguoi_lap_id` int(11) NOT NULL,
  `ngay_lap` datetime DEFAULT current_timestamp(),
  `trang_thai` enum('cho_xac_nhan','da_duyet','tu_choi') DEFAULT 'cho_xac_nhan',
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieu_yeu_cau_nguyen_lieu`
--

INSERT INTO `phieu_yeu_cau_nguyen_lieu` (`id`, `ma_phieu`, `ke_hoach_id`, `nguoi_lap_id`, `ngay_lap`, `trang_thai`, `ghi_chu`) VALUES
(1, 'YC20251224032313', 2, 7, '2025-12-24 09:23:13', 'tu_choi', ' | Lý do hủy: '),
(2, 'YC20251224053625', 4, 7, '2025-12-24 11:36:25', 'tu_choi', ' | Lý do hủy: '),
(3, 'YC20251224054523', 4, 7, '2025-12-24 11:45:23', 'da_duyet', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ton_kho` int(11) DEFAULT 0,
  `unit` varchar(20) DEFAULT 'cái'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `category_id`, `description`, `created_at`, `ton_kho`, `unit`) VALUES
(5, 'DCxRS Western Rivet Flannel Brown', 420000, 'ao1.jpg', 1, 'Áo flannel DCxRS phong cách Western, tông nâu trầm, chất liệu vải dày dặn, giữ ấm tốt, phù hợp mặc thu đông và phối streetwear.', '2025-12-22 20:43:42', -31, 'cái'),
(6, 'DCxRS Flame Raw Denim Jacket', 680000, 'ao2.jpg', 1, 'Áo khoác denim DCxRS thiết kế họa tiết flame cá tính, chất denim thô bền chắc, form jacket mạnh mẽ, phù hợp phong cách streetwear hiện đại.', '2025-12-22 20:43:57', -200, 'cái'),
(7, 'DCxRS Camo Fur Hooded Bomber Jacket - Grey', 750000, 'ao3.jpg', 1, 'Áo bomber DCxRS họa tiết camo xám, có mũ lông giữ ấm, thiết kế thể thao năng động, thích hợp thời tiết lạnh và đi chơi, dạo phố.', '2025-12-22 20:44:04', 10, 'cái'),
(8, 'Drawstring Camo Denim Cargo Pants', 520000, 'quan1.jpg', 2, 'Quần cargo denim họa tiết camo, thiết kế dây rút tiện lợi, form rộng thoải mái, chất liệu denim bền chắc, phù hợp phong cách streetwear.', '2025-12-22 20:56:46', 39, 'cái'),
(9, 'Drawstring Camo Denim Cargo Shorts', 450000, 'quan2.jpg', 2, 'Quần short cargo denim camo, thiết kế trẻ trung, thoáng mát, dây rút linh hoạt, thích hợp mặc mùa hè và hoạt động ngoài trời.', '2025-12-22 20:56:54', 55, 'cái'),
(10, 'Metal Label Wide Trouser Pants - Black', 480000, 'quan3.jpg', 2, 'Quần tây form rộng màu đen, điểm nhấn metal label cá tính, chất liệu vải đứng form, dễ phối đồ công sở và streetwear.', '2025-12-22 20:57:04', 10, 'cái');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qa_bien_ban`
--

CREATE TABLE `qa_bien_ban` (
  `id` int(11) NOT NULL,
  `phieu_yeu_cau_id` int(11) DEFAULT NULL COMMENT 'Link tới phieu_yeu_cau_kiem_tra',
  `ma_san_pham` varchar(50) DEFAULT NULL,
  `ten_san_pham` varchar(255) DEFAULT NULL,
  `ngay_san_xuat` date DEFAULT NULL,
  `lo_san_xuat` varchar(50) DEFAULT NULL,
  `nguoi_kiem_tra` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ngay_kiem_tra` date DEFAULT NULL,
  `ket_qua_chung` enum('Dat','KhongDat') DEFAULT NULL,
  `khuyen_nghi` text DEFAULT NULL,
  `huong_dan_khac_phuc` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qa_bien_ban`
--

INSERT INTO `qa_bien_ban` (`id`, `phieu_yeu_cau_id`, `ma_san_pham`, `ten_san_pham`, `ngay_san_xuat`, `lo_san_xuat`, `nguoi_kiem_tra`, `user_id`, `ngay_kiem_tra`, `ket_qua_chung`, `khuyen_nghi`, `huong_dan_khac_phuc`, `created_at`) VALUES
(1, 3, 'SP-781', 'DCxRS Camo Fur Hooded Bomber Jacket - Grey', NULL, 'KH-1766547663', NULL, 8, '2025-12-24', 'Dat', '', '', '2025-12-24 04:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `qa_chi_tiet`
--

CREATE TABLE `qa_chi_tiet` (
  `id` int(11) NOT NULL,
  `bien_ban_id` int(11) DEFAULT NULL,
  `tieu_chi` varchar(255) DEFAULT NULL,
  `tieu_chuan` varchar(255) DEFAULT NULL,
  `ket_qua` varchar(255) DEFAULT NULL,
  `ghi_chu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qa_chi_tiet`
--

INSERT INTO `qa_chi_tiet` (`id`, `bien_ban_id`, `tieu_chi`, `tieu_chuan`, `ket_qua`, `ghi_chu`) VALUES
(1, 1, 'Kích thước (Dài x Rộng)', 'Dung sai +/- 2cm', 'Dat', ''),
(2, 1, 'Ngoại quan / Màu sắc', 'Không lệch màu, không bẩn', 'Dat', ''),
(3, 1, 'Đường may / Mối nối', 'Chắc chắn, không bung chỉ', 'Dat', '');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `address`, `created_at`) VALUES
(1, 'IUH Group', '02838940390', 'Số 12 Nguyễn Văn Bảo, P. Hạnh Thông, Thành phố Hồ Chí Minh', '2025-12-17 08:01:21'),
(2, 'IUH Center', '08775444', '66 NTC', '2025-12-23 17:59:31'),
(3, 'IUH GR1', '0938339922', 'w', '2025-12-24 04:54:18'),
(4, 'IUH GR2', '0938339922', '32', '2025-12-24 04:55:47');

-- --------------------------------------------------------

--
-- Table structure for table `thanh_pham`
--

CREATE TABLE `thanh_pham` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `ton_kho` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thanh_pham`
--

INSERT INTO `thanh_pham` (`id`, `name`, `ton_kho`) VALUES
(1, 'Ao snakes', 120),
(2, 'Ao thun', 323),
(3, 'Ao Hiphop', 313),
(4, 'đ', 23),
(5, 'Aó snake', 150),
(6, 'Quần Hiphop Genz', 1000),
(7, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `thietke`
--

CREATE TABLE `thietke` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ten_sp` varchar(255) NOT NULL,
  `loai_sp` varchar(100) DEFAULT NULL,
  `so_luong` int(11) NOT NULL,
  `mau_sac` varchar(100) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `file_mau` varchar(255) DEFAULT NULL,
  `thoi_gian` varchar(50) DEFAULT NULL,
  `trang_thai` varchar(50) DEFAULT 'Chờ xử lý',
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thietke`
--

INSERT INTO `thietke` (`id`, `user_id`, `ten_sp`, `loai_sp`, `so_luong`, `mau_sac`, `mo_ta`, `file_mau`, `thoi_gian`, `trang_thai`, `ngay_tao`) VALUES
(1, 4, 'Quần hiphop for real', 'áo', 120, 'hồng', 'nhanh', NULL, '3 ngày', 'Chờ xử lý', '2025-12-24 00:52:41'),
(2, 4, 'Quần Snake', 'Quần', 120, 'hồng', 'đẹp', NULL, '7 ngày', 'Chờ xử lý', '2025-12-24 00:55:27'),
(3, 4, 'Quần Snake 2', 'Quần', 150, 'vàng', 'Đẹp', NULL, '7 ngày', 'Chờ xử lý', '2025-12-24 01:04:21'),
(4, 4, 'Ao hiphop for real 1', 'áo', 100, 'xanh nước', 'Trẻ trung', NULL, '3 ngày', 'Chờ xử lý', '2025-12-24 12:16:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('khachhang','thukho','congnhan','nhanvienqa','giamdoc','xuongtruong','quandoc','kinhdoanh') DEFAULT 'khachhang',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `gender` enum('Nam','Nữ','Khác') DEFAULT 'Nam',
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `gender`, `status`) VALUES
(1, 'admin', '202cb962ac59075b964b07152d234b70', 'giamdoc', '2025-12-10 08:47:20', 'Nam', 'active'),
(2, 'kho1', '202cb962ac59075b964b07152d234b70', 'thukho', '2025-12-10 08:47:20', 'Nam', 'active'),
(3, 'cn1', '202cb962ac59075b964b07152d234b70', 'congnhan', '2025-12-10 08:47:20', 'Nam', 'active'),
(4, 'khachhang', 'e10adc3949ba59abbe56e057f20f883e', 'khachhang', '2025-12-10 10:21:38', 'Nam', 'active'),
(5, 'congnhan1', '202cb962ac59075b964b07152d234b70', 'congnhan', '2025-12-22 19:36:33', 'Nam', 'active'),
(6, 'quandoc1', '202cb962ac59075b964b07152d234b70', 'quandoc', '2025-12-22 19:36:33', 'Nam', 'active'),
(7, 'xuongtruong1', '202cb962ac59075b964b07152d234b70', 'xuongtruong', '2025-12-22 19:36:33', 'Nam', 'active'),
(8, 'qa1', 'e10adc3949ba59abbe56e057f20f883e', 'nhanvienqa', '2025-12-23 12:38:16', 'Nữ', 'active'),
(9, 'sale1', 'e10adc3949ba59abbe56e057f20f883e', 'kinhdoanh', '2025-12-23 12:38:16', 'Nam', 'active');

-- --------------------------------------------------------

--
-- Structure for view `congdoan`
--
DROP TABLE IF EXISTS `congdoan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `congdoan`  AS SELECT `cong_doan`.`id` AS `id`, `cong_doan`.`id_ke_hoach` AS `id_ke_hoach`, `cong_doan`.`ten_cong_doan` AS `ten_cong_doan`, `cong_doan`.`chi_tieu` AS `chi_tieu`, `cong_doan`.`da_san_xuat` AS `da_san_xuat`, `cong_doan`.`trang_thai` AS `trang_thai`, `cong_doan`.`thu_tu` AS `thu_tu` FROM `cong_doan` ;

-- --------------------------------------------------------

--
-- Structure for view `congnhan`
--
DROP TABLE IF EXISTS `congnhan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `congnhan`  AS SELECT `users`.`id` AS `id`, `users`.`username` AS `ho_ten`, 'Tổ Sản Xuất' AS `to_doi`, `users`.`created_at` AS `ngay_tao` FROM `users` WHERE `users`.`role` = 'congnhan' ;

-- --------------------------------------------------------

--
-- Structure for view `lichlamviec`
--
DROP TABLE IF EXISTS `lichlamviec`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `lichlamviec`  AS SELECT `ca_lam_viec`.`id` AS `id`, `ca_lam_viec`.`ten_ca` AS `ten_ca`, `ca_lam_viec`.`gio_bat_dau` AS `gio_bat_dau`, `ca_lam_viec`.`gio_ket_thuc` AS `gio_ket_thuc` FROM `ca_lam_viec` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `baocaosuco`
--
ALTER TABLE `baocaosuco`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ca_lam_viec`
--
ALTER TABLE `ca_lam_viec`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cham_cong`
--
ALTER TABLE `cham_cong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cong_doan` (`id_cong_doan`),
  ADD KEY `id_cong_nhan` (`id_cong_nhan`),
  ADD KEY `id_ca` (`id_ca`),
  ADD KEY `id_ke_hoach` (`id_ke_hoach`);

--
-- Indexes for table `chi_tiet_don_hang_ban`
--
ALTER TABLE `chi_tiet_don_hang_ban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `don_hang_id` (`don_hang_id`);

--
-- Indexes for table `chi_tiet_don_hang_mua`
--
ALTER TABLE `chi_tiet_don_hang_mua`
  ADD PRIMARY KEY (`id`),
  ADD KEY `don_hang_id` (`don_hang_id`);

--
-- Indexes for table `cong_doan`
--
ALTER TABLE `cong_doan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ke_hoach` (`id_ke_hoach`);

--
-- Indexes for table `ct_nhap_nguyen_lieu`
--
ALTER TABLE `ct_nhap_nguyen_lieu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phieu_nhap_id` (`phieu_nhap_id`),
  ADD KEY `nguyen_lieu_id` (`nguyen_lieu_id`);

--
-- Indexes for table `ct_nhap_thanh_pham`
--
ALTER TABLE `ct_nhap_thanh_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phieu_nhap_id` (`phieu_nhap_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ct_xuat_thanh_pham`
--
ALTER TABLE `ct_xuat_thanh_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_xuat_phieu` (`phieu_xuat_id`),
  ADD KEY `fk_xuat_product` (`product_id`);

--
-- Indexes for table `ct_yeu_cau_nguyen_lieu`
--
ALTER TABLE `ct_yeu_cau_nguyen_lieu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `datmay`
--
ALTER TABLE `datmay`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daychuyen`
--
ALTER TABLE `daychuyen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dinh_muc_san_pham`
--
ALTER TABLE `dinh_muc_san_pham`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donhangkh`
--
ALTER TABLE `donhangkh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_dh` (`ma_dh`);

--
-- Indexes for table `don_hang_ban`
--
ALTER TABLE `don_hang_ban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `khach_hang_id` (`khach_hang_id`);

--
-- Indexes for table `don_hang_mua`
--
ALTER TABLE `don_hang_mua`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `kehoachsanxuat`
--
ALTER TABLE `kehoachsanxuat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_ke_hoach` (`ma_ke_hoach`),
  ADD KEY `don_hang_id` (`don_hang_id`),
  ADD KEY `day_chuyen_id` (`day_chuyen_id`);

--
-- Indexes for table `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kho_nguyen_lieu`
--
ALTER TABLE `kho_nguyen_lieu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguyen_lieu_id` (`nguyen_lieu_id`);

--
-- Indexes for table `kho_thanh_pham`
--
ALTER TABLE `kho_thanh_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `lenhsanxuat`
--
ALTER TABLE `lenhsanxuat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_lenh` (`ma_lenh`),
  ADD KEY `ke_hoach_id` (`ke_hoach_id`),
  ADD KEY `xuong_truong_id` (`xuong_truong_id`);

--
-- Indexes for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `nguyen_lieu`
--
ALTER TABLE `nguyen_lieu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `phieu_kiem_tra`
--
ALTER TABLE `phieu_kiem_tra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phieu_nhap_nguyen_lieu`
--
ALTER TABLE `phieu_nhap_nguyen_lieu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `phieu_nhap_thanh_pham`
--
ALTER TABLE `phieu_nhap_thanh_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `phieu_xuat_kho`
--
ALTER TABLE `phieu_xuat_kho`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phieu_xuat_thanh_pham`
--
ALTER TABLE `phieu_xuat_thanh_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `phieu_yeu_cau_kiem_tra`
--
ALTER TABLE `phieu_yeu_cau_kiem_tra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phieu_yeu_cau_nguyen_lieu`
--
ALTER TABLE `phieu_yeu_cau_nguyen_lieu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qa_bien_ban`
--
ALTER TABLE `qa_bien_ban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `qa_chi_tiet`
--
ALTER TABLE `qa_chi_tiet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bien_ban_id` (`bien_ban_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `thanh_pham`
--
ALTER TABLE `thanh_pham`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `thietke`
--
ALTER TABLE `thietke`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `baocaosuco`
--
ALTER TABLE `baocaosuco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ca_lam_viec`
--
ALTER TABLE `ca_lam_viec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cham_cong`
--
ALTER TABLE `cham_cong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `chi_tiet_don_hang_ban`
--
ALTER TABLE `chi_tiet_don_hang_ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `chi_tiet_don_hang_mua`
--
ALTER TABLE `chi_tiet_don_hang_mua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cong_doan`
--
ALTER TABLE `cong_doan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ct_nhap_nguyen_lieu`
--
ALTER TABLE `ct_nhap_nguyen_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ct_nhap_thanh_pham`
--
ALTER TABLE `ct_nhap_thanh_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ct_xuat_thanh_pham`
--
ALTER TABLE `ct_xuat_thanh_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ct_yeu_cau_nguyen_lieu`
--
ALTER TABLE `ct_yeu_cau_nguyen_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `datmay`
--
ALTER TABLE `datmay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `daychuyen`
--
ALTER TABLE `daychuyen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dinh_muc_san_pham`
--
ALTER TABLE `dinh_muc_san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `donhangkh`
--
ALTER TABLE `donhangkh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `don_hang_ban`
--
ALTER TABLE `don_hang_ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `don_hang_mua`
--
ALTER TABLE `don_hang_mua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kehoachsanxuat`
--
ALTER TABLE `kehoachsanxuat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `khach_hang`
--
ALTER TABLE `khach_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kho_nguyen_lieu`
--
ALTER TABLE `kho_nguyen_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kho_thanh_pham`
--
ALTER TABLE `kho_thanh_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lenhsanxuat`
--
ALTER TABLE `lenhsanxuat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `nguyen_lieu`
--
ALTER TABLE `nguyen_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `phieu_kiem_tra`
--
ALTER TABLE `phieu_kiem_tra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phieu_nhap_nguyen_lieu`
--
ALTER TABLE `phieu_nhap_nguyen_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `phieu_nhap_thanh_pham`
--
ALTER TABLE `phieu_nhap_thanh_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `phieu_xuat_kho`
--
ALTER TABLE `phieu_xuat_kho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phieu_xuat_thanh_pham`
--
ALTER TABLE `phieu_xuat_thanh_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `phieu_yeu_cau_kiem_tra`
--
ALTER TABLE `phieu_yeu_cau_kiem_tra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `phieu_yeu_cau_nguyen_lieu`
--
ALTER TABLE `phieu_yeu_cau_nguyen_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qa_bien_ban`
--
ALTER TABLE `qa_bien_ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `qa_chi_tiet`
--
ALTER TABLE `qa_chi_tiet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `thanh_pham`
--
ALTER TABLE `thanh_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `thietke`
--
ALTER TABLE `thietke`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `baocaosuco`
--
ALTER TABLE `baocaosuco`
  ADD CONSTRAINT `baocaosuco_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cham_cong`
--
ALTER TABLE `cham_cong`
  ADD CONSTRAINT `fk_chamcong_ca` FOREIGN KEY (`id_ca`) REFERENCES `ca_lam_viec` (`id`),
  ADD CONSTRAINT `fk_chamcong_congdoan` FOREIGN KEY (`id_cong_doan`) REFERENCES `cong_doan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chamcong_kehoach` FOREIGN KEY (`id_ke_hoach`) REFERENCES `kehoachsanxuat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chamcong_user_main` FOREIGN KEY (`id_cong_nhan`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chi_tiet_don_hang_ban`
--
ALTER TABLE `chi_tiet_don_hang_ban`
  ADD CONSTRAINT `chi_tiet_don_hang_ban_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang_ban` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chi_tiet_don_hang_mua`
--
ALTER TABLE `chi_tiet_don_hang_mua`
  ADD CONSTRAINT `chi_tiet_don_hang_mua_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang_mua` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cong_doan`
--
ALTER TABLE `cong_doan`
  ADD CONSTRAINT `fk_congdoan_kehoach_main` FOREIGN KEY (`id_ke_hoach`) REFERENCES `kehoachsanxuat` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ct_nhap_nguyen_lieu`
--
ALTER TABLE `ct_nhap_nguyen_lieu`
  ADD CONSTRAINT `ct_nhap_nguyen_lieu_ibfk_1` FOREIGN KEY (`phieu_nhap_id`) REFERENCES `phieu_nhap_nguyen_lieu` (`id`),
  ADD CONSTRAINT `ct_nhap_nguyen_lieu_ibfk_2` FOREIGN KEY (`nguyen_lieu_id`) REFERENCES `nguyen_lieu` (`id`);

--
-- Constraints for table `ct_nhap_thanh_pham`
--
ALTER TABLE `ct_nhap_thanh_pham`
  ADD CONSTRAINT `ct_nhap_thanh_pham_ibfk_1` FOREIGN KEY (`phieu_nhap_id`) REFERENCES `phieu_nhap_thanh_pham` (`id`),
  ADD CONSTRAINT `ct_nhap_thanh_pham_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `thanh_pham` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `ct_xuat_thanh_pham`
--
ALTER TABLE `ct_xuat_thanh_pham`
  ADD CONSTRAINT `ct_xuat_thanh_pham_ibfk_1` FOREIGN KEY (`phieu_xuat_id`) REFERENCES `phieu_xuat_thanh_pham` (`id`),
  ADD CONSTRAINT `ct_xuat_thanh_pham_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_xuat_phieu` FOREIGN KEY (`phieu_xuat_id`) REFERENCES `phieu_xuat_thanh_pham` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_xuat_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `don_hang_ban`
--
ALTER TABLE `don_hang_ban`
  ADD CONSTRAINT `don_hang_ban_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`);

--
-- Constraints for table `don_hang_mua`
--
ALTER TABLE `don_hang_mua`
  ADD CONSTRAINT `don_hang_mua_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `kehoachsanxuat`
--
ALTER TABLE `kehoachsanxuat`
  ADD CONSTRAINT `kehoachsanxuat_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang_ban` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kehoachsanxuat_ibfk_2` FOREIGN KEY (`day_chuyen_id`) REFERENCES `daychuyen` (`id`);

--
-- Constraints for table `kho_nguyen_lieu`
--
ALTER TABLE `kho_nguyen_lieu`
  ADD CONSTRAINT `kho_nguyen_lieu_ibfk_1` FOREIGN KEY (`nguyen_lieu_id`) REFERENCES `nguyen_lieu` (`id`);

--
-- Constraints for table `kho_thanh_pham`
--
ALTER TABLE `kho_thanh_pham`
  ADD CONSTRAINT `kho_thanh_pham_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `lenhsanxuat`
--
ALTER TABLE `lenhsanxuat`
  ADD CONSTRAINT `lenhsanxuat_ibfk_1` FOREIGN KEY (`ke_hoach_id`) REFERENCES `kehoachsanxuat` (`id`),
  ADD CONSTRAINT `lenhsanxuat_ibfk_2` FOREIGN KEY (`xuong_truong_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lich_lam_viec`
--
ALTER TABLE `lich_lam_viec`
  ADD CONSTRAINT `lich_lam_viec_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `phieu_nhap_nguyen_lieu`
--
ALTER TABLE `phieu_nhap_nguyen_lieu`
  ADD CONSTRAINT `phieu_nhap_nguyen_lieu_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `phieu_nhap_nguyen_lieu_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `phieu_nhap_thanh_pham`
--
ALTER TABLE `phieu_nhap_thanh_pham`
  ADD CONSTRAINT `phieu_nhap_thanh_pham_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `phieu_xuat_thanh_pham`
--
ALTER TABLE `phieu_xuat_thanh_pham`
  ADD CONSTRAINT `phieu_xuat_thanh_pham_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `qa_bien_ban`
--
ALTER TABLE `qa_bien_ban`
  ADD CONSTRAINT `qa_bien_ban_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `qa_chi_tiet`
--
ALTER TABLE `qa_chi_tiet`
  ADD CONSTRAINT `qa_chi_tiet_ibfk_1` FOREIGN KEY (`bien_ban_id`) REFERENCES `qa_bien_ban` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `thietke`
--
ALTER TABLE `thietke`
  ADD CONSTRAINT `thietke_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
