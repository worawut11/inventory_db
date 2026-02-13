<?php
// usecase_diagram.php
// วางไฟล์นี้ใน htdocs หรือ public_html แล้วเปิดด้วยเบราว์เซอร์
// ตัวอย่าง: http://localhost/usecase_diagram.php
?>
<!doctype html>
<html lang="th">

<head>
  <meta charset="utf-8">
  <title>Use Case Diagram - ระบบหอพัก (PHP + SVG)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body {
      font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      margin: 18px;
      background: #f7f9fb;
      color: #111;
    }

    h1 {
      font-size: 20px;
      margin-bottom: 6px;
    }

    p.lead {
      margin-top: 0;
      color: #555;
    }

    .container {
      display: flex;
      gap: 18px;
      flex-wrap: wrap;
    }

    .diagram {
      background: white;
      border-radius: 10px;
      padding: 12px;
      box-shadow: 0 4px 14px rgba(20, 40, 60, 0.06);
    }

    .info {
      flex: 1 1 340px;
      min-width: 300px;
    }

    .svg-wrap {
      flex: 1 1 600px;
      min-width: 320px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 10px;
    }

    table th,
    table td {
      border: 1px solid #e6e9ee;
      padding: 8px;
      text-align: left;
      font-size: 14px;
    }

    .note {
      font-size: 13px;
      color: #444;
      margin-top: 8px;
    }

    .badge {
      background: #eef5ff;
      color: #0366d6;
      padding: 4px 8px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 13px;
    }

    .legend {
      display: flex;
      gap: 10px;
      margin-top: 8px;
      align-items: center;
      flex-wrap: wrap;
    }

    .legend .item {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .actor-icon {
      width: 22px;
      height: 22px;
      display: inline-block;
    }

    code {
      background: #f2f6fb;
      padding: 3px 6px;
      border-radius: 6px;
      font-size: 13px;
    }
  </style>
</head>

<body>
  <h1>Use Case Diagram — ระบบบริหารจัดการหอพัก</h1>
  <p class="lead">ตัวอย่างหน้าเว็บที่แสดง Use Case Diagram แบบง่าย ๆ (วาดด้วย SVG) พร้อมคำอธิบาย Actor และ Use Cases — สามารถแก้ข้อความและตำแหน่งได้โดยตรงในไฟล์ PHP นี้</p>

  <div class="container">
    <div class="svg-wrap diagram">
      <!-- SVG diagram -->
      <svg viewBox="0 0 1000 520" width="100%" height="520" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Use case diagram">
        <!-- system boundary -->
        <rect x="180" y="30" width="780" height="460" rx="14" fill="#ffffff" stroke="#dfe7f2" stroke-width="2"></rect>
        <text x="190" y="54" font-size="14" fill="#2b3b52" font-weight="700">System: Dormitory Management System</text>

        <!-- Actors (left) -->
        <!-- Tenant actor (stick figure) -->
        <g id="actor-tenant" transform="translate(30,90)">
          <circle cx="20" cy="20" r="12" fill="#fff" stroke="#333" stroke-width="2"></circle>
          <line x1="20" y1="32" x2="20" y2="72" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="44" x2="4" y2="58" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="44" x2="36" y2="58" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="72" x2="6" y2="100" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="72" x2="34" y2="100" stroke="#333" stroke-width="2"></line>
          <text x="-6" y="122" font-size="13" fill="#111">Tenant</text>
        </g>

        <!-- Admin actor (right) -->
        <g id="actor-admin" transform="translate(30,320)">
          <circle cx="20" cy="20" r="12" fill="#fff" stroke="#333" stroke-width="2"></circle>
          <line x1="20" y1="32" x2="20" y2="72" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="44" x2="4" y2="58" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="44" x2="36" y2="58" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="72" x2="6" y2="100" stroke="#333" stroke-width="2"></line>
          <line x1="20" y1="72" x2="34" y2="100" stroke="#333" stroke-width="2"></line>
          <text x="-8" y="122" font-size="13" fill="#111">Admin</text>
        </g>

        <!-- Use cases (ovals inside boundary) -->
        <!-- Row 1 -->
        <ellipse cx="430" cy="110" rx="110" ry="28" fill="#f5fbff" stroke="#bcd7ff"></ellipse>
        <text x="430" y="115" font-size="14" text-anchor="middle" fill="#0b3d91">View Available Rooms</text>

        <ellipse cx="660" cy="110" rx="110" ry="28" fill="#f5fbff" stroke="#bcd7ff"></ellipse>
        <text x="660" y="115" font-size="14" text-anchor="middle" fill="#0b3d91">Request Room</text>

        <!-- Row 2 -->
        <ellipse cx="430" cy="200" rx="110" ry="28" fill="#f5fbff" stroke="#bcd7ff"></ellipse>
        <text x="430" y="205" font-size="14" text-anchor="middle" fill="#0b3d91">View Bill</text>

        <ellipse cx="660" cy="200" rx="110" ry="28" fill="#f5fbff" stroke="#bcd7ff"></ellipse>
        <text x="660" y="205" font-size="14" text-anchor="middle" fill="#0b3d91">Submit Payment</text>

        <!-- Row 3 -->
        <ellipse cx="430" cy="290" rx="110" ry="28" fill="#f5fbff" stroke="#bcd7ff"></ellipse>
        <text x="430" y="295" font-size="14" text-anchor="middle" fill="#0b3d91">Report Problem</text>

        <ellipse cx="660" cy="290" rx="110" ry="28" fill="#f5fbff" stroke="#bcd7ff"></ellipse>
        <text x="660" y="295" font-size="14" text-anchor="middle" fill="#0b3d91">View Payment History</text>

        <!-- Admin-only use cases (left inside) -->
        <ellipse cx="360" cy="380" rx="120" ry="28" fill="#fff8f0" stroke="#ffd7b5"></ellipse>
        <text x="360" y="385" font-size="14" text-anchor="middle" fill="#8a4b00">Manage Rooms</text>

        <ellipse cx="640" cy="380" rx="120" ry="28" fill="#fff8f0" stroke="#ffd7b5"></ellipse>
        <text x="640" y="385" font-size="14" text-anchor="middle" fill="#8a4b00">Generate Bills / Verify Payment</text>

        <!-- Connections: Actors -> Use Cases -->
        <!-- Tenant connections -->
        <line x1="70" y1="120" x2="320" y2="110" stroke="#707070" stroke-width="1.5"></line> <!-- to View Available Rooms -->
        <line x1="70" y1="160" x2="520" y2="110" stroke="#707070" stroke-width="1.5"></line> <!-- to Request Room -->
        <line x1="70" y1="200" x2="320" y2="200" stroke="#707070" stroke-width="1.5"></line> <!-- to View Bill -->
        <line x1="70" y1="240" x2="520" y2="200" stroke="#707070" stroke-width="1.5"></line> <!-- to Submit Payment -->
        <line x1="70" y1="280" x2="320" y2="290" stroke="#707070" stroke-width="1.5"></line> <!-- to Report Problem -->
        <line x1="70" y1="320" x2="520" y2="290" stroke="#707070" stroke-width="1.5"></line> <!-- to View Payment History -->

        <!-- Admin connections -->
        <line x1="70" y1="360" x2="240" y2="380" stroke="#707070" stroke-width="1.5"></line> <!-- to Manage Rooms -->
        <line x1="70" y1="400" x2="520" y2="380" stroke="#707070" stroke-width="1.5"></line> <!-- to Generate Bills -->

        <!-- small labels near lines (optional) -->
        <text x="230" y="90" font-size="12" fill="#333" opacity="0.0"></text>

        <!-- visual helpers: small dots where lines meet ovals (not necessary) -->
      </svg>
      <div style="font-size:13px; margin-top:8px; color:#333;">
        <span class="badge">Tip</span>
        <span style="margin-left:8px;">ลาก/เปลี่ยนตำแหน่งใน SVG ได้โดยแก้ค่า <code>cx</code>/<code>cy</code> ของ <code>&lt;ellipse&gt;</code> หรือตำแหน่ง <code>transform</code> ของ Actor</span>
      </div>
    </div>

    <div class="info diagram">
      <h3>สรุป Actors & Use Cases</h3>
      <table>
        <thead>
          <tr>
            <th>Actor</th>
            <th>Use Cases (หลัก)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Tenant</strong><br><small>ผู้เช่า</small></td>
            <td>
              View Available Rooms, Request Room, View Bill, Submit Payment, Report Problem, View Payment History
            </td>
          </tr>

          <tr>
            <td><strong>Admin</strong><br><small>ผู้ดูแล / เจ้าของ</small></td>
            <td>
              Manage Rooms, Generate Bills / Verify Payment, Manage Tenants, View Financial Reports
            </td>
          </tr>
        </tbody>
      </table>

      <div class="legend">
        <div class="item"><svg class="actor-icon" viewBox="0 0 40 40">
            <circle cx="12" cy="12" r="6" stroke="#333" fill="#fff" stroke-width="1.6" />
            <line x1="12" y1="18" x2="12" y2="30" stroke="#333" stroke-width="1.6" />
          </svg>
          <div>Actor</div>
        </div>
        <div class="item">
          <div style="width:18px;height:12px;border-radius:3px;background:#f5fbff;border:1px solid #bcd7ff"></div>
          <div>Use Case (Tenant)</div>
        </div>
        <div class="item">
          <div style="width:18px;height:12px;border-radius:3px;background:#fff8f0;border:1px solid #ffd7b5"></div>
          <div>Use Case (Admin)</div>
        </div>
      </div>

      <p class="note">
        คำอธิบาย: โค้ดนี้ใช้ SVG เพื่อวาดรูปวงรี (use case) กับ actor แบบ stick-figure และเส้นเชื่อม คุณสามารถเพิ่ม use case ใหม่ได้โดยเพิ่ม &lt;ellipse&gt; และข้อความ &lt;text&gt; และเชื่อมกับ actor โดยเพิ่ม &lt;line&gt; ที่เหมาะสม
      </p>

      <h4>วิธีแก้ไขอย่างรวดเร็ว</h4>
      <ol>
        <li>เปลี่ยนชื่อ Use Case: หา &lt;text&gt; ที่อยู่ใกล้ &lt;ellipse&gt; แล้วแก้ข้อความ</li>
        <li>ย้ายตำแหน่ง: แก้ค่า <code>cx</code> และ <code>cy</code> ของ &lt;ellipse&gt;</li>
        <li>เพิ่ม Actor ใหม่: คัดลอกกลุ่ม &lt;g id="actor-tenant"&gt; แล้วแก้ตำแหน่งและ label</li>
      </ol>
    </div>
  </div>

  <hr style="margin-top:18px;">
  <p style="font-size:13px; color:#555">อยากให้ผมแปลงเป็นภาพ PNG/ SVG เดียวให้ดาวน์โหลดได้เลยไหมครับ หรือจะให้ผมปรับให้เป็น Diagram แบบแยกกลุ่ม (เช่น Authentication, Billing, Maintenance) เพื่อใช้ในรายงาน/Visio?</p>
</body>

</html>