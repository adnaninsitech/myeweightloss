<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<?php wpautop(the_content()); ?>
<?php endwhile; wp_reset_query(); ?>
<?php get_footer(); ?>