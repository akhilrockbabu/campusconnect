<?php

require '../vendor/autoload.php';

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
$participants = [];
$participants = $participantsCollection->find(['event_id' => new ObjectId($_POST['event_id'])])->toArray();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Registrations</title>
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
    <h1>Manage Registrations</h1><br>
    <br><br>
    <?php if (!empty($participants)): ?>
        <table class="rwd-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $participant): ?>
                    <tr>
                        <td data-th='Name'><?php echo htmlspecialchars($participant['name']); ?></td>
                        <td data-th='Email'><?php echo htmlspecialchars($participant['email']); ?></td>
                        <?php if($participant['status']=='approved'): ?>
                            <td data-th='Status' style='color:lightgreen'>Approved</td>
                        <?php elseif($participant['status']=='pending'): ?>
                            <td data-th='Status' style='color:orange'>Pending</td>
                        <?php else: ?>
                            <td data-th='Status' style='color:red'>Rejected</td>
                        <?php endif; ?>
                        <td data-th='Action'>
                            <form method="POST" style="display:inline;" action="viewRegData.php">
                                <input type="hidden" name="participant_id" value="<?php echo htmlspecialchars($participant['_id']); ?>">
                                <button type="submit" class="btn btn-success">View Details</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table><br><br>
    <?php else: ?>
        <p>No participants found for this event.</p>
    <?php endif; ?>
    <form method="POST" action="generate_reg_report.php" style="display:inline;">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($_POST['event_id']); ?>">
        <button type="submit" class="btn btn-primary" style="background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 20px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease;">Generate Participants Report</button><br><br>
    </form>
    <form method="POST" action="generate_inc_report.php" style="display:inline;">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($_POST['event_id']); ?>">
        <button type="submit" class="btn btn-primary" style="background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 20px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease;">Generate Income Report</button><br><br>
    </form>

    <button type="button" onclick="window.location.href='approved_events.php'" style="background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 20px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease;">Back</button>

</div>

<script>
    function toggleImageSize(imgElement) {
        imgElement.classList.toggle('large');
    }
</script>
</body>
</html>