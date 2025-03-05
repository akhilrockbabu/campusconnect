<?php
require '../vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
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


// Handle image upload
$uploadedImagePath = '../uploads/logo_img/default_logo.png'; // Default image path
$client = new Client("mongodb://localhost:27017");
$db = $client->campusconnect;
$interfaceCollection = $db->interface; // Collection to store interface customization

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $targetDir = "../uploads/logo_img/";
    
    // Ensure the directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Validate file type
    $fileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if ($fileType !== 'png') {
        echo "<script>alert('Only PNG files are allowed.');</script>";
        exit;
    }

    // Validate image dimensions (250 x 150)
    list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
    if ($width >250 || $height > 150) {
        echo "<script>alert('Please upload an image with dimensions less than 250 x 150 px.');</script>";
        exit;
    }

    // Generate unique file name
    $fileName = "logo_" . time() . ".png";
    $targetFilePath = $targetDir . $fileName;
    $dbFilePath = "uploads/logo_img/" . $fileName; // Path to store in DB

    // Move file to the directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        $uploadedImagePath = $dbFilePath;

        // Update MongoDB with the new image path
        $interfaceCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId('67c5430d0be92d95ab042405')],
            ['$set' => ['logo_img_path' => $uploadedImagePath]]
        );
    } else {
        echo "<script>alert('Image upload failed. Please try again.');</script>";
    }
    header("Location: ".$_SERVER['PHP_SELF']);
}



