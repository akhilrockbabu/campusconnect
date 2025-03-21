<?php
// Initialize variables for form data
$recipients = $subject = $content = $status = "";
$error = "";

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

// Process form when submitted via AJAX
if (isset($_POST['ajax_submit']) && $_POST['ajax_submit'] == 'true') {
    header('Content-Type: application/json');
    
    // Get form data
    $recipients = $_POST["recipients"];
    $subject = $_POST["subject"];
    $content = $_POST["content"];
    $current_index = isset($_POST['current_index']) ? intval($_POST['current_index']) : 0;
    
    $response = array(
        'success' => false,
        'message' => '',
        'completed' => false,
        'current_index' => $current_index,
        'total_sent' => 0,
        'total_failed' => 0
    );
    
    // Process recipients
    $recipient_array = explode("\n", $recipients);
    $recipient_array = array_map('trim', $recipient_array);
    $recipient_array = array_filter($recipient_array);
    $total_recipients = count($recipient_array);
    
    if ($total_recipients == 0) {
        $response['message'] = "No valid recipients found";
        echo json_encode($response);
        exit;
    }
    
    // Check if we've processed all recipients
    if ($current_index >= $total_recipients) {
        $response['completed'] = true;
        $response['success'] = true;
        $response['message'] = "All emails processed";
        echo json_encode($response);
        exit;
    }
    
    // Get the current recipient to process
    $recipient_emails = array_values($recipient_array);
    $current_email = $recipient_emails[$current_index];
    
    // Process attachment if exists
    $has_attachment = false;
    $attachment_path = "";
    $attachment_name = "";
    
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $attachment_path = $_FILES['attachment']['tmp_name'];
        $attachment_name = $_FILES['attachment']['name'];
        $has_attachment = true;
    }
    
    // Send email to current recipient
    $success = false;
    if (filter_var($current_email, FILTER_VALIDATE_EMAIL)) {
        // Load PHPMailer only if needed
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            require '../vendor/autoload.php'; // Adjust path as needed
        }
        
        $success = send_email($current_email, $subject, $content, $has_attachment, $attachment_path, $attachment_name);
    }
    
    // Update response with results
    $response['success'] = true;
    $response['current_index'] = $current_index + 1;
    $response['total_sent'] = $success ? 1 : 0;
    $response['total_failed'] = $success ? 0 : 1;
    $response['message'] = $success ? "Email sent to {$current_email}" : "Failed to send to {$current_email}";
    
    echo json_encode($response);
    exit;
}

