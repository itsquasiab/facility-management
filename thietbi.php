<?php
require_once "assets/php/config.php";

// ====== API GET DATA ======
if (isset($_GET['action']) && $_GET['action'] === "getData") {
  $conditions = [];

  if (!empty($_GET['tinh_trang'])) {
    $conditions[] = "tb.tinh_trang = '" . $conn->real_escape_string($_GET['tinh_trang']) . "'";
  }
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
    $conditions[] = "ten_phong LIKE '%" . $conn->real_escape_string($_GET['ten_phong']) . "%'";
  }
  if (!empty($_GET['ten_thiet_bi'])) {
    $conditions[] = "tb.ten_thiet_bi LIKE '%" . $conn->real_escape_string($_GET['ten_thiet_bi']) . "%'";
  }
  if (!empty($_GET['hang_san_xuat'])) {
    $conditions[] = "tb.hang_san_xuat LIKE '%" . $conn->real_escape_string($_GET['hang_san_xuat']) . "%'";
  }
  if (!empty($_GET['nhan_vien'])) {
    $conditions[] = "nv.ho_ten LIKE '%" . $conn->real_escape_string($_GET['nhan_vien']) . "%'";
  }

  $where = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

  $sql = "
        SELECT tb.id, tb.ma_thiet_bi, tb.ten_thiet_bi, tb.loai_thiet_bi, tb.hang_san_xuat,
               tb.ngay_mua, tb.tinh_trang, tb.so_luong, tb.don_vi_tinh,
               p.ma_phong, p.ten_phong, p.day_nha, p.tang, nv.ho_ten AS nhan_vien
        FROM thietbi tb
        LEFT JOIN phong p ON tb.phong_id = p.id
        LEFT JOIN nhanvien nv ON tb.nhanvien_id = nv.id
        $where
        ORDER BY tb.id ASC
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

// ====== LẤY DỮ LIỆU CHO BỘ LỌC ======
$tinhtrangOptions = [];
$res = $conn->query("SELECT DISTINCT tinh_trang FROM thietbi");
while ($row = $res->fetch_assoc()) $tinhtrangOptions[] = $row['tinh_trang'];

$dayOptions = [];
$res = $conn->query("SELECT DISTINCT day_nha FROM phong");
while ($row = $res->fetch_assoc()) $dayOptions[] = $row['day_nha'];

$tangOptions = [];
$res = $conn->query("SELECT DISTINCT tang FROM phong ORDER BY tang ASC");
while ($row = $res->fetch_assoc()) $tangOptions[] = $row['tang'];

// Lấy filter từ URL nếu có
$maPhongURL   = $_GET['ma_phong'] ?? '';
$tenPhongURL  = $_GET['ten_phong'] ?? '';
$dayNhaURL    = $_GET['day_nha'] ?? '';
$tangURL      = $_GET['tang'] ?? '';
$nhanVienURL  = $_GET['nhan_vien'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Danh sách Thiết bị</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/tailwind.css">
</head>

<body>
  
  <?php include 'assets/components/navbar.php'?>

  <div class="p-1">
    <h1 class="text-3xl font-semibold">Danh sách các thiết bị</h1>

    <?php include 'assets/php/filter_thietbi.php'?>

    <div class="my-container table-container">
      <div class="p-1">
      <p id="count-thietbi" class="text-lg font-medium">Đang hiển thị: 0 thiết bị</p>
      </div>
      <table id="table-thietbi">
        <thead>
          <tr>
            <th>ID</th>
            <th>Mã thiết bị</th>
            <th>Tên thiết bị</th>
            <th>Loại</th>
            <th>Hãng SX</th>
            <th>Ngày mua</th>
            <th>Tình trạng</th>
            <th>Số lượng</th>
            <th>Đơn vị tính</th>
            <th>Phòng</th>
            <th>Dãy nhà</th>
            <th>Tầng</th>
            <th>Nhân viên phụ trách</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <script>
    async function fetchData() {
      const params = new URLSearchParams({
        action: "getData",
        tinh_trang: document.getElementById("filter-tinh-trang").value,
        day_nha: document.getElementById("filter-day-nha")?.value || "",
        tang: document.getElementById("filter-tang")?.value || "",
        ma_phong: document.getElementById("filter-ma-phong")?.value || "",
        ten_phong: document.getElementById("filter-ten-phong")?.value || "",
        ten_thiet_bi: document.getElementById("filter-ten-thiet-bi").value,
        hang_san_xuat: document.getElementById("filter-hang-san-xuat").value,
        nhan_vien: document.getElementById("filter-nhan-vien")?.value || "",
      });

      const res = await fetch("thietbi.php?" + params.toString());
      const data = await res.json();
      renderTable(data);
    }

    function renderTable(data) {
      document.getElementById("count-thietbi").textContent = `Đang hiển thị: ${data.length} thiết bị`;
      const tbody = document.querySelector("#table-thietbi tbody");
      tbody.innerHTML = "";
      data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.id}</td>
          <td><a href="edit_thietbi.php?id=${row.id}">${row.ma_thiet_bi}</a></td>
          <td>${row.ten_thiet_bi}</td>
          <td>${row.loai_thiet_bi}</td>
          <td>${row.hang_san_xuat}</td>
          <td>${row.ngay_mua ?? ""}</td>
          <td>${row.tinh_trang}</td>
          <td>${row.so_luong}</td>
          <td>${row.don_vi_tinh}</td>
          <td>${row.ma_phong ?? ""} - ${row.ten_phong ?? ""}</td>
          <td>${row.day_nha ?? ""}</td>
          <td>${row.tang ?? ""}</td>
          <td>${row.nhan_vien ?? ""}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    // Gọi khi load + khi thay đổi bộ lọc
    document.querySelectorAll("select, input").forEach(el => {
      el.addEventListener("change", fetchData);
      el.addEventListener("keyup", fetchData);
    });

    fetchData(); // load ban đầu
  </script>
</body>

</html>