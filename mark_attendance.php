<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Check if user is a teacher
if ($_SESSION["role"] !== "teacher") {
    header("location: dashboard.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$success_msg = $error_msg = "";
$error_details = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $attendance_data = $_POST["attendance"];
    $success = true;
    
    foreach ($attendance_data as $student_id => $status) {
        try {
            // Check if attendance already exists
            $check_sql = "SELECT attendance_id FROM attendance WHERE student_id = ? AND date = ?";
            if ($stmt = mysqli_prepare($conn, $check_sql)) {
                mysqli_stmt_bind_param($stmt, "is", $student_id, $date);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    // Update existing attendance
                    $update_sql = "UPDATE attendance SET status = ?, marked_by = ? WHERE student_id = ? AND date = ?";
                    if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                        mysqli_stmt_bind_param($update_stmt, "siis", $status, $user_id, $student_id, $date);
                        if (!mysqli_stmt_execute($update_stmt)) {
                            $success = false;
                            $error_details[] = "Error updating attendance for student ID: " . $student_id;
                        }
                        mysqli_stmt_close($update_stmt);
                    }
                } else {
                    // Insert new attendance
                    $insert_sql = "INSERT INTO attendance (student_id, date, status, marked_by) VALUES (?, ?, ?, ?)";
                    if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
                        mysqli_stmt_bind_param($insert_stmt, "issi", $student_id, $date, $status, $user_id);
                        if (!mysqli_stmt_execute($insert_stmt)) {
                            $success = false;
                            $error_details[] = "Error inserting attendance for student ID: " . $student_id;
                        }
                        mysqli_stmt_close($insert_stmt);
                    }
                }
                mysqli_stmt_close($stmt);
            }
        } catch (Exception $e) {
            $success = false;
            $error_details[] = "Error processing student ID: " . $student_id . " - " . $e->getMessage();
        }
    }
    
    if ($success) {
        // Log the activity
        logActivity($user_id, 'Mark Attendance', 'Marked attendance for date: ' . $date);
        $success_msg = "Attendance marked successfully!";
    } else {
        $error_msg = "Error marking attendance. Details: " . implode(", ", $error_details);
    }
}

// Get all students
$students = [];
$sql = "SELECT s.*, u.username 
        FROM students s 
        JOIN users u ON s.user_id = u.user_id 
        ORDER BY s.class, s.roll_number";
if ($stmt = mysqli_prepare($conn, $sql)) {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Get today's attendance
$today_attendance = [];
$today = date("Y-m-d");
$sql = "SELECT student_id, status FROM attendance WHERE date = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $today);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $today_attendance[$row["student_id"]] = $row["status"];
        }
    }
    mysqli_stmt_close($stmt);
}

// Group students by class
$class_groups = [
    'Nursery' => [],
    'LKG' => [],
    'UKG' => [],
    'Unknown' => []
];
foreach ($students as $student) {
    $class = in_array($student['class'], ['Nursery', 'LKG', 'UKG']) ? $student['class'] : 'Unknown';
    $class_groups[$class][] = $student;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance - Attendance Management System</title>
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
        .date-banner {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 18px 30px;
            margin-bottom: 30px;
            font-size: 1.25rem;
            font-weight: 500;
            color: #333;
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
            <p style="margin-bottom: 0; font-size: 15px;">Teacher</p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="attendance_history.php"><i class="fas fa-history"></i> Attendance History</a></li>
            <li class="nav-item"><a class="nav-link active" href="mark_attendance.php"><i class="fas fa-calendar-check"></i> Mark Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="date-banner">
            Today's Date: <?php echo date('F d, Y'); ?>
        </div>
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">Student List (Class-wise)</div>
            <div class="card-body">
                <form id="attendanceForm" method="post" action="">
                    <input type="hidden" name="date" value="<?php echo date("Y-m-d"); ?>">
                    <div class="table-responsive">
                        <?php foreach (['Nursery', 'LKG', 'UKG', 'Unknown'] as $class): ?>
                            <?php if (count($class_groups[$class]) > 0): ?>
                                <h5 class="mt-4 mb-2"><?php echo $class; ?> <span class="badge bg-info">Total: <?php echo count($class_groups[$class]); ?></span></h5>
                                <table class="table table-bordered table-hover table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Roll Number</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($class_groups[$class] as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                            <td>
                                                <select name="attendance[<?php echo $student['student_id']; ?>]" class="form-select attendance-select">
                                                    <option value="present" <?php echo (isset($today_attendance[$student['student_id']]) && $today_attendance[$student['student_id']] === 'present') ? 'selected' : ''; ?>>Present</option>
                                                    <option value="absent" <?php echo (isset($today_attendance[$student['student_id']]) && $today_attendance[$student['student_id']] === 'absent') ? 'selected' : ''; ?>>Absent</option>
                                                    <option value="late" <?php echo (isset($today_attendance[$student['student_id']]) && $today_attendance[$student['student_id']] === 'late') ? 'selected' : ''; ?>>Late</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 