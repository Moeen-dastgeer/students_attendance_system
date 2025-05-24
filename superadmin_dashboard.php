<?php
include 'superadmin_header.php';
include 'db.php';

// Counts
$total_campuses = $conn->query("SELECT COUNT(*) as total FROM campuses")->fetch_assoc()['total'];
$total_students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$total_teachers = $conn->query("SELECT COUNT(*) as total FROM teachers")->fetch_assoc()['total'];
?>
<?php include 'superadmin_sidebar.php';?>
<div class="container-fluid py-4">
  <div class="row">
    <!-- Campuses -->
    <div class="col-md-4 col-sm-6 mb-4">
      <div class="small-box bg-primary text-white">
        <div class="inner">
          <h3><?= $total_campuses ?></h3>
          <p>Total Campuses</p>
        </div>
        <div class="icon"><i class="fas fa-school"></i></div>
      </div>
    </div>

    <!-- Students -->
    <div class="col-md-4 col-sm-6 mb-4">
      <div class="small-box bg-success text-white">
        <div class="inner">
          <h3><?= $total_students ?></h3>
          <p>Total Students</p>
        </div>
        <div class="icon"><i class="fas fa-user-graduate"></i></div>
      </div>
    </div>

    <!-- Teachers -->
    <div class="col-md-4 col-sm-6 mb-4">
      <div class="small-box bg-warning text-dark">
        <div class="inner">
          <h3><?= $total_teachers ?></h3>
          <p>Total Teachers</p>
        </div>
        <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
      </div>
    </div>
  </div>
</div>

<?php include 'superadmin_footer.php'; ?>
