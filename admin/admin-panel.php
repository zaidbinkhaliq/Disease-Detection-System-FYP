<?php
session_start();
include '../db.php'; // Ensure the database connection is included
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = ''; // Initialize message variable

// Ensure session variables are set
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$admin_email = $_SESSION['email'];
$admin_name = $admin_email;

// Fetch all admins except the logged-in admin
$sql_admins = "SELECT id, name, email, username, status, is_verified FROM users WHERE is_admin = 1 AND email != ?";
$stmt_admins = $conn->prepare($sql_admins);
$stmt_admins->bind_param("s", $admin_email);
$stmt_admins->execute();
$result_admins = $stmt_admins->get_result();

// Fetch all users who are not admins
$sql_users = "SELECT id, name, email, username, status, is_verified, token FROM users WHERE is_admin = 0";
$result_users = $conn->query($sql_users);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $reenter_password = $_POST['reenter_password'];

        // Fetch the current password
        $sql_password = "SELECT password FROM users WHERE email = ?";
        $stmt_password = $conn->prepare($sql_password);
        $stmt_password->bind_param("s", $admin_email);
        $stmt_password->execute();
        $result_password = $stmt_password->get_result();
        $row_password = $result_password->fetch_assoc();

        if (password_verify($old_password, $row_password['password'])) {
            if ($new_password === $reenter_password) {
                // Update the password
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_sql = "UPDATE users SET password = ? WHERE email = ?";
                $stmt_update_password = $conn->prepare($update_password_sql);
                $stmt_update_password->bind_param("ss", $hashed_new_password, $admin_email);

                if ($stmt_update_password->execute()) {
                    $message = "Password updated successfully.";
                } else {
                    $message = "Error updating password: " . $conn->error;
                }
            } else {
                $message = "New password and re-entered password do not match.";
            }
        } else {
            $message = "Old password is incorrect.";
        }
    }

    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        // $user_token = $_POST['user_token'];

        // Fetch the email from the database based on user_id
        $sql_email = "SELECT email, password FROM users WHERE id = ?";
        $stmt_email = $conn->prepare($sql_email);
        $stmt_email->bind_param("i", $user_id);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();
        $row_email = $result_email->fetch_assoc();
        $email = $row_email['email'];
        $password = $row_email['password']; // Hashed password

        if ($_POST['action'] == 'enable') {
            $update_sql = "UPDATE users SET status = 'active' WHERE id = ?";
        } elseif ($_POST['action'] == 'disable') {
            $update_sql = "UPDATE users SET status = 'inactive' WHERE id = ?";
        } elseif ($_POST['action'] == 'delete') {
            $update_sql = "DELETE FROM users WHERE id = ?";
        } elseif ($_POST['action'] == 'make_admin') {
            $update_sql = "UPDATE users SET is_admin = 1, status = 'active' WHERE id = ?";
        } elseif ($_POST['action'] == 'remove_admin') {
            $update_sql = "UPDATE users SET is_admin = 0 WHERE id = ?";
        } elseif ($_POST['action'] == 'verify') {
            // PHPMailer logic to resend the verification email
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
                $mail->setFrom('malikusamaaliawan@gmail.com', 'Verification purposes'); // Sender email
                $mail->addAddress($email); // Company email

                $mail->isHTML(true);
                $mail->Subject = 'Verify your email address';
                $mail->Body = 'Please click the link below to verify your email address:<br>
                <a href="http://localhost/log/verify.php?token=' . urlencode($user_token) . '">Verify Email</a>';

                $mail->send();
                $message = "Verification email sent successfully to $email.";
            } catch (Exception $e) {
                $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } elseif ($_POST['action'] == 'recover_password') {
            // Send email with the hashed password (not recommended in production)
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
                $mail->Subject = 'Password Recovery';
                $mail->Body    = 'Hello,<br><br>Your password is: ' . $password . '<br><br>Please keep it secure and do not share it with anyone.';

                $mail->send();
                $message = "Password has been sent to the user's email.";
            } catch (Exception $e) {
                $message = "Failed to send password. Mailer Error: {$mail->ErrorInfo}";
            }
        }

        if (isset($update_sql)) {
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("i", $user_id);

            if ($stmt_update->execute()) {
                header("Location: admin-panel.php");
                exit();
            } else {
                $message = "Error updating record: " . $conn->error;
            }
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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../admin.css"> <!-- Link to your CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome -->
    <style>
        /* General Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        td {
            background-color: #ffffff;
        }

        /* Alignment Classes */
        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .actions {
            text-align: center;
        }

        /* Toggle Switch Styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #007bff;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }

        /* Hover Effects */
        tr:hover {
            background-color: #f5f5f5;
        }

        /* Navbar Styles */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        /* Dropdown Menu */
        .dropdown-item:active {
            background-color: #007bff;
        }

        /* Message Styles */
        .alert-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: block;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($admin_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="../logout.php">
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Display Message -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info alert-message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="container" style="margin-top: 80px;">
        <!-- Admins Table -->
        <h3>Admins</h3>
        <table>
            <thead>
                <tr>
                    <th class="left">ID</th>
                    <th>Name</th>
                    <th class="center">Email</th>
                    <th>Username</th>
                    <th class="center">Verified</th>
                    <th class="center">Status</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_admins->num_rows > 0): ?>
                    <?php while($row = $result_admins->fetch_assoc()): ?>
                        <tr>
                            <td class="left"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="center"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="center">
                                <span class="badge <?php echo $row['is_verified'] ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $row['is_verified'] ? 'Verified' : 'Unverified'; ?>
                                </span>
                            </td>
                            <td class="center"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                            <td class="actions">
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="hidden" name="action" value="remove_admin">
                                    <button type="submit">Remove Admin</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No admins found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <br>
        <hr>
        <br>
        
        <!-- Users Table -->
        <h3>Users</h3>
        <table>
            <thead>
                <tr>
                    <th class="left">ID</th>
                    <th>Name</th>
                    <th class="center">Email</th>
                    <th>Username</th>
                    <th class="center">Verified</th>
                    <th class="actions">Verify</th>
                    <th class="actions">Enable/Disable</th>
                    <th class="actions">Make Admin</th>
                    <th class="actions">Delete</th>
                    <th class="actions">Password Recovery</th> <!-- Password Recovery Column -->
                </tr>
            </thead>
            <tbody>
                <?php if ($result_users->num_rows > 0): ?>
                    <?php while($row = $result_users->fetch_assoc()): ?>
                        <tr>
                            <td class="left"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="center"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="center">
                                <span class="badge <?php echo $row['is_verified'] ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $row['is_verified'] ? 'Verified' : 'Unverified'; ?>
                                </span>
                            </td>
                            <td class="actions">
                                <?php if (!$row['is_verified']): ?>
                                    <form method="post" style="display:inline-block;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <input type="hidden" name="user_token" value="<?php echo htmlspecialchars($row['token']); ?>">
                                        <input type="hidden" name="action" value="verify">
                                        <button type="submit">Verify</button>
                                    </form>
                                <?php else: ?>
                                    <button class="disabled-button" disabled>Verified</button>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="hidden" name="action" value="<?php echo $row['status'] == 'active' ? 'disable' : 'enable'; ?>">
                                    <label class="switch">
                                        <input type="checkbox" onchange="this.form.submit()" <?php echo $row['status'] == 'active' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </form>
                            </td>
                            <td class="actions">
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="hidden" name="action" value="make_admin">
                                    <button type="submit">Make Admin</button>
                                </form>
                            </td>
                            <td class="actions">
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                            <td class="actions">
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <input type="hidden" name="action" value="recover_password">
                                    <button type="submit">Send Password</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alertMessage = document.querySelector('.alert-message');
            if (alertMessage) {
                setTimeout(function() {
                    alertMessage.style.display = 'none';
                }, 3000); // Hide after 3 seconds
            }
        });
    </script>
</body>
</html>













