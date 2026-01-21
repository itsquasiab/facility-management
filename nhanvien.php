<?php
require_once "assets/php/config.php";
session_start();
if (!isset($_SESSION['quyen']) || !isset($_SESSION['ho_ten'])) {
    // Chưa đăng nhập
    header("Location: login.php");
    exit;
}
include 'baomat.php';
// ========== AJAX update gán nhân viên quản lý phòng ==========
if (isset($_POST['action']) && $_POST['action'] === "assignRoom") {
  $idNhanVien = intval($_POST['id_nhanvien']);
  $idPhong    = intval($_POST['id_phong']);

  // gán nhân viên cho tất cả thiết bị trong phòng
  $sql = "UPDATE thietbi SET nhanvien_id = $idNhanVien WHERE phong_id = $idPhong";
  $ok = $conn->query($sql);

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(["success" => $ok]);
  exit;
}

// ========== AJAX lấy danh sách ==========
if (isset($_GET['action']) && $_GET['action'] === "getData") {
  // Nhân viên đã phân công (quản lý ít nhất 1 thiết bị)
  // Nhân viên đã phân công (quản lý ít nhất 1 thiết bị) + số phòng
  $sqlAssigned = "
    SELECT nv.id, nv.ho_ten, nv.chuc_vu, nv.so_dien_thoai, nv.email,
          COUNT(DISTINCT tb.phong_id) AS so_phong
    FROM nhanvien nv
    JOIN thietbi tb ON nv.id = tb.nhanvien_id
    GROUP BY nv.id, nv.ho_ten, nv.chuc_vu, nv.so_dien_thoai, nv.email
  ";
  $assigned = $conn->query($sqlAssigned)->fetch_all(MYSQLI_ASSOC);

  // Nhân viên chưa phân công
  $sqlUnassigned = "
    SELECT nv.id, nv.ho_ten, nv.chuc_vu, nv.so_dien_thoai, nv.email
    FROM nhanvien nv
    WHERE nv.id NOT IN (SELECT DISTINCT nhanvien_id FROM thietbi WHERE nhanvien_id IS NOT NULL)
  ";
  $unassigned = $conn->query($sqlUnassigned)->fetch_all(MYSQLI_ASSOC);

  // Danh sách phòng có thiết bị

  $sqlPhong = "
  SELECT DISTINCT 
    p.id AS phong_id, 
    p.ma_phong, 
    p.ten_phong, 
    p.day_nha, 
    p.tang,
    nv.ho_ten AS nguoi_quan_ly
  FROM phong p
  JOIN thietbi tb ON p.id = tb.phong_id
  LEFT JOIN nhanvien nv ON tb.nhanvien_id = nv.id
  ORDER BY p.ma_phong
";
  $rooms = $conn->query($sqlPhong)->fetch_all(MYSQLI_ASSOC);


  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    "assigned" => $assigned,
    "unassigned" => $unassigned,
    "rooms" => $rooms
  ]);
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Danh sách Nhân viên</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/tailwind.css">
</head>

<body>
  <header id="menu"></header>

  <div class="p-1 md:p-2">
    <h1 class="text-3xl font-semibold mb-1">Quản lý nhân viên</h1>

    <section class="my-container table-container my-1">
      <h2 class="text-2xl font-medium p-1">Nhân viên đã phân công</h2>
      <table id="table-assigned">
        <thead>
          <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Chức vụ</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Số phòng quản lý</th>
            <th>Xem thiết bị quản lý</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </section>

    <section class="my-container table-container my-1">
      <h2 class="text-2xl font-medium p-1">Nhân viên chưa phân công</h2>
      <table id="table-unassigned">
        <thead>
          <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Chức vụ</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Phân công quản lý phòng</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </section>
  </div>

  <script>
    let allRooms = [];

    async function fetchData() {
      const res = await fetch("nhanvien.php?action=getData");
      const data = await res.json();
      allRooms = data.rooms;
      renderAssigned(data.assigned);
      renderUnassigned(data.unassigned);
    }

    function renderAssigned(data) {
      const tbody = document.querySelector("#table-assigned tbody");
      tbody.innerHTML = "";
      data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
      <td>${row.id}</td>
      <td>${row.ho_ten}</td>
      <td>${row.chuc_vu ?? ""}</td>
      <td>${row.so_dien_thoai ?? ""}</td>
      <td>${row.email ?? ""}</td>
      <td>${row.so_phong}</td>
      <td><a href="thietbi.php?${new URLSearchParams({nhan_vien: row.ho_ten}).toString()}"> Xem thiết bị đang quản lý </a></td>
    `;
        tbody.appendChild(tr);
      });
    }

    function renderUnassigned(data) {
      const tbody = document.querySelector("#table-unassigned tbody");
      tbody.innerHTML = "";
      data.forEach(row => {
        const tr = document.createElement("tr");
        const selectOptions = allRooms.map(r => {
          const quanLy = r.nguoi_quan_ly ? ` - QL: ${r.nguoi_quan_ly}` : " - Chưa có QL";
          return `<option value="${r.phong_id}">
            ${r.ma_phong} - ${r.ten_phong} (${r.day_nha}, Tầng ${r.tang})${quanLy}
          </option>`;
        }).join("");

        tr.innerHTML = `
          <td>${row.id}</td>
          <td>${row.ho_ten}</td>
          <td>${row.chuc_vu ?? ""}</td>
          <td>${row.so_dien_thoai ?? ""}</td>
          <td>${row.email ?? ""}</td>
          <td>
            <select onchange="assignRoom(${row.id}, this.value)">
              <option value="">-- Chọn phòng --</option>
              // trong ô lựa chọn này có tên phòng dãy nhà, tầng nhưng thiếu tên người đang quản lý
              ${selectOptions}
            </select>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    async function assignRoom(idNhanVien, idPhong) {
      if (!idPhong) return;
      const res = await fetch("nhanvien.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
          action: "assignRoom",
          id_nhanvien: idNhanVien,
          id_phong: idPhong
        })
      });
      const result = await res.json();
      if (result.success) {
        alert("Phân công phòng thành công!");
        fetchData();
      } else {
        alert("Có lỗi khi phân công!");
      }
    }

    fetchData();
  </script>
  <script src="assets/js/main.js"></script>
</body>

</html>