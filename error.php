<!-- error.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error Page</title>
  <link rel="stylesheet" href="index.css">
  <style>
    body, html {
      background: url('background.jpg') no-repeat center center;
      background-size: cover;
      height: 95vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
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
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      color: black;
      position: relative;
    }

    .alert-danger {
      background-color: #dc3545;
      color: white;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      text-align: center;
    }

    .btn-back {
      display: block;
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: #fff;
      text-align: center;
      border-radius: 5px;
      text-decoration: none;
      margin-top: 15px;
    }

    .btn-back:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <div class="alert alert-danger">
      <?php
      session_start();
      if (isset($_SESSION['error_message'])) {
        echo $_SESSION['error_message'];
        unset($_SESSION['error_message']); // Clear the error message after displaying it
      } else {
        echo "An unknown error occurred.";
      }
      ?>
    </div>
    <a href="register.php" class="btn-back">Back to Registration</a>
  </div>
</body>
</html>
