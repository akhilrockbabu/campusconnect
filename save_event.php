<?php
session_start();
require 'vendor/autoload.php'; // Include Composer's autoloader

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'organizer') {
    header("Location: log_reg.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Received Data</h2>";
    echo "<pre>";
    print_r($_POST); // Print the contents of the $_POST array
    echo "</pre>";

    // Decode the form data
    $formData = json_decode($_POST['form_data'], true);
    $processedFormData = [];

    // Process the form data to remove field_label_X and use the actual labels
    foreach ($formData as $key => $value) {
        if (strpos($key, 'field_label_') === 0) {
            $processedFormData[$value['label']] = $value['type'];
        }
    }

    // Prepare the event document
    $eventDocument = [
        'event_name' => $_POST['event_name'],
        'event_desc' => $_POST['event_desc'],
        'event_rules' => $_POST['event_rules'],
        'event_date' => $_POST['event_date'],
        'event_time' => $_POST['event_time'],
        'event_venue' => $_POST['event_venue'],
        'registration_fees' => $_POST['registration_fees'],
        'upi_id' => $_POST['upi_id'],
        'status' => 'pending',
        'event_organizer' => $_SESSION['username'],
        'form_data' => $processedFormData
    ];

    if (isset($_POST['event_coupon'])) {
        $eventDocument['event_coupon'] = $_POST['event_coupon'];
    }

    if (isset($_POST['event_discount'])) {
        $eventDocument['event_discount'] = $_POST['event_discount'];
    }

    try {
        $insertResult = $eventsCollection->insertOne($eventDocument);

        if ($insertResult->getInsertedCount() === 1) {
            echo "<script>
                alert('Event and form data saved successfully!');
                window.location.href = 'event_design.php';
            </script>";
        } else {
            echo "<script>
                alert('Failed to save event and form data.');
                window.location.href = 'organizer81118.php';
            </script>";
        }
    } catch (Exception $e) {
        echo "<script>
            alert('Error: " . $e->getMessage() . "');
            window.location.href = 'organizer81118.php';
        </script>";
    }
}
?>