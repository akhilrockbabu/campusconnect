<?php
require 'vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;
$interfaceCollection = $db->interface;
$home = $interfaceCollection->findOne(['page' => 'home']);

$date = $home['program_date']['value'];
$date_font = $home['program_date']['font'];
$date_color = $home['program_date']['color'];
$date_size = $home['program_date']['size'];

$name1 = $home['program_name1']['value'];
$name1_font = $home['program_name1']['font'];
$name1_color = $home['program_name1']['color'];
$name1_size = $home['program_name1']['size'];

$name2 = $home['program_name2']['value'];
$name2_font = $home['program_name2']['font'];
$name2_color = $home['program_name2']['color'];
$name2_size = $home['program_name2']['size'];

$college = $home['college_name']['value'];
$college_font = $home['college_name']['font'];
$college_color = $home['college_name']['color'];
$college_size = $home['college_name']['size'];

$about_program = $home['about_program']['value'];
$about_program_font = $home['about_program']['font'];
$about_program_color = $home['about_program']['color'];
$about_program_size = $home['about_program']['size'];

$college_link = $home['college_link']['value'];
$college_link_font = $home['college_link']['font'];
$college_link_color = $home['college_link']['color'];
$college_link_size = $home['college_link']['size'];

$home_img_path = $home['home_img_path'];
$logo_img_path = $home['logo_img_path'];
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>MCA Takshak</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/gijgo.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/slicknav.css">

    <link rel="stylesheet" href="css/style.css">
    <!-- <link rel="stylesheet" href="css/responsive.css"> -->

    <style type="text/css">
        .slider_bg_1 {
             background-image: url('<?php echo htmlspecialchars($home_img_path, ENT_QUOTES, 'UTF-8'); ?>');
             }
    </style>
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
                                        <img src="<?php echo htmlspecialchars($logo_img_path, ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="main-menu  d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="index.php">Home</a></li>
                                            <li><a href="checkstatus.php">Check Status</a></li>
                                            <li><a href="log_reg.html">Login</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="buy_tkt">
                                    <div class="book_btn d-none d-lg-block">
                                        <a href="events.php">Register Now</a>
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

    <!-- slider_area_start -->
    <div class="slider_area">
        <div class="single_slider  d-flex align-items-center slider_bg_1 overlay">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-xl-12">
                        <div class="slider_text text-center">
                            <div class="shape_1 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".2s">
                                <img src="img/shape/shape_1.svg" alt="">
                            </div>
                            <div class="shape_2 wow fadeInDown" data-wow-duration="1s" data-wow-delay=".2s">
                                <img src="img/shape/shape_2.svg" alt="">
                            </div>
                            <span class="editable" data-field="program_date" data-value="<?php echo htmlspecialchars($date); ?>" data-font="<?php echo htmlspecialchars($date_font); ?>" data-color="<?php echo htmlspecialchars($date_color); ?>" data-size="<?php echo htmlspecialchars($date_size); ?>" style="font-family: <?php echo $date_font; ?>; color: <?php echo $date_color; ?>; font-size: <?php echo $date_size; ?>px;"><?php echo htmlspecialchars($date);?></span>
                            <h3 class="editable" data-field="program_name1" data-value="<?php echo htmlspecialchars($name1); ?>" data-font="<?php echo htmlspecialchars($name1_font); ?>" data-color="<?php echo htmlspecialchars($name1_color); ?>" data-size="<?php echo htmlspecialchars($name1_size); ?>" style="font-family: <?php echo $name1_font; ?>; color: <?php echo $name1_color; ?>; font-size: <?php echo $name1_size; ?>px;"><?php echo htmlspecialchars($name1);?></h3>
                            <h3 class="editable" data-field="program_name2" data-value="<?php echo htmlspecialchars($name2); ?>" data-font="<?php echo htmlspecialchars($name2_font); ?>" data-color="<?php echo htmlspecialchars($name2_color); ?>" data-size="<?php echo htmlspecialchars($name2_size); ?>" style="font-family: <?php echo $name2_font; ?>; color: <?php echo $name2_color; ?>; font-size: <?php echo $name2_size; ?>px;"><?php echo htmlspecialchars($name2);?></h3>
                            <p class="editable" data-field="college_name" data-value="<?php echo htmlspecialchars($college); ?>" data-font="<?php echo htmlspecialchars($college_font); ?>" data-color="<?php echo htmlspecialchars($college_color); ?>" data-size="<?php echo htmlspecialchars($college_size); ?>" style="font-family: <?php echo $college_font; ?>; color: <?php echo $college_color; ?>; font-size: <?php echo $college_size; ?>px;"><?php echo htmlspecialchars($college);?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- slider_area_end -->
    
    <!-- performar_area_start  -->
    <div class="performar_area black_bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section_title mb-80">
                        <h3 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">Our Events</h3>
                    </div>
                </div>
            </div>
            <?php
                $events = $eventsCollection->find(['status' => 'live', 'event_limit' => ['$gt' => 0]]);
                $eventCount = 0;
                foreach ($events as $event) {
                    $eventPosterPath = preg_replace('/^\.\.\//', '', $event['event_poster']);
                    
                    if ($eventCount % 2 == 0) {
                        if ($eventCount > 0) {
                            echo '</div></div></div>'; 
                        }
                        echo '<div class="row justify-content-center"><div class="col-lg-8"><div class="row">';
                    }
            ?>
            <div class="col-lg-6 col-md-6">
                <div class="single_performer wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">
                    <div data-tilt class="thumb">
                        <a href="display_event.php?event_id=<?php echo urlencode($event['_id']); ?>">
                            <img src="<?php echo htmlspecialchars($eventPosterPath); ?>" alt="Document Preview">
                        </a>
                    </div>
                    <div class="performer_heading">
                        <h4><?php echo htmlspecialchars($event['event_name']); ?></h4>
                        <span><?php echo htmlspecialchars($event['event_desc']); ?></span>
                    </div>
                </div>
            </div>
            <?php
                    $eventCount++;
                }
                if ($eventCount % 2 != 0) {
                    echo '</div></div></div>'; 
                }
                if ($eventCount == 0) {
                    echo '  <footer class="footer">
                                <div class="footer_top">
                                    <div class="footer_widget">
                                        <div class="address_details text-center">
                                            <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">No Events are currently available!</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>'; }
            ?>
        </div>
    </div>
    <!-- performar_area_end  -->

    <!-- about_area_start  -->
    <div class="about_area black_bg extra_padd">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section_title text-center mb-80">
                        <h3 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s" >About Program</h3>
                        <div style="text-align: center;">
                            <p class="editable" data-field="about_program" data-value="<?php echo htmlspecialchars($about_program); ?>" data-font="<?php echo htmlspecialchars($about_program_font); ?>" data-color="<?php echo htmlspecialchars($about_program_color); ?>" data-size="<?php echo htmlspecialchars($about_program_size); ?>" class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s" style="text-align:justify; font-family: <?php echo $about_program_font; ?>; color: <?php echo $about_program_color; ?>; font-size: <?php echo $about_program_size; ?>px;"><?php echo htmlspecialchars($about_program); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- about_area_end  -->

    <!-- footer_start  -->
    <footer class="footer">
        <div class="footer_top">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="footer_widget">
                            <div class="address_details text-center">
                                <h4 class="editable" data-field="program_date" data-value="<?php echo htmlspecialchars($date); ?>" data-font="<?php echo htmlspecialchars($date_font); ?>" data-color="<?php echo htmlspecialchars($date_color); ?>" data-size="<?php echo htmlspecialchars($date_size); ?>" class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s" style="font-family: <?php echo $date_font; ?>; color: <?php echo $date_color; ?>; font-size: <?php echo $date_size; ?>px;"><?php echo htmlspecialchars($date); ?></h4>
                                <h3 class="editable" data-field="college_name" data-value="<?php echo htmlspecialchars($college); ?>" data-font="<?php echo htmlspecialchars($college_font); ?>" data-color="<?php echo htmlspecialchars($college_color); ?>" data-size="<?php echo htmlspecialchars($college_size); ?>" class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s" style="font-family: <?php echo $college_font; ?>; color: <?php echo $college_color; ?>; font-size: <?php echo $college_size; ?>px;"><?php echo htmlspecialchars($college); ?></h3>
                                <a href="events.php" class="boxed-btn3 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".6s">Register Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copy-right_text">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <p class="copy_right text-center wow fadeInDown" data-wow-duration="1s" data-wow-delay=".5s" style="font-family: <?php echo $college_link_font; ?>; color: <?php echo $college_link_color; ?>; font-size: <?php echo $college_link_size; ?>px;">Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved |<a href="<?php echo htmlspecialchars($college_link); ?>"><?php echo htmlspecialchars($college); ?></a></p>
                        <p class="copy_right text-center wow fadeInDown" data-wow-duration="1s" data-wow-delay=".5s">Developed By:</p>
                        <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s" style="text-align: center; color: white;">
                            <a href="https://www.instagram.com/rockiey._">Akhil Rock Babu</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer_end  -->

    <!-- JS here -->
    <script src="js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/ajax-form.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/scrollIt.js"></script>
    <script src="js/jquery.scrollUp.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/gijgo.min.js"></script>
    <script src="js/nice-select.min.js"></script>
    <script src="js/jquery.slicknav.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/tilt.jquery.js"></script>
    <script src="js/plugins.js"></script>



    <!--contact js-->
    <script src="js/contact.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/mail-script.js"></script>


    <script src="js/main.js"></script>
</body>

</html>
