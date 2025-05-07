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
<head><title>View Attendance</title></head>
<body>
<h2>Attendance by Date</h2>
<form method="GET">
    <input type="date" name="date" required>
    <button type="submit">View</button>
</form>
<?php
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $sql = "SELECT s.name, s.roll, a.status FROM attendance a JOIN students s ON a.student_id = s.id WHERE a.date = '$date'";
    $result = $conn->query($sql);
    echo "<table border='1'><tr><th>Name</th><th>Roll</th><th>Status</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['name']}</td><td>{$row['roll']}</td><td>{$row['status']}</td></tr>";
    }
    echo "</table>";
}
?>
</body>
</html>