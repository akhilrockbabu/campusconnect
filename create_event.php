<?php
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    session_start();

    if (!isset($_SESSION['username']) || $_SESSION['role']!='organizer') {
        header("Location: log_reg.html");
        exit();
    }
    $username = $_SESSION['username'];
?>

<i class="zmdi zmdi-calendar-note input-icon js-btn-calendar"></i>

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
    <link href="css/event_reg.css" rel="stylesheet" media="all">
</head>

<body>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">
                    <h2 class="title">Registration Form</h2>
                    <form method="POST" action="formbuilder.php">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Event Name</label>
                                    <input class="input--style-4" type="text" name="event_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Short Description</label>
                                    <input class="input--style-4" type="text" name="event_desc" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Rules & Info</label>
                                    <input class="input--style-4" type="text" name="event_rules">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Event Date</label>
                                    <input class="input--style-4" type="date" name="event_date" min="<?php echo $tomorrow; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Venue</label>
                                    <input class="input--style-4" type="text" name="event_venue">
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Registration Fees</label>
                                    <input class="input--style-4" type="number" name="event_fees" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">UPI ID</label>
                                    <input class="input--style-4" type="text" name="upi_id" pattern="^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Promo Code</label>
                                    <input class="input--style-4" type="text" style="width:100%" name="event_coupon" id="promo_code" pattern="[A-Za-z0-9]*">
                                </div>
                            </div>
                            <div class="col-2" id="discount_percentage_container" style="display: none;">
                                <div class="input-group">
                                    <label class="label">Discount Percentage</label>
                                    <input class="input--style-4" type="number" style="width:100%" name="event_discount" id="discount_percentage" min="1" max="100">
                                </div>
                            </div>
                        </div>
                        <!-- <div class="input-group">
                            <label class="label">Subject</label>
                            <div class="rs-select2 js-select-simple select--no-search">
                                <select name="subject">
                                    <option disabled="disabled" selected="selected">Choose option</option>
                                    <option>Subject 1</option>
                                    <option>Subject 2</option>
                                    <option>Subject 3</option>
                                </select>
                                <div class="select-dropdown"></div>
                            </div>
                        </div>-->
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

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->