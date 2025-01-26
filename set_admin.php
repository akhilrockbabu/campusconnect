<?php
require 'vendor/autoload.php';

use MongoDB\Client;

function addAdminToUsers($username, $password, $name, $email) {
    $client = new Client("mongodb://localhost:27017");
    $db = $client->campusconnect;
    $usersCollection = $db->users;
    $adminCollection = $db->admin;

    // Create unique indexes
    $usersCollection->createIndex(['username' => 1], ['unique' => true]);
    $adminCollection->createIndex(['username' => 1], ['unique' => true]);
    $adminCollection->createIndex(['email' => 1], ['unique' => true]);

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $userData = [
        'username' => $username,
        'role' => 'admin',
        'password' => $hashedPassword,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $adminData = [
        'username' => $username,
        'name' => $name,
        'email' => $email,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    try {
        // Insert into users collection
        $usersResult = $usersCollection->insertOne($userData);
        echo "User added successfully with ID: " . $usersResult->getInsertedId() . "<br>";

        // Insert into admin collection
        $adminResult = $adminCollection->insertOne($adminData);
        echo "Admin added successfully with ID: " . $adminResult->getInsertedId();
    } catch (MongoDB\Driver\Exception\DuplicateKeyException $e) {
        echo "Error: Username or Email already exists!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Example usage
addAdminToUsers('admin', 'admin', 'Akhil Rock Babu', 'akhilrockbabu@gmail.com');
?>
