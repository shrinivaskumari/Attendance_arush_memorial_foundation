<?php
function class_display(
    $class
) {
    return in_array($class, ["Nursery", "LKG", "UKG"]) ? $class : "Unknown";
}

session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "config/database.php";

// Get user information
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
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

// Get attendance records for students
$attendance_records = [];
if ($role === "student" && $student_info) {
    $sql = "SELECT a.*, u.username as marked_by FROM attendance a JOIN users u ON a.marked_by = u.user_id WHERE a.student_id = ? ORDER BY a.date DESC LIMIT 10";
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

// Get all students for teachers
$students = [];
if ($role === "teacher") {
    $sql = "SELECT s.*, u.username FROM students s JOIN users u ON s.user_id = u.user_id ORDER BY s.class, s.roll_number";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $students[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Group students by class
$grouped_students = [
    "Nursery" => [],
    "LKG" => [],
    "UKG" => [],
    "Unknown" => []
];
foreach ($students as $student) {
    $class = class_display($student['class']);
    $grouped_students[$class][] = $student;
}

// Handle attendance marking
if ($_SERVER["REQUEST_METHOD"] == "POST" && $role === "teacher") {
    $date = $_POST["date"];
    $attendance_data = $_POST["attendance"];
    
    foreach ($attendance_data as $student_id => $status) {
        // Check if attendance already exists
        $check_sql = "SELECT attendance_id FROM attendance WHERE student_id = ? AND date = ?";
        if ($check_stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "is", $student_id, $date);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                // Update existing attendance
                $update_sql = "UPDATE attendance SET status = ?, marked_by = ? WHERE student_id = ? AND date = ?";
                if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                    mysqli_stmt_bind_param($update_stmt, "siis", $status, $user_id, $student_id, $date);
                    mysqli_stmt_execute($update_stmt);
                    mysqli_stmt_close($update_stmt);
                }
            } else {
                // Insert new attendance
                $insert_sql = "INSERT INTO attendance (student_id, date, status, marked_by) VALUES (?, ?, ?, ?)";
                if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
                    mysqli_stmt_bind_param($insert_stmt, "issi", $student_id, $date, $status, $user_id);
                    mysqli_stmt_execute($insert_stmt);
                    mysqli_stmt_close($insert_stmt);
                }
            }
            mysqli_stmt_close($check_stmt);
        }
    }
    
    // Log the activity
    logActivity($user_id, 'Mark Attendance', 'Marked attendance for date: ' . $date);
    
    // Redirect to prevent form resubmission
    header("location: dashboard.php");
    exit;
}

// For student info display
$class_display = isset($student_info["class"]) && in_array($student_info["class"], ["Nursery", "LKG", "UKG"]) ? $student_info["class"] : "Unknown";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Attendance Management System</title>
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
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=random" alt="Profile">
            <h5 style="margin-bottom: 0;"> <?php echo htmlspecialchars($username); ?> </h5>
            <p style="margin-bottom: 0; font-size: 15px;"> <?php echo ucfirst($role); ?> </p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="attendance_history.php"><i class="fas fa-history"></i> Attendance History</a></li>
            <?php if ($role === "teacher"): ?>
            <li class="nav-item"><a class="nav-link" href="mark_attendance.php"><i class="fas fa-calendar-check"></i> Mark Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="welcome-section">
            <h2>Welcome back, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Here's what's happening with your attendance today.</p>
        </div>

        <?php if ($role === "student" && $student_info): ?>
        <!-- Student Dashboard -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-calendar-check"></i>
                    <div class="stats-number">
                        <?php
                        $present_count = 0;
                        foreach ($attendance_records as $record) {
                            if ($record['status'] === 'present') {
                                $present_count++;
                            }
                        }
                        echo $present_count;
                        ?>
                    </div>
                    <div class="stats-label">Present Days</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-calendar-times"></i>
                    <div class="stats-number">
                        <?php
                        $absent_count = 0;
                        foreach ($attendance_records as $record) {
                            if ($record['status'] === 'absent') {
                                $absent_count++;
                            }
                        }
                        echo $absent_count;
                        ?>
                    </div>
                    <div class="stats-label">Absent Days</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-clock"></i>
                    <div class="stats-number">
                        <?php
                        $late_count = 0;
                        foreach ($attendance_records as $record) {
                            if ($record['status'] === 'late') {
                                $late_count++;
                            }
                        }
                        echo $late_count;
                        ?>
                    </div>
                    <div class="stats-label">Late Days</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Student Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-user me-2"></i><strong>Name:</strong> <?php echo htmlspecialchars($student_info["full_name"]); ?></p>
                        <p class="mb-2"><i class="fas fa-id-card me-2"></i><strong>Roll Number:</strong> <?php echo htmlspecialchars($student_info["roll_number"]); ?></p>
                        <p class="mb-2"><i class="fas fa-graduation-cap me-2"></i><strong>Class:</strong> <?php echo htmlspecialchars($class_display); ?></p>
                        <p class="mb-0"><i class="fas fa-envelope me-2"></i><strong>Email:</strong> <?php echo htmlspecialchars($student_info["email"]); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Attendance</h5>
                        <a href="attendance_history.php" class="btn btn-primary btn-action">
                            <i class="fas fa-history me-1"></i> View Full History
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Marked By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($attendance_records)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No attendance records found.</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($attendance_records as $record): ?>
                                    <tr>
                                        <td><?php echo date('F j, Y', strtotime($record['date'])); ?></td>
                                        <td>
                                            <?php
                                            $status_class = '';
                                            switch ($record['status']) {
                                                case 'present':
                                                    $status_class = 'status-present';
                                                    break;
                                                case 'absent':
                                                    $status_class = 'status-absent';
                                                    break;
                                                case 'late':
                                                    $status_class = 'status-late';
                                                    break;
                                            }
                                            ?>
                                            <span class="attendance-status <?php echo $status_class; ?>">
                                                <?php echo ucfirst($record['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($record['marked_by']); ?></td>
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

        <?php elseif ($role === "teacher"): ?>
        <!-- Teacher Dashboard -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-users"></i>
                    <div class="stats-number"><?php echo count($students); ?></div>
                    <div class="stats-label">Total Students</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-calendar-check"></i>
                    <div class="stats-number">
                        <?php
                        $today = date('Y-m-d');
                        $sql = "SELECT COUNT(*) as count FROM attendance WHERE date = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "s", $today);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['count'];
                        ?>
                    </div>
                    <div class="stats-label">Today's Attendance</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-chart-line"></i>
                    <div class="stats-number">
                        <?php
                        $sql = "SELECT COUNT(DISTINCT date) as count FROM attendance";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['count'];
                        ?>
                    </div>
                    <div class="stats-label">Total Days</div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Student List (Class-wise)</h5>
                        <a href="mark_attendance.php" class="btn btn-primary btn-action">
                            <i class="fas fa-calendar-check me-1"></i> Mark Attendance
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php foreach (["Nursery", "LKG", "UKG", "Unknown"] as $class): ?>
                                <?php if (count($grouped_students[$class]) > 0): ?>
                                    <h5 class="mt-4 mb-2"><?php echo $class; ?></h5>
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Roll Number</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($grouped_students[$class] as $student): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td>
                                                    <a href="view_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-info btn-sm btn-action">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-warning btn-sm btn-action">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
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