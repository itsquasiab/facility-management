<?php
// Kết nối MySQL
require_once "config.php";
$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8");

$data = json_decode(file_get_contents("php://input"), true);
$headers = $data['headers'];
$rows = $data['rows'];
print_r($headers);
$table = 'main'; // 🔁 Thay tên bảng tại đây

$colNames = array_map(function($h) use ($conn) {
    return '`' . $conn->real_escape_string($h) . '`';
}, $headers);

$successCount = 0;
foreach ($rows as $row) {
    $escapedValues = array_map(function($v) use ($conn) {
        return "'" . $conn->real_escape_string($v) . "'";
    }, $row);

   // $sql = "INSERT INTO `$table` (" . implode(",", $colNames) . ")
         //   VALUES (" . implode(",", $escapedValues) . ")";

    //if ($conn->query($sql)) {
      //  $successCount++;
    //}
}

//echo "Đã lưu thành công {$successCount} dòng vào cơ sở dữ liệu.";
?>
