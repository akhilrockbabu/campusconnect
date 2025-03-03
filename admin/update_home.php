<?php
require '../vendor/autoload.php';
use MongoDB\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'];
    $value = $_POST['value'];
    $font = $_POST['font'];
    $color = $_POST['color'];
    $size = $_POST['size'];

    $client = new Client("mongodb://localhost:27017");
    $db = $client->campusconnect;
    $interfaceCollection = $db->interface;

    $updateResult = $interfaceCollection->updateOne(
        ['page' => 'home'],
        ['$set' => [
            "$field.value" => $value,
            "$field.font" => $font,
            "$field.color" => $color,
            "$field.size" => $size
        ]]
    );

    if ($updateResult->getModifiedCount() > 0) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>