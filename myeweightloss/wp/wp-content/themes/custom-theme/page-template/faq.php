<?php /* Template Name: faq Template */ ?>


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