<?php

require 'vendor/autoload.php';
use MongoDB\BSON\ObjectId;
use MongoDB\Client;
use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $event_desc = $_POST['event_desc'];
    $event_venue = $_POST['event_venue'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $participant_name = $_POST['participant_name'];
    $participant_id = $_POST['participant_id'];
}

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$participantsCollection = $db->participants;
$participant = $participantsCollection->findOne(['_id' => new ObjectId($participant_id)]);

$institution_image = $participant['InstitutionID'] ?? 'default_image_path.jpg'; // Default image if not found
$payment_proof = $participant['PaymentProof'] ?? 'default_payment_proof.jpg'; // Default payment proof

// Get the local server IP (use 'localhost' if accessing from the same PC)
$serverIP = "192.168.1.5"; // Fetches local IP
if ($serverIP == "::1" || $serverIP == "127.0.0.1") {
    $serverIP = "localhost"; // Use localhost for local testing
}

// Generate the public URL for the image
$imageURL = "http://$serverIP/campusconnect/" . $payment_proof;

// Generate QR code using GoQR API
$qrCodeURL = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($imageURL);

// Convert QR code image to base64
$qrCodeImage = file_get_contents($qrCodeURL);
$qrCodeBase64 = base64_encode($qrCodeImage);

// Generate PDF
if (isset($_POST['download_pdf'])) {
    $dompdf = new Dompdf();
    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Event Ticket</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Staatliches&display=swap");
            @import url("https://fonts.googleapis.com/css2?family=Nanum+Pen+Script&display=swap");

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body, html {
                height: 100vh;
                display: grid;
                font-family: "Staatliches", cursive;
                background: #d83565;
                color: black;
                font-size: 14px;
                letter-spacing: 0.1em;
            }

            .ticket {
                margin: auto;
                display: flex;
                background: white;
                box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;
            }

            .left {
                display: flex;
            }

            .image {
                height: 250px;
                width: 250px;
                background-image: url("' . htmlspecialchars($institution_image) . '");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                opacity: 0.85;
            }

            .admit-one {
                position: absolute;
                color: darkgray;
                height: 250px;
                padding: 0 10px;
                letter-spacing: 0.15em;
                display: flex;
                text-align: center;
                justify-content: space-around;
                writing-mode: vertical-rl;
                transform: rotate(-180deg);
            }

            .admit-one span:nth-child(2) {
                color: white;
                font-weight: 700;
            }

            .ticket-number {
                height: 250px;
                width: 250px;
                display: flex;
                justify-content: flex-end;
                align-items: flex-end;
                padding: 5px;
            }

            .ticket-info {
                padding: 10px 30px;
                display: flex;
                flex-direction: column;
                text-align: center;
                justify-content: space-between;
                align-items: center;
            }

            .date {
                border-top: 1px solid gray;
                border-bottom: 1px solid gray;
                padding: 5px 0;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: space-around;
            }

            .date span {
                width: 100px;
            }

            .date span:first-child {
                text-align: left;
            }

            .date span:last-child {
                text-align: right;
            }

            .date .june-29 {
                color: #d83565;
                font-size: 20px;
            }

            .show-name {
                font-size: 32px;
                font-family: "Nanum Pen Script", cursive;
                color: #d83565;
            }

            .show-name h1 {
                font-size: 48px;
                font-weight: 700;
                letter-spacing: 0.1em;
                color: #4a437e;
            }

            .time {
                padding: 10px 0;
                color: #4a437e;
                text-align: center;
                display: flex;
                flex-direction: column;
                gap: 10px;
                font-weight: 700;
            }

            .time span {
                font-weight: 400;
                color: gray;
            }

            .right {
                width: 180px;
                border-left: 1px dashed #404040;
            }

            .barcode img {
                height: 100px;
                margin: 40px;
                width: 100px;
            }

        </style>
    </head>
    <body>
    <div class="ticket">
        <div class="left">
            <div class="image">
                <p class="admit-one">
                    <span>ADMIT ONE</span>
                    <span>ADMIT ONE</span>
                    <span>ADMIT ONE</span>
                </p>
                <div class="ticket-number">
                    <p>' . htmlspecialchars($participant_id) . '</p>
                </div>
            </div>
            <div class="ticket-info">
                <p class="date">
                    <span>' . htmlspecialchars(date('l', strtotime($event_date))) . '</span>
                    <span class="june-29">' . htmlspecialchars(date('F jS', strtotime($event_date))) . '</span>
                    <span>' . htmlspecialchars(date('Y', strtotime($event_date))) . '</span>
                </p>
                <div class="show-name">
                    <h1>' . htmlspecialchars($event_name) . '</h1>
                    <h2>' . htmlspecialchars($participant_name) . '</h2>
                </div>
                <div class="time">
                    <p>' . DateTime::createFromFormat('H:i', $event_time)->format('h:i A') . '</p>
                </div>
                <p class="location">
                    <span>' . htmlspecialchars($event_venue) . '</span>
                    <span class="separator"><i class="far fa-smile"></i></span>
                    <span>College Name</span>
                </p>
            </div>
        </div>
        <div class="right">
            <p class="admit-one">
                <span>ADMIT ONE</span>
                <span>ADMIT ONE</span>
                <span>ADMIT ONE</span>
            </p>
            <div class="right-info-container">
                <div class="show-name">
                    <h1>' . htmlspecialchars($event_name) . '</h1>
                </div>
                <div class="time">
                    <p>' . DateTime::createFromFormat('H:i', $event_time)->format('h:i A') . '</p>
                </div>
                <div class="barcode">
                    <img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="QR Code">
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>';
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('ticket.pdf', ['Attachment' => 1]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Ticket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Staatliches&display=swap");
        @import url("https://fonts.googleapis.com/css2?family=Nanum+Pen+Script&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100vh;
            display: grid;
            font-family: "Staatliches", cursive;
            background: #d83565;
            color: black;
            font-size: 14px;
            letter-spacing: 0.1em;
        }

        .ticket {
            margin: auto;
            display: flex;
            background: white;
            box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;
        }

        .left {
            display: flex;
        }

        .image {
            height: 250px;
            width: 250px;
            background-image: url("<?php echo htmlspecialchars($institution_image); ?>");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.85;
        }

        .admit-one {
            position: absolute;
            color: darkgray;
            height: 250px;
            padding: 0 10px;
            letter-spacing: 0.15em;
            display: flex;
            text-align: center;
            justify-content: space-around;
            writing-mode: vertical-rl;
            transform: rotate(-180deg);
        }

        .admit-one span:nth-child(2) {
            color: white;
            font-weight: 700;
        }

        .ticket-number {
            height: 250px;
            width: 250px;
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
            padding: 5px;
        }

        .ticket-info {
            padding: 10px 30px;
            display: flex;
            flex-direction: column;
            text-align: center;
            justify-content: space-between;
            align-items: center;
        }

        .date {
            border-top: 1px solid gray;
            border-bottom: 1px solid gray;
            padding: 5px 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        .date span {
            width: 100px;
        }

        .date span:first-child {
            text-align: left;
        }

        .date span:last-child {
            text-align: right;
        }

        .date .june-29 {
            color: #d83565;
            font-size: 20px;
        }

        .show-name {
            font-size: 32px;
            font-family: "Nanum Pen Script", cursive;
            color: #d83565;
        }

        .show-name h1 {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #4a437e;
        }

        .time {
            padding: 10px 0;
            color: #4a437e;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-weight: 700;
        }

        .time span {
            font-weight: 400;
            color: gray;
        }

        .right {
            width: 180px;
            border-left: 1px dashed #404040;
        }

        .barcode img {
            height: 100px;
            margin: 40px;
            width: 100px;
        }

        .download-button {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
        }

    </style>
</head>
<body>
<div class="ticket">
    <div class="left">
        <div class="image">
            <p class="admit-one">
                <span>ADMIT ONE</span>
                <span>ADMIT ONE</span>
                <span>ADMIT ONE</span>
            </p>
            <div class="ticket-number">
                <p><?php echo htmlspecialchars($participant_id); ?></p>
            </div>
        </div>
        <div class="ticket-info">
            <p class="date">
                <span><?php echo htmlspecialchars(date('l', strtotime($event_date))); ?></span>
                <span class="june-29"><?php echo htmlspecialchars(date('F jS', strtotime($event_date))); ?></span>
                <span><?php echo htmlspecialchars(date('Y', strtotime($event_date))); ?></span>
            </p>
            <div class="show-name">
                <h1><?php echo htmlspecialchars($event_name); ?></h1>
                <h2><?php echo htmlspecialchars($participant_name); ?></h2>
            </div>
            <div class="time">
                <p><?php echo DateTime::createFromFormat('H:i', $event_time)->format('h:i A'); ?></p>
            </div>
            <p class="location">
                <span><?php echo htmlspecialchars($event_venue); ?></span>
                <span class="separator"><i class="far fa-smile"></i></span>
                <span>College Name</span>
            </p>
        </div>
    </div>
    <div class="right">
        <p class="admit-one">
            <span>ADMIT ONE</span>
            <span>ADMIT ONE</span>
            <span>ADMIT ONE</span>
        </p>
        <div class="right-info-container">
            <div class="show-name">
                <h1><?php echo htmlspecialchars($event_name); ?></h1>
            </div>
            <div class="time">
                <p><?php echo DateTime::createFromFormat('H:i', $event_time)->format('h:i A'); ?></p>
            </div>
            <div class="barcode">
                <img src="data:image/png;base64,<?php echo $qrCodeBase64; ?>" alt="QR Code">
            </div>
        </div>
    </div>
</div>
<form method="post" action="">
    <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($event_name); ?>">
    <input type="hidden" name="event_desc" value="<?php echo htmlspecialchars($event_desc); ?>">
    <input type="hidden" name="event_venue" value="<?php echo htmlspecialchars($event_venue); ?>">
    <input type="hidden" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>">
    <input type="hidden" name="event_time" value="<?php echo htmlspecialchars($event_time); ?>">
    <input type="hidden" name="participant_name" value="<?php echo htmlspecialchars($participant_name); ?>">
    <input type="hidden" name="participant_id" value="<?php echo htmlspecialchars($participant_id); ?>">
    <input type="hidden" name="download_pdf" value="1">
    <button type="submit" class="download-button">Download Ticket as PDF</button>
</form>
</body>
</html>
