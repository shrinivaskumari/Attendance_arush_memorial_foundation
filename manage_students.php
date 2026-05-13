<?php
session_start();
require_once "config/database.php";

// Only allow teachers or admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ["teacher", "admin"])) {
    header("location: index.php");
    exit;
}

// Fetch all students
$students = [];
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
    <title>Manage Students - Attendance Management System</title>
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
        .btn-action {
            margin-right: 5px;
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
            <h2>Manage Students</h2>
            <a href="add_student.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Add Student</a>
        </div>
        <div class="card">
            <div class="card-header">Student List (Class-wise)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php foreach (['Nursery', 'LKG', 'UKG', 'Unknown'] as $class): ?>
                        <?php if (count($class_groups[$class]) > 0): ?>
                            <h5 class="mt-4 mb-2"><?php echo $class; ?> <span class="badge bg-info">Total: <?php echo count($class_groups[$class]); ?></span></h5>
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Roll Number</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($class_groups[$class] as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                                        <td>
                                            <a href="view_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-info btn-sm btn-action"><i class="fas fa-eye"></i></a>
                                            <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-warning btn-sm btn-action"><i class="fas fa-edit"></i></a>
                                            <a href="delete_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Are you sure you want to delete this student?');"><i class="fas fa-trash"></i></a>
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
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 