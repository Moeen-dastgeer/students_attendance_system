<?php
include 'db.php';
$course_id = $_POST['course_id'] ?? 0;
$shift_id = $_POST['shift_id'] ?? 0;

$students = $conn->query("SELECT s.*, c.course_name, sh.shift_name 
    FROM students s 
    JOIN courses c ON s.course_id = c.id 
    JOIN shifts sh ON s.shift_id = sh.id 
    WHERE s.course_id = $course_id AND s.shift_id = $shift_id");
?>
<!DOCTYPE html>
<html>
<head>
  <title>ID Cards</title>
  <style>
    body { margin: 0; padding: 0; font-family: sans-serif; }
    .page { display: flex; flex-wrap: wrap; page-break-after: always; }
    .card { width: 300px; height: 180px; border: 1px solid #000; margin: 10px; padding: 8px; position: relative; box-sizing: border-box; }
    .back { background-color: #f9f9f9; }
    .front img { float: right; width: 50px; height: 50px; object-fit: cover; border: 1px solid #000; }
    .line { margin: 2px 0; font-size: 13px; }
  </style>
</head>
<body onload="window.print()">
  <div class="page">
    <?php while($s = $students->fetch_assoc()): ?>
      <div class="card front">
        <img src="uploads/<?= $s['image'] ?>" alt="Student">
        <div class="line"><strong>Institute:</strong> <?= $_SESSION['campus_name'] ?? 'Campus' ?></div>
        <div class="line"><strong>Name:</strong> <?= $s['name'] ?></div>
        <div class="line"><strong>Father Name:</strong> <?= $s['guardian_name'] ?></div>
        <div class="line"><strong>Course:</strong> <?= $s['course_name'] ?></div>
        <div class="line"><strong>Address:</strong> <?= $s['address'] ?></div>
        <div class="line"><strong>Phone:</strong> <?= $s['student_phone'] ?></div>
      </div>
      <div class="card back">
        <div class="line"><strong>Fatima Institute Of Computer Education</strong></div>
        <div class="line">Faisalabad Road Okara</div>
        <div class="line">Phone: 044-2661147</div>
        <div class="line" style="margin-top:20px;">If found, please return to the institute.</div>
        <div class="line" style="margin-top:30px;">Signature: __________________</div>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>
