<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Shape
 * @since Shape 1.0
 */

get_header(); ?>

        <section class="beer-sec mn-hd">
         <div class="container">
            <form role="search" method="get" id="searchform" action="<?php echo home_url( '/' )?>">
                     <div class="field">
                        <input type="text" name="s" value="<?php echo get_search_query(); ?>"  placeholder="Search...">
                     </div>
                  </form>
            <div class="row align-items-start">
               <div class="col-md-3">
                  <?php get_sidebar( 'primary' ); ?>
               </div>
               <div class="col-md-9">
                  <div class="row align-items-start">
                     <div class="col-md-12">
                        <div class="Drafttext">
                    <?php if ( have_posts() ) : ?>
                    <h4><?php printf( __( 'Search Results for: %s', 'shape' ), '<span>' . get_search_query() . '</span>' ); ?><br><br></h4>
                <?php while ( have_posts() ) : the_post(); ?>

                    <?php the_content(); ?>

                <?php endwhile; ?>

                

            <?php else : ?>

                <h4>Sorry Nothig Found</h4>

            <?php endif; ?>

            </div><!-- #content .site-content -->
        </section><!-- #primary .content-area -->
<?php get_footer(); ?>