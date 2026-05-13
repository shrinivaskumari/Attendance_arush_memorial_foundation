<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

// Get student information if the user is a student
$student_info = null;
if ($role === "student") {
    $sql = "SELECT * FROM students WHERE user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $student_info = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch attendance records
$attendance_records = [];
$class_groups = [
    'Nursery' => [],
    'LKG' => [],
    'UKG' => [],
    'Unknown' => []
];
if ($role === "teacher") {
    $sql = "SELECT a.date, a.status, s.full_name, s.roll_number, s.class, u.username as marked_by 
            FROM attendance a 
            JOIN students s ON a.student_id = s.student_id 
            JOIN users u ON a.marked_by = u.user_id 
            ORDER BY a.date DESC";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $attendance_records[] = $row;
    }
} else if ($role === "student" && $student_info) {
    $sql = "SELECT a.date, a.status, u.username as marked_by 
            FROM attendance a 
            JOIN users u ON a.marked_by = u.user_id 
            WHERE a.student_id = ? 
            ORDER BY a.date DESC";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $student_info["student_id"]);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $attendance_records[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Group attendance records by class for teachers
if ($role === "teacher") {
    foreach ($attendance_records as $row) {
        $class = in_array($row['class'], ['Nursery', 'LKG', 'UKG']) ? $row['class'] : 'Unknown';
        $class_groups[$class][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance History - Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(180deg, #6a82fb 0%, #fc5c7d 100%);
            color: white;
            width: 250px;
            min-height: 100vh;
            padding: 30px 20px 20px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .user-profile {
            text-align: center;
            margin-bottom: 30px;
        }
        .user-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .nav {
            width: 100%;
        }
        .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 12px 15px;
            margin: 5px 0;
            border-radius: 5px;
            font-size: 16px;
        }
        .nav-link.active, .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .main-content {
            flex: 1;
            padding: 40px 40px 40px 40px;
            background: #f8f9fa;
            min-width: 0;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            margin-bottom: 30px;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 18px 24px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .card-body {
            padding: 24px;
        }
        .table-responsive {
            margin-top: 10px;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-primary {
            background-color: #6a82fb;
            border: none;
            padding: 10px 24px;
            font-size: 1rem;
            border-radius: 6px;
        }
        .btn-primary:hover {
            background-color: #5a6eea;
        }
        @media (max-width: 900px) {
            .main-wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                min-height: auto;
                flex-direction: row;
                justify-content: space-between;
                padding: 15px 10px;
            }
            .main-content {
                padding: 20px 5vw;
            }
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <div class="sidebar">
        <div class="user-profile">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION["username"]); ?>&background=random" alt="Profile">
            <h5 style="margin-bottom: 0;"> <?php echo htmlspecialchars($_SESSION["username"]); ?> </h5>
            <p style="margin-bottom: 0; font-size: 15px;"> <?php echo ucfirst($role); ?> </p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="attendance_history.php"><i class="fas fa-history"></i> Attendance History</a></li>
            <?php if ($role === "teacher"): ?>
            <li class="nav-item"><a class="nav-link" href="mark_attendance.php"><i class="fas fa-calendar-check"></i> Mark Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Attendance History</h2>
            <a href="dashboard.php" class="btn btn-primary btn-action">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <?php if ($role === "student" && $student_info): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-2"><i class="fas fa-user me-2"></i><strong>Name:</strong> <?php echo htmlspecialchars($student_info["full_name"]); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-2"><i class="fas fa-id-card me-2"></i><strong>Roll Number:</strong> <?php echo htmlspecialchars($student_info["roll_number"]); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-2"><i class="fas fa-graduation-cap me-2"></i><strong>Class:</strong> <?php echo htmlspecialchars($student_info["class"]); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i><strong>Email:</strong> <?php echo htmlspecialchars($student_info["email"]); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Attendance Records</h5>
                <?php if ($role === "teacher"): ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary btn-action" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-2"></i>Export to Excel
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-action" onclick="printTable()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="attendanceTable">
                        <thead>
                            <tr>
                                <?php if ($role === "teacher"): ?>
                                <th>Student Name</th>
                                <th>Roll Number</th>
                                <th>Class</th>
                                <?php endif; ?>
                                <th>Date</th>
                                <th>Status</th>
                                <?php if ($role === "teacher"): ?>
                                <th>Marked By</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($attendance_records)): ?>
                            <tr>
                                <td colspan="<?php echo $role === 'teacher' ? 6 : 2; ?>" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No attendance records found.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($attendance_records as $row): ?>
                            <tr>
                                <?php if ($role === "teacher"): ?>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['class']); ?></td>
                                <?php endif; ?>
                                <td><?php echo date('F j, Y', strtotime($row['date'])); ?></td>
                                <td>
                                    <span class="attendance-status status-<?php echo htmlspecialchars($row['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                    </span>
                                </td>
                                <?php if ($role === "teacher"): ?>
                                <td><?php echo htmlspecialchars($row['marked_by']); ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($role === "teacher"): ?>
        <div class="card">
            <div class="card-header">Attendance History (Class-wise)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php foreach (['Nursery', 'LKG', 'UKG', 'Unknown'] as $class): ?>
                        <?php if (count($class_groups[$class]) > 0): ?>
                            <h5 class="mt-4 mb-2"><?php echo $class; ?></h5>
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Roll Number</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Marked By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($class_groups[$class] as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                                        <td><?php echo date('F j, Y', strtotime($row['date'])); ?></td>
                                        <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['marked_by']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 