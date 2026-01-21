<?php
session_start();
require_once "config.php";
// Bật debug khi cần
ini_set('display_errors', 1);
error_reporting(E_ALL);
$id = $_SESSION['id']; // giả sử đã đăng nhập
$quyen = $_SESSION['quyen'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "Mật khẩu mới không khớp.";
        exit;
    }

    // Lấy mật khẩu hiện tại từ DB
    $query = "SELECT pass FROM nhanvien WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Lỗi prepare: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Kiểm tra mật khẩu hiện tại
    if (!password_verify($current_password, $hashed_password)) {
        echo "Mật khẩu hiện tại không đúng.";
        exit;
    }

    // Hash mật khẩu mới
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Cập nhật mật khẩu
    $update = "UPDATE nhanvien SET pass = ? WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("si", $new_hashed_password, $id);
    if ($stmt->execute()) {
        echo "Đổi mật khẩu thành công!";
        if ($quyen == 1) {
            header("Location: ../../index.html");
        } elseif ($quyen == 2) {
            header("Location: ../../thietbi.php?nhan_vien=" . $ho_ten);
        } else {
            header("Location: ../../index.html");
        }
    } else {
        echo "Có lỗi xảy ra.";
    }
    $stmt->close();
    $conn->close();
}
