<?php
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

// Fetch the current user's status from the database
include 'db.php';
$user_id = $_SESSION['user_id'];
$sql = "SELECT status, password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // If no user is found, assume the account has been deleted
    session_destroy();
    header("Location: login.php?message=Your account has been deleted by the admin.");
    exit();
} else {
    $row = $result->fetch_assoc();
    if ($row['status'] == 'inactive') {
        // If the account is disabled, show a message
        echo "<div class='alert alert-danger' id='alert-message'>Your account has been disabled by the admin. Please contact support for further assistance.</div>";
        exit(); // Stop the script to prevent further access
    }
    $current_password_hash = $row['password'];
}

if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $reenter_password = $_POST['reenter_password'];

    // Validate old password: check against both hashed and plain text
    if (!password_verify($old_password, $current_password_hash) && $old_password !== $current_password_hash) {
        echo "<div class='alert alert-danger' id='alert-message'>Old password is incorrect or passwords do not match.</div>";
    } elseif ($new_password !== $reenter_password) {
        echo "<div class='alert alert-danger' id='alert-message'>New passwords do not match.</div>";
    } else {
        // Hash the new password
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the password in the database
        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_password_hash, $user_id);
        if ($update_stmt->execute()) {
            echo "<div class='alert alert-success' id='alert-message'>Password changed successfully.</div>";
        } else {
            echo "<div class='alert alert-danger' id='alert-message'>Error updating password. Please try again later.</div>";
        }
        $update_stmt->close();
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Application Selector</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            color: rgb(255, 255, 255);
            overflow: hidden;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: url('main.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3); /* Adjust opacity here for fading effect */
            z-index: -1; /* Ensure it is behind the content */
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(25, 25, 25, 0.5);
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-top: 80px; /* Adjust this to push content below the navbar */
        }

        .button {
            background-color: #80cfdb;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border-radius: 8px;
        }

        .button:hover {
            background: #0ef;
            color: #082910;
            box-shadow: 0 0 50px #0ef;
        }

        .btn-logout {
            background-color: transparent;
            color: #dc3545;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            font-size: 16px;
            margin-top: 10px;
        }

        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 2;
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        /* Dropdown Menu */
        .dropdown-item:active {
            background-color: #007bff;
        }

        /* Adjust Modal Styling */
        .modal-content {
            color: #000;
        }

        .modal-header, .modal-body, .modal-footer {
            background-color: #f8f9fa;
        }

        #alert-message {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    padding: 10px;
    border-radius: 5px;
    display: none;
}

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Medical Center</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user"></i> <?php echo $_SESSION['email']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>

                            <li><a class="dropdown-item" href="ContactUs.php" >Contact Us</a></li>
                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="post" action="logout.php">
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="overlay"></div> <!-- Overlay for reducing the image opacity -->
    <div class="content">
        <h1>Medical ChatBot</h1>
        <p>Explore a smarter way to manage your health with our Medical Chatbot, <br>your 24/7 assistant for instant health advice and medical information.</p>
        <a href="http://localhost:5000" class="button">Doctor</a>
        <a href="http://localhost:8501" class="button">Lab</a>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Enter Old Password</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Enter New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="reenter_password" class="form-label">Re-enter New Password</label>
                            <input type="password" class="form-control" id="reenter_password" name="reenter_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show the alert message if it exists
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                alertMessage.style.display = 'block';
                // Hide the message after 3 seconds
                setTimeout(function () {
                    alertMessage.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>
