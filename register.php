<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
 
 
 <style>
    body, html {
      background: url('background.jpg') no-repeat center center;
      background-size: cover; /* Cover the entire viewport */
      height: 95vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
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

    .form-container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      position: relative;
    }

    .alert {
      position: fixed;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1000;
      padding: 10px;
      border-radius: 5px;
      width: 100%;
      max-width: 400px;
      text-align: center;
      color: white;
      margin-top: 20px;
    }

    .alert-danger {
      background-color: #dc3545;
    }

    .alert-success {
      background-color: #28a745;
    }

    .btn-transparent {
      display: block;
      width: 100%;
      padding: 10px;
      border: 2px solid #007bff;
      background-color: transparent;
      color: #007bff;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      transition: background-color 0.3s, color 0.3s;
    }

    .btn-transparent:hover {
      background-color: #007bff;
      color: #fff;
    }
  </style>




  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const alert = document.querySelector(".alert");
      if (alert) {
        setTimeout(function() {
          alert.style.display = 'none';
        }, 4000);
      }
    });
  </script>



</head>


<body>
  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      include 'db.php';
      include 'send_email.php';

      $name = $conn->real_escape_string($_POST['name']);
      $username = $conn->real_escape_string($_POST['username']);
      $email = $conn->real_escape_string($_POST['email']);
      $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
      $token = bin2hex(random_bytes(50));
      $otp = rand(100000, 999999); // Generate a random OTP

      $sql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          echo "<div class='alert alert-danger'>Username or Email already exists.</div>";
      } else {
          $sql = "INSERT INTO users (name, username, email, password, token, otp, is_verified) VALUES ('$name', '$username', '$email', '$password', '$token', '$otp', 0)";
          if ($conn->query($sql) === TRUE) {
              sendVerificationEmail($email, $token);
              echo "<div class='alert alert-success'>Registration successful! Check your email to verify your account.</div>";
          } else {
              echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
          }
      }

      $conn->close();
  }
  ?>

  <div class="form-container">
    <form action="register.php" method="POST">
        <h3>Register Here</h3>

        <label for="name">Name</label>
        <input type="text" placeholder="Name" id="name" name="name" autocomplete="off" required>

        <label for="username">User Name</label>
        <input type="text" placeholder="User Name" id="username" name="username" autocomplete="off" required>

        <label for="email">Email</label>
        <input type="email" placeholder="Email" id="email" name="email" autocomplete="off" required>

        <label for="password">Password</label>
        <input type="password" placeholder="Password" id="password" name="password" autocomplete="off" required>

        <button type="submit">Register</button>

        <a class="rg" href="login.php">
            <div class="reg">Login</div>
        </a>
    </form>

    <a href="login.php">Already have an account? Login here</a>
  </div>
</body>
</html>
