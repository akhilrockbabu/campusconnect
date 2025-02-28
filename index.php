<?php
require 'vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$eventsCollection = $db->events;

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
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="index.php">Home</a></li>
                                            <li><a href="about.html">About</a></li>
                                            <li><a href="checkstatus.php">Check Status</a></li>
                                            <li><a href="log_reg.html">Login</a></li>
                                            <li><a href="contact.html">Contact</a></li>
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
                            <span class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".3s">25 Sep - 28 Sep, 2024</span>
                            <h3 class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".4s">MCA</h3>
                            <h3 class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".4s">Takshak  2024</h3>
                            <p class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".5s">Department of Computer Applications, MACE</p>
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
                $events = $eventsCollection->find(['status' => 'live']);;
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
                            <p class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s" style="text-align:justify;">As Mar Athanasius College of Engineering rekindles the spirit of its renowned national technological festival, Takshak'24, we stand on the brink of an extraordinary celebration of ingenuity and innovation. We aim to ignite your curiosity with thought-provoking talks, an extensive array of exhibitions, a spectacular motor show, and an exhilarating exchange of ideas as tech enthusiasts from diverse backgrounds come together.
                                It is a testament to the simple wonders of life and honours the common bond between the branches within our college and the general public. We open our doors to young engineers from across the nation, offering a platform to ignite their scientific passion with the vast array of knowledge on display. As we prepare to unveil the 17th edition of Takshak, it promises to be a vibrant tapestry, interweaving the brilliance of future innovation with the charm of vintage nostalgia.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6">
                    <div class="about_thumb">
                        <div class="shap_3  wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".4s">
                            <img src="img/shape/shape_3.svg" alt="">
                        </div>
                        <div class="thumb_inner  wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">
                            <img src="img/about/Group 1.jpg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6">
                    <div class="about_info pl-68">
                        <h4 class=" wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".5s">MCA TAKSHAK</h4>
                        <p class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".6s" style="text-align: justify;">Takshak 2024, hosted by the Department of Computer Applications at Mar Athanasius College of Engineering, is an exciting platform for showcasing talent and innovation. This year's edition will feature a variety of competitions and exhibitions, including web designing, video editing, coding, digital art, and prompt designing, all aimed at promoting creativity and technical skills among participants. The event will span three days, from September 26th to 28th, providing students from across Kerala the opportunity to engage in stimulating challenges and demonstrate their expertise in science and technology.</p>
                        <a href="event.html" class="boxed-btn3  wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".7s">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- about_area_end  -->

    <div class="program_details_area detials_bg_1 overlay2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section_title text-center mb-80  wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">
                        <h3>Program Details</h3>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="program_detail_wrap">
                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class=" wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".3s">9 AM - 12 PM</span>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">25 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Seminar Hall</h4>
                                </div>
                                <div class="thumb wow fadeInUp" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img/Workshop.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".6s">Workshop</h4>
                            </div>
                        </div>
                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">9 AM - 5 PM</span>
                                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">26 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Seminar Hall</h4>
                                </div>
                                <div class="thumb wow fadeInRight" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img/techtrivia.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".6s">Tech Trivia</h4>
                            </div>
                        </div>
                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class=" wow fadeInLeft" data-wow-duration="1s" data-wow-delay=".3s">Afternoon</span>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">26 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Lab 1</h4>
                                </div>
                                <div class="thumb  wow fadeInUp" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img\codomizer.jpg" alt="">
                                </div>
                                <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".6s">Codomizer</h4>
                            </div>
                        </div>
                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">Afternoon</span>
                                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">26 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Lab 2</h4>
                                </div>
                                <div class="thumb wow fadeInRight" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img\promptify.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".6s">Promptify</h4>
                            </div>
                        </div>
                        
                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">9 AM - 5 PM</span>
                                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">27 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Seminar Hall</h4>
                                </div>
                                <div class="thumb wow fadeInRight" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img\asar.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".6s">Asar Dagyara</h4>
                            </div>
                        </div>

                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">9 AM - 5 PM</span>
                                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">27 - 28 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Lab 1</h4>
                                </div>
                                <div class="thumb wow fadeInRight" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img\valorant.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".6s">Valorant Vortex</h4>
                            </div>
                        </div>

                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">Forenoon</span>
                                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">27 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Lab 2</h4>
                                </div>
                                <div class="thumb wow fadeInRight" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img\hacknslash.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".6s">Hack & Slash</h4>
                            </div>
                        </div>


                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">Afternoon</span>
                                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">27 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Lab 2</h4>
                                </div>
                                <div class="thumb wow fadeInRight" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img\futureframes.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".6s">Future Frames</h4>
                            </div>
                        </div>

                        <div class="single_propram">
                            <div class="inner_wrap">
                                <div class="circle_img"></div>
                                <div class="porgram_top">
                                    <span class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">9 AM - 5 PM</span>
                                    <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".4s">28 Sep 2024</h4>
                                    <h4 class=" wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">MCA Seminar Hall & Lab 1</h4>
                                </div>
                                <div class="thumb wow fadeInRight" data-wow-duration="1s" data-wow-delay=".5s">
                                    <img src="img\EditAThon.jpg" alt="">
                                </div>
                                <h4 class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".6s">Edit-a-Thon</h4>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer_start  -->
    <footer class="footer">
        <div class="footer_top">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="footer_widget">
                            <div class="address_details text-center">
                                <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">25 Sep - 28 Sep, 2024</h4>
                                <h3 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s">Department of Computer Applications, MACE</h3>
                                <a href="events.html" class="boxed-btn3 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".6s">Register Now</a>
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
                        <p class="copy_right text-center wow fadeInDown" data-wow-duration="1s" data-wow-delay=".5s">Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved |<a href="https://www.mace.ac.in/computer-application">Department of Computer Applications, MACE</a></p>
                        <p class="copy_right text-center wow fadeInDown" data-wow-duration="1s" data-wow-delay=".5s">Developed By:</p>
                        <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".4s" style="text-align: center; color: white;">
                            <a href="https://www.instagram.com/rockiey._">Akhil Rock Babu</a> | 
                            <a href="https://www.instagram.com/aj__wildr">Ajaidev S</a> | 
                            <a href="https://www.instagram.com/__.anurag_mohan.__">Anurag Mohan</a> | 
                            <a href="https://www.instagram.com/d0na_jince">Dona Jince</a> | 
                            <a href="https://www.instagram.com/nikhi__la">Nikhila Baby</a>
                          </h4>
                          
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
