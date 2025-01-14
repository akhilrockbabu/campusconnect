<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role']!='organizer') {
    header("Location: log_reg.html");
    exit();
}
$username = $_SESSION['username'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_desc = $_POST['event_desc'];
    $event_rules = $_POST['event_rules'];
    $event_date = $_POST['event_date'];
    $event_venue = $_POST['event_venue'];
    $registration_fees = $_POST['event_fees'];
    $upi_id = $_POST['upi_id'];
    $promo_code = $_POST['event_coupon'];
    $discount_percentage = $_POST['event_discount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Form Builder</h2>
        <form id="form-builder" action="save_form.php" method="POST">
            <div id="form-fields"></div>
            <button type="button" class="btn btn-primary mt-3" onclick="addField()">Add Field</button>
            <button type="submit" class="btn btn-success mt-3">Save Form</button>
        </form>
    </div>

    <script>
        function addField() {
            const formFields = document.getElementById('form-fields');
            const fieldCount = formFields.children.length;

            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'form-group row';

            const label = document.createElement('label');
            label.className = 'col-sm-2 col-form-label';
            label.innerText = `Field ${fieldCount + 1}`;
            fieldDiv.appendChild(label);

            const inputDiv = document.createElement('div');
            inputDiv.className = 'col-sm-4';
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `field_${fieldCount + 1}`;
            input.className = 'form-control';
            input.placeholder = 'enter the label';
            inputDiv.appendChild(input);
            fieldDiv.appendChild(inputDiv);

            const selectDiv = document.createElement('div');
            selectDiv.className = 'col-sm-4';
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
            select.appendChild(optionText);
            select.appendChild(optionNumber);
            select.appendChild(optionEmail);
            selectDiv.appendChild(select);
            fieldDiv.appendChild(selectDiv);

            formFields.appendChild(fieldDiv);
        }
    </script>
</body>
</html>
