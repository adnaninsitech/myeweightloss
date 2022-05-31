<?php /* Template Name: service Template */ ?>


<?php get_header(); ?>


<section class="services-page">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="top-text">
                   <?php echo the_content(); ?>
                </div>
            </div>
        </div>
        <?php  $index_query = new WP_Query(array( 'post_type' => 'services', 'posts_per_page' => '-1' , 'order'=>'asc')); ?>
    <?php while ($index_query->have_posts()) : $index_query->the_post(); ?>  
                 <div class="ser-box">
                    <div class="img-box">
                        <img src="<?php echo the_post_thumbnail_url(); ?>" alt="" class="img-fluid">
                    </div>
                    <div class="text">
                        <h2><?php echo the_title(); ?></h2>
                        <?php echo the_content(); ?>
                       <a href="#">Make An Appointment</a>
                    </div>
                </div>
                
                <?php endwhile; wp_reset_query(); ?>

       
    </div>
</section>

<?php $index_query = new WP_Query(array('post_type'=>'page', 'p'=>'9')); ?>
<?php while ($index_query->have_posts()) : $index_query->the_post(); ?> 
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
                                    <div class="col-lg-6 col-md-12 col-sm-12">
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
