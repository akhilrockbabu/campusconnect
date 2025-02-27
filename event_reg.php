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

// Generate UPI QR code URL
function generateUpiQr($upi_id, $amount, $payee_name) {
    $upi_string = "upi://pay?pa=$upi_id&pn=$payee_name&am=$amount&cu=INR";
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upi_string);
    return $qr_url;
}

$upiQrUrl = generateUpiQr($event['upi_id'], $event['registration_fees'], 'Campus Connect');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participantData = [
        'event_id' => new MongoDB\BSON\ObjectId($eventId),
    ];

    // Collect all form fields except 'event_id' and 'promo'
    foreach ($_POST as $key => $value) {
        if ($key !== 'event_id' && $key !== 'promo') {
            $participantData[$key] = $value;
        }
    }

    // Handle file upload for Institution ID
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

    // Handle file upload for Payment Proof
    if (isset($_FILES['PaymentProof']) && $_FILES['PaymentProof']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['PaymentProof']['type'];
        $fileSize = $_FILES['PaymentProof']['size'];

        if (in_array($fileType, $allowedTypes) && $fileSize <= 5000000) { // 5MB limit
            $uploadDir = 'uploads/payment_proof/';
            $fileExtension = pathinfo($_FILES['PaymentProof']['name'], PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $paymentProofPath = $uploadDir . $uniqueFileName;

            if (!move_uploaded_file($_FILES['PaymentProof']['tmp_name'], $paymentProofPath)) {
                echo "<script>alert('File upload failed.'); window.location.href = 'event_reg.php';</script>";
                exit;
            } 
        } else {
            echo "<script>alert('Invalid file type or file size exceeds limit.'); window.location.href = 'event_reg.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please upload a payment proof image.'); window.location.href = 'event_reg.php';</script>";
        exit;
    }
    $participantData['PaymentProof'] = $paymentProofPath;
    $participantData['status'] = 'pending';


    // Insert participant data into the collection
    try {
        $participantsCollection->insertOne($participantData);
        $eventLimit = $event['event_limit'] - 1;
        $eventsCollection->updateOne(['_id' => new MongoDB\BSON\ObjectId($eventId)], ['$set' => ['event_limit' => $eventLimit]]);
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

    <style>
        .validate-button {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>

<body>
<script>
function validateForm() {
    const email = document.getElementById("email").value.trim();
    const name = document.getElementById("name").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const profileImage = document.getElementById("profileImage").files[0];
    const paymentproof = document.getElementById("paymentproof").files[0];

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
    } else if (phone.length < 10 || phone.length > 10) {
        errorMessages.push("Phone number must be 10 digits.");
    }

    if (!profileImage) {
        errorMessages.push("Profile picture is required.");
    } else if (!["image/jpeg", "image/png", "image/jpg"].includes(profileImage.type)) {
        errorMessages.push("Profile picture must be a JPEG or PNG or JPG file.");
    }

    if (!paymentproof) {
        errorMessages.push("Payment proof is required.");
    } else if (!["image/jpeg", "image/png", "image/jpg"].includes(paymentproof.type)) {
        errorMessages.push("Payment proof must be a JPEG or PNG or JPG file.");
    }

    // Validate phone numbers generated from the database
    const phoneFields = document.querySelectorAll('input[type="phone"]');
    phoneFields.forEach(field => {
        const phoneValue = field.value.trim();
        if (phoneValue.length !== 10) {
            errorMessages.push(`Phone number for ${field.name} must be 10 digits.`);
        }
    });

    if (errorMessages.length > 0) {
        alert(errorMessages.join("\n"));
        return false;
    }

    return true;
}

function validatePromoCode() {
    const promoCode = document.getElementById("promo").value.trim();
    const eventCoupon = "<?php echo htmlspecialchars($event['event_coupon']); ?>";
    const eventDiscount = "<?php echo htmlspecialchars($event['event_discount']); ?>";
    const registrationFees = "<?php echo htmlspecialchars($event['registration_fees']); ?>";
    const upiId = "<?php echo htmlspecialchars($event['upi_id']); ?>";
    const payeeName = "Campus Connect";

    if (promoCode === eventCoupon) {
        const discountedAmount = registrationFees - (registrationFees * (eventDiscount / 100));
        const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" + encodeURIComponent("upi://pay?pa=" + upiId + "&pn=" + payeeName + "&am=" + discountedAmount + "&cu=INR");
        document.getElementById("upiQrCode").src = qrUrl;
        document.getElementById("promoMessage").innerHTML = "Promo code validated!";
        document.getElementById("promoMessage").style.color = "green";
    } else {
        const discountedAmount = registrationFees;
        const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" + encodeURIComponent("upi://pay?pa=" + upiId + "&pn=" + payeeName + "&am=" + discountedAmount + "&cu=INR");
        document.getElementById("upiQrCode").src = qrUrl;
        document.getElementById("promoMessage").innerHTML = "Invalid promo code.";
        document.getElementById("promoMessage").style.color = "red";
    }
}
</script>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                    <h2 class="title"><?php echo htmlspecialchars($event['event_name']); ?> Registration</h2>
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
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Promo Code</label>
                                    <input class="input--style-4" type="text" style="width:140px" id="promo" name="promo">
                                    <button type="button" class="validate-button" onclick="validatePromoCode()">Check</button>
                                    <p id="promoMessage"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">UPI Payment QR Code</label>
                                    <img id="upiQrCode" src="<?php echo htmlspecialchars($upiQrUrl); ?>" alt="UPI Payment QR Code">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Upload Payment Proof</label>
                                    <input type="file" id="paymentproof" name="PaymentProof" accept="image/*" placeholder="Upload your Institution ID">
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
    
</body>

</html>