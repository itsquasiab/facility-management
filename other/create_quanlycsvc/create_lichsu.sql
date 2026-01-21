CREATE TABLE lichsu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thietbi_id INT NOT NULL,
    truong VARCHAR(100) NOT NULL,       -- cột nào bị sửa
    gia_tri_cu TEXT,                    -- giá trị cũ
    gia_tri_moi TEXT,                   -- giá trị mới
    nguoi_sua VARCHAR(100),             -- người sửa (sau này có thể lấy từ session)
    ngay_sua DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thietbi_id) REFERENCES thietbi(id)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
