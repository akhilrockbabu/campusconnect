<?php

require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../log_reg.html");
    exit();
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$usersCollection = $db->users;
$adminCollection = $db->admin;

$username = $_SESSION['username'];

// Fetch current user details
$user = $usersCollection->findOne(['username' => $username]);
$admin = $adminCollection->findOne(['username' => $username]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['password']);
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);

    $errorMessages = [];

    // Validate email
    if (empty($newEmail)) {
        $errorMessages[] = "Email is required.";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email format.";
    }

    // Validate username
    if (empty($newUsername)) {
        $errorMessages[] = "Username is required.";
    }

    // Validate name
    if (empty($newName)) {
        $errorMessages[] = "Name is required.";
    }

    // Validate new password if provided
    if (!empty($newPassword)) {
        if (empty($currentPassword)) {
            $errorMessages[] = "Current password is required to change the password.";
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $errorMessages[] = "Current password is incorrect.";
        } elseif (strlen($newPassword) < 8) {
            $errorMessages[] = "New password must be at least 8 characters.";
        }
    }

    if (empty($errorMessages)) {
    try{
        // Update users collection
        $updateUserFields = [
            'username' => $newUsername
        ];
        if (!empty($newPassword)) {
            $updateUserFields['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        $usersCollection->updateOne(
            ['username' => $username],
            ['$set' => $updateUserFields]
        );

        // Update admin collection
        $adminCollection->updateOne(
            ['username' => $username],
            ['$set' => [
                'username' => $newUsername,
                'name' => $newName,
                'email' => $newEmail
            ]]
        );

        // Destroy session and log out
        session_destroy();
        echo "<script>alert('Profile updated successfully. You will be logged out.'); window.location.href = '../log_reg.html';</script>";
        exit();
    }
    catch (DuplicateKeyException $e) {
        echo "<script>alert('Username or email already exists.');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Username or email already exists.');</script>";
    }
    } else {
        echo "<script>alert('" . implode("\\n", $errorMessages) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>CampusConnect</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="../css/event_reg.css" rel="stylesheet" media="all">
    <style>
        .body {
            background-color: #009579;
        }
        .btn-success {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            margin-left: 200px;
            margin-bottom: 2px;
        }
        .btn-success a {
            color: white;
            text-decoration: none;
        }
        .btn-success:hover {
            background-color: darkgreen;
        }
    </style>
</head>

<body>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                    <h2 class="title">Update Profile</h2>
                    <form method="POST" action="">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Username</label>
                                    <input class="input--style-4" type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Current Password (required to change password)</label>
                                    <input class="input--style-4" type="password" name="current_password">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">New Password</label>
                                    <input class="input--style-4" type="password" name="password">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Name</label>
                                    <input class="input--style-4" type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Email</label>
                                    <input class="input--style-4" type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="p-t-15">
                            <button class="btn btn--radius-2 btn--blue" type="submit">Update</button>
                            <button class="btn btn-success"><a href="admin6096.php">Back to Home</a></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
</body>

</html>