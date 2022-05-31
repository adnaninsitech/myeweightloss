<?php /* start WPide restore code */
                                    if ($_POST["restorewpnonce"] === "bcfce682fe85ed4aa790570d647f66de09321b7cdd"){
                                        if ( file_put_contents ( "/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/themes/custom-theme/functions.php" ,  preg_replace("#<\?php /\* start WPide(.*)end WPide restore code \*/ \?>#s", "", file_get_contents("/home/creapkxk/insitechstaging.com/demo/myeweightloss/wp/wp-content/plugins/wpide/backups/themes/custom-theme/functions_2022-03-24-20.php") )  ) ){
                                            echo "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file.";
                                        }
                                    }else{
                                        echo "-1";
                                    }
                                    die();
                            /* end WPide restore code */ ?><?php
add_theme_support( 'post-thumbnails' );
add_filter( 'use_block_editor_for_post_type', '__return_false' );
add_action( 'after_setup_theme', 'register_custom_nav_menus' );
function register_custom_nav_menus() {
  register_nav_menus( array(
    'header_menu' => 'Header Menu',
    'header_menu_reponsive' => 'Header Menu Reponsive',
    'footer_menu' => 'Footer Menu',
  )
);
}

require_once ( get_stylesheet_directory() . '/theme-option.php' );
function theme_scripts() {

  
   
    
    
wp_enqueue_style( 'aos', get_template_directory_uri() . '/assets/css/aos-animate.css' );
wp_enqueue_style( 'lib', get_template_directory_uri() . '/assets/css/lib.css' );
    wp_enqueue_style( 'custom-style', get_template_directory_uri() . '/assets/css/style.css' );
    wp_enqueue_style( 'theme-style', get_stylesheet_uri());   
   
    wp_enqueue_script( 'aos-js', get_template_directory_uri() . '/assets/js/aos-animate.js', array(), '1.0.0', true );
    wp_enqueue_script( 'lib-js', get_template_directory_uri() . '/assets/js/lib.js', array(), '1.0.0', true );
    
   
  }
add_action( 'wp_enqueue_scripts', 'theme_scripts' );


function widget_registration(){
    register_sidebar( array(
        'name' => 'Shop Widget',
        'id' => 'shop-page-widget',
        'description' => $description,
        'before_widget' => '<div class="sidepro">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'widget_registration');

function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 260,
        //'subcategory_archive_thumbnail_size' => 2000,
        'single_image_width'    => 461,

        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 12,
            'default_columns' => 4,
            'min_columns'     => 3,
            'max_columns'     => 8,
        ),
    ) );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    //add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

function disable_woo_commerce_sidebar() {
    remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10); 
}
add_action('init', 'disable_woo_commerce_sidebar');

function woocommerce_product_loop_start() { echo ' <div class="shop-products-wrap"><ul>'; }
function woocommerce_product_loop_end() { echo '</ul></div>'; }


add_action ( "woocommerce_before_shop_loop_item", "after_li_started",11);

function after_li_started () {
    echo '<div class="col-md-12"><div class="probox"><div>';
}


add_action ( "woocommerce_after_shop_loop_item", "before_li_started",4);

function before_li_started () { 
    global $product;
    $product = wc_get_product( get_the_ID() );
$sale_price =   get_post_meta(get_the_id(), '_sale_price' , true);
$regular_price =   get_post_meta(get_the_id(), '_regular_price' , true);
if($sale_price){
    $price = $sale_price;
}elseif($regular_price){
    $price = $regular_price;
}else{
    $price = $product->get_variation_regular_price( 'max', true );
}
    
    echo '</div><div class="procon"><ul class="rate">'.add_star_rating().'</ul><p>'.get_the_title().'</p><h6><span>PKR </span>'.get_woocommerce_currency_symbol().$price.'</h6> <h5><span>PKR </span>'.get_woocommerce_currency_symbol().$price.'</h5> <a href="'.get_the_permalink().'?add-to-cart='.get_the_ID().'" class="cartbtn">Add to cart</a>
                           </div>
                        </div>
                     </div>';

}

