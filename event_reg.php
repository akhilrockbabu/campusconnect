<?php
require 'vendor/autoload.php';
use MongoDB\Client;

// Connect to MongoDB
$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;
$participantsCollection = $db->participants;

// Get the event_id from the GET parameters
$eventId = isset($_GET['event_id']) ? $_GET['event_id'] : '';

if ($eventId) {
    // Fetch the event from the events collection using the event_id
    $event = $eventsCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($eventId)]);
} else {
    echo "No event ID provided.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participantData = [
        'event_id' => new MongoDB\BSON\ObjectId($eventId),
        'status' => 'pending'
    ];

    // Collect all form fields
    foreach ($_POST as $key => $value) {
        if ($key !== 'event_id') {
            $participantData[$key] = $value;
        }
    }

    // Handle file upload
    if (isset($_FILES['InstitutionID']) && $_FILES['InstitutionID']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['InstitutionID']['type'];
        $fileSize = $_FILES['InstitutionID']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= 5000000) { // 5MB limit
            $uploadDir = 'uploads/parti_institution_ID/';
            $fileExtension = pathinfo($_FILES['InstitutionID']['name'], PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $imagePath = $uploadDir . $uniqueFileName;


            if (!move_uploaded_file($_FILES['InstitutionID']['tmp_name'], $imagePath)) {
                echo "<script>alert('File upload failed.'); window.location.href = 'event_reg.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid file type or file size exceeds limit.'); window.location.href = 'event_reg.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please upload a profile image.'); window.location.href = 'event_reg.php';</script>";
        exit;
    }
    $participantData['InstitutionID'] = $imagePath;

    // Insert participant data into the collection
    try {
        $participantsCollection->insertOne($participantData);
        echo "<script>alert('Registration successful!'); window.location.href = 'display_event.php?event_id=" . urlencode($eventId) . "';</script>";
    } catch (Exception $e) {
        if ($e->getCode() == 11000) { // Duplicate key error
            echo "<script>alert('Given Email ID is already registered for this event'); window.location.href = 'display_event.php?event_id=" . urlencode($eventId) . "';</script>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Event Registration">
    <meta name="author" content="Campus Connect">
    <meta name="keywords" content="Event Registration">

    <!-- Title Page-->
    <title>Event Registration</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/event_reg.css" rel="stylesheet" media="all">
</head>

<body>
<script>
function validateForm() {
    const email = document.getElementById("email").value.trim();

    const name = document.getElementById("name").value.trim();
    
    const phone = document.getElementById("phone").value.trim();
   
    const profileImage = document.getElementById("profileImage").files[0];

    let errorMessages = [];

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        errorMessages.push("Email is required.");
    } else if (!emailPattern.test(email)) {
        errorMessages.push("Invalid email format.");
    }

    if (!name) {
        errorMessages.push("Name is required.");
    }

    if (!phone) {
        errorMessages.push("Phone number is required.");
    } else if (phone.length < 10 || phone.length > 15) {
        errorMessages.push("Phone number must be between 10 and 15 digits.");
    }

    if (!profileImage) {
        errorMessages.push("Profile picture is required.");
    } else if (!["image/jpeg", "image/png"].includes(profileImage.type)) {
        errorMessages.push("Profile picture must be a JPEG or PNG file.");
    }

    if (errorMessages.length > 0) {
        alert(errorMessages.join("\n"));
        return false;
    }

    return true;
    }
</script>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                    <h2 class="title"><?php echo $event['event_name']?> Registration</h2>
                    <form id="event_reg" action="" enctype="multipart/form-data" method="POST" onsubmit="return validateForm()">
                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($eventId); ?>">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Name</label>
                                    <input class="input--style-4" type="text" id="name" name="name" required>
                                </div>
                            </div>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Email ID</label>
                                    <input class="input--style-4" type="email" id="email" name="email" required>
                                </div>
                            </div>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Phone No</label>
                                    <input class="input--style-4" type="number" id="phone" name="phone" required>
                                </div>
                            </div>
                        </div>                        
                        <?php
                        if (isset($event['form_data']) && is_object($event['form_data'])) {
                            $formData = (array) $event['form_data']; // Convert object to associative array
                            foreach ($formData as $key => $validation) {
                        ?>
                            <div class="row row-space">
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label"><?php echo htmlspecialchars($key); ?></label>
                                        <input class="input--style-4" type="<?php echo htmlspecialchars($validation); ?>" name="<?php echo htmlspecialchars($key); ?>" required>
                                    </div>
                                </div>
                            </div>
                        <?php
                            }
                        }
                        ?>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Upload Institution ID</label>
                                    <input type="file" id="profileImage" name="InstitutionID" accept="image/*" placeholder="Upload your Institution ID">
                                </div>
                            </div>
                        </div>
                        <div class="p-t-15">
                            <button class="btn btn--radius-2 btn--blue" type="submit">Submit</button>
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
    <script>
    document.getElementById('promo_code').addEventListener('input', function() {
        var promoCode = document.getElementById('promo_code').value;
        var discountPercentageContainer = document.getElementById('discount_percentage_container');
        var discountPercentage = document.getElementById('discount_percentage');
        if (promoCode.trim() !== '') {
            discountPercentageContainer.style.display = 'block';
            discountPercentage.required = true;
        } else {
            discountPercentageContainer.style.display = 'none';
            discountPercentage.required = false;
            discountPercentage.value = '';
        }
    });
    </script>

</body>

</html>