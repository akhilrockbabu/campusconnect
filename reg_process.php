<?php

require 'vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$collection = $db->users;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $email = $_POST['email'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $institution = $_POST['institution_name'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    if (isset($_FILES['InstitutionID']) && $_FILES['InstitutionID']['error'] == 0)
    {
        $fileTmpPath = $_FILES['InstitutionID']['tmp_name'];
        $fileName = $_FILES['InstitutionID']['name'];
        $fileSize = $_FILES['InstitutionID']['size'];
        $fileType = $_FILES['InstitutionID']['type'];

        $allowedTypes = ['image/jpeg', 'image/png' , 'image/jpg'];

        if (in_array($fileType, $allowedTypes) && $fileSize < 5000000)
        {
            $uploadDir = 'uploads/institution_ID/';
            if (!is_dir($uploadDir))
            {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = uniqid('img_') . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destination))
            {
                $imagePath = $destination;
            } 
            else
            {
                echo "<script>alert('Error uploading file.'); window.location.href = 'log_reg.html';</script>";
                exit;
            }
        }
        else
        {
            echo "<script>alert('Invalid file type or file size exceeds limit.'); window.location.href = 'log_reg.html';</script>";
            exit;
        }
    }
    else
    {
        echo "<script>alert('Please upload a profile image.'); window.location.href = 'log_reg.html';</script>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $userData = [
        'email' => $email,
        'username' => $username,
        'role' => 'organizer',
        'name' => $name,
        'institution_name' => $institution,
        'phone' => $phone,
        'password' => $hashedPassword,
        'Institution_img' => $imagePath,
        'status' => 'pending'
    ];

    $insertResult = $collection->insertOne($userData);

    if ($insertResult->getInsertedCount() == 1)
    {
        echo "<script>alert('Signup successful!'); window.location.href = 'log_reg.html';</script>";
    }
    else
    {
        echo "<script>alert('There was an error during signup.'); window.location.href = 'log_reg.html';</script>";
    }
}
else
{
    echo "<script>alert('Invalid request method.'); window.location.href = 'log_reg.html';</script>";
}
?>
