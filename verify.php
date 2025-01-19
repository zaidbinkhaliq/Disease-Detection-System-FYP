<?php
include 'db.php';
include 'send_email.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    $sql = "SELECT * FROM users WHERE token='$token'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $sql = "UPDATE users SET is_verified=1, token=NULL WHERE token='$token'";
        if ($conn->query($sql) === TRUE) {
            // JavaScript code to display the prompt
            echo "<script>
                    alert('You are verified!');
                  </script>";
        } else {
            echo "<div class='alert alert-danger'>Error updating record: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid token or already verified.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>No token provided.</div>";
}
$conn->close();
?>
