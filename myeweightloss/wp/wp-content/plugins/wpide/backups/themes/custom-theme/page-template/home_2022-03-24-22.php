<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "bcfce682fe85ed4aa790570d647f66de09321b7cdd"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/page-template/home.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/page-template/home_2022-03-24-22.php") )  ) ){
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
$bio = get_field('bio_section'); 



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
            
             <?php $x=1;  $index_query = new WP_Query(array( 'post_type' => 'services', 'posts_per_page' => '-1' , 'order'=>'asc')); ?>
    <?php while ($index_query->have_posts()) : $index_query->the_post(); ?>  
    
                <div class="col-lg-4">
                    <div class="box box<?php echo $x;?>">
                        <div class="img-box">
                            <img src="<?php echo the_post_thumbnail_url(); ?>" class="img-fluid" alt="">
                        </div>
                        <div class="arrow-box">
                            <h3><?php echo the_title(); ?></h3>
                            <a><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                
        <?php $x++; endwhile; wp_reset_query(); ?>
                
            </div>
        </div>
    </section>
    
<section class="home-sec-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="text">
                     <?php echo $bio['content']; ?>
                        
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="box">
                        <img src="<?php echo $bio['image_1']; ?>" class="img-fluid girl" alt="">
                        <img src="<?php echo $bio['image_2']; ?>" class="img-fluid sign" alt="">
                        <a href="<?php echo $bio['link']; ?>" class="contact">Contact Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="home-sec-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text">
                        <h2>Sign up for your first consultation</h2>
                        <form action="">
                            <input type="text" placeholder="Enter Email Address">
                            <input type="submit" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
    <section class="home-sec-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="heading">
                        <span>MY EWEIGHT LOSS JOURNEY</span>
                        <h2>Frequently Asked Questions</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="main">
                        <div class="container">
                            <div class="accordion" id="faq">
                                <div class="row">
                                <?php $x=1; if( have_rows('faq') ): while( have_rows('faq') ) : the_row(); ?>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header" id="faqhead<?php echo $x; ?>">
                                                <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse"
                                                    data-target="#faq<?php echo $x; ?>" aria-expanded="true" aria-controls="faq<?php echo $x; ?>"><?php echo get_sub_field('question'); ?></a>
                                            </div>

                                            <div id="faq<?php echo $x; ?>" class="collapse" aria-labelledby="faqhead<?php echo $x; ?>"
                                                data-parent="#faq">
                                                <div class="card-body">
                                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime
                                                    mollitia,
                                                    molestiae quas vel sint commodi repudiandae consequuntur voluptatum
                                                    laborum
                                                    numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga
                                                    praesentium
                                                    optio, eaque rerum! Provident similique accusantium nemo autem.
                                                    Veritatis
                                                    obcaecati tenetur iure eius earum ut molestias architecto voluptate
                                                    aliquam
                                                    nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat,
                                                    odit,
                                                    tenetur error, harum nesciunt ipsum debitis quas aliquid.
                                                </div>
                                            </div>
                                        </div>

                                
                                        
                                    </div>
                                    
                                    <?php $x++; endwhile; endif; ?>

                                    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section> 

<?php get_footer(); ?>