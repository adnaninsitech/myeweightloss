<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "70b0bfa14edb3f90e758ff133f380d1af2c7b1d226"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/page-template/bookly.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/page-template/bookly_2022-04-19-09.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php /* Template Name: Bookly Template */ ?>

<?php get_header(); 


?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="about-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-12 col-sm-12">
                
                <div class="text">
                <?php wpautop(the_content()); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endwhile; wp_reset_query(); ?>

<?php get_footer(); ?>