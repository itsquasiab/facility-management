<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đổi mật khẩu</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/tailwind.css">
  <style>
    body {
      height: 100vh;
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
    <h1 class="text-2xl md:text-3xl font-semibold text-center">Đổi mật khẩu</h1>

    <form method="POST" action="assets/php/change_password.php" class="flex flex-col gap-0.5 my-1">
      <div class="form-wrapper">
        <label for="current_password">Mật khẩu hiện tại:</label>
        <input type="password" id="current_password" name="current_password" required>
        <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
          <img class="visibility_icon" alt="Hiện mật khẩu">
        </button>
      </div>

      <div class="form-wrapper">
        <label for="new_password">Mật khẩu mới:</label>
        <input type="password" id="new_password" name="new_password" required>
        <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
          <img class="visibility_icon" alt="Hiện mật khẩu">
        </button>
      </div>

      <div class="form-wrapper">
        <label for="confirm_password">Xác nhận mật khẩu mới:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
          <img class="visibility_icon" alt="Hiện mật khẩu">
        </button>
      </div>

      <div class="grid gap-0.5 mt-0.5">
        <button type="submit" class="mt-0.5">Đổi mật khẩu</button>
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