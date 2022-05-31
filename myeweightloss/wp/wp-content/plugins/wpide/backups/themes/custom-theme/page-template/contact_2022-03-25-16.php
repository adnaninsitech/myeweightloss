<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "bcfce682fe85ed4aa790570d647f66de9f1e450e07"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/page-template/contact.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/page-template/contact_2022-03-25-16.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php /* Template Name: Contact Template */ ?>


<?php get_header(); ?>


<section class="contact-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="heading">
                    <h2>Get in Touch</h2>
                </div>
            </div>
        </div>
        <!--<form action="">
            <div class="row">
                <div class="col-lg-6">
                    <input type="text" placeholder="Full Name">
                    <input type="email" placeholder="Email">
                    <input type="tel" placeholder="Contact">
                    <input type="text" placeholder="Your Subject">
                </div>
                <div class="col-lg-6">
                    <textarea name="" id="" cols="30" rows="10" placeholder="Your Message Here"></textarea>
                    <input type="submit" value="Submit">
                </div>
            </div>
        </form>-->
        <?php echo do_shortcode('[contact-form-7 id="90" title="Contact form 1"]'); ?>
    </div>
</section>

<?php get_footer(); ?>