// Regular form submission (non-AJAX, fallback)
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['ajax_submit'])) {
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

// Count emails for JavaScript
function countEmails($text) {
    if (empty($text)) return 0;
    $lines = explode("\n", $text);
    $lines = array_map('trim', $lines);
    $lines = array_filter($lines);
    return count($lines);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect Email Sender</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-light: #60ad5e;
            --primary-dark: #005005;
            --secondary-color: #0277bd;
            --text-color: #333;
            --text-light: #666;
            --background-color: #f5f5f5;
            --card-color: #ffffff;
            --success-color: #4caf50;
            --error-color: #f44336;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: var(--card-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .header h1 {
            color: var(--primary-color);
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: var(--text-light);
            font-size: 16px;
        }

        .logo {
            font-size: 36px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.2);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        textarea#content {
            min-height: 200px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background-color: #f9f9f9;
            border: 1px dashed #ddd;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .file-input-label:hover {
            background-color: #f0f0f0;
            border-color: #ccc;
        }

        .file-input-label i {
            margin-right: 10px;
            font-size: 18px;
            color: var(--primary-color);
        }

        .file-input-label span {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .status {
            margin: 20px 0;
            padding: 15px;
            border-radius: var(--border-radius);
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .status i {
            margin-right: 10px;
            font-size: 20px;
        }

        .success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .error {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(244, 67, 54, 0.3);
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background-color: #0360a5;
        }

        .btn i {
            margin-right: 8px;
        }

        .progress-container {
            display: none;
            margin-top: 20px;
        }

        .progress-bar {
            height: 20px;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-inner {
            height: 100%;
            width: 0;
            background-color: var(--primary-color);
            text-align: center;
            line-height: 20px;
            color: white;
            font-size: 12px;
            transition: width 0.5s;
        }

        .progress-status {
            margin-top: 8px;
            font-size: 14px;
            color: var(--text-light);
            text-align: center;
        }

        .email-counter {
            display: flex;
            justify-content: flex-end;
            margin-top: 5px;
            font-size: 14px;
            color: var(--text-light);
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: var(--text-light);
        }

        .send-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .stat-item {
            font-size: 14px;
            color: var(--text-light);
        }

        .stat-success {
            color: var(--success-color);
        }

        .stat-error {
            color: var(--error-color);
        }

        .btn-disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><i class="fas fa-envelope-open-text"></i></div>
            <h1>CampusConnect Email Sender</h1>
            <p>Send bulk emails to your recipients efficiently</p>
        </div>
        
        <?php if (!empty($status)): ?>
            <div class="status success">
                <i class="fas fa-check-circle"></i>
                <?php echo $status; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="status error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div id="statusMessage"></div>
        
        <form method="post" enctype="multipart/form-data" id="emailForm">
            <div class="form-group">
                <label for="recipients"><i class="fas fa-users"></i> Recipients (one email per line):</label>
                <textarea id="recipients" name="recipients" class="form-control" required><?php echo htmlspecialchars($recipients); ?></textarea>
                <div class="email-counter" id="emailCounter">0 email(s)</div>
            </div>
            
            <div class="form-group">
                <label for="subject"><i class="fas fa-heading"></i> Subject:</label>
                <input type="text" id="subject" name="subject" class="form-control" required value="<?php echo htmlspecialchars($subject); ?>">
            </div>
            
            <div class="form-group">
                <label for="content"><i class="fas fa-edit"></i> Content:</label>
                <textarea id="content" name="content" class="form-control" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="attachment"><i class="fas fa-paperclip"></i> Attachment:</label>
                <div class="file-input-wrapper">
                    <label class="file-input-label">
                        <i class="fas fa-file-upload"></i>
                        <span id="fileNameDisplay">No file selected</span>
                        <input type="file" id="attachment" name="attachment" class="file-input">
                    </label>
                </div>
            </div>
            
            <button type="button" id="sendButton" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send Emails
            </button>
        </form>
        
        <div id="progressContainer" class="progress-container">
            <div class="progress-bar">
                <div id="progressBar" class="progress-bar-inner">0%</div>
            </div>
            <div id="progressStatus" class="progress-status">Sending emails...</div>
            <div class="send-stats">
                <div class="stat-item">Processed: <span id="processedCount">0</span>/<span id="totalCount">0</span></div>
                <div class="stat-item stat-success">Sent: <span id="successCount">0</span></div>
                <div class="stat-item stat-error">Failed: <span id="failCount">0</span></div>
            </div>
        </div>
        
        <form method="post" action="admin6096.php">
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Admin Panel
            </button>
        </form>
        
        <div class="footer">
            &copy; <?php echo date('Y'); ?> CampusConnect. All rights reserved.
        </div>
    </div>

    <script>
        // File input handling
        document.getElementById('attachment').addEventListener('change', function() {
            var fileName = this.files.length > 0 ? this.files[0].name : 'No file selected';
            document.getElementById('fileNameDisplay').textContent = fileName;
        });
        
        // Email counter
        document.getElementById('recipients').addEventListener('input', updateEmailCount);
        
        function updateEmailCount() {
            const recipientsText = document.getElementById('recipients').value;
            let count = 0;
            
            if (recipientsText.trim()) {
                const lines = recipientsText.split('\n').filter(line => line.trim().length > 0);
                count = lines.length;
            }
            
            document.getElementById('emailCounter').textContent = count + ' email(s)';
            return count;
        }
        
        // Initialize email count
        updateEmailCount();
        
        // Form data to FormData object
        function getFormData() {
            const form = document.getElementById('emailForm');
            const formData = new FormData(form);
            formData.append('ajax_submit', 'true');
            return formData;
        }
        
        // Status message handling
        function showStatus(message, isError = false) {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.innerHTML = `
                <div class="status ${isError ? 'error' : 'success'}">
                    <i class="fas fa-${isError ? 'exclamation-circle' : 'check-circle'}"></i>
                    ${message}
                </div>
            `;
        }
        
        // Send individual email and update progress
        async function sendEmail(currentIndex, totalEmails) {
            const formData = getFormData();
            formData.append('current_index', currentIndex);
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const result = await response.json();
                
                // Update stats
                const processedCount = document.getElementById('processedCount');
                const successCount = document.getElementById('successCount');
                const failCount = document.getElementById('failCount');
                
                processedCount.textContent = currentIndex + 1;
                successCount.textContent = parseInt(successCount.textContent) + result.total_sent;
                failCount.textContent = parseInt(failCount.textContent) + result.total_failed;
                
                // Update progress bar
                const progressBar = document.getElementById('progressBar');
                const progressStatus = document.getElementById('progressStatus');
                const percentage = Math.floor(((currentIndex + 1) / totalEmails) * 100);
                
                progressBar.style.width = percentage + '%';
                progressBar.textContent = percentage + '%';
                
                // Continue or complete
                if (result.completed || currentIndex + 1 >= totalEmails) {
                    progressStatus.textContent = 'Email sending complete!';
                    document.getElementById('sendButton').disabled = false;
                    document.getElementById('sendButton').classList.remove('btn-disabled');
                    return;
                }
                
                // Process next email
                await sendEmail(currentIndex + 1, totalEmails);
                
            } catch (error) {
                console.error('Error:', error);
                showStatus('Error sending emails: ' + error.message, true);
                document.getElementById('sendButton').disabled = false;
                document.getElementById('sendButton').classList.remove('btn-disabled');
            }
        }
        
        // Start sending emails
        document.getElementById('sendButton').addEventListener('click', async function() {
            const form = document.getElementById('emailForm');
            
            // Basic form validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const emailCount = updateEmailCount();
            if (emailCount === 0) {
                showStatus('Please add at least one email recipient', true);
                return;
            }
            
            // Disable send button
            this.disabled = true;
            this.classList.add('btn-disabled');
            
            // Show and reset progress container
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressStatus = document.getElementById('progressStatus');
            
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            progressStatus.textContent = 'Sending emails...';
            
            // Reset counters
            document.getElementById('processedCount').textContent = '0';
            document.getElementById('totalCount').textContent = emailCount;
            document.getElementById('successCount').textContent = '0';
            document.getElementById('failCount').textContent = '0';
            
            // Start sending emails immediately
            try {
                await sendEmail(0, emailCount);
            } catch (error) {
                console.error('Error starting email process:', error);
                showStatus('Failed to start email sending: ' + error.message, true);
                this.disabled = false;
                this.classList.remove('btn-disabled');
            }
        });
    </script>
</body>
</html>