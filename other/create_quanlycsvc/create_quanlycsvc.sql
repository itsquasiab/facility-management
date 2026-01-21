CREATE DATABASE IF NOT EXISTS quanlycsvc
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE quanlycsvc;

CREATE TABLE phong (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ma_phong VARCHAR(50) UNIQUE NOT NULL, -- mã phòng
    ten_phong VARCHAR(255) NOT NULL, -- tên phòng
    day_nha VARCHAR(100), -- dãy nhà
    tang INT, -- tầng
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE nhanvien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(255) NOT NULL, -- họ tên
    chuc_vu VARCHAR(100), -- chức vụ (giáo viên, hiệu trưởng, hiệu phó, kế toán,...)
    so_dien_thoai VARCHAR(20), -- số điện thoại
    email VARCHAR(100), -- email
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE thietbi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_thiet_bi VARCHAR(255) NOT NULL, -- tên thiết bị
    ma_thiet_bi VARCHAR(100) UNIQUE NOT NULL, -- mã thiết bị
    loai_thiet_bi VARCHAR(100), -- loại thiết bị
    hang_san_xuat VARCHAR(100), -- hãng sản xuất
    ngay_mua DATE, -- ngày mua
    tinh_trang VARCHAR(100),  -- tình trạng (đang sử dụng, hư hỏng, bảo trì)
    so_luong INT DEFAULT 1,
    don_vi_tinh VARCHAR(50) DEFAULT 'cái',
    phong_id INT,             -- FK liên kết phòng
    nhanvien_id INT,          -- FK liên kết nhân viên phụ trách
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (phong_id) REFERENCES phong(id),
    FOREIGN KEY (nhanvien_id) REFERENCES nhanvien(id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE baotri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thietbi_id INT NOT NULL,
    ngay_yeu_cau DATE NOT NULL, -- ngày yêu cầu
    ngay_bat_dau DATE, -- ngày bắt đầu
    ngay_hoan_thanh DATE, -- ngày hoàn thành
    noi_dung TEXT, -- nội dung
    chi_phi DECIMAL(15,2), -- chi phí
    trang_thai VARCHAR(100), -- "Đang xử lý", "Hoàn thành", "Hủy"
    nhanvien_id INT,         -- ai xử lý
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (thietbi_id) REFERENCES thietbi(id),
    FOREIGN KEY (nhanvien_id) REFERENCES nhanvien(id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;