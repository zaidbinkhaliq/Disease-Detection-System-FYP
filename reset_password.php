<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        $token = $_POST['token'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
        } else {
            // Check if the token is valid
            $token_check_sql = "SELECT * FROM password_resets WHERE token = ? AND expires >= NOW()";
            $stmt = $conn->prepare($token_check_sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $message = "<div class='alert alert-danger'>Invalid or expired token.</div>";
            } else {
                $reset = $result->fetch_assoc();
                $user_id = $reset['user_id'];

                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the user's password in the database
                $update_password_sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($update_password_sql);
                $stmt->bind_param("si", $hashed_password, $user_id);

                if ($stmt->execute()) {
                    // Delete the token after successful password reset
                    $delete_token_sql = "DELETE FROM password_resets WHERE token = ?";
                    $stmt = $conn->prepare($delete_token_sql);
                    $stmt->bind_param("s", $token);
                    $stmt->execute();

                    $message = "<div class='alert alert-success'>Your password has been reset successfully. You can now <a href='login.php'>log in</a> with your new password.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Failed to reset password. Please try again later.</div>";
                }
            }
        }
    }
}

$conn->close();
?>



<?php
if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']);
    echo "The token is: " . $token;
} else {
    echo "No token provided.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-container {
            height: auto;
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
            color: #ffffff;
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
        .btn-primary {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            padding: 15px 0;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition-duration: .4s;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <?php if ($message) { echo $message; } ?>
        <form method="post" action="">
            <h3>Reset Your Password</h3>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Enter new password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Confirm new password" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
</body>
</html>
