<?php
session_start();
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location:index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Attendance Report</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h2>Student-wise Attendance</h2>

<form method="GET">
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php
        $students = $conn->query("SELECT * FROM students");
        while($s = $students->fetch_assoc()) {
            $selected = ($_GET['student_id'] ?? '') == $s['id'] ? 'selected' : '';
            echo "<option value='{$s['id']}' $selected>{$s['name']} - {$s['roll']}</option>";
        }
        ?>
    </select>
    <label>From:</label>
    <input type="date" name="start_date" required value="<?php echo $_GET['start_date'] ?? ''; ?>">
    <label>To:</label>
    <input type="date" name="end_date" required value="<?php echo $_GET['end_date'] ?? ''; ?>">
    <button type="submit">View Report</button>
</form>

<?php
if (isset($_GET['student_id'], $_GET['start_date'], $_GET['end_date'])) {
    $sid = $_GET['student_id'];
    $start = $_GET['start_date'];
    $end = $_GET['end_date'];

    $student = $conn->query("SELECT * FROM students WHERE id = $sid")->fetch_assoc();
    
    echo "<h3>Student: {$student['name']} ({$student['roll']})</h3>";
    echo "<a href='student_profile.php?id=$sid' target='_blank'>👤 View Full Profile</a><br><br>";

    // Attendance query
    $sql = "SELECT date, status FROM attendance WHERE student_id = $sid AND date BETWEEN '$start' AND '$end' ORDER BY date";
    $result = $conn->query($sql);

    // Summary counts
    $summary = ['present' => 0, 'absent' => 0, 'late' => 0];

    echo "<table><tr><th>Date</th><th>Status</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['date']}</td><td>{$row['status']}</td></tr>";
        if (isset($summary[$row['status']])) {
            $summary[$row['status']]++;
        }
    }
    echo "</table>";

    // Summary
    echo "<h3>Summary ($start to $end)</h3>";
    echo "<ul>
        <li>✅ Present: {$summary['present']}</li>
        <li>❌ Absent: {$summary['absent']}</li>
        <li>🕒 Late: {$summary['late']}</li>
    </ul>";

    // Export links
    echo "<div style='margin: 10px 0;'>
        <a href='export_single_student_pdf.php?id=$sid&start_date=$start&end_date=$end' target='_blank'>🧾 Export PDF</a> |
        <a href='export_single_student_excel.php?id=$sid&start_date=$start&end_date=$end'>📊 Export Excel</a>
    </div>";

    // Chart
    echo "<canvas id='attendanceChart' style='max-width:600px; margin-top:30px;'></canvas>";
    echo "
    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    label: 'Attendance Count',
                    data: [{$summary['present']}, {$summary['absent']}, {$summary['late']}],
                    backgroundColor: ['green', 'red', 'orange']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Attendance Summary Chart'
                    },
                    legend: { display: false }
                }
            }
        });
    </script>";
}
?>
</body>
</html>
