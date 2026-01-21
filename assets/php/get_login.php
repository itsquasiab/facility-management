<?php
session_start();
require 'config.php';

// Ký tự tiếng Việt
$conn->set_charset("utf8mb4");

$error = "";

// Nếu người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Nhận dữ liệu từ form
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $password = $_POST['password'];

    // Kiểm tra không để trống
    if (empty($so_dien_thoai) || empty($password)) {
        $error = "Vui lòng nhập đủ thông tin!";
    } else {
        // Truy vấn CSDL
        $sql = "SELECT id, pass, quyen, ho_ten FROM nhanvien WHERE so_dien_thoai = ? LIMIT 1";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $so_dien_thoai);
            $stmt->execute();
            $stmt->store_result();

            // Nếu tìm thấy 1 tài khoản
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $hashed_password, $quyen, $ho_ten);
                $stmt->fetch();

                // Kiểm tra mật khẩu hash
                if (password_verify($password, $hashed_password)) {

                    // Lưu session đăng nhập
                    $_SESSION['id'] = $id;
                    $_SESSION['ho_ten'] = $ho_ten;
                    $_SESSION['quyen'] = $quyen;

                    // Chuyển theo quyền
                    if ($quyen == 1) {
                        header("Location: ../../index.html");
                    } elseif ($quyen == 2) {
                        header("Location: ../../thietbi.php?nhan_vien=".$ho_ten);
                    } else {
                        header("Location: ../../index.html");
                    }
                    exit();

                } else {
                    $error = "⚠️ Mật khẩu không đúng!";
                }

            } else {
                $error = "⚠️ Số điện thoại không tồn tại!";
            }

            $stmt->close();
        } else {
            $error = "Lỗi hệ thống!";
        }
    }

    $_SESSION['error_login'] = $error;
    header("Location: login.php");
    exit();
}
?>
