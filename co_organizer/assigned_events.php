<?php

require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'co-organizer') {
    header("Location: ../log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;
$participantsCollection = $db->participants;

// Fetch the logged-in user's username
$organizerUsername = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'];
    $action = $_POST['action'];

    // Update the event status based on the action
    if ($action === 'make_live') {
        $eventsCollection->updateOne(['_id' => new ObjectId($eventId)], ['$set' => ['status' => 'live']]);
    } elseif ($action === 'make_hold') {
        $eventsCollection->updateOne(['_id' => new ObjectId($eventId)], ['$set' => ['status' => 'hold']]);
    }
}

// Fetch all approved, hold, or live events for the logged-in co-organizer
$events = $eventsCollection->find([
    'co-organizers' => $organizerUsername,
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
    <h1>Assigned Events</h1><br>
    <table class="rwd-table">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Description</th>
                <th>Date</th>
                <th>View Registrations</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td data-th='Event Name' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td data-th='Description' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_desc']); ?></td>
                    <td data-th='Date' style="width: auto; white-space: nowrap;"><?php echo htmlspecialchars($event['event_date']); ?></td>
                    <td data-th='View Registrations'>
                        <form method="POST" style="display:inline;" action="manage_registerations.php">
                            <input type="hidden" name="event_id" value="<?php echo $event['_id']; ?>">
                            <button type="submit" name="action" value="" class="btn btn-success">View Registrations</button>    
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table><br><br>
    <button type="button" onclick="window.location.href='coOrganizer2002.php'" style="background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 20px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease;">Back</button>
</div>
</body>
</html>