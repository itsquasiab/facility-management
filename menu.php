<?php session_start(); // phải gọi đầu tiên trước khi dùng $_SESSION 
?>
<nav class="mx-1 flex justify-between">
  <a href="javascript:void(0);" class="menu-icon" onclick="toggleNav()">
    <img class="menu-icon" alt="Menu">
  </a>
  <div class="flex justify-between" id="nav-content">
    <div id="nav-links">
      <a href="index.html">Trang chủ</a>

      <?php if (isset($_SESSION['quyen'])): ?>
        <a href="thietbi.php">Thiết bị</a>

        <?php if (isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1): ?>
          <a href="phong.php">Phòng</a>
          <a href="nhanvien.php">Nhân viên</a>
        <?php endif; ?>

        <a href="baotri.php">Bảo trì</a>
        <a href="lichsu.php">Lịch sử</a>
      <?php endif; ?>
    </div>

    <div id="nav-links">
      <?php
      if (!isset($_SESSION['quyen']) || !isset($_SESSION['ho_ten'])) {
        echo "<a href='login.php'>Đăng nhập</a>";
      } else {
        echo "<div class='nav-dropdown'>
                <button>Người dùng: " . htmlspecialchars($_SESSION['ho_ten']) . "</button>
                <div class='nav-dropdown-content'>
                  <a href='doimk.php'>Đổi mật khẩu</a>
                  <a href='assets/php/logout.php'>Đăng xuất</a>
                </div>
              </div>";
      }
      ?>
    </div>
  </div>
</nav>