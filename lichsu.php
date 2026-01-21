<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lịch sử chỉnh sửa</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>

<body>
    <header id="menu"></header>

    <div class="p-1 md:p-2">
        <h1 class="text-3xl font-semibold mb-1">Lịch sử chỉnh sửa thiết bị</h1>

        <div class="my-container table-container my-1">
            <p id="count-phong" class="text-lg font-medium p-1">Đang hiển thị: 0 lần sửa đổi</p>
            <table id="table-phong">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người chỉnh sửa</th>
                        <th>Ngày sửa</th>
                        <th>Nội dung sửa</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>

</html>