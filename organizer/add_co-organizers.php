<?php

require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'organizer') {
    header("Location: log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$coOrganizersCollection = $db->co_organizers;
$organizerCollection = $db->organizers;
$usersCollection = $db->users;
$eventsCollection = $db->events;
$organizer = $organizerCollection->findOne(['username' => $_SESSION['username']]);
$organizerEmail = $organizer['email'];
$eventId = $_POST['event_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_co_organizer'])) {
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    $errorMessages = [];

    // Validate email
    if (empty($email)) {
        $errorMessages[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email format.";
    }

    // Validate name
    if (empty($name)) {
        $errorMessages[] = "Name is required.";
    }

    // Validate phone
    if (empty($phone)) {
        $errorMessages[] = "Phone number is required.";
    } elseif (strlen($phone) !== 10) {
        $errorMessages[] = "Phone number must be 10 digits.";
    }

    if (empty($errorMessages)) {
        // Generate random username and password
        $username = bin2hex(random_bytes(5)); // 10 characters
        $password = bin2hex(random_bytes(4)); // 8 characters

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into co_organizers collection
        $coOrganizersCollection->insertOne([
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);

        // Insert into users collection
        $usersCollection->insertOne([
            'username' => $username,
            'role' => 'co-organizer',
            'password' => $hashedPassword
        ]);

        // Update events collection to add co-organizer
        $eventsCollection->updateOne(
            ['_id' => new ObjectId($eventId)],
            ['$addToSet' => ['co-organizers' => $username]]
        );

        // Fetch event name
        $event = $eventsCollection->findOne(['_id' => new ObjectId($eventId)]);
        $eventName = $event['event_name'];

        // Send email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'campusconnect.events@gmail.com'; // SMTP username
            $mail->Password = 'cuut pyiw rrqh feub'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom($organizerEmail, 'Campus Connect');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'You have been added as a Co-Organizer';
            $mail->Body    = "You have been added as a co-organizer for the event $eventName. <br> Login credentials: <br> Username: $username <br> Password: $password";

            $mail->send();
            echo "<script>alert('Co-Organizer added successfully and email sent.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Co-Organizer added successfully but email could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('" . implode("\\n", $errorMessages) . "');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_existing_co_organizer'])) {
    $selectedUsername = trim($_POST['existing_co_organizer']);

    // Fetch co-organizer details
    $coOrganizer = $coOrganizersCollection->findOne(['username' => $selectedUsername]);
    $coOrganizerEmail = $coOrganizer['email'];

    // Update events collection to add co-organizer
    $eventsCollection->updateOne(
        ['_id' => new ObjectId($eventId)],
        ['$addToSet' => ['co-organizers' => $selectedUsername]]
    );

    // Fetch event name
    $event = $eventsCollection->findOne(['_id' => new ObjectId($eventId)]);
    $eventName = $event['event_name'];

    // Send email
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'campusconnect.events@gmail.com'; // SMTP username
        $mail->Password = 'cuut pyiw rrqh feub'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom($organizerEmail, 'Campus Connect');
        $mail->addAddress($coOrganizerEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'You have been added as a Co-Organizer';
        $mail->Body    = "You have been added as a co-organizer for the event $eventName.";

        $mail->send();
        echo "<script>alert('Existing Co-Organizer added successfully and email sent.');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Existing Co-Organizer added successfully but email could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Co-Organizers</title>
    <style>
        body {
            background-color: #008060;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        input, button, select {
            width: 75%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>
<script>
function validateForm() {
    const email = document.getElementById("email").value.trim();
    const name = document.getElementById("name").value.trim();
    const phone = document.getElementById("phone").value.trim();

    let errorMessages = [];

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        errorMessages.push("Email is required.");
    } else if (!emailPattern.test(email)) {
        errorMessages.push("Invalid email format.");
    }

    if (!name) {
        errorMessages.push("Name is required.");
    }

    if (!phone) {
        errorMessages.push("Phone number is required.");
    } else if (phone.length !== 10) {
        errorMessages.push("Phone number must be 10 digits.");
    }

    if (errorMessages.length > 0) {
        alert(errorMessages.join("\n"));
        return false;
    }

    return true;
}
</script>

<div class="container">
    <h2>Add New Co-Organizer</h2>
    <form id="signupForm" action="" method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="add_co_organizer" value="1">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
        <input type="text" id="email" name="email" placeholder="Enter the email">
        <input type="text" id="name" name="name" placeholder="Enter the Name">
        <input type="number" id="phone" name="phone" placeholder="Enter the Phone Number">
        <button type="submit">Add Co-Organizer</button>
    </form>
    <br>
    <h2>Add Existing Co-Organizer</h2>
    <form id="existingCoOrganizerForm" action="" method="POST">
        <input type="hidden" name="add_existing_co_organizer" value="1">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
        <select name="existing_co_organizer" id="existing_co_organizer">
            <?php
            $coOrganizers = $coOrganizersCollection->find();
            foreach ($coOrganizers as $coOrganizer) {
                echo '<option value="' . htmlspecialchars($coOrganizer['username']) . '">' . htmlspecialchars($coOrganizer['username']) . '</option>';
            }
            ?>
        </select>
        <button type="submit">Add Existing Co-Organizer</button>
    </form>
    <br>
    <a href="approved_events.php"><button>Back</button></a>
</div>

</body>
</html>
