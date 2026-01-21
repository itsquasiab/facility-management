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
$form = [
  'ten_thiet_bi' => $thietbi['ten_thiet_bi'],
  'ma_thiet_bi' => $thietbi['ma_thiet_bi'],
  'loai_thiet_bi' => $thietbi['loai_thiet_bi'],
  'hang_san_xuat' => $thietbi['hang_san_xuat'],
  'ngay_mua' => $thietbi['ngay_mua'],
  'tinh_trang' => $thietbi['tinh_trang'],
  'so_luong' => $thietbi['so_luong'],
  'don_vi_tinh' => $thietbi['don_vi_tinh'],
  'mo_ta' => $thietbi['mo_ta'],
  'phong_id' => $thietbi['phong_id'],
  'nhanvien_id' => $thietbi['nhanvien_id']
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $ten        = $conn->real_escape_string($_POST["ten_thiet_bi"] ?? "");
  $ma         = $conn->real_escape_string($_POST["ma_thiet_bi"] ?? "");
  $loai       = $conn->real_escape_string($_POST["loai_thiet_bi"] ?? "");
  $hang       = $conn->real_escape_string($_POST["hang_san_xuat"] ?? "");
  $ngay_raw   = $_POST["ngay_mua"] ?? "";
  $ngay       = $ngay_raw !== "" ? $conn->real_escape_string($ngay_raw) : null;
  $tinhtrang  = $conn->real_escape_string($_POST["tinh_trang"] ?? "");
  $so_luong_moi = intval($_POST["so_luong"] ?? 0);
  $don_vi     = $conn->real_escape_string($_POST["don_vi_tinh"] ?? "");
  $mo_ta      = $conn->real_escape_string($_POST["mo_ta"] ?? "");
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
    'mo_ta' => htmlspecialchars($_POST["mo_ta"] ?? ""),
    'phong_id' => $phong === "NULL" ? "" : $phong,
    'nhanvien_id' => $nhanvien === "NULL" ? "" : $nhanvien
  ];

  $so_luong_cu = intval($thietbi["so_luong"]);

  if ($so_luong_moi > $so_luong_cu) {
    $error = "Không được tăng số lượng. Số lượng mới phải nhỏ hơn hoặc bằng số lượng hiện có ({$so_luong_cu}).";
  } else {
    $ngay_sql = $ngay ? "'$ngay'" : "NULL";
    $phong_sql = $phong === "NULL" ? "NULL" : $phong;
    $nhanvien_sql = $nhanvien === "NULL" ? "NULL" : $nhanvien;

    // chuẩn bị tên nhân viên (dùng để redirect)
    $nhanvien_ten = "";
    if ($nhanvien !== "NULL" && $nhanvien > 0) {
      $rs_nv = $conn->query("SELECT ho_ten FROM nhanvien WHERE id=$nhanvien");
      if ($rs_nv && $row = $rs_nv->fetch_assoc()) {
        $nhanvien_ten = $row['ho_ten'];
      }
    }

    if ($so_luong_moi < $so_luong_cu) {
      $conn->begin_transaction();
      try {
        // Bản ghi cũ: giảm số lượng
        $so_con_lai = $so_luong_cu - $so_luong_moi;
        $sql_update = "UPDATE thietbi SET 
                ten_thiet_bi='$ten', ma_thiet_bi='$ma', loai_thiet_bi='$loai',
                hang_san_xuat='$hang', ngay_mua=$ngay_sql,             
                so_luong=$so_con_lai, don_vi_tinh='$don_vi',
                mo_ta='$mo_ta',
                phong_id=$phong_sql, nhanvien_id=$nhanvien_sql
                WHERE id=$id";
        if (!$conn->query($sql_update)) throw new Exception("Update thất bại: " . $conn->error);

        // Bản ghi mới: số lượng tách ra, tình trạng mới
        $sql_insert = "INSERT INTO thietbi 
                (ten_thiet_bi, ma_thiet_bi, loai_thiet_bi, hang_san_xuat, ngay_mua, tinh_trang, so_luong, don_vi_tinh, phong_id, nhanvien_id)
                VALUES 
                ('$ten','$ma','$loai','$hang',$ngay_sql,'$tinhtrang',$so_luong_moi,'$don_vi',$phong_sql,$nhanvien_sql)";
        if (!$conn->query($sql_insert)) throw new Exception("Insert thất bại: " . $conn->error);

        $conn->commit();
        header("Location: thietbi.php?nhan_vien=" . urlencode($nhanvien_ten));
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
                mo_ta='$mo_ta',
                phong_id=$phong_sql, nhanvien_id=$nhanvien_sql
                WHERE id=$id";
      if ($conn->query($sql_update)) {
        header("Location: thietbi.php?nhan_vien=" . urlencode($nhanvien_ten));
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sửa thiết bị</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/tailwind.css">
</head>

<body>
  <div class="p-1 md:p-2">
    <h1 class="text-3xl font-semibold mb-1">Điều chuyển, báo hỏng & bảo trì...</h1>

    <?php if ($error): ?>
      <div class="error-banner p-1 mb-1">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <table>
        <thead>
          <tr class="table-edit-form">
            <th class="text-right w-40">Tên thiết bị:</th>
            <th>
              <input class="input w-full" type="text" name="ten_thiet_bi" required
                value="<?= $form['ten_thiet_bi'] ?>">
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-right">Mã thiết bị:</td>
            <td><input class="input w-full" type="text" name="ma_thiet_bi" required value="<?= $form['ma_thiet_bi'] ?>">
            </td>
          </tr>

          <tr>
            <td class="text-right">Loại:</td>
            <td><input class="input w-full" type="text" name="loai_thiet_bi" value="<?= $form['loai_thiet_bi'] ?>"></td>
          </tr>

          <tr>
            <td class="text-right">Hãng sản xuất:</td>
            <td><input class="input w-full" type="text" name="hang_san_xuat" value="<?= $form['hang_san_xuat'] ?>"></td>
          </tr>

          <tr>
            <td class="text-right">Ngày mua:</td>
            <td><input class="input w-full" type="date" name="ngay_mua" value="<?= $form['ngay_mua'] ?>"></td>
          </tr>

          <tr>
            <td class="text-right align-top">Tình trạng:</td>
            <td>
              <label class="custom-radio">
                <input class="radio" type="radio" name="tinh_trang" value="Đang sử dụng"
                  <?= ($form['tinh_trang'] == "Đang sử dụng" ? "checked" : "") ?>>
                <span class="radio"></span>
                <span class="text-green-600 font-semibold">Đang sử dụng</span>
              </label>
              <br>
              <label class="custom-radio">
                <input class="radio" type="radio" name="tinh_trang" value="Hỏng" <?= ($form['tinh_trang'] == "Hỏng" ? "checked" : "") ?>>
                <span class="radio"></span>
                <span class="text-red-600 font-semibold">Hỏng</span>
              </label>
              <br>
              <label class="custom-radio">
                <input class="radio" type="radio" name="tinh_trang" value="Bảo trì" <?= ($form['tinh_trang'] == "Bảo trì" ? "checked" : "") ?>>
                <span class="radio"></span>
                <span class="text-yellow-600 font-semibold">Bảo trì</span>
              </label>
            </td>
          </tr>

          <tr>
            <td class="text-right">Số lượng (≤
              <?= intval($thietbi['so_luong']) ?>):
            </td>
            <td><input class="input w-full" type="number" name="so_luong" min="0"
                max="<?= intval($thietbi['so_luong']) ?>" required value="<?= $form['so_luong'] ?>"></td>
          </tr>

          <tr>
            <td class="text-right">Đơn vị tính:</td>
            <td><input class="input w-full" type="text" name="don_vi_tinh" value="<?= $form['don_vi_tinh'] ?>"></td>
          </tr>

          <tr>
            <td class="text-right">Mô tả:</td>
            <td>
              <textarea class="input w-full" rows="3" name="mo_ta" value="<?= $form['mo_ta'] ?>"></textarea>
            </td>
          </tr>

          <tr>
            <td class="text-right">Phòng:</td>
            <td>
              <select name="phong_id" class="input w-full">
                <option value="">-- Không chọn --</option>
                <?php foreach ($phongOptions as $p): ?>
                  <option value="<?= $p['id'] ?>" <?= ($form['phong_id'] == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['ma_phong'] . ' - ' . $p['ten_phong']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>

          <tr>
            <td class="text-right">Nhân viên:</td>
            <td>
              <select name="nhanvien_id" class="input w-full">
                <option value="">-- Không chọn --</option>
                <?php foreach ($nhanvienOptions as $nv): ?>
                  <option value="<?= $nv['id'] ?>" <?= ($form['nhanvien_id'] == $nv['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nv['ho_ten']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>

          <tr>
            <td></td>
            <td class="flex gap-0.5">
              <button class="secondary" type="submit">Lưu</button>
              <a class="button secondary" href="thietbi.php">Quay lại</a>
            </td>
          </tr>
        </tbody>
      </table>
    </form>

  </div>

  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <script src="assets/js/quill_setup.js"></script>
</body>

</html>