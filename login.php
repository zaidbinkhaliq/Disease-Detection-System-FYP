<?php
session_start();
include 'db.php'; // Ensure the database connection path is correct
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = ""; // Initialize message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the "Forgot Password" form submission
    if (isset($_POST['email']) && !isset($_POST['password'])) {
        $email = $conn->real_escape_string($_POST['email']);

        // Check if the email exists
        $email_check_sql = "SELECT * FROM users WHERE email = ?";
        $email_stmt = $conn->prepare($email_check_sql);
        $email_stmt->bind_param("s", $email);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();

        if ($email_result->num_rows == 0) {
            // Email does not exist
            $message = "<div class='alert alert-danger'>Invalid email. Please try again.</div>";
        } else {
            // Email exists, send the password to the user's email
            $row = $email_result->fetch_assoc();
            $password = $row['password'];

            // Send email with the existing password
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'malikusamaaliawan@gmail.com';
                $mail->Password   = 'hvdx qouo icwu cmox';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('malikusamaaliawan@gmail.com', 'Support Team');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your Password for [Your Site Name]';
                $mail->Body    = 'Hello,<br><br>Your password is: ' . htmlspecialchars($password) . '<br><br>Please keep it secure and do not share it with anyone.';

                $mail->send();
                $message = "<div class='alert alert-success success-alert'>Your password has been sent to your email.</div>";
                echo "<meta http-equiv='refresh' content='3;url=login.php'>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger'>Failed to send email. Please try again later.</div>";
            }
        }

        $email_stmt->close();
    }

    // Handle the "Login" form submission
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $conn->real_escape_string($_POST['password']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='alert alert-danger'>Invalid email format.</div>";
        } else {
            // Prepare SQL to fetch the user by email
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Fetch user data
                $row = $result->fetch_assoc();
                $stored_password = $row['password'];

                // Check if the email is verified
                if ($row['is_verified'] == 1) {
                    // Verify the password: check against both hashed and plain text
                    if (password_verify($password, $stored_password) || $password === $stored_password) {
                        // Set session variables specific to the user
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['logged_in'] = true;

                        // Check if the user is an admin
                        if ($row['is_admin']) {
                            $_SESSION['is_admin'] = true;
                            header("Location: admin/admin-panel.php");
                        } else {
                            $_SESSION['is_admin'] = false;
                            header("Location: main.php");
                        }
                        exit(); // Ensure no further code is executed
                    } else {
                        $message = "<div class='alert alert-danger'>Invalid password.</div>";
                    }
                } else {
                    $message = "<div class='alert alert-danger'>Your email address is not verified. Please verify your email before logging in.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>No user found with this email.</div>";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        *, *:before, *:after {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background: url(cover.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
            height: 100vh;
            color: #ffffff;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        .form-container {
            height: 520px;
            width: 400px;
            background-color: rgba(255, 255, 255, 0.03);
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            border-radius: 10px;
            backdrop-filter: blur(15px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
            padding: 50px 35px;
        }

        .form-container * {
            font-family: "Poppins", sans-serif;
            color: #ffffff;
            letter-spacing: 0.5px;
            outline: none;
            border: none;
        }

        .form-container h3 {
            font-size: 40px;
            font-weight: 700;
            line-height: 42px;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group label {
            color: #5c5a5a;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            border-radius: 3px;
            border: none;
        }

        .form-control::placeholder {
            color: #eaeaea;
        }

        .form-control:hover {
            outline: 2px solid #0001;
        }

        .btn-transparent {
            width: 100%;
            background-color: #ffffff;
            color: #080710;
            padding: 15px 0;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition-duration: .4s;
            border: none;
        }

        .btn-transparent:hover {
            background-color: #ebebeb;
        }

        .hide {
            display: none;
        }

        .alert, .success-alert {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2000;
            width: 90%;
            max-width: 400px;
            text-align: center;
            color: black;
        }

        .alert a, .success-alert a {
            color: #0056b3;
            text-decoration: underline;
        }

        .alert a:hover, .success-alert a:hover {
            color: #004080;
            text-decoration: none;
        }

        .support-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: transparent;
            color: #ffffff;
            font-size: 18px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .support-btn:hover {
            color: #ddd;
        }
    </style>
    <script>
        function toggleForms() {
            var loginForm = document.getElementById('loginForm');
            var forgetPasswordForm = document.getElementById('forgetPasswordForm');
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                forgetPasswordForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                forgetPasswordForm.style.display = 'block';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const alert = document.querySelector(".alert");
            if (alert) {
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</head>
<body>

<!-- <a href="support.php" class="support-btn">Support</a> -->

<div class="form-container">
    <?php
    if ($message) {
        echo $message;
    }
    ?>
    <div id="loginForm">
        <h3>Login Here</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="loginEmail">Email address</label>
                <input type="email" class="form-control" id="loginEmail" name="email" placeholder="name@example.com" required>
            </div>
            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <br>
            <p class="mt-3"><a href="#" onclick="toggleForms()">Forgot Password?</a></p>
            <a href="register.php" class="text-white">Don't have an account? Register here</a>
        </form>
    </div>
    
    <div id="forgetPasswordForm" class="hide">
        <h3>Forgot Password</h3>
        <form method="post" action="">
            <div class="form-group">
                <label for="forgetEmail">Email address</label>
                <input type="email" class="form-control" id="forgetEmail" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Password</button>
            <p class="mt-3"><a href="#" onclick="toggleForms()">Back to Login</a></p>
        </form>
    </div>
</div>
</body>
</html>
