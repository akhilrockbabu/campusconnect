<?php
session_start();
use MongoDB\Client;
require '../vendor/autoload.php';
$client = new Client("mongodb://localhost:27017");

if (!isset($_SESSION['username']) || $_SESSION['role']!='organizer') {
    header("Location: ../log_reg.html");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Form Builder</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --success-color: #2ecc71;
            --dark-success: #27ae60;
            --danger-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 6px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .header-container {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            box-shadow: var(--box-shadow);
        }
        
        .header-title {
            margin: 0;
            padding: 0 15px;
            font-weight: 600;
        }

        .header-subtitle {
            margin: 5px 0 0;
            padding: 0 15px;
            font-weight: 300;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .main-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }
        
        .form-section-title {
            color: var(--dark-color);
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }
        
        .default-field {
            background-color: #f8f9fa;
            border-left: 3px solid var(--primary-color);
            padding: 10px;
            margin-bottom: 15px;
            border-radius: var(--border-radius);
        }
        
        .custom-field {
            background-color: white;
            border: 1px dashed #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
        }
        
        .custom-field:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: var(--dark-success);
            border-color: var(--dark-success);
        }
        
        .btn-success a {
            color: white;
            text-decoration: none;
        }
        
        .close-btn {
            background: none;
            border: none;
            color: var(--danger-color);
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 5px;
            border-radius: 50%;
        }
        
        .close-btn:hover {
            background-color: rgba(231, 76, 60, 0.1);
        }
        
        .actions-container {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .tooltip-icon {
            color: var(--primary-color);
            margin-left: 5px;
            cursor: pointer;
        }
        
        .field-type-icon {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        .badge {
            font-size: 12px;
            font-weight: 400;
            padding: 5px 10px;
            margin-left: 10px;
            border-radius: 20px;
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .badge-light {
            background-color: #e9ecef;
            color: #495057;
        }
        
        .animation-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(52, 152, 219, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(52, 152, 219, 0);
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="header-container text-center">
        <h2 class="header-title">Event Form Builder</h2>
        <p class="header-subtitle">Design a professional registration form for your event</p>
    </div>
    
    <div class="main-container">
        <form id="form-builder" action="save_event.php" method="POST">
            <h4 class="form-section-title">
                <i class="fas fa-clipboard-list mr-2"></i> 
                Default Registration Fields
                <span class="badge badge-light">Required</span>
            </h4>
            
            <div id="form-fields">
                <!-- Default fields -->
                <div class="default-field">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            <i class="fas fa-user field-type-icon"></i>Name
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="field_label_name" value="Name" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="default-field">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            <i class="fas fa-envelope field-type-icon"></i>Email
                        </label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" name="field_label_email" value="Email" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="default-field">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            <i class="fas fa-phone field-type-icon"></i>Phone No
                        </label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" name="field_label_phone" value="Phone No" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="default-field">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            <i class="fas fa-university field-type-icon"></i>College Name
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="field_label_college" value="College Name" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="default-field">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">
                            <i class="fas fa-id-card field-type-icon"></i>Institution ID Proof
                        </label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="field_label_institution_id" value="Institution ID Proof" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <h4 class="form-section-title mt-4">
                <i class="fas fa-plus-circle mr-2"></i> 
                Custom Fields
                <span class="badge badge-light">Optional</span>
            </h4>
            
            <div id="custom-fields-container"></div>
            
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
            
            <div class="actions-container">
                <div>
                    <button type="button" class="btn btn-primary" onclick="addField()">
                        <i class="fas fa-plus mr-2"></i>Add Custom Field
                    </button>
                    <button type="button" class="btn btn-outline-secondary ml-2" onclick="toggleFieldsHelp()">
                        <i class="fas fa-question-circle mr-1"></i>Help
                    </button>
                </div>
                <div>
                    <a href="create_event.php" class="btn btn-outline-dark mr-2">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                    <button type="submit" class="btn btn-success animation-pulse" onclick="saveFormData()">
                        <i class="fas fa-save mr-2"></i>Save Form
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel">Form Builder Help</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6><i class="fas fa-info-circle text-primary mr-2"></i>About Field Types:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-font text-muted mr-2"></i><strong>Text:</strong> For collecting short text responses</li>
                        <li class="mb-2"><i class="fas fa-hashtag text-muted mr-2"></i><strong>Number:</strong> For collecting numeric information</li>
                        <li class="mb-2"><i class="fas fa-envelope text-muted mr-2"></i><strong>Email:</strong> Will validate proper email format</li>
                        <li class="mb-2"><i class="fas fa-phone text-muted mr-2"></i><strong>Phone Number:</strong> For collecting contact numbers</li>
                    </ul>
                    <h6 class="mt-4"><i class="fas fa-lightbulb text-primary mr-2"></i>Tips:</h6>
                    <ul>
                        <li>Give clear, descriptive labels to your fields</li>
                        <li>Only add fields that are necessary for your event</li>
                        <li>Consider the mobile experience - keep it simple</li>
                        <li>Default fields cannot be removed</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Got it!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<script>
    function addField() {
        const customFieldsContainer = document.getElementById('custom-fields-container');
        const formFields = document.getElementById('form-fields');
        const fieldCount = formFields.children.length + customFieldsContainer.children.length;

        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'custom-field';
        
        const formGroupDiv = document.createElement('div');
        formGroupDiv.className = 'form-group row';

        const labelDiv = document.createElement('div');
        labelDiv.className = 'col-sm-3';

        const labelInput = document.createElement('input');
        labelInput.type = 'text';
        labelInput.name = `field_label_${fieldCount + 1}`;
        labelInput.className = 'form-control';
        labelInput.placeholder = 'Enter field label';
        labelInput.required = true;
        labelDiv.appendChild(labelInput);

        const inputDiv = document.createElement('div');
        inputDiv.className = 'col-sm-7';

        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group';

        const inputGroupPrepend = document.createElement('div');
        inputGroupPrepend.className = 'input-group-prepend';
        
        const inputGroupText = document.createElement('span');
        inputGroupText.className = 'input-group-text';
        inputGroupText.innerHTML = '<i class="fas fa-cog"></i>';
        inputGroupPrepend.appendChild(inputGroupText);
        
        inputGroup.appendChild(inputGroupPrepend);

        const select = document.createElement('select');
        select.name = `field_type_${fieldCount + 1}`;
        select.className = 'form-control';
        select.onchange = updateFieldIcon;
        
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
        
        inputGroup.appendChild(select);
        inputDiv.appendChild(inputGroup);

        const closeButtonDiv = document.createElement('div');
        closeButtonDiv.className = 'col-sm-2 text-right';

        const closeButton = document.createElement('button');
        closeButton.className = 'close-btn';
        closeButton.type = 'button';
        closeButton.innerHTML = '<i class="fas fa-times-circle"></i>';
        closeButton.onclick = function() {
            customFieldsContainer.removeChild(fieldDiv);
        };
        closeButtonDiv.appendChild(closeButton);

        formGroupDiv.appendChild(labelDiv);
        formGroupDiv.appendChild(inputDiv);
        formGroupDiv.appendChild(closeButtonDiv);
        fieldDiv.appendChild(formGroupDiv);
        
        customFieldsContainer.appendChild(fieldDiv);
        
        // Add animation
        fieldDiv.classList.add('animation-pulse');
        setTimeout(() => {
            fieldDiv.classList.remove('animation-pulse');
        }, 2000);
        
        // Focus on the new input
        labelInput.focus();
    }

    function updateFieldIcon(e) {
        const select = e.target;
        const fieldType = select.value;
        const inputGroupText = select.parentElement.querySelector('.input-group-text');
        
        let iconClass = 'fas fa-font';
        
        switch(fieldType) {
            case 'number':
                iconClass = 'fas fa-hashtag';
                break;
            case 'email':
                iconClass = 'fas fa-envelope';
                break;
            case 'phone':
                iconClass = 'fas fa-phone';
                break;
        }
        
        inputGroupText.innerHTML = `<i class="${iconClass}"></i>`;
    }

    function saveFormData() {
        const customFieldsContainer = document.getElementById('custom-fields-container');
        const formData = {};

        // Process all custom fields
        for (let i = 0; i < customFieldsContainer.children.length; i++) {
            const fieldDiv = customFieldsContainer.children[i];
            const labelInput = fieldDiv.querySelector('input[type="text"]');
            const selectInput = fieldDiv.querySelector('select');
            
            if (!labelInput.value.trim()) {
                alert('Please enter a label for all custom fields.');
                labelInput.focus();
                return false;
            }
            
            formData[labelInput.name] = {
                label: labelInput.value,
                type: selectInput ? selectInput.value : 'text'
            };
        }

        document.getElementById('form_data').value = JSON.stringify(formData);
        return true;
    }

    function toggleFieldsHelp() {
        $('#helpModal').modal('toggle');
    }

    document.getElementById('form-builder').onsubmit = function() {
        return saveFormData();
    };
</script>
</body>
</html>