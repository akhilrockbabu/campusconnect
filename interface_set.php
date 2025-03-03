<?php
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$interfaceCollection = $db->interface;

$interfaceData = [
    "_id" => new ObjectId(),
    "page" => "home",
    "program_date" => [
        "value" => "28 Feb - 3 Mar,2025",
        "font" => "Times New Roman",
        "color" => "#FF5733"
    ],
    "program_name1" => [
        "value" => "MACE",
        "font" => "Verdana",
        "color" => "#008000"
    ],
    "program_name2" => [
        "value" => "Takshak 2025",
        "font" => "Georgia",
        "color" => "#0000FF"
    ],
    "college_name" => [
        "value" => "Mar Athanasius College of Engineering",
        "font" => "Tahoma",
        "color" => "#800080"
    ],
    "about_program" => [
        "value" => "As Mar Athanasius College of Engineering rekindles the spirit of its r…",
        "font" => "Calibri",
        "color" => "#444444"
    ],
    "college_link" => [
        "value" => "https://www.mace.ac.in/",
        "font" => "Courier New",
        "color" => "#FF0000"
    ]
];

try {
    $result = $interfaceCollection->insertOne($interfaceData);
    echo "Document inserted successfully with ID: " . $result->getInsertedId();
} catch (MongoDB\Driver\Exception\DuplicateKeyException $e) {
    echo "Error: Duplicate key detected!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
