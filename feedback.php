<?php

require 'vendor/autoload.php';

use MongoDB\Client;

session_start();

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$feedbackCollection = $db->feedback;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['event_name'] ?? '';
    $participantName = $_POST['participant_name'] ?? '';
    $participantEmail = $_POST['participant_email'] ?? '';
    $feedbackText = $_POST['feedback_text'] ?? '';
    $rating = $_POST['rating'] ?? 0;

    if (!empty($eventName) && !empty($participantName) && !empty($participantEmail) && !empty($feedbackText) && $rating > 0) {
        $feedbackDocument = [
            'event_name' => $eventName,
            'participant_name' => $participantName,
            'participant_email' => $participantEmail,
            'feedback_text' => $feedbackText,
            'rating' => intval($rating),
            'submitted_at' => new MongoDB\BSON\UTCDateTime()
        ];

        $feedbackCollection->insertOne($feedbackDocument);

        echo "<script>alert('Feedback submitted successfully.'); window.location.href = 'viewStatus.php?email=" . urlencode($participantEmail) . "';</script>";
        exit();
    } else {
        echo "<script>alert('Please fill in all fields and select a rating.');</script>";
    }
} else {
    $eventName = $_POST['event_name'] ?? '';
    $participantName = $_POST['participant_name'] ?? '';
    $participantEmail = $_POST['participant_email'] ?? '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Feedback</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star-rating {
            direction: rtl;
            display: inline-block;
            padding: 20px;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            color: #bbb;
            font-size: 45px; /* Increased font size */
            padding: 0;
            cursor: pointer;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input[type="radio"]:checked ~ label {
            color: #f2b600;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Submit Feedback</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="event_name">Event Name</label>
            <input type="text" class="form-control" id="event_name" name="event_name" value="<?php echo htmlspecialchars($eventName); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="participant_name">Participant Name</label>
            <input type="text" class="form-control" id="participant_name" name="participant_name" value="<?php echo htmlspecialchars($participantName); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="participant_email">Participant Email</label>
            <input type="email" class="form-control" id="participant_email" name="participant_email" value="<?php echo htmlspecialchars($participantEmail); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="feedback_text">Feedback</label>
            <textarea class="form-control" id="feedback_text" name="feedback_text" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="rating">Rating</label>
            <div class="star-rating">
                <input type="radio" id="5-stars" name="rating" value="5"><label for="5-stars" class="star">&#9733;</label>
                <input type="radio" id="4-stars" name="rating" value="4"><label for="4-stars" class="star">&#9733;</label>
                <input type="radio" id="3-stars" name="rating" value="3"><label for="3-stars" class="star">&#9733;</label>
                <input type="radio" id="2-stars" name="rating" value="2"><label for="2-stars" class="star">&#9733;</label>
                <input type="radio" id="1-stars" name="rating" value="1"><label for="1-stars" class="star">&#9733;</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>