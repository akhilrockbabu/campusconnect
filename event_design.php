<?php

require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;

// Fetch the event details
$eventId = $_GET['event_id'] ?? '';
$event = $eventsCollection->findOne(['_id' => new ObjectId($eventId)]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update event details
    $banner = $_FILES['banner']['name'];
    $poster = $_FILES['poster']['name'];

    // Move uploaded files to a directory
    move_uploaded_file($_FILES['banner']['tmp_name'], "uploads/$banner");
    move_uploaded_file($_FILES['poster']['tmp_name'], "uploads/$poster");

    // Update event details
    $updateData = [
        'banner' => $banner,
        'poster' => $poster,
        'event_name' => $_POST['event_name'],
        'event_desc' => $_POST['event_desc'],
        'event_rules' => $_POST['event_rules'],
        'event_date' => $_POST['event_date'],
        'event_time' => $_POST['event_time'],
        'event_venue' => $_POST['event_venue'],
        'registration_fees' => $_POST['registration_fees'],
        'upi_id' => $_POST['upi_id']
    ];

    $eventsCollection->updateOne(['_id' => new ObjectId($eventId)], ['$set' => $updateData]);

    header("Location: event_design.php?event_id=$eventId");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Design</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .img-preview {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        .img-preview.large {
            width: 100%;
            height: 300px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Event Design</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="banner">Event Banner</label>
            <input type="file" class="form-control" id="banner" name="banner" required>
            <?php if (isset($event['banner'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($event['banner']); ?>" class="img-preview large" alt="Event Banner">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="poster">Event Poster</label>
            <input type="file" class="form-control" id="poster" name="poster" required>
            <?php if (isset($event['poster'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($event['poster']); ?>" class="img-preview" alt="Event Poster">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="event_name">Event Name</label>
            <input type="text" class="form-control" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="event_desc">Event Description</label>
            <textarea class="form-control" id="event_desc" name="event_desc" required><?php echo htmlspecialchars($event['event_desc']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="event_rules">Event Rules</label>
            <textarea class="form-control" id="event_rules" name="event_rules" required><?php echo htmlspecialchars($event['event_rules']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="event_date">Event Date</label>
            <input type="date" class="form-control" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
        </div>
        <div class="form-group">
            <label for="event_time">Event Time</label>
            <input type="time" class="form-control" id="event_time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
        </div>
        <div class="form-group">
            <label for="event_venue">Event Venue</label>
            <input type="text" class="form-control" id="event_venue" name="event_venue" value="<?php echo htmlspecialchars($event['event_venue']); ?>" required>
        </div>
        <div class="form-group">
            <label for="registration_fees">Registration Fees</label>
            <input type="number" class="form-control" id="registration_fees" name="registration_fees" value="<?php echo htmlspecialchars($event['registration_fees']); ?>" required>
        </div>
        <div class="form-group">
            <label for="upi_id">UPI ID</label>
            <input type="text" class="form-control" id="upi_id" name="upi_id" value="<?php echo htmlspecialchars($event['upi_id']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
</body>
</html>