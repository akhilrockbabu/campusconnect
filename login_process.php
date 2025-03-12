<?php
require 'vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$collection = $client->campusconnect->users;

function sendAlertAndRedirect($message)
{
    echo "<script>alert('$message'); window.location.href='log_reg.html';</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    $user = $collection->findOne(['username' => $username]);

    if ($user)
    {
        if (password_verify($password, $user['password']))
        {
            session_start();
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin')
            {
                header("Location: admin/admin6096.php");
                exit();
            } 
            elseif ($user['role'] === 'organizer')
            {
                if ($user['status'] === 'approved')
                {
                    header("Location: organizer/organizer81118.php");
                    exit();
                } else
                {
                    sendAlertAndRedirect("You are not approved yet ! You will recieve an email once approved. Kindly be patient.");
                }
            } 
            elseif ($user['role'] === 'co-organizer')
            {
                header("Location: co_organizer/coOrganizer2002.php");
                exit();
            } 
            else 
            {
                sendAlertAndRedirect("Invalid role.");
            }
        } 
        else 
        {
            sendAlertAndRedirect("Invalid username or password.");
        }
    } 
    else 
    {
        sendAlertAndRedirect("Invalid username or password.");
    }
}
?>
