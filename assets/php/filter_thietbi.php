<section class="my-container p-1 my-1">
    <h1 class="text-2xl font-semibold">Bộ lọc & tìm kiếm</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-1 mt-1">
        <div>
            <label>Tên thiết bị:</label><br>
            <input class="w-full" id="filter-ten-thiet-bi" placeholder="VD: Bàn ghế, Dụng cụ,..." type="text" /><br>

            <label>Hãng sản xuất:</label><br>
            <input class="w-full" id="filter-hang-san-xuat" placeholder="VD: Dell, HP,..." type="text" /><br>

            <label>Tình trạng:</label><br>
            <select class="w-full" id="filter-tinh-trang">
                <option value="">-- Tất cả --</option>
                <?php foreach ($tinhtrangOptions as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>">
                        <?= htmlspecialchars($opt) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
        </div>

        <div>
            <label>Tên phòng:</label><br>
            <input class="w-full" id="filter-ten-phong" type="text" value="<?= htmlspecialchars($tenPhongURL) ?>"
                placeholder="VD: 10 Tin, STEM,..." /><br>

            <label>Mã phòng:</label><br>
            <input class="w-full" id="filter-ma-phong" type="text" value="<?= htmlspecialchars($maPhongURL) ?>"
                placeholder="VD: C301, D102,..." /><br>

            <div class="grid grid-cols-1 md:grid-cols-2 md:gap-1">
                <div>
                    <label>Dãy nhà:</label><br>
                    <select class="w-full" id="filter-day-nha">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($dayOptions as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $opt == $dayNhaURL ? 'selected' : '' ?>>
                                <?= htmlspecialchars($opt) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>
                </div>

                <div>
                    <label>Tầng:</label><br>
                    <select class="w-full" id="filter-tang">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($tangOptions as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $opt == $tangURL ? 'selected' : '' ?>>
                                <?= htmlspecialchars($opt) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Kiểm tra quyền
    if ($_SESSION['quyen'] == 1): // admin mới hiển thị
    ?>
        <div class="mt-1">
            <label>Tìm kiếm nhân viên:</label><br>
            <input class="w-full" id="filter-nhan-vien" type="text"
                value="<?= htmlspecialchars($nhanVienURL) ?>"
                placeholder="VD: Nguyễn Văn A,..." /><br>
        </div>
    <?php else: ?>
        <!-- Ẩn phần hiển thị nhưng vẫn giữ value trong input ẩn -->
        <input type="hidden" id="filter-nhan-vien" value="<?= htmlspecialchars($nhanVienURL) ?>">
    <?php endif; ?>

</section>