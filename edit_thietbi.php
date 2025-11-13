<?php
require_once "assets/php/config.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("ID không hợp lệ");

// Lấy dữ liệu thiết bị theo ID
$sql = "SELECT * FROM thietbi WHERE id=$id";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) die("Thiết bị không tồn tại");
$thietbi = $res->fetch_assoc();

// Lấy danh sách phòng
$phongOptions = [];
$resPhong = $conn->query("SELECT * FROM phong ORDER BY ma_phong");
while ($row = $resPhong->fetch_assoc()) $phongOptions[] = $row;

// Lấy danh sách nhân viên
$nhanvienOptions = [];
$resNV = $conn->query("SELECT * FROM nhanvien ORDER BY ho_ten");
while ($row = $resNV->fetch_assoc()) $nhanvienOptions[] = $row;

$error = "";
// Giá trị hiển thị trong form (nếu submit lỗi thì giữ giá trị đã nhập)
$form = [
  'ten_thiet_bi' => $thietbi['ten_thiet_bi'],
  'ma_thiet_bi' => $thietbi['ma_thiet_bi'],
  'loai_thiet_bi' => $thietbi['loai_thiet_bi'],
  'hang_san_xuat' => $thietbi['hang_san_xuat'],
  'ngay_mua' => $thietbi['ngay_mua'],
  'tinh_trang' => $thietbi['tinh_trang'],
  'so_luong' => $thietbi['so_luong'],
  'don_vi_tinh' => $thietbi['don_vi_tinh'],
  'phong_id' => $thietbi['phong_id'],
  'nhanvien_id' => $thietbi['nhanvien_id']
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // lấy và escape input
  $ten        = $conn->real_escape_string($_POST["ten_thiet_bi"] ?? "");
  $ma         = $conn->real_escape_string($_POST["ma_thiet_bi"] ?? "");
  $loai       = $conn->real_escape_string($_POST["loai_thiet_bi"] ?? "");
  $hang       = $conn->real_escape_string($_POST["hang_san_xuat"] ?? "");
  $ngay_raw   = $_POST["ngay_mua"] ?? "";
  $ngay       = $ngay_raw !== "" ? $conn->real_escape_string($ngay_raw) : null;
  $tinhtrang  = $conn->real_escape_string($_POST["tinh_trang"] ?? "");
  $so_luong_moi = intval($_POST["so_luong"] ?? 0);
  $don_vi     = $conn->real_escape_string($_POST["don_vi_tinh"] ?? "");
  $phong      = isset($_POST["phong_id"]) && $_POST["phong_id"] !== "" ? intval($_POST["phong_id"]) : "NULL";
  $nhanvien   = isset($_POST["nhanvien_id"]) && $_POST["nhanvien_id"] !== "" ? intval($_POST["nhanvien_id"]) : "NULL";

  // cập nhật form để hiển thị lại nếu có lỗi
  $form = [
    'ten_thiet_bi' => htmlspecialchars($_POST["ten_thiet_bi"] ?? ""),
    'ma_thiet_bi' => htmlspecialchars($_POST["ma_thiet_bi"] ?? ""),
    'loai_thiet_bi' => htmlspecialchars($_POST["loai_thiet_bi"] ?? ""),
    'hang_san_xuat' => htmlspecialchars($_POST["hang_san_xuat"] ?? ""),
    'ngay_mua' => $ngay_raw,
    'tinh_trang' => htmlspecialchars($_POST["tinh_trang"] ?? ""),
    'so_luong' => $so_luong_moi,
    'don_vi_tinh' => htmlspecialchars($_POST["don_vi_tinh"] ?? ""),
    'phong_id' => $phong === "NULL" ? "" : $phong,
    'nhanvien_id' => $nhanvien === "NULL" ? "" : $nhanvien
  ];

  $so_luong_cu = intval($thietbi["so_luong"]);

  // VALIDATION: không cho phép tăng số lượng
  if ($so_luong_moi > $so_luong_cu) {
    $error = "Không được tăng số lượng. Số lượng mới phải nhỏ hơn hoặc bằng số lượng hiện có ({$so_luong_cu}).";
  } else {
    // chuẩn bị SQL (dùng inline cho NULL handling giống cấu trúc hiện có)
    $ngay_sql = $ngay ? "'$ngay'" : "NULL";
    $phong_sql = $phong === "NULL" ? "NULL" : $phong;
    $nhanvien_sql = $nhanvien === "NULL" ? "NULL" : $nhanvien;

    if ($so_luong_moi < $so_luong_cu) {
      $conn->begin_transaction();
      try {
        // Bản ghi cũ: chỉ giảm số lượng, giữ nguyên tình trạng cũ
        $sql_update = "UPDATE thietbi SET 
                ten_thiet_bi='$ten', ma_thiet_bi='$ma', loai_thiet_bi='$loai',
                hang_san_xuat='$hang', ngay_mua=$ngay_sql,
                tinh_trang='" . $conn->real_escape_string($thietbi['tinh_trang']) . "', 
                so_luong=$so_luong_moi, don_vi_tinh='$don_vi',
                phong_id=$phong_sql, nhanvien_id=$nhanvien_sql
                WHERE id=$id";
        if (!$conn->query($sql_update)) throw new Exception("Update thất bại: " . $conn->error);

        // Bản ghi mới: số lượng còn lại, tình trạng mới
        $so_con_lai = $so_luong_cu - $so_luong_moi;
        $sql_insert = "INSERT INTO thietbi 
                (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
                VALUES 
                ('$ten','$ma','$loai','$hang',$ngay_sql,'$tinhtrang',$so_con_lai,'$don_vi',$phong_sql,$nhanvien_sql)";
        if (!$conn->query($sql_insert)) throw new Exception("Insert thất bại: " . $conn->error);

        $conn->commit();
        header("Location: thietbi.php");
        exit;
      } catch (Exception $e) {
        $conn->rollback();
        $error = "Lỗi khi tách bản ghi: " . $e->getMessage();
      }
    } else {
      // so_luong_moi == so_luong_cu -> update bình thường
      $sql_update = "UPDATE thietbi SET 
                ten_thiet_bi='$ten', ma_thiet_bi='$ma', loai_thiet_bi='$loai',
                hang_san_xuat='$hang', ngay_mua=$ngay_sql,
                tinh_trang='$tinhtrang', so_luong=$so_luong_moi, don_vi_tinh='$don_vi',
                phong_id=$phong_sql, nhanvien_id=$nhanvien_sql
                WHERE id=$id";
      if ($conn->query($sql_update)) {
        header("Location: thietbi.php");
        exit;
      } else {
        $error = "Lỗi cập nhật: " . $conn->error;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Sửa thiết bị</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/tailwind.css">
</head>

<body>
  <div class="container p-4">
    <h1 class="text-2xl font-semibold mb-2">Sửa thiết bị</h1>

    <?php if ($error): ?>
      <div style="color: #721c24; background:#f8d7da; padding:10px; border-radius:6px; margin-bottom:12px;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="flex flex-col gap-0.5">
      <label>Tên thiết bị:
        <input class="input" type="text" name="ten_thiet_bi" required value="<?= $form['ten_thiet_bi'] ?>">
      </label>

      <label>Mã thiết bị:
        <input class="input" type="text" name="ma_thiet_bi" required value="<?= $form['ma_thiet_bi'] ?>">
      </label>

      <label>Loại:
        <input class="input" type="text" name="loai_thiet_bi" value="<?= $form['loai_thiet_bi'] ?>">
      </label>

      <label>Hãng SX:
        <input class="input" type="text" name="hang_san_xuat" value="<?= $form['hang_san_xuat'] ?>">
      </label>

      <label>Ngày mua:
        <input class="input" type="date" name="ngay_mua" value="<?= $form['ngay_mua'] ?>">
      </label>

      <label>Tình trạng:
        <div class="flex gap-0.5"> 
          <input type="radio" name="tinh_trang" value="Đang sử dụng"> Đang sử dụng 
          <input type="radio" name="tinh_trang" value="Hỏng"> Hỏng 
          <input type="radio" name="tinh_trang" value="Bảo trì"> Bảo trì 
        </div>
      </label>

      <label>Số lượng (≤ <?= intval($thietbi['so_luong']) ?>):
        <input class="input" type="number" name="so_luong" min="0" max="<?= intval($thietbi['so_luong']) ?>" required value="<?= $form['so_luong'] ?>">
      </label>

      <label>Đơn vị tính:
        <input class="input" type="text" name="don_vi_tinh" value="<?= $form['don_vi_tinh'] ?>">
      </label>

      <label>Phòng:
        <select name="phong_id">
          <option value="">-- Không chọn --</option>
          <?php foreach ($phongOptions as $p): ?>
            <option value="<?= $p['id'] ?>" <?= ($form['phong_id'] == $p['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['ma_phong'] . ' - ' . $p['ten_phong']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Nhân viên:
        <select name="nhanvien_id">
          <option value="">-- Không chọn --</option>
          <?php foreach ($nhanvienOptions as $nv): ?>
            <option value="<?= $nv['id'] ?>" <?= ($form['nhanvien_id'] == $nv['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($nv['ho_ten']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <div class="flex gap-0.5">
        <button type="submit">Lưu</button>
        <a class="button" href="thietbi.php">Quay lại</a>
      </div>
    </form>
  </div>
</body>

</html>