<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>📊 Dashboard รายงานสต็อก</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      text-align: center;
      padding: 20px;
      transition: background-color 0.3s, color 0.3s;
    }

    h2 {
      margin-bottom: 10px;
    }

    .btn {
      display: inline-block;
      margin: 10px;
      padding: 8px 15px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .chart-container {
      width: 100%;
      max-width: 600px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    canvas {
      width: 100% !important;
      height: 300px !important;
    }

    input[type="text"] {
      padding: 8px;
      width: 300px;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .dark-mode {
      background-color: #222;
      color: #fff;
    }

    .dark-mode .chart-container {
      background-color: #333;
    }
  </style>
</head>
<body>
  <h2>📊 Dashboard รายงานสต็อกสินค้า</h2>
  <a href="index.php" class="btn">⬅️ กลับหน้าหลัก</a>
  <button onclick="toggleTheme()" class="btn">🌓 สลับธีม</button>
  <br>
  <input type="text" id="searchInput" placeholder="🔍 ค้นหาสินค้า..." oninput="filterCharts()">

  <?php
  $data = $conn->query("SELECT name, quantity FROM products");
  $names = [];
  $qtys = [];
  while ($row = $data->fetch_assoc()) {
      $names[] = $row['name'];
      $qtys[] = $row['quantity'];
  }
  $total = array_sum($qtys);
  echo "<h3>📦 สินค้าทั้งหมดในคลัง: $total รายการ</h3>";
  ?>

  <div class="chart-container"><canvas id="barChart"></canvas></div>
  <div class="chart-container"><canvas id="pieChart"></canvas></div>
  <div class="chart-container"><canvas id="lineChart"></canvas></div>

  <script>
    const labels = <?php echo json_encode($names); ?>;
    const dataQty = <?php echo json_encode($qtys); ?>;

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
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true } }
        }
      });

      pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            label: 'จำนวนสินค้า',
            data: dataQty,
            backgroundColor: [
              '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });

      lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'แนวโน้มสต็อก',
            data: dataQty,
            borderColor: 'rgba(255, 99, 132, 1)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });
    }

    function filterCharts() {
      const keyword = document.getElementById('searchInput').value.toLowerCase();
      const filteredLabels = [];
      const filteredData = [];

      labels.forEach((label, index) => {
        if (label.toLowerCase().includes(keyword)) {
          filteredLabels.push(label);
          filteredData.push(dataQty[index]);
        }
      });

      barChart.data.labels = filteredLabels;
      barChart.data.datasets[0].data = filteredData;
      barChart.update();

      pieChart.data.labels = filteredLabels;
      pieChart.data.datasets[0].data = filteredData;
      pieChart.update();

      lineChart.data.labels = filteredLabels;
      lineChart.data.datasets[0].data = filteredData;
      lineChart.update();
    }

    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
    }

    createCharts(labels, dataQty);
  </script>
</body>
</html>
