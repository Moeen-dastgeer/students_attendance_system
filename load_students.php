<?php
include 'db.php';

$course_id = $_GET['course_id'] ?? '';
$shift_id = $_GET['shift_id'] ?? '';
$date = date('Y-m-d');

if (!$course_id || !$shift_id) {
    echo "<div class='alert alert-warning'>⚠️ Please select course and shift.</div>";
    exit;
}

$students = $conn->query("SELECT * FROM students WHERE course_id = $course_id AND shift_id = $shift_id");

if ($students->num_rows == 0) {
    echo "<div class='alert alert-info'>ℹ️ No students found for selected course and shift.</div>";
    exit;
}

echo '<table class="table table-bordered table-hover">';
echo '<thead class="table-light">
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Roll</th>
          <th>Status</th>
          <th>Mark</th>
        </tr>
      </thead><tbody>';

while ($s = $students->fetch_assoc()) {
    $id = $s['id'];
    $img = $s['image'] ? "<img src='uploads/{$s['image']}' width='50' class='img-thumbnail'>" : '-';

    // Get today's status
    $att = $conn->query("SELECT status FROM attendance WHERE student_id = $id AND date = '$date'");
    $status = $att->num_rows > 0 ? $att->fetch_assoc()['status'] : '';
    
    $badgeClass = match ($status) {
        'present' => 'bg-success',
        'absent' => 'bg-danger',
        'late'   => 'bg-warning text-dark',
        'leave'  => 'bg-info',
        default  => 'bg-secondary'
    };
    $statusText = $status ? ucfirst($status) : '-';

    echo "<tr id='row-$id'>
            <td>$img</td>
            <td>{$s['name']}</td>
            <td>{$s['roll']}</td>
            <td><span id='status_{$id}' class='badge $badgeClass'>$statusText</span></td>
            <td>
              <button class='btn btn-success btn-sm' onclick=\"setAttendance($id, 'present')\">✅ Present</button>
              <button class='btn btn-danger btn-sm mx-2' onclick=\"setAttendance($id, 'absent')\">❌ Absent</button>
              <button class='btn btn-warning btn-sm' onclick=\"setAttendance($id, 'late')\">⏰ Late</button>
              <button class='btn btn-info btn-sm ms-2' onclick=\"setAttendance($id, 'leave')\">✈️ Leave</button>
            </td>
          </tr>";
}

echo '</tbody></table>';
?>
