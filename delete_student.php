<?php
session_start();
require_once "config/database.php";

// Only allow teachers or admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ["teacher", "admin"])) {
    header("location: index.php");
    exit;
}

// Check if student ID is provided
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($student_id <= 0) {
    header("location: manage_students.php");
    exit;
}

// Fetch student info to verify existence and get user_id
$student = null;
$sql = "SELECT s.*, u.user_id FROM students s JOIN users u ON s.user_id = u.user_id WHERE s.student_id = ?";
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

// Process deletion when confirmed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm_delete"])) {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Delete attendance records first (due to foreign key constraint)
        $sql = "DELETE FROM attendance WHERE student_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Delete student record
        $sql = "DELETE FROM students WHERE student_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Delete user record
        $sql = "DELETE FROM users WHERE user_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $student["user_id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Redirect to manage students page
        header("location: manage_students.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        $error_message = "An error occurred while deleting the student. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Student - Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .wrapper {
            width: 100%;
            padding: 20px;
        }
        .main-content {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }
        .card-body {
            padding: 20px;
        }
        .btn-action {
            margin-right: 5px;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Delete Student</h2>
                <a href="view_student.php?id=<?php echo $student_id; ?>" class="btn btn-primary btn-action">
                    <i class="fas fa-arrow-left me-2"></i>Back to Student Details
                </a>
            </div>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Confirm Deletion</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                        <p>You are about to delete the following student:</p>
                        <ul>
                            <li><strong>Name:</strong> <?php echo htmlspecialchars($student["full_name"]); ?></li>
                            <li><strong>Roll Number:</strong> <?php echo htmlspecialchars($student["roll_number"]); ?></li>
                            <li><strong>Class:</strong> <?php echo htmlspecialchars($student["class"]); ?></li>
                        </ul>
                        <p class="mb-0">This action will:</p>
                        <ul>
                            <li>Delete all attendance records for this student</li>
                            <li>Delete the student's profile</li>
                            <li>Delete the associated user account</li>
                        </ul>
                        <p class="mb-0"><strong>This action cannot be undone!</strong></p>
                    </div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $student_id); ?>" method="post">
                        <div class="form-group">
                            <button type="submit" name="confirm_delete" class="btn btn-danger" onclick="return confirm('Are you absolutely sure you want to delete this student?');">
                                <i class="fas fa-trash me-2"></i>Delete Student
                            </button>
                            <a href="view_student.php?id=<?php echo $student_id; ?>" class="btn btn-secondary">Cancel</a>
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