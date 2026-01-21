<?php
require_once "assets/php/config.php";
session_start();
if (!isset($_SESSION['quyen']) || !isset($_SESSION['ho_ten'])) {
  // Chưa đăng nhập
  header("Location: login.php");
  exit;
}
include 'baomat.php';
// ====== XỬ LÝ AJAX ======

// Lấy dữ liệu phòng có thiết bị
if (isset($_GET['action']) && $_GET['action'] === "getData") {
  $conditions = [];

  if (!empty($_GET['day_nha'])) {
    $conditions[] = "p.day_nha = '" . $conn->real_escape_string($_GET['day_nha']) . "'";
  }
  if (!empty($_GET['tang'])) {
    $conditions[] = "p.tang = '" . $conn->real_escape_string($_GET['tang']) . "'";
  }
  if (!empty($_GET['ma_phong'])) {
    $conditions[] = "p.ma_phong LIKE '%" . $conn->real_escape_string($_GET['ma_phong']) . "%'";
  }
  if (!empty($_GET['ten_phong'])) {
    $conditions[] = "p.ten_phong LIKE '%" . $conn->real_escape_string($_GET['ten_phong']) . "%'";
  }

  $where = count($conditions) ? "AND " . implode(" AND ", $conditions) : "";

  $sql = "
        SELECT DISTINCT p.id, p.ma_phong, p.ten_phong, p.day_nha, p.tang,
               MAX(t.nhanvien_id) AS nhanvien_id
        FROM phong p
        JOIN thietbi t ON p.id = t.phong_id
        WHERE 1=1 $where
        GROUP BY p.id, p.ma_phong, p.ten_phong, p.day_nha, p.tang
        ORDER BY p.id ASC
    ";

  $result = $conn->query($sql);
  $data = [];
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data);
  exit;
}

// Cập nhật nhân viên quản lý cho phòng
if (isset($_POST['action']) && $_POST['action'] === "assignNhanvien") {
  $phong_id = intval($_POST['phong_id']);
  $nhanvien_id = intval($_POST['nhanvien_id']);

  $stmt = $conn->prepare("UPDATE thietbi SET nhanvien_id = ? WHERE phong_id = ?");
  $stmt->bind_param("ii", $nhanvien_id, $phong_id);
  $success = $stmt->execute();
  $stmt->close();

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(["success" => $success]);
  exit;
}

// ====== LẤY DỮ LIỆU CHO BỘ LỌC ======
$dayOptions = [];
$res = $conn->query("SELECT DISTINCT day_nha FROM phong");
while ($row = $res->fetch_assoc()) $dayOptions[] = $row['day_nha'];

$tangOptions = [];
$res = $conn->query("SELECT DISTINCT tang FROM phong ORDER BY tang ASC");
while ($row = $res->fetch_assoc()) $tangOptions[] = $row['tang'];

// ====== LẤY DANH SÁCH NHÂN VIÊN ======
$nhanvienOptions = [];
$res = $conn->query("SELECT id, ho_ten FROM nhanvien ORDER BY ho_ten ASC");
while ($row = $res->fetch_assoc()) $nhanvienOptions[] = $row;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Danh sách Phòng</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/tailwind.css">
</head>

<body>
  <header id="menu"></header>

  <div class="p-1 md:p-2">
    <h1 class="text-3xl font-semibold mb-1">Danh sách các phòng (có thiết bị)</h1>

    <?php include 'assets/php/filter_phong.php' ?>

    <div class="my-container table-container my-1">
      <p id="count-phong" class="text-lg font-medium p-1">Đang hiển thị: 0 phòng</p>
      <table id="table-phong">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên phòng</th>
            <th>Mã phòng</th>
            <th>Dãy nhà</th>
            <th>Tầng</th>
            <th>Phân công nhân viên</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <script>
    const nhanvienList = <?= json_encode($nhanvienOptions, JSON_UNESCAPED_UNICODE) ?>;

    async function fetchData() {
      const params = new URLSearchParams({
        action: "getData",
        day_nha: document.getElementById("filter-day-nha").value,
        tang: document.getElementById("filter-tang").value,
        ma_phong: document.getElementById("filter-ma-phong").value,
        ten_phong: document.getElementById("filter-ten-phong").value,
      });

      const res = await fetch("phong.php?" + params.toString());
      const data = await res.json();
      renderTable(data);
    }

    function renderTable(data) {
      document.getElementById("count-phong").textContent = `Đang hiển thị: ${data.length} phòng`;
      const tbody = document.querySelector("#table-phong tbody");
      tbody.innerHTML = "";
      data.forEach(row => {
        const tr = document.createElement("tr");

        // tạo select nhân viên
        let selectHTML = `<select onchange="assignNhanvien(${row.id}, this.value)">`;
        selectHTML += `<option value="">-- Chọn nhân viên --</option>`;
        nhanvienList.forEach(nv => {
          const selected = (nv.id == row.nhanvien_id) ? "selected" : "";
          selectHTML += `<option value="${nv.id}" ${selected}>${nv.ho_ten}</option>`;
        });
        selectHTML += `</select>`;

        tr.innerHTML = `
          <td>${row.id}</td>
          <td>${row.ten_phong ?? ""}</td>
          <td>${row.ma_phong ?? ""}</td>
          <td>${row.day_nha ?? ""}</td>
          <td>${row.tang ?? ""}</td>
          <td>${selectHTML}</td>
          <td>
            <a href="thietbi.php?${new URLSearchParams({
              ma_phong: row.ma_phong,
              ten_phong: row.ten_phong,
              day_nha: row.day_nha,
              tang: row.tang
            }).toString()}">
            Xem thiết bị
            </a>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    async function assignNhanvien(phong_id, nhanvien_id) {
      const formData = new FormData();
      formData.append("action", "assignNhanvien");
      formData.append("phong_id", phong_id);
      formData.append("nhanvien_id", nhanvien_id);

      const res = await fetch("phong.php", {
        method: "POST",
        body: formData
      });
      const result = await res.json();
      if (result.success) {
        alert("Cập nhật nhân viên thành công!");
      } else {
        alert("Có lỗi xảy ra khi cập nhật.");
      }
    }

    // Gọi khi load + khi thay đổi bộ lọc
    document.querySelectorAll("select, input").forEach(el => {
      el.addEventListener("change", fetchData);
      el.addEventListener("keyup", fetchData);
    });

    fetchData(); // load ban đầu
  </script>
  <script src="assets/js/main.js"></script>
</body>

</html>