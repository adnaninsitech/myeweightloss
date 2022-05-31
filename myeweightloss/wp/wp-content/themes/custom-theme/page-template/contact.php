<?php /* Template Name: Contact Template */ ?>


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
        
        <?php echo do_shortcode('[contact-form-7 id="90" title="Contact form 1"]'); ?>
    </div>
</section>

<?php get_footer(); ?>