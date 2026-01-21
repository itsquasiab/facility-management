<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dán Excel có Alt+Enter</title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/tailwind.css">
  <style>
    #excelInput {
      width: 100%;
      height: 150px;
      font-family: monospace;
      border: 2px solid #ccc;
      padding: 8px;
      box-sizing: border-box;
      white-space: pre;
      outline: none;
      resize: vertical;
    }

    #excelTable {
      border-collapse: collapse;
      margin-top: 20px;
    }

    #excelTable td {
      border: 1px solid #999;
      padding: 6px 10px;
      min-width: 80px;
      vertical-align: top;
      white-space: pre-wrap;
    }
  </style>
</head>

<body>

  <textarea id="excelInput" placeholder="Dán dữ liệu từ Excel vào đây"></textarea>
  <div><b>Kết quả dạng bảng:</b></div>
  <table id="excelTable" border="1" cellpadding="8" cellspacing="0"></table>
  <br>
  <button id="saveBtn">💾 Lưu vào CSDL</button>
  <a href="index.html" class="button secondary">Về trang chủ</a>
  <div id='kq'></div>
  <script>
    let parsedData = [];

    document.getElementById('excelInput').addEventListener('paste', (e) => {
      setTimeout(() => {
        const text = document.getElementById('excelInput').value;
        let temp = text.replace(/\r\n/g, '\n');
        temp = temp.replace(/"([^"]*)"/g, (match, p1) => {
          return '"' + p1.replace(/\n/g, '⏎') + '"';
        });

        const lines = temp.split('\n');
        parsedData = [];

        const table = document.getElementById('excelTable');
        table.innerHTML = '';

        lines.forEach(line => {
          if (line.trim() === '') return;
          const row = document.createElement('tr');
          const cells = line.split('\t');
          const rowData = [];

          cells.forEach(cell => {
            cell = cell.replace(/⏎/g, '<br>');
            if (cell.startsWith('"') && cell.endsWith('"')) {
              cell = cell.substring(1, cell.length - 1);
            }
            rowData.push(cell.replace(/<br>/g, '\n'));

            const td = document.createElement('td');
            td.innerHTML = cell;
            row.appendChild(td);
          });

          parsedData.push(rowData);
          table.appendChild(row);
        });
      }, 100);
    });

    document.getElementById('saveBtn').addEventListener('click', () => {
      if (parsedData.length < 2) {
        alert("Không có đủ dữ liệu để lưu (cần ít nhất 1 dòng tiêu đề và 1 dòng dữ liệu).");
        return;
      }

      const headers = parsedData[0];
      const rows = parsedData.slice(1);

      fetch('assets/php/insert_excel.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            headers,
            rows
          })
        })
        .then(res => res.text())
        .then(response => {
          //alert(response);
          const text = document.getElementById('kq').innerHTML = response;
        })
        .catch(error => {
          console.error('Lỗi:', error);
          alert('Đã xảy ra lỗi khi gửi dữ liệu.');
        });
    });
  </script>
</body>

</html>