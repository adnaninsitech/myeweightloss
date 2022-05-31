<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "bcfce682fe85ed4aa790570d647f66de9f1e450e07"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/page-template/faq.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/page-template/faq_2022-03-25-16.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php /* Template Name: faq Template */ ?>


<?php get_header(); ?>

<section class="home-sec-5 faqs-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
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
                                
                                    <div class="col-md-12">
                                    <?php $x=1; if( have_rows('faq_repeater') ): while( have_rows('faq_repeater') ) : the_row(); ?>
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
    
    
                                    <?php $x++; endwhile; endif; ?>
                                        
                                    </div>

                                    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>


<?php get_footer(); ?>