add_action('woocommerce_before_shop_loop_item_title','middle_li_started',11);
function middle_li_started()
{
    echo '';
}
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );



add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );
function wcs_woo_remove_reviews_tab($tabs) {
unset($tabs['reviews']);
return $tabs;
}
add_action('init', 'blogs');
function blogs()
{
    $labels = array(
        'name' => _x('Blogs', 'post type general name'),
        'singular_name' => _x('blogs', 'post type singular name'),
        'add_new' => _x('Add New', 'blogs'),
        'add_new_item' => __('Add New Blogs'),
        'edit_item' => __('Edit Blogs'),
        'new_item' => __('New Blogs'),
        'view_item' => __('View Blogs'),
        'search_items' => __('Search Blogs'),
        'not_found' => __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'menu_icon' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title','thumbnail','editor')
    );
    register_post_type('blogs', $args);

}


function add_star_rating() {
global $woocommerce, $product;
$product = wc_get_product(get_the_ID());
$average = $product->get_average_rating();
$rating_count = $product->get_rating_count();

echo '<div class="star-rating"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong> '.__( 'out of 5', 'woocommerce' ).' '.$average.'</span></div><div class="star-rating-box">'.$average.'</div>';
}

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 ); 

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

add_filter( 'woocommerce_product_tabs', 'bbloomer_remove_product_tabs', 9999 );
  
function bbloomer_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] ); 
    return $tabs;
}
add_action( 'woocommerce_single_product_summary', 'add_star_rating', 6 );
add_action( 'woocommerce_single_product_summary','my_wishlist_button',5 );
function my_wishlist_button()
{
    //echo '<div class="wish"><a href="'.get_the_permalink().'?add_to_wishlist='.get_the_ID().'"><i class="far fa-heart"></i></a></div>';
}


function crunchify_stop_loading_wp_embed_and_jquery() {
    if (!is_admin()) {
        wp_deregister_script('wp-embed');
        //wp_deregister_script('jquery');  // Bonus: remove jquery too if it's not required
    }
}
add_action('init', 'crunchify_stop_loading_wp_embed_and_jquery');
remove_action( 'rest_api_init', 'wp_oembed_register_route' );
add_filter( 'embed_oembed_discover', '__return_false' );
remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );
//add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
add_filter('show_admin_bar', '__return_false');
remove_action( 'wp_head', 'wp_resource_hints', 2 );
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
/*
function theme_name_get_year_archive_array() {
  $years = array();
  $years_args = array(
    'type' => 'monthly',
    'format' => 'custom', // My advise: WordPress Core shoud be add support "format=array" to keep it easy to catch for many custom cases
    'before' => '',
    'after' => '|',
    'echo' => false,
    'post_type' => 'post',
    'order' => 'ASC'
  );
 
  // Get Years
  $years_content = wp_get_archives($years_args);
  if (!empty($years_content) ) {
    $years_arr = explode('|', $years_content);
    $years_arr = array_filter($years_arr, function($item) {
      return trim($item) !== '';
    }); // Remove empty whitespace item from array
 
    foreach($years_arr as $year_item) {
      $year_row = trim($year_item);
      preg_match('/href=["\']?([^"\'>]+)["\']>(.+)<\/a>/', $year_row, $year_vars);
 
      if (!empty($year_vars)) {
        $years[] = array(
          'name' => $year_vars[2], // Ex: January 2020
          'value' => $year_vars[1] // Ex: http://demo.com/2020/01/
        );
      }
    }
  }
 
  return $years;
}
*/



 
// Run before the headers and cookies are sent.
//add_action( 'after_setup_theme', 'wpdocs_custom_login' );





/*add_action('init','custom_login');

function custom_login(){
 global $pagenow;
 if( 'wp-login.php' == $pagenow && $_GET['action']!="logout") {
  wp_redirect(home_url());
  exit();
 }
}*/
