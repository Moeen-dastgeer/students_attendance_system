<?php
include 'admin_header.php';
include 'db.php';

$course_id = $_GET['course_id'] ?? '';
$shift_id = $_GET['shift_id'] ?? '';

// Course & Shift Dropdown
$courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
$shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
?>

<style>
  * {
    box-sizing: border-box;
  }
  body {
    font-family: Arial, sans-serif;
  }
  .container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
  }
  .id-card {
    width: 4.5cm;
    height: 8cm;
    border: 1px solid #333;
    padding: 3px;
    font-size: 10px;
    background-color: #fdfdfd;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .header {
    background-color: blue;
    color: white;
    padding: 5px;
    text-align: center;
    font-weight: bold;
    font-size: 11px;
  }
  .header div {
    font-size: 9px;
    font-weight: normal;
  }
  .top-section {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
  }
  .photo {
    width: 2cm;
    height: 2cm;
    border: 1px solid #ccc;
    background-color: #eee;
  }
  .photo img, .logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .logo {
    width: 2cm;
    background-color: #ddd;
    margin-bottom: 2px;
  }
  .year {
    text-align: center;
    font-weight: bold;
    font-size: 10px;
  }
  .student-name {
    font-weight: bold;
    font-size: 12px;
    text-align: center;
    margin-top: 5px;
    color: blue;
  }
  .info-table {
    width: 100%;
    margin-top: 5px;
    border-collapse: collapse;
  }
  .info-table th,
  .info-table td {
    padding: 2px 4px;
    text-align: left;
    vertical-align: top;
    font-size: 10px;
  }
  .footer {
    text-align: right;
    font-size: 8px;
    margin-top: 5px;
    margin-bottom: 5px;
  }
  .page-break {
    page-break-after: always;
  }
  @media print {
    body {
      margin: 0;
    }
    .no-print {
      display: none;
    }
  }
</style>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <button onclick="window.print()" class="btn btn-primary no-print">🖨️ Print</button>
  </div>

  <?php
  if ($course_id && $shift_id):
    $students = $conn->query("SELECT s.*, c.course_name, sh.shift_name FROM students s
                              JOIN courses c ON s.course_id = c.id
                              JOIN shifts sh ON s.shift_id = sh.id
                              WHERE s.course_id = $course_id AND s.shift_id = $shift_id");

    if ($students->num_rows === 0):
      echo "<div class='alert alert-info'>ℹ️ No students found for selected course and shift.</div>";
    else:
      $count = 0;
      echo '<div class="container">';
      while ($row = $students->fetch_assoc()):
        if ($count > 0 && $count % 12 == 0):
          echo '</div><div class="page-break"></div><div class="container">';
        endif;
        $count++;
  ?>
    <div class="id-card">
      <div class="header">
        FATIMA INSTITUTE OF<br>
        COMPUTER EDUCATION<br>
        AND RESOURCES
        <div>
          Faisalabad Road Okara<br>
          Ph: 044-2661147
        </div>
      </div>
      <div class="top-section">
        <div class="photo">
          <img src="uploads/<?= $row['image'] ?>" alt="Student Photo">
        </div>
        <div>
          <div class="logo">
            <img src="ficer.jpg" alt="Logo">
          </div>
          <div class="year">2025</div>
        </div>
      </div>
      <div class="student-name"><?= strtoupper($row['name']) ?></div>
      <table class="info-table">
        <tr><th>Class</th><td>: <?= $row['course_name'] ?></td></tr>
        <tr><th>Shift</th><td>: <?= $row['shift_name'] ?></td></tr>
        <tr><th>F/Name</th><td>: <?= $row['guardian_name'] ?></td></tr>
        <tr><th>Address</th><td>: <?= $row['address'] ?></td></tr>
      </table>
      <div class="footer">Principal</div>
    </div>
  <?php endwhile;
      echo '</div>';
    endif;
  endif;
  ?>
</div>

<?php include 'admin_footer.php'; ?>
