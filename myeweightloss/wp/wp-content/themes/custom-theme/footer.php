 <?php
global $options;
global $logo;
global $copyrite;
$options = get_option('cOptn');
$logo = $options['logo'];
$copyrite = $options['copyright']
 ?>



<footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="box1">
                        <?php echo $options['FooterAbout']; ?>
                        <p class="privacy"><a href="">Privacy Policy</a>    |   <a href="">Terms & Conditions</a></p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="box2">
                        <h2>Quick Links</h2>
                        <div class="links">
                        <?php wp_nav_menu( array('menu'=> 'Links','menu_class' => 'links', 'container' => false ) ); ?>
                           <!-- <a href="">Home</a>
                            <a href="">About Us</a>
                            <a href="">Services</a>
                            <a href="">FAQs</a>
                            <a href="">Contact Us</a>-->
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="box3">
                        <h2>Get Connected</h2>
                        <div class="social-links">
                            <a href=""><img src="<?php echo get_template_directory_uri(); ?>/assets/images/footer/icon-1.png" alt=""></a>
                            <a href=""><img src="<?php echo get_template_directory_uri(); ?>/assets/images/footer/icon-2.png" alt=""></a>
                            <a href=""><img src="<?php echo get_template_directory_uri(); ?>/assets/images/footer/icon-3.png" alt=""></a>
                            <a href=""><img src="<?php echo get_template_directory_uri(); ?>/assets/images/footer/icon-4.png" alt=""></a>
                        </div>
                        <h2 class="contact">Contact</h2>
                        <p><?php echo $options['Address']; ?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="copy-right">
                        <p><?php echo $options['copyright']; ?> <a href="https://insitech.ae/" target="_blank">Insitech&nbsp; <img src="<?php echo get_template_directory_uri(); ?>/assets/images/footer/insitech.png" alt=""></a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>



 

<?php wp_footer(); ?>
<script>
        AOS.init({
            duration: 1200,
        });

    </script>
</body>
</html>