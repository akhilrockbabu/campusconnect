<?php

require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\Driver\Exception\Exception;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$usersCollection = $db->users;
$organizersCollection = $db->organizers;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $imagePath = '';

    // Handle file upload
    if (isset($_FILES['InstitutionID']) && $_FILES['InstitutionID']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['InstitutionID']['type'];
        $fileSize = $_FILES['InstitutionID']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= 5000000) { // 5MB limit
            $uploadDir = 'uploads/institution_ID/';
            $fileExtension = pathinfo($_FILES['InstitutionID']['name'], PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $imagePath = $uploadDir . $uniqueFileName;

            if (!move_uploaded_file($_FILES['InstitutionID']['tmp_name'], $imagePath)) {
                echo "<script>alert('File upload failed.'); window.location.href = 'log_reg.html';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid file type or file size exceeds limit.'); window.location.href = 'log_reg.html';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please upload a profile image.'); window.location.href = 'log_reg.html';</script>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into users collection
    $userData = [
        'username' => $username,
        'password' => $hashedPassword,
        'role' => 'organizer',
        'status' => 'pending'
    ];

    // Insert into organizers collection
    $organizerData = [
        'username' => $username,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'department' => $department,
        'InstitutionID' => $imagePath
    ];

    try {
        $serverStatusCursor = $client->admin->command(['serverStatus' => 1]);
        $serverStatus = current($serverStatusCursor->toArray());
        $isReplicaSet = isset($serverStatus['repl']) && $serverStatus['repl']['setName'];

        if ($isReplicaSet) {
            $session = $client->startSession();
            $session->startTransaction();

            try {
                $usersResult = $usersCollection->insertOne($userData, ['session' => $session]);
                $organizersResult = $organizersCollection->insertOne($organizerData, ['session' => $session]);

                if ($usersResult->getInsertedCount() === 1 && $organizersResult->getInsertedCount() === 1) {
                    $session->commitTransaction();
                    echo "<script>alert('Registration successful.'); window.location.href = 'log_reg.html';</script>";
                } else {
                    $session->abortTransaction();
                    echo "<script>alert('Registration failed.'); window.location.href = 'log_reg.html';</script>";
                }
            } catch (MongoDB\Driver\Exception\DuplicateKeyException $e) {
                $session->abortTransaction();
                echo "<script>alert('Username or Email already exists!'); window.location.href = 'log_reg.html';</script>";
            } catch (Exception $e) {
                $session->abortTransaction();
                echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = 'log_reg.html';</script>";
            } finally {
                $session->endSession();
            }
        } else {
            // Non-transactional approach for standalone MongoDB servers
            $usersResult = $usersCollection->insertOne($userData);
            $organizersResult = $organizersCollection->insertOne($organizerData);

            if ($usersResult->getInsertedCount() === 1 && $organizersResult->getInsertedCount() === 1) {
                echo "<script>alert('Registration successful.'); window.location.href = 'log_reg.html';</script>";
            } else {
                echo "<script>alert('Registration failed.'); window.location.href = 'log_reg.html';</script>";
            }
        }
    } catch (MongoDB\Driver\Exception\DuplicateKeyException $e) {
        echo "<script>alert('Username or Email already exists!'); window.location.href = 'log_reg.html';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = 'log_reg.html';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.location.href = 'log_reg.html';</script>";
}
?>
