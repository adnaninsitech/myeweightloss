<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "70b0bfa14edb3f90e758ff133f380d1a9f1e450e07"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/header.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/header_2022-03-25-16.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<title><?php bloginfo( 'title' ); ?></title>
<?php wp_head();?>
	

        
</head>
<?php
global $options;
global $logo;
global $copyrite;
$options = get_option('cOptn');
$logo = $options['logo'];
$copyrite = $options['copyrite'];
$size = 344;
$options['logo'] = wp_get_attachment_image($logo, array($size, $size), false);
$att_img = wp_get_attachment_image($logo, array($size, $size), false);
$logoSrc = wp_get_attachment_url($logo);
$att_src_thumb = wp_get_attachment_image_src($logo, array($size, $size), false);

?>


    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
<body <?php body_class(); ?>>


<header>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.php"><img src="<?php echo $logoSrc; ?>" class="img-fluid"  alt=""></a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <div class="header-call">
                                <a href="tel:<?php echo $options['phone_number']; ?>">Call <?php echo $options['phone_number']; ?></a>
                            </div>
                            	<?php wp_nav_menu( array('menu'=> 'Menu 1','menu_class' => '', 'container' => false ) ); ?>
                           
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link home-link" href="<?php echo site_url(); ?>">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link about-link" href="<?php echo site_url(); ?>/about-us/">About Us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ser-link" href="<?php echo site_url(); ?>/about-us/services.php">Services</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link faq-link" href="faqs.php">FAQs</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link contact-link" href="contact.php">Contact Us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link search" href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/search-icon.png" class="img-fluid" alt=""></a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    
  <?php if(Is_home()|| is_front_page() ) { ?>




<?php } else { ?>


<section class="inner-title <?php if(is_page(65)) {echo 'about-title'; } else if(is_page(73))  {echo 'services-title' ; } else if(is_page(80)) {echo 'faqs-title' ;} else if(is_page(88)) {echo 'contact-title'; } ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="text">
                    <h1><?php echo the_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>








<?php } ?>  
    

