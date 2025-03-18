<?php

require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$participantsCollection = $db->participants;
$eventsCollection = $db->events;

$email = $_POST['email'] ?? $_GET['email'] ?? '';

$participants = [];
if ($email) {
    $participants = $participantsCollection->find(['email' => $email])->toArray();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/table_style.css">
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

        .btn-enabled {
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-enabled:hover {
            background-color: darkgreen;
        }

        .btn-disabled {
            background-color: grey;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>View Status</h1><br>
    <?php if (!empty($participants)): ?>
        <table class="rwd-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Event Description</th>
                    <th>Event Venue</th>
                    <th>Event Date</th>
                    <th>Event Time</th>
                    <th>Status</th>
                    <th>View Ticket</th>
                    <th>Submit Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $participant): ?>
                    <?php
                    $event = $eventsCollection->findOne(['_id' => new ObjectId($participant['event_id'])]);
                    ?>
                    <tr>
                        <td data-th='Event Name'><?php echo htmlspecialchars($event['event_name']); ?></td>
                        <td data-th='Event Description'><?php echo htmlspecialchars($event['event_desc']); ?></td>
                        <td data-th='Event Venue'><?php echo htmlspecialchars($event['event_venue']); ?></td>
                        <td data-th='Event Date'><?php echo htmlspecialchars($event['event_date']); ?></td>
                        <td data-th='Event Time'><?php echo DateTime::createFromFormat('H:i', $event['event_time'])->format('h:i A'); ?></td>
                        <?php if($participant['status']=='approved'): ?>
                            <td data-th='Status' style='color:lightgreen'>Approved</td>
                        <?php elseif($participant['status']=='pending'): ?>
                            <td data-th='Status' style='color:orange'>Pending</td>
                        <?php else: ?>
                            <td data-th='Status' style='color:red'>Rejected</td>
                        <?php endif; ?>
                        <?php if($participant['status']=='approved'){?>
                        <td data-th='View Ticket'>
                            <form action="viewTicket.php" method="post" target="_blank">
                                <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>">
                                <input type="hidden" name="event_desc" value="<?php echo htmlspecialchars($event['event_desc']); ?>">
                                <input type="hidden" name="event_venue" value="<?php echo htmlspecialchars($event['event_venue']); ?>">
                                <input type="hidden" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>">
                                <input type="hidden" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>">
                                <input type="hidden" name="participant_name" value="<?php echo htmlspecialchars($participant['name']); ?>">
                                <input type="hidden" name="participant_id" value="<?php echo htmlspecialchars($participant['_id']); ?>">
                                <button type="submit" class="btn btn-enabled">View Ticket</button>
                            </form>
                        </td>
                        <?php }
                        else{?>
                            <td data-th='View Ticket'>
                                <button type="button" class="btn btn-disabled" disabled>View Ticket</button>
                            </td>
                        <?php }
                        if($participant['status']=='approved'){ ?>
                        <td data-th='Submit Feedback'>
                            <form action="feedback.php" method="post">
                                <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>">
                                <input type="hidden" name="participant_name" value="<?php echo htmlspecialchars($participant['name']); ?>">
                                <input type="hidden" name="participant_email" value="<?php echo htmlspecialchars($participant['email']); ?>">
                                <button type="submit" class="btn btn-enabled">Submit Feedback</button>
                            </form>
                        </td>
                        <?php }
                        else{?>
                            <td data-th='Submit Feedback'>
                                <button type="button" class="btn btn-disabled" disabled>Submit Feedback</button>
                            </td>
                        <?php }
                        ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table><br><br>
    <?php else: ?>
        <p>No Registrations found for this email.</p>
    <?php endif; ?>

    <button type="button" onclick="window.location.href='checkstatus.php'" class="btn btn-primary">Back</button>

</div>

<script>
    function toggleImageSize(imgElement) {
        imgElement.classList.toggle('large');
    }
</script>
</body>
</html>