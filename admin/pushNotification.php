<?php
// Initialize variables for form data
$recipients = $subject = $content = $status = "";
$error = "";

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $recipients = $_POST["recipients"];
    $subject = $_POST["subject"];
    $content = $_POST["content"];
    
    // Validate inputs
    if (empty($recipients) || empty($subject) || empty($content)) {
        $error = "All fields are required";
    } else {
        // Process recipients
        $recipient_array = explode("\n", $recipients);
        $recipient_array = array_map('trim', $recipient_array);
        $recipient_array = array_filter($recipient_array);
        
        // Check if there are valid recipients
        if (count($recipient_array) == 0) {
            $error = "No valid recipients found";
        } else {
            // Require PHPMailer
            require '../vendor/autoload.php'; // Adjust path as needed
            
            // Process attachment if exists
            $has_attachment = false;
            $attachment_path = "";
            $attachment_name = "";
            
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $attachment_path = $_FILES['attachment']['tmp_name'];
                $attachment_name = $_FILES['attachment']['name'];
                $has_attachment = true;
            }
            
            // Send emails
            $success_count = 0;
            $fail_count = 0;
            
            foreach ($recipient_array as $to) {
                if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                    $success = send_email($to, $subject, $content, $has_attachment, $attachment_path, $attachment_name);
                    if ($success) {
                        $success_count++;
                    } else {
                        $fail_count++;
                    }
                } else {
                    $fail_count++;
                }
            }
            
            // Set status message
            $status = "Email sending complete: $success_count sent successfully, $fail_count failed.";
        }
    }
}

// Function to send email using PHPMailer
function send_email($to, $subject, $content, $has_attachment, $attachment_path, $attachment_name) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'campusconnect.events@gmail.com'; // Replace with your email
        $mail->Password = 'cuut pyiw rrqh feub'; // Replace with your email password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('campusconnect.events@gmail.com', 'CampusConnect');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        
        // Add attachment if exists
        if ($has_attachment && !empty($attachment_path)) {
            $mail->addAttachment($attachment_path, $attachment_name);
        }
        
        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error for debugging
        error_log("Error sending email to $to: {$mail->ErrorInfo}");
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Sender</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color:rgb(0, 99, 0);
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background: #ffffff;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        textarea {
            height: 100px;
        }
        textarea#content {
            height: 200px;
        }
        .status {
            margin: 20px 0;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        button {
            display: block;
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-btn {
            background-color: #007bff;
            margin-top: 10px;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        .progress-bar {
            display: none;
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 20px;
        }
        .progress-bar-inner {
            width: 0;
            height: 20px;
            background-color: #4CAF50;
            text-align: center;
            line-height: 20px;
            color: white;
        }
    </style>
    <script>
        function showProgressBar() {
            var progressBar = document.getElementById('progress-bar');
            var progressBarInner = document.getElementById('progress-bar-inner');
            progressBar.style.display = 'block';
            var width = 0;
            var interval = setInterval(function() {
                if (width >= 100) {
                    clearInterval(interval);
                } else {
                    width++;
                    progressBarInner.style.width = width + '%';
                    progressBarInner.innerHTML = width + '%';
                }
            }, 100);
        }
    </script>
</head>
<body>
    <h1>Email Sender</h1>
    
    <?php if (!empty($status)): ?>
        <div class="status success"><?php echo $status; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="status error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data" onsubmit="showProgressBar()">
        <div class="form-group">
            <label for="recipients">Recipients (one email per line):</label>
            <textarea id="recipients" name="recipients" required><?php echo htmlspecialchars($recipients); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="subject">Subject:</label>
            <textarea id="subject" name="subject" rows="1" required><?php echo htmlspecialchars($subject); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="attachment">Attachment:</label>
            <input type="file" id="attachment" name="attachment">
        </div>
        
        <button type="submit">Send Emails</button>
    </form>
    
    <div id="progress-bar" class="progress-bar">
        <div id="progress-bar-inner" class="progress-bar-inner">0%</div>
    </div>
    
    <form method="post" action="admin6096.php">
        <button type="submit" class="back-btn">Back</button>
    </form>
</body>
</html>
