<?php
session_start();
use MongoDB\Client;
require '../vendor/autoload.php';
$client = new Client("mongodb://localhost:27017");

if (!isset($_SESSION['username']) || $_SESSION['role']!='organizer') {
    header("Location: log_reg.html");
    exit();
}
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action']) && $_POST['form_action'] === 'create_event')
{
    $eventDetails = [
        'event_name' => $_POST['event_name'],
        'event_desc' => $_POST['event_desc'],
        'event_rules' => $_POST['event_rules'],
        'event_date' => $_POST['event_date'],
        'event_time' => $_POST['event_time'],
        'event_venue' => $_POST['event_venue'],
        'registration_fees' => $_POST['event_fees'],
        'event_limit' => $_POST['event_limit'],
        'upi_id' => $_POST['upi_id'],
        'event_organizer' => $username,
    ];

    if (isset($_POST['event_coupon']) && isset($_POST['event_discount'])) {
        $eventDetails['event_coupon'] = $_POST['event_coupon'];
        $eventDetails['event_discount'] = $_POST['event_discount'];
    }

    $_SESSION['event_details'] = $eventDetails;
    header("Location: formbuilder.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<style>
        body {
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

        .close-btn {
            background: none;
            border: none;
            color: red;
            font-size: 20px;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Design Your Event Form</h2>
    <form id="form-builder" action="save_event.php" method="POST">
        <div id="form-fields">
            <!-- Default fields -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="field_label_name" value="Name" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-4">
                    <input type="email" class="form-control" name="field_label_email" value="Email" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Phone No</label>
                <div class="col-sm-4">
                    <input type="tel" class="form-control" name="field_label_phone" value="Phone No" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Institution ID Proof</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="field_label_institution_id" value="Institution ID Proof" readonly>
                </div>
            </div>
        </div>
        <input type="hidden" name="form_data" id="form_data">
        <input type="hidden" name="event_name" value="<?php echo $_SESSION['event_details']['event_name']; ?>">
        <input type="hidden" name="event_desc" value="<?php echo $_SESSION['event_details']['event_desc']; ?>">
        <input type="hidden" name="event_rules" value="<?php echo $_SESSION['event_details']['event_rules']; ?>">
        <input type="hidden" name="event_date" value="<?php echo $_SESSION['event_details']['event_date']; ?>">
        <input type="hidden" name="event_time" value="<?php echo $_SESSION['event_details']['event_time']; ?>">
        <input type="hidden" name="event_venue" value="<?php echo $_SESSION['event_details']['event_venue']; ?>">
        <input type="hidden" name="registration_fees" value="<?php echo $_SESSION['event_details']['registration_fees']; ?>">
        <input type="hidden" name="event_limit" value="<?php echo $_SESSION['event_details']['event_limit']; ?>">
        <input type="hidden" name="event_organizer" value="<?php echo $_SESSION['event_details']['event_organizer']; ?>">
        <input type="hidden" name="upi_id" value="<?php echo $_SESSION['event_details']['upi_id']; ?>">
        <?php if (isset($_SESSION['event_details']['event_coupon'])): ?>
            <input type="hidden" name="event_coupon" value="<?php echo $_SESSION['event_details']['event_coupon']; ?>">
        <?php endif; ?>
        <?php if (isset($_SESSION['event_details']['event_discount'])): ?>
            <input type="hidden" name="event_discount" value="<?php echo $_SESSION['event_details']['event_discount']; ?>">
        <?php endif; ?>
        <p>Add more fields to the form:</p>
        <button type="button" class="btn btn-primary mt-3" onclick="addField()">Add Field</button>
        <button type="submit" class="btn btn-success mt-3" onclick="saveFormData()">Save Form</button>
    </form>
    <br>
    <button class="btn btn-success" style="margin-left:150px;"><a href="create_event.php">Back to Home</a></button>
</div>

<script>
    function addField() {
        const formFields = document.getElementById('form-fields');
        const fieldCount = formFields.children.length;

        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'form-group row';

        const labelDiv = document.createElement('div');
        labelDiv.className = 'col-sm-2';

        const labelInput = document.createElement('input');
        labelInput.type = 'text';
        labelInput.name = `field_label_${fieldCount + 1}`;
        labelInput.className = 'form-control';
        labelInput.placeholder = 'Enter label';
        labelInput.required = true; // Make the label input required
        labelDiv.appendChild(labelInput);

        const inputDiv = document.createElement('div');
        inputDiv.className = 'col-sm-4';

        const select = document.createElement('select');
        select.name = `field_type_${fieldCount + 1}`;
        select.className = 'form-control';
        const optionText = document.createElement('option');
        optionText.value = 'text';
        optionText.innerText = 'Text';
        const optionNumber = document.createElement('option');
        optionNumber.value = 'number';
        optionNumber.innerText = 'Number';
        const optionEmail = document.createElement('option');
        optionEmail.value = 'email';
        optionEmail.innerText = 'Email';
        const optionPhone = document.createElement('option');
        optionPhone.value = 'phone';
        optionPhone.innerText = 'Phone Number';

        select.appendChild(optionText);
        select.appendChild(optionNumber);
        select.appendChild(optionEmail);
        select.appendChild(optionPhone);
        inputDiv.appendChild(select);

        const closeButtonDiv = document.createElement('div');
        closeButtonDiv.className = 'col-sm-1';

        const closeButton = document.createElement('button');
        closeButton.className = 'close-btn';
        closeButton.innerHTML = '<i class="fas fa-times"></i>';
        closeButton.onclick = function() {
            formFields.removeChild(fieldDiv);
        };
        closeButtonDiv.appendChild(closeButton);

        fieldDiv.appendChild(labelDiv);
        fieldDiv.appendChild(inputDiv);
        fieldDiv.appendChild(closeButtonDiv);
        formFields.appendChild(fieldDiv);
    }

    function saveFormData() {
        const formFields = document.getElementById('form-fields');
        const formData = {};

        for (let i = 0; i < formFields.children.length; i++) {
            const labelInput = formFields.children[i].querySelector('input');
            const selectInput = formFields.children[i].querySelector('select');
            if (!labelInput.readOnly) { // Only include fields that are not readonly
                if (!labelInput.value) {
                    alert('Please enter a label for all fields.');
                    return false;
                }
                formData[labelInput.name] = {
                    label: labelInput.value,
                    type: selectInput ? selectInput.value : 'text'
                };
            }
        }

        document.getElementById('form_data').value = JSON.stringify(formData);
        return true;
    }

    document.getElementById('form-builder').onsubmit = function() {
        return saveFormData();
    };
</script>
</body>
</html>
