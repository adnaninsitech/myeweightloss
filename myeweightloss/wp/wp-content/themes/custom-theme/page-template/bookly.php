<?php /* Template Name: Bookly Template */ ?>

<?php get_header(); 


?>
<?php while ( have_posts() ) : the_post(); ?>

<section class="about-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                
                <div class="text">
                <?php wpautop(the_content()); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endwhile; wp_reset_query(); ?>

<?php get_footer(); ?>