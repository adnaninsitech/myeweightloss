<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "bcfce682fe85ed4aa790570d647f66de09321b7cdd"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/page-template/home.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/page-template/home_2022-03-24-21.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php /* Template Name: Home Template */ ?>


<?php get_header(); 

$banner = get_field('banner'); 
$left = get_field('section_1_left'); 
$right = get_field('section_1_right'); 



?>


<section class="banner-sec">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-6">
                    <div class="text">
                        <?php echo $banner['content']; ?>
                        <a href="<?php echo $banner['link']; ?>" class="learn">LEARN MORE</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="home-sec-1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-5 bg-green">
                    <div class="left-box">
                        <h2><?php echo $left['title']; ?></h2>
                       <?php echo $left['content']; ?>
                        <a href="<?php echo $left['link']; ?>" class="make">Make An Appointment</a>
                    </div>
                </div>
                <div class="col-lg-7 no-pad">
                    <div class="right-box bg-green-2">
                        <h2><?php echo $right['title']; ?></h2>
                         <?php echo $right['content']; ?>
                        <a href="<?php echo $right['link']; ?>" class="about">About Us</a>  
                        <div class="icon-img">
                            <img src="<?php echo $right['image']; ?>" class="img-fluid" alt="">
                        </div>      
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="home-sec-2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading">
                        <span>Services</span>
                        <h2>Your weight loss support</h2>
                    </div>
                </div>
            </div>
            <div class="row">
            
             <?php  $index_query = new WP_Query(array( 'post_type' => 'jobs', 'posts_per_page' => '-1' , 'order'=>'asc')); ?>
    <?php while ($index_query->have_posts()) : $index_query->the_post(); ?>  
    
                <div class="col-lg-4">
                    <div class="box box1">
                        <div class="img-box">
                            <img src="assets/images/home/sec-2/1.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="arrow-box">
                            <h3>FDA Approved Medications</h3>
                            <a><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
<?php endwhile; wp_reset_query(); ?>
                <div class="col-lg-4">
                    <div class="box box2">
                        <div class="img-box">
                            <img src="assets/images/home/sec-2/2.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="arrow-box">
                            <h3>Nutrition Plans</h3>
                            <a><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="box box3">
                        <div class="img-box">
                            <img src="assets/images/home/sec-2/3.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="arrow-box">
                            <h3>Counseling</h3>
                            <a><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>