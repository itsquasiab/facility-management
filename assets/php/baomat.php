<?php
if (!isset($_SESSION['quyen']) || !isset($_SESSION['ho_ten'])) {
    // Chưa đăng nhập
    header("Location: ../../login.php");
    exit;
}

// Nếu là quyền 2 (giáo viên), chỉ cho xem thiết bị của họ
if ($_SESSION['quyen'] == 2) {

    // Nếu không có param hoặc param khác tên giáo viên => redirect đúng tên
    if (!isset($_GET['nhan_vien']) || $_GET['nhan_vien'] !== $_SESSION['ho_ten']) {
        header("Location: ../../thietbi.php?nhan_vien=" . urlencode($_SESSION['ho_ten']));
        exit;
    }
}
?>