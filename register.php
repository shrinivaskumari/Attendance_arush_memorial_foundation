<?php
session_start();
require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $role = trim($_POST["role"]);
    $roll_number = trim($_POST["roll_number"]);
    $class = trim($_POST["class"]);
    
    $username_err = $password_err = $confirm_password_err = $full_name_err = $email_err = $role_err = $roll_number_err = $class_err = "";
    
    // Validate username
    if (empty($username)) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = $username;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate email
    if (empty($email)) {
        $email_err = "Please enter an email.";
    } else {
        $email = $email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        } else {
            // Check if email already exists
            $sql = "SELECT user_id FROM users WHERE email = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_email);
                $param_email = $email;
                
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
    
    // Validate full name
    if (empty($full_name)) {
        $full_name_err = "Please enter your full name.";
    } else {
        $full_name = $full_name;
    }
    
    // Validate role
    if (empty($role)) {
        $role_err = "Please select a role.";
    } elseif (!in_array($role, ['student', 'teacher'])) {
        $role_err = "Invalid role selected.";
    }
    
    // Validate password
    if (empty($password)) {
        $password_err = "Please enter a password.";     
    } elseif (strlen($password) < 6) {
        $password_err = "Password must have at least 6 characters.";
    }
    
    // Validate confirm password
    if (empty($confirm_password)) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Only validate roll number and class for students
    if ($role === 'student') {
        if (empty($roll_number)) {
            $roll_number_err = "Please enter your roll number.";
        }
        if (empty($class) || !in_array($class, ['Nursery', 'LKG', 'UKG'])) {
            $class_err = "Please select a valid class (Nursery, LKG, UKG).";
        }
    }
    
    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && 
        empty($full_name_err) && empty($email_err) && empty($role_err) && 
        ($role === 'teacher' || (empty($roll_number_err) && empty($class_err)))) {
        
        // Insert into users table
        $sql = "INSERT INTO users (username, password, role, email, full_name) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_role, $param_email, $param_full_name);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_role = $role;
            $param_email = $email;
            $param_full_name = $full_name;
            
            if (mysqli_stmt_execute($stmt)) {
                $user_id = mysqli_insert_id($conn);
                
                // Only insert into students table if role is student
                if ($role === 'student') {
                    $sql = "INSERT INTO students (user_id, full_name, email, roll_number, class) VALUES (?, ?, ?, ?, ?)";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        $roll_number = 'STU' . str_pad($user_id, 5, '0', STR_PAD_LEFT);
                        mysqli_stmt_bind_param($stmt, "issss", $user_id, $full_name, $email, $roll_number, $class);
                        if (mysqli_stmt_execute($stmt)) {
                            header("location: index.php");
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }
                    }
                } else {
                    // If teacher, just redirect to login
                    header("location: index.php");
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .register-container {
            max-width: 500px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .register-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .btn-register {
            background: #4e73df;
            border: none;
            width: 100%;
            padding: 12px;
        }
        .btn-register:hover {
            background: #2e59d9;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center">
        <div class="register-container">
            <h2 class="register-title">Registration</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($username) ? $username : ''; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($full_name) ? $full_name : ''; ?>">
                        <span class="invalid-feedback"><?php echo $full_name_err; ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($email) ? $email : ''; ?>">
                        <span class="invalid-feedback"><?php echo $email_err; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>" id="roleSelect">
                            <option value="">Select Role</option>
                            <option value="student" <?php echo (isset($role) && $role === 'student') ? 'selected' : ''; ?>>Student</option>
                            <option value="teacher" <?php echo (isset($role) && $role === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                        </select>
                        <span class="invalid-feedback"><?php echo $role_err; ?></span>
                    </div>
                </div>
                <div id="studentFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_number" class="form-control <?php echo (!empty($roll_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($roll_number) ? $roll_number : ''; ?>">
                            <span class="invalid-feedback"><?php echo $roll_number_err; ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class</label>
                            <select name="class" class="form-control <?php echo (!empty($class_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Class</option>
                                <option value="Nursery" <?php echo (isset($class) && $class === 'Nursery') ? 'selected' : ''; ?>>Nursery</option>
                                <option value="LKG" <?php echo (isset($class) && $class === 'LKG') ? 'selected' : ''; ?>>LKG</option>
                                <option value="UKG" <?php echo (isset($class) && $class === 'UKG') ? 'selected' : ''; ?>>UKG</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $class_err; ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary btn-register">Register</button>
                </div>
                <div class="login-link">
                    <p>Already have an account? <a href="index.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('roleSelect').addEventListener('change', function() {
            var studentFields = document.getElementById('studentFields');
            if (this.value === 'student') {
                studentFields.style.display = 'block';
            } else {
                studentFields.style.display = 'none';
            }
        });

        // Show student fields if role is already selected as student
        if (document.getElementById('roleSelect').value === 'student') {
            document.getElementById('studentFields').style.display = 'block';
        }
    </script>
</body>
</html> 