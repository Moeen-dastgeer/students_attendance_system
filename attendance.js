function setAttendance(studentId, status) {
  fetch('set_attendance.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'student_id=' + studentId + '&status=' + status
  })
  .then(response => response.text())
  .then(data => {
    const badge = document.getElementById('status_' + studentId);
    if (badge) {
      // Reset badge classes
      badge.className = 'badge text-capitalize';

      const colors = {
        present: 'bg-success',
        absent: 'bg-danger',
        late: 'bg-warning',
        leave: 'bg-info'
      };

      const colorClass = colors[status] || 'bg-secondary';
      badge.classList.add(colorClass);
      if (status === 'late') badge.classList.add('text-dark');

      badge.innerText = status;
    }

    const resultBox = document.getElementById("result");
    if (resultBox) {
      resultBox.innerHTML = `<div class="alert alert-info mt-2 mb-0 py-1 px-2">✅ ${data}</div>`;
    }
  });
}

function loadStudentsByCourseShift() {
  const courseId = document.getElementById('course_filter').value;
  const shiftId = document.getElementById('shift_filter').value;

  if (!courseId || !shiftId) {
    document.getElementById('student_table').innerHTML = '';
    return;
  }

  fetch(`load_students.php?course_id=${courseId}&shift_id=${shiftId}`)
    .then(response => response.text())
    .then(data => {
      document.getElementById('student_table').innerHTML = data;
    });
}
