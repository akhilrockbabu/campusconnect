<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use MongoDB\Client;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin') {
    header("Location: log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$usersCollection = $db->users;
$organizersCollection = $db->organizers;
$adminCollection = $db->admin;

// Fetch admin email using the username in session variable
$adminUsername = $_SESSION['username'];
$admin = $adminCollection->findOne(['username' => $adminUsername]);
$adminEmail = $admin['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $targetUsername = $_POST['target_username'];

    // Fetch target user's email
    $targetUser = $organizersCollection->findOne(['username' => $targetUsername]);
    $targetUserEmail = $targetUser['email'];

    if ($action === 'Approve') {
        // Update the user status
        $usersCollection->updateOne(['username' => $targetUsername], ['$set' => ['status' => 'approved']]);
        
        // Send email notification
        $mail = new PHPMailer(true);  // Instantiate PHPMailer

        try {
            // Server settings
            $mail->isSMTP();  
            $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'campusconnect.events@gmail.com';  // Admin's email address (replace with actual)
            $mail->Password = 'zjof zgel zsdx bamx';  // Admin's email password (use app password for Gmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom($adminEmail, 'CampusConnect Admin');  // Admin's email address
            $mail->addAddress($targetUserEmail); // Target user's email

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'CampusConnect - Organizer Access Granted';
            $mail->Body    = "Dear {$targetUser['name']},<br><br>We are pleased to inform you that your registration as an organizer on CampusConnect has been approved by the admin. You now have full access to register events and manage our institution's activities on the platform.<br>You can log in to your account using the credentials provided during registration and start creating and managing events for our institution.<br>If you have any questions or need assistance, feel free to reach out to our support team at campusconnect.events@gmail.com or visit our help section on the platform.<br>We wish you a successful experience on CampusConnect!<br>Best Regards,<br>CampusConnect Team.<br>Admin Approved : {$adminEmail}";

            // Send email
            $mail->send();
            $message = 'Approval email has been sent.';
        } catch (Exception $e) {
            $message = "Error sending email: {$mail->ErrorInfo}";
        }
    } elseif ($action === 'Reject') {
        $reason = $_POST['rejection_reason'];

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'campusconnect.events@gmail.com';
            $mail->Password = 'zjof zgel zsdx bamx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom($adminEmail, 'CampusConnect Admin');
            $mail->addAddress($targetUserEmail);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'CampusConnect - Organizer Access Denied';
            $mail->Body = "Dear {$targetUser['name']},<br><br>We regret to inform you that your request to become an organizer has been denied for the following reason:<br><br><strong style='color: red;'>{$reason}</strong><br><br>Best Regards,<br>CampusConnect Team";

            // Send email
            $mail->send();
            $message = 'Rejection email has been sent with the reason.';
        } catch (Exception $e) {
            $message = "Error sending email: {$mail->ErrorInfo}";
        }

        // Remove the user from the database if rejected
        $usersCollection->deleteOne(['username' => $targetUsername]);
        $message = 'Organizer has been rejected and removed from the system.';
    }

    // Redirect back to the manage organizers page with a message
    //header("Location: manage_organizers.php?message=" . urlencode($message));
    header("Location: manage_organizers.php");
    exit();
}

// Fetch all organizers with pending status
$pendingOrganizers = $usersCollection->find(['role' => 'organizer', 'status' => 'pending']);

$documents = [];
foreach ($pendingOrganizers as $organizer) {
    $username = $organizer['username'];
    $organizerDetails = $organizersCollection->findOne(['username' => $username]);
    if ($organizerDetails) {
        $documents[] = [
            'username' => $username,
            'name' => $organizerDetails['name'],
            'email' => $organizerDetails['email'],
            'institution_id' => $organizerDetails['InstitutionID'],
            'department' => $organizerDetails['department']
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Organizers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
    <style>
        .img-preview {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .img-preview.large {
            width: 500px;
            height: 500px;
            z-index: 1000;
            position: relative;
        }

        button:hover {
        background-color: #0056b3;
        }

        .message {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Organizers</h1><br>

    <!-- Display success or failure message -->
    <?php if (isset($_GET['message'])): ?>
        <div class="message">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <table class="rwd-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Institution ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($documents as $doc) {
                echo "<tr>";
                echo "<td data-th='Name'>" . htmlspecialchars($doc['name']) . "</td>";
                echo "<td data-th='Email'>" . htmlspecialchars($doc['email']) . "</td>";
                echo "<td data-th='Department'>" . htmlspecialchars($doc['department']) . "</td>";
                echo "<td data-th='Document Preview'>";
                echo "<img src='" . htmlspecialchars($doc['institution_id']) . "' alt='Document Preview' class='img-preview' onclick='toggleImageSize(this)'>";
                echo "</td>";
                echo "<td>";
                echo "<form method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='target_username' value='" . htmlspecialchars($doc['username']) . "'>";
                echo "<button type='submit' name='action' value='Approve' class='btn btn-success'>Approve</button>";
                echo "</form>";
                echo "<br><br>";
                echo "<form method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='target_username' value='" . htmlspecialchars($doc['username']) . "'>";
                echo "<textarea name='rejection_reason' required placeholder='Reason for rejection' style='margin-top: 10px; display:block; color: red;'></textarea>";
                echo "<button type='submit' name='action' value='Reject' class='btn btn-danger'>Reject</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table><br><br>
    <button type="button" onclick="window.location.href='admin6096.php'" style="background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 20px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease;">Back</button>

</div>

<script>
    function toggleImageSize(imgElement) {
        imgElement.classList.toggle('large');
    }
</script>
</body>
</html>