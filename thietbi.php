<?php
require_once "assets/php/config.php";
session_start();
// Bật debug khi cần
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['quyen']) || !isset($_SESSION['ho_ten'])) {
    // Chưa đăng nhập
    header("Location: login.php");
    exit;
}

// Nếu là quyền 2 (giáo viên), chỉ cho xem thiết bị của họ
if ($_SESSION['quyen'] == 2) {

    // Nếu không có param hoặc param khác tên giáo viên => redirect đúng tên
    if (!isset($_GET['nhan_vien']) || $_GET['nhan_vien'] !== $_SESSION['ho_ten']) {
        header("Location: thietbi.php?nhan_vien=" . urlencode($_SESSION['ho_ten']));
        exit;
    }
}


// ======================= API GET DATA =======================
if (isset($_GET['action']) && $_GET['action'] === "getData") {
    $conditions = [];

    // Lọc theo tình trạng
    if (!empty($_GET['tinh_trang'])) {
        $conditions[] = "tb.tinh_trang = '" . $conn->real_escape_string($_GET['tinh_trang']) . "'";
    }
    // Lọc theo dãy nhà
    if (!empty($_GET['day_nha'])) {
        $conditions[] = "p.day_nha = '" . $conn->real_escape_string($_GET['day_nha']) . "'";
    }
    // Lọc theo tầng
    if (!empty($_GET['tang'])) {
        $conditions[] = "p.tang = '" . $conn->real_escape_string($_GET['tang']) . "'";
    }
    // Lọc theo mã phòng
    if (!empty($_GET['ma_phong'])) {
        $conditions[] = "p.ma_phong LIKE '%" . $conn->real_escape_string($_GET['ma_phong']) . "%'";
    }
    // Lọc theo tên phòng
    if (!empty($_GET['ten_phong'])) {
        $conditions[] = "p.ten_phong LIKE '%" . $conn->real_escape_string($_GET['ten_phong']) . "%'";
    }
    // Lọc theo tên thiết bị
    if (!empty($_GET['ten_thiet_bi'])) {
        $conditions[] = "tb.ten_thiet_bi LIKE '%" . $conn->real_escape_string($_GET['ten_thiet_bi']) . "%'";
    }
    // Lọc theo hãng sản xuất
    if (!empty($_GET['hang_san_xuat'])) {
        $conditions[] = "tb.hang_san_xuat LIKE '%" . $conn->real_escape_string($_GET['hang_san_xuat']) . "%'";
    }
    // Lọc theo nhân viên
    if (!empty($_GET['nhan_vien'])) {
        $conditions[] = "nv.ho_ten LIKE '%" . $conn->real_escape_string($_GET['nhan_vien']) . "%'";
    }

    $where = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    $sql = "
        SELECT 
            tb.id, tb.ma_thiet_bi, tb.ten_thiet_bi, tb.loai_thiet_bi, 
            tb.hang_san_xuat, tb.ngay_mua, tb.tinh_trang, tb.so_luong, tb.don_vi_tinh,
            p.ma_phong, p.ten_phong, p.day_nha, p.tang,
            nv.ho_ten AS nhan_vien
        FROM thietbi tb
        LEFT JOIN phong p ON tb.phong_id = p.id
        LEFT JOIN nhanvien nv ON tb.nhanvien_id = nv.id
        $where
        ORDER BY tb.ma_thiet_bi ASC
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

// ======================= LẤY DỮ LIỆU CHO BỘ LỌC =======================
$tinhtrangOptions = [];
$res = $conn->query("SELECT DISTINCT tinh_trang FROM thietbi");
while ($row = $res->fetch_assoc()) {
    $tinhtrangOptions[] = $row['tinh_trang'];
}

$dayOptions = [];
$res = $conn->query("SELECT DISTINCT day_nha FROM phong");
while ($row = $res->fetch_assoc()) {
    $dayOptions[] = $row['day_nha'];
}

$tangOptions = [];
$res = $conn->query("SELECT DISTINCT tang FROM phong ORDER BY tang ASC");
while ($row = $res->fetch_assoc()) {
    $tangOptions[] = $row['tang'];
}

// Lấy filter từ URL nếu có
$maPhongURL  = $_GET['ma_phong']   ?? '';
$tenPhongURL = $_GET['ten_phong']  ?? '';
$dayNhaURL   = $_GET['day_nha']    ?? '';
$tangURL     = $_GET['tang']       ?? '';
$nhanVienURL = $_GET['nhan_vien']  ?? '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Danh sách thiết bị</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>

<body>
    <header id="menu"></header>

    <div class="p-1 md:p-2">
        <h1 class="text-3xl font-semibold mb-1">Danh sách các thiết bị
            <?php if ($_SESSION['quyen'] == 2) {
                echo " do giáo viên: " . $_SESSION['ho_ten'] . " quản lý";
            } ?>
        </h1>

        <?php include 'assets/php/filter_thietbi.php' ?>

        <div class="my-1 flex gap-0.5">
            <button id="btn-rutgon" class="secondary">Rút gọn</button>
            <button id="btn-full" class="secondary">Đầy đủ</button>
            <button onclick="exportTableToExcel('table-thietbi')" class="secondary">Xuất Excel</button>
        </div>

        <div class="my-container table-container my-1">
            <p id="count-thietbi" class="text-lg font-medium p-1">Đang hiển thị: 0 thiết bị</p>
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
        let originalData = []; // dữ liệu gốc
        let isRutGon = false; // trạng thái hiển thị

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
            originalData = await res.json();

            renderTable();
        }

        // Hàm gom nhóm theo mã + tình trạng
        function groupData(data) {
            const grouped = {};
            data.forEach(row => {
                const key = row.ten_thiet_bi + "___" + row.tinh_trang;
                if (!grouped[key]) {
                    grouped[key] = {
                        ...row
                    };
                } else {
                    grouped[key].so_luong =
                        parseInt(grouped[key].so_luong) + parseInt(row.so_luong);
                }
            });
            return Object.values(grouped);
        }

        function renderTable() {
            let data = isRutGon ? groupData(originalData) : originalData;

            document.getElementById("count-thietbi").textContent =
                `Đang hiển thị: ${data.length} thiết bị`;

            const tbody = document.querySelector("#table-thietbi tbody");
            tbody.innerHTML = "";

            data.forEach((row, index) => {
                const tr = document.createElement("tr");

                // Xác định màu dựa trên trạng thái
                let statusColor = "";
                if (row.tinh_trang === "Đang sử dụng") statusColor = "text-green-600";
                else if (row.tinh_trang === "Hỏng") statusColor = "text-red-600";
                else if (row.tinh_trang === "Bảo trì") statusColor = "text-yellow-600";

                tr.innerHTML = `
            <td>${index + 1}</td> <!-- Số thứ tự bắt đầu từ 1 -->
            <td>${
                isRutGon 
                    ? row.ma_thiet_bi 
                    : `<a href="edit_thietbi.php?id=${row.id}">${row.ma_thiet_bi}</a>`
            }</td>
            <td>${row.ten_thiet_bi}</td>
            <td>${row.loai_thiet_bi}</td>
            <td>${row.hang_san_xuat}</td>
            <td>${row.ngay_mua ?? ""}</td>
            <td><span class="${statusColor} font-semibold">${row.tinh_trang}</span></td>
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

        // Nút bấm
        document.getElementById("btn-rutgon").addEventListener("click", () => {
            isRutGon = true;
            renderTable();
        });
        document.getElementById("btn-full").addEventListener("click", () => {
            isRutGon = false;
            renderTable();
        });

        // Gọi khi load + khi thay đổi bộ lọc
        document.querySelectorAll("select, input").forEach(el => {
            el.addEventListener("change", fetchData);
            el.addEventListener("keyup", fetchData);
        });

        fetchData(); // load ban đầu
    </script>
    </script>
    <script>
        fetch('menu.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('menu').innerHTML = data;
            })
            .catch(error => {
                console.error('Lỗi khi tải menu:', error);
            });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function exportTableToExcel(tableID) {
            var table = document.getElementById(tableID);
            var html = table.outerHTML.replace(/ /g, '%20');

            // Lấy ngày hiện tại
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
            var yyyy = today.getFullYear();

            var filename = 'thietbi_' + dd + '-' + mm + '-' + yyyy + '.xls'; // Định dạng: data_2025-11-05.xls

            var a = document.createElement('a');
            a.href = 'data:application/vnd.ms-excel,' + html;
            a.download = filename;
            a.click();
        }
    </script>
    <script src="assets/js/main.js"></script>
</body>

</html>