<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
        }

        .form-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: -6px;
            top: 25.8px;
            cursor: pointer;
            background: none;
            border: none;
            box-shadow: none;
        }
    </style>
</head>

<body class="image-background">

    <div class="glass rounded-2xl p-2 md:p-3">
        <h1 class="text-2xl md:text-3xl font-semibold text-center">Đăng nhập hệ thống</h1>

        <form method="POST" action="assets/php/get_login.php" class="flex flex-col gap-0.5 my-1">

            <div class="form-wrapper">
                <label>Số điện thoại:</label>
                <input type="text" name="so_dien_thoai" required>
            </div>

            <div class="form-wrapper">
                <label for="password">Mật khẩu mới:</label>
                <input type="password" id="password" name="password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                    <img class="visibility_icon" alt="Hiện mật khẩu">
                </button>
            </div>

            <div class="grid gap-0.5 mt-0.5">
                <button type="submit">Đăng nhập</button>
                <a href="index.html" class="button secondary">Về trang chủ</a>
            </div>
        </form>
        <div class="text-sm text-gray-800 text-center">© 2025 Phần mềm quản lý CSVC</div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }
    </script>

</body>

</html>