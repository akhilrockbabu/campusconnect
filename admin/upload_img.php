<?php
require '../vendor/autoload.php';
use MongoDB\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imgField = $_POST['imgField'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        $uploadFileDir = '../uploads/home_img/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $client = new Client("mongodb://localhost:27017");
            $db = $client->campusconnect;
            $interfaceCollection = $db->interface;

            $updateResult = $interfaceCollection->updateOne(
                ['page' => 'home'],
                ['$set' => [
                    "$imgField" => $dest_path
                ]]
            );

            if ($updateResult->getModifiedCount() > 0) {
                echo 'success';
            } else {
                echo 'error';
            }
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
}
?>