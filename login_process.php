<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$collection = $db->users;

function sendAlertAndRedirect($message) {
    echo "<script>
            alert('$message');
            window.location.href = 'log_reg.html';
          </script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    $user = $collection->findOne(['username' => $username]);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin6096.php");
                exit();
            } else {
                if($user['status'] === 'approved') {
                    header("Location: organizer81118.php");
                    exit();
                } else {
                    sendAlertAndRedirect("You are not approved yet! kindly be patient");
                }
            }
        } else {
            sendAlertAndRedirect("Invalid username or password.");
        }
    } else {
        sendAlertAndRedirect("Invalid username or password.");
    }
} else {
    sendAlertAndRedirect("Invalid request.");
}
