<?php

require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'organizer') {
    header("Location: log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$coOrganizersCollection = $db->co_organizers;
$usersCollection = $db->users;
$eventsCollection = $db->events;
$eventId = $_POST['event_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_co_organizer'])) {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    $errorMessages = [];

    // Validate email
    if (empty($email)) {
        $errorMessages[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email format.";
    }

    // Validate username
    if (empty($username)) {
        $errorMessages[] = "Username is required.";
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

    // Validate password
    if (empty($password)) {
        $errorMessages[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errorMessages[] = "Password must be at least 8 characters.";
    }

    // Validate confirm password
    if ($password !== $confirmPassword) {
        $errorMessages[] = "Passwords do not match.";
    }

    if (empty($errorMessages)) {
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

        echo "<script>alert('Co-Organizer added successfully.');</script>";
    } else {
        echo "<script>alert('" . implode("\\n", $errorMessages) . "');</script>";
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
        input, button {
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
    const username = document.getElementById("username").value.trim();
    const name = document.getElementById("name").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();

    let errorMessages = [];

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        errorMessages.push("Email is required.");
    } else if (!emailPattern.test(email)) {
        errorMessages.push("Invalid email format.");
    }

    if (!username) {
        errorMessages.push("Username is required.");
    }

    if (!name) {
        errorMessages.push("Name is required.");
    }

    if (!phone) {
        errorMessages.push("Phone number is required.");
    } else if (phone.length !== 10) {
        errorMessages.push("Phone number must be 10 digits.");
    }

    if (!password) {
        errorMessages.push("Password is required.");
    } else if (password.length < 8) {
        errorMessages.push("Password must be at least 8 characters.");
    }

    if (password !== confirmPassword) {
        errorMessages.push("Passwords do not match.");
    }

    if (errorMessages.length > 0) {
        alert(errorMessages.join("\n"));
        return false;
    }

    return true;
}
</script>

<div class="container">
    <h2>Add Co-Organizer</h2>
    <form id="signupForm" action="" method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="add_co_organizer" value="1">
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
        <input type="text" id="email" name="email" placeholder="Enter the email">
        <input type="text" id="username" name="username" placeholder="Create a username">
        <input type="text" id="name" name="name" placeholder="Enter the Name">
        <input type="number" id="phone" name="phone" placeholder="Enter the Phone Number">
        <input type="password" id="password" name="password" placeholder="Create a password">
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm the password">
        <button type="submit">Add Co-Organizer</button>
    </form>
    <br>
    <a href="approved_events.php"><button>Back</button></a>
</div>

</body>
</html>
