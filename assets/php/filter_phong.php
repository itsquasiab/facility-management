<section class="my-container p-1 my-1">
    <h2 class="text-2xl font-semibold">Bộ lọc & tìm kiếm</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 md:gap-1 mt-1">

        <div>
            <label>Tên phòng:</label><br>
            <input class="w-full" id="filter-ten-phong" placeholder="VD: 10 Tin, STEM,..." type="text" /><br>
        </div>

        <div>
            <label>Mã phòng:</label><br>
            <input class="w-full" id="filter-ma-phong" placeholder="VD: C301, D102,..." type="text" /><br>
        </div>

        <div>
            <label>Dãy nhà:</label><br>
            <select class="w-full" id="filter-day-nha">
                <option value="">-- Tất cả --</option>
                <?php foreach ($dayOptions as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                <?php endforeach; ?>
            </select><br>
        </div>

        <div>
            <label>Tầng:</label><br>
            <select class="w-full" id="filter-tang">
                <option value="">-- Tất cả --</option>
                <?php foreach ($tangOptions as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                <?php endforeach; ?>
            </select><br>
        </div>

    </div>
</section>