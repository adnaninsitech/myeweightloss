<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "bcfce682fe85ed4aa790570d647f66de9f1e450e07"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/page-template/about.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/page-template/about_2022-03-25-15.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php /* Template Name: About Template */ ?>

<?php get_header(); 

$section1abt = get_field('abt_section_1');

?>


<section class="about-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="img-box">
                    <img src="<?php echo $section1abt['image']; ?>" alt="" class="img-fluid">
                </div>
            </div>

            <div class="col-lg-7 col-md-12 col-sm-12">
                <div class="text">
                <?php echo $section1abt['content']; ?>
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
    
  <?php $index_query = new WP_Query(array('post_type'=>'page', 'p'=>'9')); ?>
<?php while ($index_query->have_posts()) : $index_query->the_post(); ?>   
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
                                                   <?php echo get_sub_field('answer'); ?>
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
    
    <?php endwhile; ?>

<?php get_footer(); ?>