<?php
session_start();

// Xóa toàn bộ session
$_SESSION = [];
session_unset();
session_destroy();

// Chuyển về trang đăng nhập
header("Location: ../../login.php");
exit;