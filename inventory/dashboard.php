<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>📊 Dashboard รายงานสต็อก</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      text-align: center;
      padding: 20px;
    }

    h2 {
      margin-bottom: 20px;
    }

    .btn {
      display: inline-block;
      margin-bottom: 30px;
      padding: 8px 15px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .chart-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto 40px auto; /* จัดกึ่งกลาง */
      background: #fff;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    canvas {
      width: 100% !important;
      height: 300px !important;
    }
  </style>
</head>
<body>
  <h2>📊 Dashboard รายงานสต็อกสินค้า</h2>
  <a href="index.php" class="btn">⬅️ กลับหน้าหลัก</a>

  <?php
  $data = $conn->query("SELECT name, quantity FROM products");
  $names = [];
  $qtys = [];
  while ($row = $data->fetch_assoc()) {
      $names[] = $row['name'];
      $qtys[] = $row['quantity'];
  }
  ?>

  <div class="chart-container">
    <canvas id="barChart"></canvas>
  </div>

  <div class="chart-container">
    <canvas id="pieChart"></canvas>
  </div>

  <script>
  const labels = <?php echo json_encode($names); ?>;
  const dataQty = <?php echo json_encode($qtys); ?>;

  new Chart(document.getElementById('barChart'), {
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
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  new Chart(document.getElementById('pieChart'), {
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
  </script>
</body>
</html>
