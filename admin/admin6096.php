<?php
session_start();
require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$adminCollection = $db->admin;
$eventsCollection = $db->events;
$participantsCollection = $db->participants;

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../log_reg.html");
    exit();
}
$username = $_SESSION['username'];
$admin = $adminCollection->findOne(['username' => $username]);

// Fetch all events
$events = $eventsCollection->find(['status' => ['$in' => ['approved','live','hold']]])->toArray();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #343a40;
        }

        .header {
            background-color: #343a40;
            color: white;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar {
            display: flex;
            gap: 20px;
        }

        .nav-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .nav-btn:hover {
            background-color: #0056b3;
        }

        .nav-btn:focus {
            outline: none;
        }

        .main-content {
            margin-top: 70px;
            padding: 20px;
        }

        .section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
        }

        .event_container{
            background-color: rgba(60, 50, 50, 0.7); /* Dark background with transparency */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .event-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .event-tile {
            background-color: rgba(255, 255, 255, 0.7); /* Dark background with transparency */
            color: black; /* White text */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex: 1 1 calc(33.333% - 40px); /* 3 tiles per row, accounting for margin */
            box-sizing: border-box;
            transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
        }

        .event-tile:hover {
            background-color: rgba(20, 129, 202, 0.7); /* White background with transparency */
            color: white; /* Black text */
        }

        .event-details {
            flex: 1;
        }

        .event-details h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .event-details p {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .event-stats {
            text-align: right;
        }

        .event-stats p {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .event-poster {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-left: 20px;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .navbar {
                margin-top: 10px;
                flex-direction: column;
                gap: 10px;
            }

            .nav-btn {
                width: 100%;
                padding: 12px;
                font-size: 18px;
            }

            .section {
                padding: 15px;
            }

            .event-tile {
                flex-direction: column;
                align-items: flex-start;
            }

            .event-stats {
                text-align: left;
                margin-top: 10px;
            }

            .event-poster {
                width: 100%;
                height: auto;
                margin-left: 0;
                margin-top: 10px;
            }
        }

    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>CampusConnect</h1>
            </div>
            <nav class="navbar">
                <a href="manage_organizers.php"><button class="nav-btn" id="dashboardBtn">Manage Organizers</button></a>
                <a href="manage_events.php"><button class="nav-btn" id="dashboardBtn">Manage Events</button></a>
                <a href="viewFeedback.php"><button class="nav-btn" id="dashboardBtn">View Feedbacks</button></a>
                <a href="customize_home.php"><button class="nav-btn" id="dashboardBtn">Customize Interface</button></a>
                <a href="updateProfile.php"><button class="nav-btn" id="dashboardBtn">Update Profile</button></a>
                <a href="../logout.php"><button class="nav-btn" id="dashboardBtn">Logout</button></a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <section class="section">
            <h2>Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h2>
            <p>Admin Dashboard</p>
            <p>Choose an option from the top navigation bar to get started.</p>
        </section>
        <?php if (count($events) != 0)
        { ?>
        <h1> Your Events </h1><br>
        <div class="event_container">
        <?php
        $count = 0;
        foreach ($events as $event):
            if ($count % 3 == 0): // Open a new row for every 3 events
        ?>
            <div class="event-row">
        <?php endif; ?>

                <?php
                $eventId = new ObjectId($event['_id']);
                $acceptedCount = $participantsCollection->countDocuments(['event_id' => $eventId, 'status' => 'approved']);
                $rejectedCount = $participantsCollection->countDocuments(['event_id' => $eventId, 'status' => 'rejected']);
                $totalCount = $participantsCollection->countDocuments(['event_id' => $eventId]);
                ?>
                <div class="event-tile">
                    <div class="event-details">
                        <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        <p><strong>Event Capacity Remaining:</strong> <?php echo htmlspecialchars($event['event_limit']); ?></p>
                        <p><strong>Total Participants Registered:</strong> <?php echo $totalCount; ?></p>
                        <p><strong>Participants Accepted:</strong> <?php echo $acceptedCount; ?></p>
                        <p><strong>Participants Rejected:</strong> <?php echo $rejectedCount; ?></p>
                    </div>
                    <div class="event-stats">
                        <?php if (!empty($event['event_poster'])): ?>
                            <img src="<?php echo htmlspecialchars($event['event_poster']); ?>" alt="Event Poster" class="event-poster">
                        <?php endif; ?>
                    </div>
                </div>

        <?php
            $count++;
            if ($count % 3 == 0 || $count == count($events)): // Close row after 3 events or last event
        ?>
            </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php } ?>
    </div>

    <script src="scripts.js"></script>
</body>
</html>
