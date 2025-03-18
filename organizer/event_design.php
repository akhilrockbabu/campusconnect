<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'organizer') {
    header("Location: ../log_reg.html");
    exit();
}

// Get the event document from the session
$event = $_SESSION['event_document'] ?? null;

if (!$event) {
    echo "No event details found.";
    exit();
}

// Format the event time
$eventTime = DateTime::createFromFormat('H:i', $event['event_time']);
$formattedEventTime = $eventTime ? $eventTime->format('h:i A') : '';

require '../vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$organizersCollection = $db->organizers;

$organizer = $organizersCollection->findOne(['username' => $event['event_organizer']]);

// Handle image upload
$uploadedImagePath = '../img/click here to upload your event poster.jpg'; // Default image path
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $eventName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $event['event_name']); // Sanitize event name
    $targetDir = "../img/events_posters/";
    $fileName = $eventName . "_" . time() . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        $uploadedImagePath = $targetFilePath;
        // Update the event document in the session
        $_SESSION['event_document']['event_poster'] = $uploadedImagePath;
    } else {
        echo "<script>alert('Image upload failed.');</script>";
    }
}

// Handle save event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    $eventDocument = $_SESSION['event_document'];
    $eventsCollection = $db->events;
    $eventDocument['status'] = 'pending';
    $eventsCollection->insertOne($eventDocument);
    echo "<script>alert('Event saved successfully'); window.location.href = 'organizer81118.php';</script>";
    exit();
}

?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($event['event_name']); ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/themify-icons.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/gijgo.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/slicknav.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/responsive.css">
</head>

<body>
    <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

    <!-- header-start -->
    <header>
        <div class="header-area ">
            <div id="sticky-header" class="main-header-area">
                <div class="container">
                    <div class="header_bottom_border">
                        <div class="row align-items-center">
                            <div class="col-xl-3 col-lg-3">
                                <div class="logo">
                                    <a href="index.html"></a>
                                        <img src="img/logo.png" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="main-menu  d-none d-lg-block">
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="buy_tkt">
                                    <div class="book_btn d-none d-lg-block">
                                        <a href="registration/asar_dagyara.php">Register Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mobile_menu d-block d-lg-none"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- header-end -->
    
     <!-- bradcam_area -->
     <div class="bradcam_area">
        <div class="single_bradcam  d-flex align-items-center bradcam_bg_1 overlay">
              <div class="container">
                    <div class="row align-items-center justify-content-center">
                       <div class="col-xl-12">
                          <div class="bradcam_text text-center">
                            <div class="shape_1 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".2s">
                                <img src="img/shape/shape_1.svg" alt="">
                            </div>
                            <div class="shape_2 wow fadeInDown" data-wow-duration="1s" data-wow-delay=".3s">
                                <img src="img/shape/shape_2.svg" alt="">
                            </div>
                                <h3 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s" style="color: white;"><?php echo htmlspecialchars($event['event_desc']); ?></h4>
                          </div>
                       </div>
                    </div>
              </div>
           </div>
     </div>
    <!-- bradcam_area end -->


    <!--================Blog Area =================-->
    <section class="blog_area section-padding">
        <div class="container" style="margin-bottom:-100px;">
            <div class="row">
                <div class="col-lg-8 mb-5 mb-lg-0">
                    <div class="blog_left_sidebar">
                        <article class="blog_item">
                        <div class="blog_item_img">
                            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                                <img id="uploadImage" class="card-img rounded-0" src="<?php echo $uploadedImagePath; ?>" alt="Click to upload image" style="cursor: pointer;">
                                <input type="file" id="imageInput" name="image" style="display: none;" accept="image/*">
                                <a href="#" class="blog_item_date">
                                    <h3>Register Now</h3>
                                </a>
                            </form>
                        </div>

                            <div class="blog_details">
                                <a class="d-inline-block" href="single-blog.html">
                                    <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
                                </a>
                                <h4>Registration Fees</h4>
                                <p style="text-align:justify;">
                                <?php echo htmlspecialchars($event['registration_fees']); ?>
                                </p>
                                <h4>General Rules</h4>
                                <p style="text-align:justify;">
                                <?php echo htmlspecialchars($event['event_rules']); ?></li>  
                                </p>
                                <p style="text-align:justify;">We wish you the best of luck—enjoy the event and make the most of this exciting experience!</p>
                                <ul class="blog-info-link">
                                    <li><i></i>Venue : <?php echo htmlspecialchars($event['event_venue']); ?></li>
                                    <li><i></i> Date : <?php echo htmlspecialchars($event['event_date']); ?></li>
                                    <li><i></i> Time : <?php echo htmlspecialchars($formattedEventTime); ?></li>
                                </ul>
                            </div>
                        </article>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog_right_sidebar">
                        <aside class="single_sidebar_widget post_category_widget">
                            <h4 class="widget_title"><?php echo htmlspecialchars($event['event_name']); ?> Coordinators</h4>
                            <h5 class="widget_title"><?php echo htmlspecialchars($organizer['name']); ?></h5>
                            <h6 class="widget_title">
                                <a href="tel:<?php echo htmlspecialchars($organizer['name']); ?>"><i class="fas fa-phone"></i><?php echo htmlspecialchars($organizer['phone']); ?></a>
                            </h6>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================Blog Area =================-->

<!-- Save Event Button -->
        <div class="col-lg-12 text-center">
            <form method="POST">
                <button type="submit" name="save_event" class="btn btn-primary" style="margin-bottom:200px;">Save Event</button>
            </form>


    <!-- JS here -->
    <script src="../js/vendor/modernizr-3.5.0.min.js"></script>
<script src="../js/vendor/jquery-1.12.4.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/owl.carousel.min.js"></script>
<script src="../js/isotope.pkgd.min.js"></script>
<script src="../js/ajax-form.js"></script>
<script src="../js/waypoints.min.js"></script>
<script src="../js/jquery.counterup.min.js"></script>
<script src="../js/imagesloaded.pkgd.min.js"></script>
<script src="../js/scrollIt.js"></script>
<script src="../js/jquery.scrollUp.min.js"></script>
<script src="../js/wow.min.js"></script>
<script src="../js/nice-select.min.js"></script>
<script src="../js/jquery.slicknav.min.js"></script>
<script src="../js/jquery.magnific-popup.min.js"></script>
<script src="../js/plugins.js"></script>
<script src="../js/gijgo.min.js"></script>

<!--contact js-->
<script src="../js/contact.js"></script>
<script src="../js/jquery.ajaxchimp.min.js"></script>
<script src="../js/jquery.form.js"></script>
<script src="../js/jquery.validate.min.js"></script>
<script src="../js/mail-script.js"></script>

<script src="../js/main.js"></script>

    <script>
    document.getElementById('uploadImage').addEventListener('click', function() {
        document.getElementById('imageInput').click();
    });

    document.getElementById('imageInput').addEventListener('change', function() {
        document.getElementById('uploadForm').submit();
    });

    document.getElementById('saveEventButton').addEventListener('click', function() {
        // Redirect to organizer81118.php with an alert
        alert('Event saved successfully');
        window.location.href = 'organizer81118.php';
    });
</script>
   
</body>
</html>