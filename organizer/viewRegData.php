<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'organizer') {
    header("Location: ../log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$participantsCollection = $db->participants;
$organizerCollection = $db->organizers;
$eventsCollection = $db->events;
$paymentsCollection = $db->payments;

// Fetch organizer email using the username in session variable
$organizerUsername = $_SESSION['username'];
$organizer = $organizerCollection->findOne(['username' => $organizerUsername]);
$organizerEmail = $organizer['email'] ?? '';

$participantId = $_POST['participant_id'] ?? null;
$participant = null;

if ($participantId) {
    $participant = $participantsCollection->findOne(['_id' => new ObjectId($participantId)]);
}

$event_id = $participant['event_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $participantEmail = $participant['email'];

    if ($action === 'Approve') {
        // Update the participant status
        $participantsCollection->updateOne(['_id' => new ObjectId($participantId)], ['$set' => ['status' => 'approved']]);

        // Insert into payments collection
        $paymentsCollection->insertOne([
            'email' => $participant['email'],
            'event_id' => $participant['event_id'],
            'name' => $participant['name'],
            'phone' => $participant['phone'],
            'amount' => $participant['amount'],
            'timestamp' => $participant['timestamp']
        ]);
        

        // Send email notification
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'campusconnect.events@gmail.com';
            $mail->Password = 'cuut pyiw rrqh feub';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom($organizerEmail, 'CampusConnect Admin');
            $mail->addAddress($participantEmail);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'CampusConnect - Registration Approved';
            $mail->Body = "Dear {$participant['name']},<br><br>Your registration for the event has been approved.<br>Best Regards,<br>CampusConnect Team<br>Organizer Approved by: {$organizerUsername}";

            // Send email
            $mail->send();
            $message = 'Approval email has been sent.';
        } catch (Exception $e) {
            $message = "Error sending email: {$mail->ErrorInfo}";
        }
    } elseif ($action === 'Reject') {
        $reason = $_POST['rejection_reason'];

        // Send email notification
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'campusconnect.events@gmail.com';
            $mail->Password = 'cuut pyiw rrqh feub';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom($organizerEmail, 'CampusConnect Admin');
            $mail->addAddress($participantEmail);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'CampusConnect - Registration Rejected';
            $mail->Body = "Dear {$participant['name']},<br><br>Your registration for the event has been rejected for the following reason:<br><br><strong style='color: red;'>{$reason}</strong><br><br>Organizer Rejected by: {$organizerUsername}";

            // Send email
            $mail->send();
            $message = 'Rejection email has been sent with the reason.';
        } catch (Exception $e) {
            $message = "Error sending email: {$mail->ErrorInfo}";
        }

        // Update the participant status to rejected
        $participantsCollection->updateOne(['_id' => new ObjectId($participantId)], ['$set' => ['status' => 'rejected']]);
        $eventsCollection->updateOne(['_id' => new ObjectId($event_id)],  ['$inc' => ['event_limit' => 1]] );

        // Delete the record from payments collection
        $paymentsCollection->deleteOne(['email' => $participantEmail, 'event_id' => $event_id]);
    }

    // Redirect back to the manage registrations page with a message
    echo "<form id='redirectForm' method='POST' action='manage_registerations.php'>
            <input type='hidden' name='event_id' value='" . htmlspecialchars($participant['event_id']) . "'>
          </form>
          <script type='text/javascript'>
              document.getElementById('redirectForm').submit();
          </script>";
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Registration Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(22, 132, 210);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            outline: none;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            box-shadow: 0 4px #999;
        }
        .btn:hover {background-color: #0056b3}
        .btn:active {
            background-color: #0056b3;
            box-shadow: 0 2px #666;
            transform: translateY(2px);
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {background-color: #c82333}
        .btn-danger:active {
            background-color: #c82333;
            box-shadow: 0 2px #666;
            transform: translateY(2px);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
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
            height: 1000px;
            z-index: 1000;
            position: relative;
        }
    </style>
    <script>
        function toggleImageSize(imgElement) {
            imgElement.classList.toggle('large');
        }
    </script>
</head>
<body>
<div class="container">
    <form method="POST" action="manage_registerations.php">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($participant['event_id']); ?>">
        <button type="submit" class="btn btn-primary">Back</button>
    </form>
    <h1>Participant Registration Data</h1>
    <?php if ($participant): ?>
        <table>
            <?php foreach ($participant as $key => $value): ?>
                <?php if ($key !== '_id' && $key !== 'event_id'): ?>
                    <tr>
                        <th><?php echo htmlspecialchars($key); ?></th>
                        <td>
                            <?php if ($key === 'InstitutionID' || $key === 'PaymentProof'): ?>
                                <img src="<?php echo htmlspecialchars('../' . $value); ?>" alt="<?php echo htmlspecialchars($key); ?>" class="img-preview" onclick="toggleImageSize(this)">
                            <?php else: ?>
                                <?php echo htmlspecialchars($value); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
        <form method="POST">
            <input type="hidden" name="participant_id" value="<?php echo htmlspecialchars($participantId); ?>">
            <button type="submit" name="action" value="Approve" class="btn btn-success">Approve</button><br>
        </form>
        <form method="POST">
                <input type="hidden" name="participant_id" value="<?php echo htmlspecialchars($participantId); ?>">
                <br><br><label for="rejection_reason">Reason for Rejection:</label>
                <textarea name="rejection_reason" id="rejection_reason" rows="4" required></textarea>
                <button type="submit" name="action" value="Reject" class="btn btn-danger">Reject</button>
        </form>
        <br>
    <?php else: ?>
        <p>No participant found with the given ID.</p>
    <?php endif; ?>
</div>
<script>
    function toggleImageSize(imgElement) {
        imgElement.classList.toggle('large');
    }
</script>
</body>
</html>