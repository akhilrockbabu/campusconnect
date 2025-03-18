<?php

require '../vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'organizer') {
    header("Location: ../log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;
$coOrganizersCollection = $db->co_organizers;

$eventId = $_POST['event_id'] ?? $_GET['event_id'] ?? '';
if (empty($eventId)) {
    echo "<script>alert('Event ID is missing.'); window.location.href = 'approved_events.php';</script>";
    exit();
}

$event = $eventsCollection->findOne(['_id' => new ObjectId($eventId)]);
if (!$event) {
    echo "<script>alert('Event not found.'); window.location.href = 'approved_events.php';</script>";
    exit();
}
$coOrganizers = $event['co-organizers'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_co_organizer'])) {
    $coOrganizerUsername = $_POST['co_organizer_username'];
    
    $eventsCollection->updateOne(
        ['_id' => new ObjectId($eventId)],
        ['$pull' => ['co-organizers' => $coOrganizerUsername]]
    );
    
    $coOrganizer = $coOrganizersCollection->findOne(['username' => $coOrganizerUsername]);
    $coOrganizerEmail = $coOrganizer['email'] ?? '';
    $eventName = $event['event_name'];
    
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
        $mail->addAddress($coOrganizerEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Removal of Co-Organizer Role';
        $mail->Body = "You have been removed as a co-organizer for the event $eventName.";

        $mail->send();
        echo "<script>alert('Co-Organizer removed successfully and email sent.'); window.location.href = 'remove_co-organizers.php?event_id=$eventId';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Co-Organizer removed successfully but email could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.location.href = 'remove_co-organizers.php?event_id=$eventId';</script>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Co-Organizers</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Co-Organizers for Event: <?php echo htmlspecialchars($event['event_name'] ?? ''); ?></h2>
    <table class="rwd-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coOrganizers as $coOrganizerUsername): ?>
                <?php 
                $coOrganizer = $coOrganizersCollection->findOne(['username' => $coOrganizerUsername]);
                $coOrganizerName = $coOrganizer['name'] ?? 'Unknown';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($coOrganizerUsername); ?></td>
                    <td><?php echo htmlspecialchars($coOrganizerName); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
                            <input type="hidden" name="co_organizer_username" value="<?php echo htmlspecialchars($coOrganizerUsername); ?>">
                            <button type="submit" name="remove_co_organizer" class="btn btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table><br>
    <button onclick="window.location.href='approved_events.php'" class="btn btn-primary">Back</button>
</div>
</body>
</html>
