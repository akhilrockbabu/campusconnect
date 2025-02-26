<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_otp') {
    $email = $_POST['email'];
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 45; // 45 seconds expiry
    $_SESSION['email'] = $email;

    // Send OTP email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'campusconnect.events@gmail.com';
        $mail->Password = 'cuut pyiw rrqh feub';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('campusconnect.events@gmail.com', 'CampusConnect');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for CampusConnect';
        $mail->Body = "Your OTP is: <strong>$otp</strong>. It is valid for 45 seconds.";

        $mail->send();
        $message = 'OTP has been sent to your email.';
    } catch (Exception $e) {
        $message = "Error sending email: {$mail->ErrorInfo}";
    }
} else {
    // Clear the session email if not set
    unset($_SESSION['email']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Check Status</title>
  <link rel="stylesheet" href="css/login_style.css">
  <style>
    .btn-success {
        background-color: rgb(155, 8, 26);
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
    }
    .btn-success:hover {
        background-color: rgba(219, 110, 14, 0.72);
    }
    .btn-success:active {
        background-color: #1e7e34;
    }
  </style>
  <script>
    function validateForm() {
        const email = document.getElementById("email").value.trim();
        const otp = document.getElementById("otp").value.trim();
        const otpField = document.getElementById("otpField");
        const otpExpiry = <?php echo isset($_SESSION['otp_expiry']) ? $_SESSION['otp_expiry'] : 'null'; ?>;
        const currentTime = Math.floor(Date.now() / 1000);

        let errorMessages = [];

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            errorMessages.push("Email is required.");
        } else if (!emailPattern.test(email)) {
            errorMessages.push("Invalid email format.");
        }

        if (otpField.style.display !== 'none' && (!otp || otp !== '<?php echo isset($_SESSION['otp']) ? $_SESSION['otp'] : ''; ?>' || currentTime > otpExpiry)) {
            errorMessages.push("Invalid or expired OTP.");
        }

        if (errorMessages.length > 0) {
            alert(errorMessages.join("\n"));
            return false;
        }

        return true;
    }

    function sendOtp() {
        const email = document.getElementById("email").value.trim();
        if (!email) {
            alert("Please enter your email ID.");
            return;
        }

        document.getElementById("sendOtpForm").submit();
    }

    function startTimer() {
        const otpField = document.getElementById("otpField");
        otpField.style.display = 'block';
        let timer = 45;
        const timerElement = document.getElementById("timer");
        const interval = setInterval(() => {
            if (timer > 0) {
                timer--;
                timerElement.textContent = `Time left: ${timer} seconds`;
            } else {
                clearInterval(interval);
                timerElement.textContent = "OTP expired. Please request a new OTP.";
                otpField.style.display = 'none';
            }
        }, 1000);
    }
  </script>
</head>
<body>
  <div class="container">
    <div class="login form">
      <header>Check Status</header>
      <form id="sendOtpForm" action="checkstatus.php" method="post">
        <input type="hidden" name="action" value="send_otp">
        <input type="email" name="email" id="email" placeholder="Enter your Email ID" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
        <button type="button" class="btn btn-success" onclick="sendOtp()">Send OTP</button>
      </form>
      <form id="checkStatusForm" action="viewStatus.php" method="post" onsubmit="return validateForm()">
        <div id="otpField" style="display: none;">
          <input type="number" name="otp" id="otp" placeholder="Enter the 6 digit OTP">
          <p id="timer"></p>
        </div>
        <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
        <input type="submit" class="button" value="Check Status">
      </form>
    </div> 
  </div>
  <?php if (isset($message)): ?>
    <script>
      alert('<?php echo $message; ?>');
      startTimer();
    </script>
  <?php endif; ?>
</body>
</html>
