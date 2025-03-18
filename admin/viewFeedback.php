<?php

require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$feedbackCollection = $db->feedback;

$feedbacks = $feedbackCollection->find()->toArray();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/table_style.css">
    <style>
        .star-rating {
            display: inline-block;
        }
        .star-rating .star {
            font-size: 24px;
            color: grey;
        }
        .star-rating .star.checked {
            color: #f2b600;
        }
        .feedback-text {
            text-align: justify;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Feedbacks</h1><br>
    <?php if (!empty($feedbacks)): ?>
        <table class="rwd-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Participant Name</th>
                    <th>Participant Email</th>
                    <th>Feedback</th>
                    <th>Rating</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                    <tr>
                        <td data-th='Event Name'><?php echo htmlspecialchars($feedback['event_name']); ?></td>
                        <td data-th='Participant Name'><?php echo htmlspecialchars($feedback['participant_name']); ?></td>
                        <td data-th='Participant Email'><?php echo htmlspecialchars($feedback['participant_email']); ?></td>
                        <td data-th='Feedback' class="feedback-text"><?php echo htmlspecialchars($feedback['feedback_text']); ?></td>
                        <td data-th='Rating'>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo $i <= $feedback['rating'] ? 'checked' : ''; ?>">&#9733;</span>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td data-th='Submitted At'><?php echo htmlspecialchars($feedback['submitted_at']->toDateTime()->format('d-m-Y H:i')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table><br><br>
    <?php else: ?>
        <p>No Feedbacks Found.</p>
    <?php endif; ?>

    <button type="button" onclick="window.location.href='admin6096.php'" class="btn btn-primary">Back</button>

</div>

<script>
    function toggleImageSize(imgElement) {
        imgElement.classList.toggle('large');
    }
</script>
</body>
</html>