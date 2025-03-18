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
$eventsCollection = $db->events;
$participantsCollection = $db->participants;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'];

    // Delete the event from the events collection
    $eventsCollection->deleteOne(['_id' => new ObjectId($eventId)]);

    // Delete the participants associated with the event from the participants collection
    $participantsCollection->deleteMany(['event_id' => new ObjectId($eventId)]);

    // Redirect back to the approved events page
    header("Location: approved_events.php");
    exit();
}
?>