<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

// ดึงข้อมูลสินค้า
$stmt = $conn->prepare("SELECT name, quantity FROM products");
$stmt->execute();
$result = $stmt->get_result();

$names = [];
$qtys  = [];

while ($row = $result->fetch_assoc()) {
  $names[] = $row['name'];
  $qtys[]  = (int)$row['quantity'];
}

$total = array_sum($qtys);
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>📊 Dashboard รายงานสต็อก</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial;
      background: #f9f9f9;
      text-align: center;
      padding: 20px;
    }

    h2 {
      margin-bottom: 10px;
    }

    .btn {
      display: inline-block;
      margin: 10px;
      padding: 8px 15px;
      background: #4CAF50;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      border: none;
      cursor: pointer;
    }

    .chart-container {
      width: 100%;
      max-width: 700px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, .1);
    }

    canvas {
      height: 320px !important;
    }

    input {
      padding: 8px;
      width: 300px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .dark-mode {
      background: #111;
      color: white;
    }

    .dark-mode .chart-container {
      background: #222;
    }
  </style>
</head>

<body>

  <h2>📊 Dashboard รายงานสต็อกสินค้า</h2>

  <a href="index.php" class="btn">⬅️ กลับหน้าหลัก</a>
  <button onclick="toggleTheme()" class="btn">🌓 สลับธีม</button>
  <br>

  <input type="text" id="searchInput" placeholder="🔍 ค้นหาสินค้า..." oninput="filterCharts()">

  <h3>📦 จำนวนสินค้าทั้งหมด: <?= number_format($total); ?> ชิ้น</h3>

  <div class="chart-container"><canvas id="barChart"></canvas></div>
  <div class="chart-container"><canvas id="pieChart"></canvas></div>
  <div class="chart-container"><canvas id="lineChart"></canvas></div>

  <script>
    const labels = <?= json_encode($names); ?>;
    const dataQty = <?= json_encode($qtys); ?>;

    let barChart, pieChart, lineChart;

    function createCharts(labels, dataQty) {
      const barCtx = document.getElementById('barChart').getContext('2d');
      const pieCtx = document.getElementById('pieChart').getContext('2d');
      const lineCtx = document.getElementById('lineChart').getContext('2d');

      barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'จำนวนสินค้า',
            data: dataQty,
            backgroundColor: '#36a2eb'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            data: dataQty
          }]
        },
        options: {
          responsive: true
        }
      });

      lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'แนวโน้มสต็อก',
            data: dataQty,
            borderColor: '#ff6384',
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true
        }
      });
    }

    function filterCharts() {
      const key = document.getElementById('searchInput').value.toLowerCase();
      const fl = [],
        fd = [];

      labels.forEach((l, i) => {
        if (l.toLowerCase().includes(key)) {
          fl.push(l);
          fd.push(dataQty[i]);
        }
      });

      barChart.data.labels = fl;
      barChart.data.datasets[0].data = fd;
      barChart.update();

      pieChart.data.labels = fl;
      pieChart.data.datasets[0].data = fd;
      pieChart.update();

      lineChart.data.labels = fl;
      lineChart.data.datasets[0].data = fd;
      lineChart.update();
    }

    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
    }

    createCharts(labels, dataQty);
  </script>

</body>

</html>