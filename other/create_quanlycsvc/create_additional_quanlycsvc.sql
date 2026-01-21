CREATE TABLE thietbi_tinhtrang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thietbi_id INT NOT NULL,
    tinh_trang ENUM('Đang sử dụng','Hư hỏng','Bảo trì') NOT NULL,
    so_luong INT NOT NULL DEFAULT 0,
    FOREIGN KEY (thietbi_id) REFERENCES thietbi(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
