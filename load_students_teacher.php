<?php
include 'db.php';

$course_id = intval($_GET['course_id'] ?? 0);
$shift_id = intval($_GET['shift_id'] ?? 0);
$date = date('Y-m-d');

if (!$course_id || !$shift_id) {
    echo "<div class='alert alert-warning'>⚠️ Please select course and shift.</div>";
    exit;
}

$students = $conn->query("SELECT * FROM students 
                          WHERE course_id = $course_id 
                            AND shift_id = $shift_id 
                            AND status = 'active'");

if ($students->num_rows === 0) {
    echo "<div class='alert alert-info'>ℹ️ No students found for this course and shift.</div>";
    exit;
}

echo '<table class="table table-bordered align-middle">';
echo '<thead class="table-dark">
        <tr>
          <th>Name</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead><tbody>';

while ($s = $students->fetch_assoc()) {
    $id = $s['id'];
    $name = htmlspecialchars($s['name']);

    // Check today's attendance
    $att = $conn->query("SELECT status FROM attendance WHERE student_id = $id AND date = '$date'");
    $status = $att->num_rows > 0 ? $att->fetch_assoc()['status'] : '';

    $badgeColor = match ($status) {
        'present' => 'success',
        'absent' => 'danger',
        'late'   => 'warning',
        'leave'  => 'info',
        default  => 'secondary'
    };

    $statusText = $status ? ucfirst($status) : '-';
    $statusBadge = "<span class='badge bg-$badgeColor text-capitalize' id='status_$id'>$statusText</span>";

    echo "<tr id='row-$id'>
            <td>$name</td>
            <td class='status-badge'>$statusBadge</td>
            <td>
              <div class='btn-group' role='group'>
                <button class='btn btn-success btn-sm' onclick='markAttendance($id, \"present\")'>✅ Present</button>
                <button class='btn btn-danger btn-sm mx-2' onclick='markAttendance($id, \"absent\")'>❌ Absent</button>
                <button class='btn btn-warning btn-sm' onclick='markAttendance($id, \"late\")'>⏰ Late</button>
                <button class='btn btn-info btn-sm ms-2' onclick='markAttendance($id, \"leave\")'>✈️ Leave</button>
              </div>
            </td>
          </tr>";
}

echo '</tbody></table>';
?>
