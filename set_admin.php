<?php
require 'vendor/autoload.php';

use MongoDB\Client;

function addAdminToUsers($username, $password, $email) {
    $client = new Client("mongodb://localhost:27017");
    $db = $client->campusconnect;
    $collection = $db->users;

    $collection->createIndex(['username' => 1], ['unique' => true]);

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $adminData = [
        'username' => $username,
        'email' => $email,
        'role' => 'admin',
        'password' => $hashedPassword,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    try {
        $result = $collection->insertOne($adminData);
        echo "Admin added successfully with ID: " . $result->getInsertedId();
    } catch (MongoDB\Driver\Exception\DuplicateKeyException $e) {
        echo "Error: Username '" . $username . "' already exists!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$username = 'admin';
$password = 'hacker@2025';
$email = 'akhilrockbabu@gmail.com';
addAdminToUsers($username, $password, $email);
?>
