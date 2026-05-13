<?php
session_start();
require_once "config/database.php";

// Only allow teachers or admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ["teacher", "admin"])) {
    header("location: index.php");
    exit;
}

// Initialize variables
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$full_name = $email = $roll_number = $class = $section = $phone = $address = "";
$full_name_err = $email_err = $roll_number_err = $class_err = "";

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

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate full name
    if (empty(trim($_POST["full_name"]))) {
        $full_name_err = "Please enter full name.";
    } else {
        $full_name = trim($_POST["full_name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        } else {
            // Check if email exists (excluding current student)
            $sql = "SELECT student_id FROM students WHERE email = ? AND student_id != ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $email, $student_id);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        $email_err = "This email is already registered.";
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    // Validate roll number
    if (empty(trim($_POST["roll_number"]))) {
        $roll_number_err = "Please enter roll number.";
    } else {
        $roll_number = trim($_POST["roll_number"]);
        // Check if roll number exists (excluding current student)
        $sql = "SELECT student_id FROM students WHERE roll_number = ? AND student_id != ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $roll_number, $student_id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $roll_number_err = "This roll number is already registered.";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate class
    if (empty(trim($_POST["class"]))) {
        $class_err = "Please enter class.";
    } else {
        $class = trim($_POST["class"]);
    }
    
    // Get other fields
    $section = trim($_POST["section"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    
    // Check input errors before updating
    if (empty($full_name_err) && empty($email_err) && empty($roll_number_err) && empty($class_err)) {
        // Update student information
        $sql = "UPDATE students SET full_name = ?, email = ?, roll_number = ?, class = ?, section = ?, phone = ?, address = ? WHERE student_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssssi", $full_name, $email, $roll_number, $class, $section, $phone, $address, $student_id);
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to view student page
                header("location: view_student.php?id=" . $student_id);
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
} else {
    // Initialize form with existing values
    $full_name = $student["full_name"];
    $email = $student["email"];
    $roll_number = $student["roll_number"];
    $class = $student["class"];
    $section = $student["section"] ?? "";
    $phone = $student["phone"] ?? "";
    $address = $student["address"] ?? "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student - Attendance Management System</title>
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
            max-width: 1200px;
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
        .form-group {
            margin-bottom: 1rem;
        }
        .btn-action {
            margin-right: 5px;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edit Student</h2>
                <a href="view_student.php?id=<?php echo $student_id; ?>" class="btn btn-primary btn-action">
                    <i class="fas fa-arrow-left me-2"></i>Back to Student Details
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Student Information</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $student_id); ?>" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $full_name; ?>">
                                    <span class="invalid-feedback"><?php echo $full_name_err; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Roll Number</label>
                                    <input type="text" name="roll_number" class="form-control <?php echo (!empty($roll_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $roll_number; ?>">
                                    <span class="invalid-feedback"><?php echo $roll_number_err; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Class</label>
                                    <select name="class" class="form-control <?php echo (!empty($class_err)) ? 'is-invalid' : ''; ?>">
                                        <option value="">Select Class</option>
                                        <option value="Nursery" <?php echo ($class === 'Nursery') ? 'selected' : ''; ?>>Nursery</option>
                                        <option value="LKG" <?php echo ($class === 'LKG') ? 'selected' : ''; ?>>LKG</option>
                                        <option value="UKG" <?php echo ($class === 'UKG') ? 'selected' : ''; ?>>UKG</option>
                                    </select>
                                    <span class="invalid-feedback"><?php echo $class_err; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Section</label>
                                    <input type="text" name="section" class="form-control" value="<?php echo $section; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo $phone; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo $address; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Student</button>
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