<?php
session_start();
require_once "config/database.php";

// Only allow teachers or admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ["teacher", "admin"])) {
    header("location: index.php");
    exit;
}

// Get student ID from query
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($student_id <= 0) {
    header("location: manage_students.php");
    exit;
}

// Fetch student info
$student = null;
$sql = "SELECT s.*, u.username FROM students s JOIN users u ON s.user_id = u.user_id WHERE s.student_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $student = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}
if (!$student) {
    header("location: manage_students.php");
    exit;
}

// Fetch attendance records for this student
$attendance_records = [];
$sql = "SELECT a.date, a.status, a.remarks, u.username as marked_by FROM attendance a JOIN users u ON a.marked_by = u.user_id WHERE a.student_id = ? ORDER BY a.date DESC";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $attendance_records[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student - Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; }
        .main-wrapper { display: flex; min-height: 100vh; }
        .sidebar { background: linear-gradient(180deg, #6a82fb 0%, #fc5c7d 100%); color: white; width: 250px; min-height: 100vh; padding: 30px 20px 20px 20px; display: flex; flex-direction: column; align-items: center; }
        .user-profile { text-align: center; margin-bottom: 30px; }
        .user-profile img { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px; }
        .nav { width: 100%; }
        .nav-link { color: rgba(255,255,255,0.9); padding: 12px 15px; margin: 5px 0; border-radius: 5px; font-size: 16px; }
        .nav-link.active, .nav-link:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .main-content { flex: 1; padding: 40px 40px 40px 40px; background: #f8f9fa; min-width: 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 30px; }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; padding: 18px 24px; font-size: 1.1rem; font-weight: 600; }
        .card-body { padding: 24px; }
        .table-responsive { margin-top: 10px; }
        .table th, .table td { vertical-align: middle; text-align: center; }
        .btn-primary { background-color: #6a82fb; border: none; padding: 10px 24px; font-size: 1rem; border-radius: 6px; }
        .btn-primary:hover { background-color: #5a6eea; }
        .btn-action { margin-right: 5px; }
        @media (max-width: 900px) { .main-wrapper { flex-direction: column; } .sidebar { width: 100%; min-height: auto; flex-direction: row; justify-content: space-between; padding: 15px 10px; } .main-content { padding: 20px 5vw; } }
    </style>
</head>
<body>
<div class="main-wrapper">
    <div class="sidebar">
        <div class="user-profile">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION["username"] ?? ''); ?>&background=random" alt="Profile">
            <h5 style="margin-bottom: 0;"> <?php echo htmlspecialchars($_SESSION["username"] ?? ''); ?> </h5>
            <p style="margin-bottom: 0; font-size: 15px;"> <?php echo ucfirst($_SESSION["role"] ?? ''); ?> </p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="attendance_history.php"><i class="fas fa-history"></i> Attendance History</a></li>
            <li class="nav-item"><a class="nav-link" href="mark_attendance.php"><i class="fas fa-calendar-check"></i> Mark Attendance</a></li>
            <li class="nav-item"><a class="nav-link active" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Student Details</h2>
            <a href="manage_students.php" class="btn btn-primary btn-action"><i class="fas fa-arrow-left me-2"></i>Back to List</a>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <p class="mb-2"><i class="fas fa-user me-2"></i><strong>Name:</strong> <?php echo htmlspecialchars($student["full_name"]); ?></p>
                        <p class="mb-2"><i class="fas fa-id-card me-2"></i><strong>Roll Number:</strong> <?php echo htmlspecialchars($student["roll_number"]); ?></p>
                        <p class="mb-2"><i class="fas fa-graduation-cap me-2"></i><strong>Class:</strong> <?php echo htmlspecialchars($student["class"]); ?></p>
                        <?php if (!empty($student["section"])): ?><p class="mb-2"><i class="fas fa-layer-group me-2"></i><strong>Section:</strong> <?php echo htmlspecialchars($student["section"]); ?></p><?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i><strong>Email:</strong> <?php echo htmlspecialchars($student["email"]); ?></p>
                        <p class="mb-2"><i class="fas fa-user-tag me-2"></i><strong>Username:</strong> <?php echo htmlspecialchars($student["username"]); ?></p>
                        <?php if (!empty($student["phone"])): ?><p class="mb-2"><i class="fas fa-phone me-2"></i><strong>Phone:</strong> <?php echo htmlspecialchars($student["phone"]); ?></p><?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <?php if (!empty($student["address"])): ?><p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i><strong>Address:</strong> <?php echo htmlspecialchars($student["address"]); ?></p><?php endif; ?>
                        <p class="mb-2"><i class="fas fa-calendar-alt me-2"></i><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($student["created_at"])); ?></p>
                        <p class="mb-2"><i class="fas fa-user-clock me-2"></i><strong>Last Updated:</strong> <?php echo date('F j, Y', strtotime($student["updated_at"])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Attendance Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Marked By</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendance_records)): ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No attendance records found.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($attendance_records as $row): ?>
                            <tr>
                                <td><?php echo date('F j, Y', strtotime($row['date'])); ?></td>
                                <td><span class="attendance-status status-<?php echo htmlspecialchars($row['status']); ?>"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></span></td>
                                <td><?php echo htmlspecialchars($row['marked_by']); ?></td>
                                <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 