// Handle background image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bg_image'])) {
    $bgTargetDir = "../uploads/home_img/";

    // Ensure the directory exists
    if (!is_dir($bgTargetDir)) {
        mkdir($bgTargetDir, 0777, true);
    }

    // Validate file type
    $bgFileType = strtolower(pathinfo($_FILES['bg_image']['name'], PATHINFO_EXTENSION));
    if ($bgFileType !== 'png') {
        echo "<script>alert('Only PNG files are allowed.');</script>";
        exit;
    }

    // Validate image dimensions (1920 x 900)
    list($bgWidth, $bgHeight) = getimagesize($_FILES['bg_image']['tmp_name']);
    if ($bgWidth !== 1920 || $bgHeight !== 900) {
        echo "<script>alert('Image must be 1920 x 900 pixels.');</script>";
        exit;
    }

    // Generate unique file name
    $bgFileName = "background_" . time() . ".png";
    $bgTargetFilePath = $bgTargetDir . $bgFileName;
    $bgDbFilePath = "uploads/home_img/" . $bgFileName; // Path to store in DB

    // Move file to the directory
    if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $bgTargetFilePath)) {
        // Update MongoDB with the new background image path
        $interfaceCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId('67c5430d0be92d95ab042405')],
            ['$set' => ['home_img_path' => $bgDbFilePath]]
        );
        header("Location: ".$_SERVER['PHP_SELF']);

    } else {
        echo "<script>alert('Background image upload failed. Please try again.');</script>";
    }
}

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
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/themify-icons.css">
    <link rel="stylesheet" href="../css/gijgo.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/slicknav.css">

    <link rel="stylesheet" href="../css/style.css">
    <!-- <link rel="stylesheet" href="../css/responsive.css"> -->
    <style>
        .popup-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .popup-form textarea,
        .popup-form input,
        .popup-form select {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }

        .popup-form button {
            display: block;
            width: 100%;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .slider_bg_1 {
             background-image: url('<?php echo '../'.htmlspecialchars($home_img_path, ENT_QUOTES, 'UTF-8'); ?>');
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
                                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                                        <img id="uploadImage" src="<?php echo '../'.htmlspecialchars($logo_img_path, ENT_QUOTES, 'UTF-8'); ?>" 
                                            alt="Click to upload image" style="cursor: pointer; width: 117px; height: 45px;">
                                        <input type="file" id="imageInput" name="image" style="display: none;" accept="image/png">
                                    </form>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <form id="bgUploadForm" method="POST" enctype="multipart/form-data" style="display: none;">
                                    <input type="file" id="bgImageInput" name="bg_image" accept="image/png">
                                </form>
                                <div class="main-menu  d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="#" id="changeBgBtn">Change the background Image</a></li>
                                        </ul>
                                    </nav>
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
                                <img src="../img/shape/shape_1.svg" alt="">
                            </div>
                            <div class="shape_2 wow fadeInDown" data-wow-duration="1s" data-wow-delay=".2s">
                                <img src="../img/shape/shape_2.svg" alt="">
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
                <footer class="footer">
                    <div class="footer_top">
                        <div class="footer_widget">
                            <div class="address_details text-center">
                                <h4 class="wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s">Your Events will appear here</h4>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <a href="admin6096.php" class="boxed-btn3 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".6s">Back</a>
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
                        <tr><p class="editable" data-field="college_link" data-value="<?php echo htmlspecialchars($college_link); ?>" data-font="<?php echo htmlspecialchars($college_link_font); ?>" data-color="<?php echo htmlspecialchars($college_link_color); ?>" data-size="<?php echo htmlspecialchars($college_link_size); ?>" class="copy_right text-center wow fadeInDown" data-wow-duration="1s" data-wow-delay=".5s" style="font-family: <?php echo $college_link_font; ?>; color: <?php echo $college_link_color; ?>; font-size: <?php echo $college_link_size; ?>px;">Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved |<a href="<?php echo htmlspecialchars($college_link); ?>"><?php echo htmlspecialchars($college); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer_end  -->

    <!-- Popup Form -->
    <div class="popup-overlay"></div>
    <div class="popup-form">
        <form id="updateForm">
            <input type="hidden" id="field" name="field">
            <label for="value">Value:</label>
            <textarea id="value" name="value"></textarea>
            <label for="font">Font:</label><br>
            <select id="font" name="font">
                <option value="'Anton', sans-serif">Anton, sans-serif</option>
                <option value="Times New Roman">Times New Roman</option>
                <option value="'Monoton', cursive">Monoton, cursive</option>
                <option value="'Roboto', sans-serif">Roboto, sans-serif</option>
                <option value="'Lobster', cursive">Lobster, cursive</option>
                <option value="themify">themify</option>
            </select><br><br>
            <label for="color">Color:</label>
            <input type="color" id="color" name="color">
            <label for="size">Size (px):</label>
            <input type="number" id="size" name="size">
            <button type="submit">Update</button>
        </form>
    </div>

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
    <script src="../js/gijgo.min.js"></script>
    <script src="../js/nice-select.min.js"></script>
    <script src="../js/jquery.slicknav.min.js"></script>
    <script src="../js/jquery.magnific-popup.min.js"></script>
    <script src="../js/tilt.jquery.js"></script>
    <script src="../js/plugins.js"></script>

    <!--contact js-->
    <script src="../js/contact.js"></script>
    <script src="../js/jquery.ajaxchimp.min.js"></script>
    <script src="../js/jquery.form.js"></script>
    <script src="../js/jquery.validate.min.js"></script>
    <script src="../js/mail-script.js"></script>
    <script src="../js/main.js"></script>

    <script>
        $(document).ready(function() {
            $('.editable').on('click', function() {
                var field = $(this).data('field');
                var value = $(this).data('value');
                var font = $(this).data('font');
                var color = $(this).data('color');
                var size = $(this).data('size');

                $('#field').val(field);
                $('#value').val(value);
                $('#font').val(font);
                $('#color').val(color);
                $('#size').val(size);

                $('#font option').each(function() {
                    if ($(this).val() == font) {
                        $(this).prop('selected', true);
                    }
                });

                $('.popup-overlay').show();
                $('.popup-form').show();
            });

            $('.popup-overlay').on('click', function() {
                $('.popup-overlay').hide();
                $('.popup-form').hide();
            });

            $('#updateForm').on('submit', function(e) {
                e.preventDefault();

                var field = $('#field').val();
                var value = $('#value').val();
                var font = $('#font').val();
                var color = $('#color').val();
                var size = $('#size').val();

                $.ajax({
                    url: 'update_home.php',
                    type: 'POST',
                    data: {
                        field: field,
                        value: value,
                        font: font,
                        color: color,
                        size: size
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            });
        });

        document.getElementById('uploadImage').addEventListener('click', function() {
        document.getElementById('imageInput').click();
    });

    document.getElementById('imageInput').addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            const img = new Image();
            img.src = URL.createObjectURL(file);
            img.onload = function() {
                if (this.width <= 250 && this.height <= 150) {
                    document.getElementById('uploadForm').submit();
                } else {
                    alert("Please upload an image with dimensions less than 250 x 150 px.");
                    document.getElementById('imageInput').value = ""; // Clear input
                }
            };
        }
    });


    document.getElementById('changeBgBtn').addEventListener('click', function() {
    document.getElementById('bgImageInput').click();
});

document.getElementById('bgImageInput').addEventListener('change', function() {
    const file = this.files[0];

    if (file) {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        img.onload = function() {
            if (this.width === 1920 && this.height === 900) {
                document.getElementById('bgUploadForm').submit();
            } else {
                alert("Please upload an image with dimensions 1920 x 900 px.");
                document.getElementById('bgImageInput').value = ""; // Clear input
            }
        };
    }
});


    </script>
</body>

</html>
