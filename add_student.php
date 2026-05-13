<?php
session_start();
require_once "config/database.php";

// Only allow teachers or admins
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !in_array($_SESSION["role"], ["teacher", "admin"])) {
    header("location: index.php");
    exit;
}

$full_name = $email = $class = $section = $roll_number = $phone = $address = $username = "";
$full_name_err = $email_err = $class_err = $roll_number_err = $username_err = "";
$success_msg = $error_msg = "";

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
            $sql = "SELECT user_id FROM users WHERE email = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $email_err = "This email is already registered.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $username_err = "This username is already taken.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    // Validate class
    if (empty($_POST["class"]) || !in_array($_POST["class"], ["Nursery", "LKG", "UKG"])) {
        $class_err = "Please select a valid class (Nursery, LKG, UKG).";
    } else {
        $class = $_POST["class"];
    }
    // Validate roll number
    if (empty(trim($_POST["roll_number"]))) {
        $roll_number_err = "Please enter roll number.";
    } else {
        $roll_number = trim($_POST["roll_number"]);
        $sql = "SELECT student_id FROM students WHERE roll_number = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $roll_number);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $roll_number_err = "This roll number is already registered.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    // Other fields
    $section = trim($_POST["section"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    // If no errors, insert into users and students
    if (empty($full_name_err) && empty($email_err) && empty($class_err) && empty($roll_number_err) && empty($username_err)) {
        $default_password = password_hash("student123", PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, role, email, full_name) VALUES (?, ?, 'student', ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $username, $default_password, $email, $full_name);
            if (mysqli_stmt_execute($stmt)) {
                $user_id = mysqli_insert_id($conn);
                $sql2 = "INSERT INTO students (user_id, full_name, email, roll_number, class, section, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                if ($stmt2 = mysqli_prepare($conn, $sql2)) {
                    mysqli_stmt_bind_param($stmt2, "isssssss", $user_id, $full_name, $email, $roll_number, $class, $section, $phone, $address);
                    if (mysqli_stmt_execute($stmt2)) {
                        header("location: manage_students.php");
                        exit;
                    } else {
                        $error_msg = "Error adding student (student table).";
                    }
                    mysqli_stmt_close($stmt2);
                }
            } else {
                $error_msg = "Error adding student (user table).";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student - Attendance Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .main-content { max-width: 700px; margin: 40px auto; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; padding: 18px 24px; font-size: 1.1rem; font-weight: 600; }
        .card-body { padding: 24px; }
        .form-group { margin-bottom: 1rem; }
        .invalid-feedback { display: block; }
    </style>
</head>
<body>
<div class="main-content">
    <div class="card">
        <div class="card-header">Add Student</div>
        <div class="card-body">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($full_name); ?>">
                            <span class="invalid-feedback"><?php echo $full_name_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Roll Number</label>
                            <input type="text" name="roll_number" class="form-control <?php echo (!empty($roll_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($roll_number); ?>">
                            <span class="invalid-feedback"><?php echo $roll_number_err; ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Section</label>
                            <input type="text" name="section" class="form-control" value="<?php echo htmlspecialchars($section); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Add Student</button>
                    <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
                </div>
                <div class="form-text text-muted">Default password for new students is <b>student123</b></div>
            </form>
        </div>
    </div>
</div>
</body>
</html> 