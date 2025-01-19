<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requiredFields = [
        'fullName',
        'emailAddress',
        'mobileNumber',
        'contactNumber',
        'whatsappNumber',
        'subject',
        'message'
    ];

    $isValid = true;
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $isValid = false;
            break;
        }
    }

    if ($isValid) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'malikusamaaliawan@gmail.com';
            $mail->Password   = 'hvdx qouo icwu cmox'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('malikusamaaliawan@gmail.com', 'Usama Ali'); // Sender email
            $mail->addAddress('malikusamaaliawan@gmail.com'); // Company email
            $mail->addAddress($_POST['emailAddress']); // User email

            // Include the session email and name
            $sessionEmail = $_SESSION['email'];
            $sessionName = $_SESSION['name'];

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = htmlspecialchars($_POST['subject']);
            
            $message = '<html><head><style>
            #infoTable {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }
            #infoTable td, #infoTable th {
                border: 1px solid #ddd;
                padding: 8px;
            }
            #infoTable tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            #infoTable tr:hover {
                background-color: #ddd;
            }
            #infoTable th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: #4CAF50;
                color: white;
            }
            </style></head><body>';
            $message .= '<h3 style="color:#000000;">' . htmlspecialchars($_POST['subject']) . '</h3>';
            $message .= '<table id="infoTable">
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>';
            $message .= '
                <tr>
                    <td>Name</td>
                    <td>' . htmlspecialchars($_POST['fullName']) . '</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>' . htmlspecialchars($_POST['emailAddress']) . '</td>
                </tr>
                <tr>
                    <td>Mobile No.</td>
                    <td>' . htmlspecialchars($_POST['mobileNumber']) . '</td>
                </tr>
                <tr>
                    <td>Contact No.</td>
                    <td>' . htmlspecialchars($_POST['contactNumber']) . '</td>
                </tr>
                <tr>
                    <td>WhatsApp No.</td>
                    <td>' . htmlspecialchars($_POST['whatsappNumber']) . '</td>
                </tr>
                <tr>
                    <td>Text</td>
                    <td>' . nl2br(htmlspecialchars($_POST['message'])) . '</td>
                </tr>';
            $message .= '</table>';
            $message .= '<p>Submitted by: ' . htmlspecialchars($sessionName) . ' (' . htmlspecialchars($sessionEmail) . ')</p>';
            $message .= '</body></html>';

            $mail->Body = $message;
            $mail->send();
            echo "<script>alert('Mail sent');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Message not sent, Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Please fill all the fields');</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>

body {
            background: url(contactus.jpg);
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

            margin: 20px auto;
            padding: 20px;
            background-color: transparent;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
        }

        .form-group {
            margin-bottom: 15px;
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

        .row .form-group {
            padding: 0 15px;
        }


        .btn-transparent:hover {
      background-color: #007bff;
      color: #fff;
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

    <div class="container">
        <div class="form-container">
        <h3>Contact Us</h3>
        <br>
        <br>
            <form action="" method="POST" class="mbr-form form-with-styler" data-form-title="Contact Form">
                <div class="row">
                    <div class="col-lg-4 form-group">
                        <label for="fullName" class="form-control-label mbr-fonts-style display-7">Full Name:</label>
                        <input type="text" name="fullName" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" placeholder="Enter your name" required class="form-control display-7" id="fullName" readonly>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label for="emailAddress" class="form-control-label mbr-fonts-style display-7">Email:</label>
                        <input type="email" name="emailAddress" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" placeholder="Enter your email" required class="form-control display-7" id="emailAddress" readonly>
                    </div>
                    <div class="col-lg-4 form-group">
                        <label for="mobileNumber" class="form-control-label mbr-fonts-style display-7">Mobile No.:</label>
                        <input type="text" name="mobileNumber" placeholder="Enter your mobile number" required class="form-control display-7" id="mobileNumber">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 form-group">
                        <label for="contactNumber" class="form-control-label mbr-fonts-style display-7">Contact No.:</label>
                        <input type="text" name="contactNumber" placeholder="Enter your contact number" required class="form-control display-7" id="contactNumber">
                    </div>
                    <div class="col-lg-4 form-group">
                        <label for="whatsappNumber" class="form-control-label mbr-fonts-style display-7">WhatsApp No.:</label>
                        <input type="text" name="whatsappNumber" placeholder="Enter your WhatsApp number" class="form-control display-7" id="whatsappNumber">
                    </div>
                    <div class="col-lg-12 form-group">
                        <label for="subject" class="form-control-label mbr-fonts-style display-7">Subject:</label>
                        <input type="text" name="subject" placeholder="Enter the subject" required class="form-control display-7" id="subject">
                        </div>
                        </div>
                <div class="row">
                    <div class="col-lg-12 form-group">
                        <label for="message" class="form-control-label mbr-fonts-style display-7">Message:</label>
                        <textarea name="message" placeholder="Enter your message here..." required class="form-control display-7" id="message" rows="4"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <button type="submit" class="btn btn-primary btn-form display-4" style="background:none; border: none" >SEND FORM</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
