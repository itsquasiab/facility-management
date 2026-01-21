-- Giả lập dữ liệu thiết bị cho các phòng trong trường học
-- Tự động phân loại theo loai_phong

INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Bộ bàn ghế học sinh', CONCAT('BG_', ma_phong), 'Nội thất', 'Hòa Phát', '2023-08-01', 'Đang sử dụng', 40, 'bộ', id, 1
FROM phong WHERE loai_phong IN ('Lớp học', 'Lớp học (DP)', 'Phòng học bộ môn');

INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Bảng đen', CONCAT('BD_', ma_phong), 'Thiết bị giảng dạy', 'Thiết bị GD Việt Nam', '2023-08-01', 'Đang sử dụng', 1, 'cái', id, 1
FROM phong WHERE loai_phong IN ('Lớp học', 'Lớp học (DP)', 'Phòng học bộ môn');

INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'TV Samsung 55 inch', CONCAT('TV_', ma_phong), 'Thiết bị điện tử', 'Samsung', '2023-08-01', 'Đang sử dụng', 1, 'cái', id, 1
FROM phong WHERE loai_phong IN ('Lớp học', 'Lớp học (DP)', 'Phòng học bộ môn');

-- Phòng thực hành
INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Dụng cụ thí nghiệm khoa học', CONCAT('TN_', ma_phong), 'Dụng cụ thí nghiệm', 'Thiết bị GD Việt Nam', '2023-08-01', 'Đang sử dụng', 15, 'bộ', id, 2
FROM phong WHERE loai_phong = 'Phòng thực hành';

-- Phòng STEM
INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Bộ dụng cụ STEM Robotics', CONCAT('STEM_', ma_phong), 'Thiết bị STEM', 'LEGO Education', '2023-08-01', 'Đang sử dụng', 5, 'bộ', id, 3
FROM phong WHERE loai_phong = 'Phòng STEM';

INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Máy tính để bàn', CONCAT('PC_', ma_phong), 'Thiết bị CNTT', 'Dell', '2023-08-01', 'Đang sử dụng', 15, 'máy', id, 3
FROM phong WHERE loai_phong = 'Phòng STEM';

-- Phòng máy tính
INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Máy tính để bàn', CONCAT('PC_', ma_phong), 'Thiết bị CNTT', 'Dell', '2023-08-01', 'Đang sử dụng', 30, 'máy', id, 4
FROM phong WHERE loai_phong = 'Phòng máy tính';

INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Màn hình LCD 24 inch', CONCAT('MH_', ma_phong), 'Thiết bị CNTT', 'Dell', '2023-08-01', 'Đang sử dụng', 30, 'cái', id, 4
FROM phong WHERE loai_phong = 'Phòng máy tính';

INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Chuột quang', CONCAT('MS_', ma_phong), 'Thiết bị CNTT', 'Logitech', '2023-08-01', 'Đang sử dụng', 30, 'cái', id, 4
FROM phong WHERE loai_phong = 'Phòng máy tính';

INSERT INTO thietbi (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
SELECT 'Bàn phím', CONCAT('KB_', ma_phong), 'Thiết bị CNTT', 'Logitech', '2023-08-01', 'Đang sử dụng', 30, 'cái', id, 4
FROM phong WHERE loai_phong = 'Phòng máy tính';
