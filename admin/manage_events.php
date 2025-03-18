<?php

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;
$organizersCollection = $db->organizers;

// Fetch admin email using the username in session variable
$adminUsername = $_SESSION['username'];
$adminEmail = $db->admin->findOne(['username' => $adminUsername])['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $eventId = $_POST['event_id'];
    $reason = $_POST['reason'] ?? '';

    $event = $eventsCollection->findOne(['_id' => new ObjectId($eventId)]);
    $organizer = $organizersCollection->findOne(['username' => $event['event_organizer']]);

    if ($action === 'approve') {
        $eventsCollection->updateOne(['_id' => new ObjectId($eventId)], ['$set' => ['status' => 'approved']]);
        $subject = "Event Approved";
        $body = "Your event '{$event['event_name']}' has been approved.";
    } elseif ($action === 'reject') {
        $eventsCollection->deleteOne(['_id' => new ObjectId($eventId)]);
        $subject = "Event Rejected";
        $body = "Your event '{$event['event_name']}' has been rejected. Reason: $reason";
    }


    if ($action === 'make_live') {
        $eventsCollection->updateOne(['_id' => new ObjectId($eventId)], ['$set' => ['status' => 'live']]);
    } elseif ($action === 'make_hold') {
        $eventsCollection->updateOne(['_id' => new ObjectId($eventId)], ['$set' => ['status' => 'hold']]);
    }

    // Send email to organizer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'campusconnect.events@gmail.com'; // SMTP username
        $mail->Password = 'cuut pyiw rrqh feub'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($adminEmail, 'CampusConnect Admin');
        $mail->addAddress($organizer['email']); // Add a recipient

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    header("Location: manage_events.php");
    exit();
}

// Fetch all pending events
$events = $eventsCollection->find(['status' => 'pending']);


$approved_events = $eventsCollection->find([
    'status' => ['$in' => ['approved', 'hold', 'live']]
]);

?>

<!DOCTYPE html>
<html lang="en">
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
    <script>
        function confirmDelete(event) {
            if (!confirm("Deleting this event may delete the event details and registration details entirely from the database. Think twice before you do.")) {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h1>Manage Pending Events</h1><br>
    <table class="rwd-table">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Description</th>
                <th>Date</th>
                <th>Organizer</th>
                <th>Department</th>
                <th>Registration Fees</th>
                <th>Event Poster</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <?php
                $organizer = $organizersCollection->findOne(['username' => $event['event_organizer']]);
                ?>
                <tr>
                    <td data-th='Event Name' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td data-th='Description' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_desc']); ?></td>
                    <td data-th='Date' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_date']); ?></td>
                    <td data-th='Organizer' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($organizer['name']); ?></td>
                    <td data-th='Department'><?php echo htmlspecialchars($organizer['department']); ?></td>
                    <td data-th='Registration Fees'><?php echo htmlspecialchars($event['registration_fees']); ?></td>
                    <td data-th='Document Preview'>
                        <img src="<?php echo htmlspecialchars($event['event_poster']); ?>" alt="Document Preview" class="img-preview" onclick="toggleImageSize(this)">
                    </td>
                    <td data-th='Action'>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $event['_id']; ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $event['_id']; ?>">
                            <input type="text" name="reason" placeholder="Reason for rejection" style="margin-top: 10px; display:block; color: red;" required>
                            <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table><br><br>
</div>

<div class="container mt-5">
    <h1>Manage Approved Events</h1><br>
    <table class="rwd-table">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Description</th>
                <th>Date</th>
                <th>Live/Hold</th>
                <th>View Registrations</th>
                <th>Delete Event</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($approved_events as $event): ?>
                <tr>
                    <td data-th='Event Name' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td data-th='Description' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_desc']); ?></td>
                    <td data-th='Date' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_date']); ?></td>
                    <td data-th='Action'>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="event_id" value="<?php echo $event['_id']; ?>">
                            <?php if ($event['status'] === 'live'): ?>
                                <button type="submit" name="action" value="make_hold" class="btn btn-danger">Make it hold</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="make_live" class="btn btn-success">Make it live</button>
                            <?php endif; ?>
                        </form>
                    </td>
                    <td data-th='View Registrations'>
                        <form method="POST" style="display:inline;" action="manage_registerations.php">
                            <input type="hidden" name="event_id" value="<?php echo $event['_id']; ?>">
                            <button type="submit" name="action" value="" class="btn btn-success">View Registrations</button>    
                        </form>
                    </td>
                    <td data-th='Delete'>
                        <form method="POST" style="display:inline;" action="delete_event.php" onsubmit="confirmDelete(event)">
                            <input type="hidden" name="event_id" value="<?php echo $event['_id']; ?>">
                            <button type="submit" name="action" value="" class="btn btn-danger">Delete Event</button>    
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
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