<?php
/*
Author: Eddie Machado
URL: http://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, etc.
*/

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'library/bones.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
// require_once( 'library/admin.php' );

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function bones_ahoy() {

  //Allow editor style.
  add_editor_style( get_stylesheet_directory_uri() . '/library/css/editor-style.css' );

  // let's get language support going, if you need it
  load_theme_textdomain( 'bonestheme', get_template_directory() . '/library/translation' );

  // USE THIS TEMPLATE TO CREATE CUSTOM POST TYPES EASILY
  require_once( 'library/custom-post-type.php' );

  // launching operation cleanup
  add_action( 'init', 'bones_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'bones_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'bones_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  bones_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'bones_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'bones_filter_ptags_on_images' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'bones_excerpt_more' );

} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
	$content_width = 680;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'bones-thumb-600', 600, 150, true );
add_image_size( 'bones-thumb-300', 300, 100, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 100 sized image,
we would use the function:
<?php the_post_thumbnail( 'bones-thumb-300' ); ?>
for the 600 x 150 image:
<?php the_post_thumbnail( 'bones-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'bones_custom_image_sizes' );

function bones_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'bones-thumb-600' => __('600px by 150px'),
        'bones-thumb-300' => __('300px by 100px'),
    ) );
}

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

/************* THEME CUSTOMIZE *********************/

/* 
  A good tutorial for creating your own Sections, Controls and Settings:
  http://code.tutsplus.com/series/a-guide-to-the-wordpress-theme-customizer--wp-33722
  
  Good articles on modifying the default options:
  http://natko.com/changing-default-wordpress-theme-customization-api-sections/
  http://code.tutsplus.com/tutorials/digging-into-the-theme-customizer-components--wp-27162
  
  To do:
  - Create a js for the postmessage transport method
  - Create some sanitize functions to sanitize inputs
  - Create some boilerplate Sections, Controls and Settings
*/

function bones_theme_customizer($wp_customize) {
  // $wp_customize calls go here.
  //
  // Uncomment the below lines to remove the default customize sections 

  // $wp_customize->remove_section('title_tagline');
  // $wp_customize->remove_section('colors');
  // $wp_customize->remove_section('background_image');
  // $wp_customize->remove_section('static_front_page');
  // $wp_customize->remove_section('nav');

  // Uncomment the below lines to remove the default controls
  // $wp_customize->remove_control('blogdescription');
  
  // Uncomment the following to change the default section titles
  // $wp_customize->get_section('colors')->title = __( 'Theme Colors' );
  // $wp_customize->get_section('background_image')->title = __( 'Images' );
}

add_action( 'customize_register', 'bones_theme_customizer' );

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function bones_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'bonestheme' ),
		'description' => __( 'The first (primary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	/*
	to add more sidebars or widgetized areas, just copy
	and edit the above sidebar code. In order to call
	your new sidebar just use the following code:

	Just change the name to whatever your new
	sidebar's id is, for example:

	register_sidebar(array(
		'id' => 'sidebar2',
		'name' => __( 'Sidebar 2', 'bonestheme' ),
		'description' => __( 'The second (secondary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	To call the sidebar in your template, you can just copy
	the sidebar.php file and rename it to your sidebar's name.
	So using the above example, it would be:
	sidebar-sidebar2.php

	*/
} // don't remove this bracket!


/************* COMMENT LAYOUT *********************/

// Comment Layout
function bones_comments( $comment, $args, $depth ) {
   $GLOBALS['comment'] = $comment; ?>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
    <article  class="cf">
      <header class="comment-author vcard">
        <?php
        /*
          this is the new responsive optimized comment image. It used the new HTML5 data-attribute to display comment gravatars on larger screens only. What this means is that on larger posts, mobile sites don't have a ton of requests for comment images. This makes load time incredibly fast! If you'd like to change it back, just replace it with the regular wordpress gravatar call:
          echo get_avatar($comment,$size='32',$default='<path_to_url>' );
        */
        ?>
        <?php // custom gravatar call ?>
        <?php
          // create variable
          $bgauthemail = get_comment_author_email();
        ?>
        <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>?s=40" class="load-gravatar avatar avatar-48 photo" height="40" width="40" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />
        <?php // end custom gravatar call ?>
        <?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'bonestheme' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'bonestheme' ),'  ','') ) ?>
        <time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__( 'F jS, Y', 'bonestheme' )); ?> </a></time>

      </header>
      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert alert-info">
          <p><?php _e( 'Your comment is awaiting moderation.', 'bonestheme' ) ?></p>
        </div>
      <?php endif; ?>
      <section class="comment_content cf">
        <?php comment_text() ?>
      </section>
      <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </article>
  <?php // </li> is added by WordPress automatically ?>
<?php
} // don't remove this bracket!


/*
This is a modification of a function found in the
twentythirteen theme where we can declare some
external fonts. If you're using Google Fonts, you
can replace these fonts, change it in your scss files
and be up and running in seconds.
*/
function bones_fonts() {
  wp_enqueue_style('googleFonts', '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic');
}

add_action('wp_enqueue_scripts', 'bones_fonts');
/*Mason Functions*/
function updateCalendars() {
  error_log(var_export($form, true));
  //Grab Post into array
  $booking_info = $_POST;
  //configure prices to make them free
  //look at calendar ID
  $booked_ID = $booking_info['calendar_id'];



  /*$reservation_id = $DOPBSP->classes->backend_reservation->add($calendar_id,
                                                                                 $language,
                                                                                 $currency,
                                                                                 $currency_code,
                                                                                 $reservation,
                                                                                 $form,
                                                                                 $address_billing,
                                                                                 $address_shipping,
                                                                                 $payment_method,
                                                                                 $token);
 */ 
  /*if ($booked_ID == 'joint') {
    //Update lodge and cabin
  } elseif($booked_ID == 'Lodge' || $booked_ID == 'Cabin') {
    //Update joint
  }
  */
} 

add_action('dopbsp_action_book_after', 'updateCalendars');

/** START OF CUSTOM FUNCTIONS  **/
// dumps svg code without cluttering template file
function getMaineSVG(){
  return '<svg class="maine_svg" version="1.1" viewbox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" ><g><path d="M134.994 2.934 C 129.203 4.696,125.769 7.418,120.710 14.259 C 117.764 18.241,111.797 25.775,107.450 31.000 C 103.103 36.225,99.001 41.175,98.334 42.000 C 97.668 42.825,95.633 45.321,93.812 47.547 C 78.685 66.034,73.057 75.708,73.022 83.282 C 72.994 89.383,71.217 93.571,65.507 100.991 C 57.514 111.378,56.971 113.333,57.348 130.339 L 57.678 145.178 53.923 147.549 C 49.350 150.435,48.718 151.397,48.035 156.500 C 47.532 160.258,47.123 160.682,41.276 163.500 C 33.876 167.067,27.912 172.632,25.365 178.348 C 24.347 180.631,22.690 183.184,21.683 184.019 C 20.418 185.068,19.679 187.452,19.291 191.730 L 18.729 197.922 15.892 194.885 C 10.911 189.552,-1.563 199.574,0.369 207.358 C 0.661 208.536,1.179 212.425,1.518 216.000 C 1.858 219.575,2.316 223.062,2.536 223.749 C 2.756 224.436,2.987 235.011,3.048 247.249 C 3.131 263.784,3.528 270.528,4.591 273.500 C 5.595 276.306,5.961 281.232,5.818 290.000 C 5.626 301.763,6.171 309.478,7.935 320.000 C 8.350 322.475,8.565 334.267,8.413 346.205 C 8.074 372.850,8.381 374.191,17.250 384.785 C 19.142 387.045,20.010 389.229,20.033 391.785 C 20.119 401.364,31.421 403.606,38.873 395.522 C 41.143 393.060,43.057 390.697,43.127 390.272 C 43.196 389.848,43.386 387.324,43.549 384.664 C 43.867 379.472,45.368 377.172,50.513 373.992 C 53.937 371.876,56.000 368.293,56.000 364.464 C 56.000 362.936,57.872 359.954,61.000 356.500 C 63.750 353.463,66.056 350.084,66.125 348.989 C 66.194 347.895,66.362 346.550,66.500 346.000 C 66.638 345.450,66.806 344.135,66.875 343.077 C 67.205 338.006,75.198 332.897,85.289 331.307 C 92.186 330.221,94.728 326.077,91.716 320.829 C 90.838 319.297,90.495 317.812,90.955 317.528 C 91.414 317.244,92.027 317.628,92.316 318.381 C 92.935 319.994,98.563 321.037,99.828 319.772 C 101.835 317.765,110.493 316.232,111.817 317.649 C 113.957 319.941,117.909 319.223,120.843 316.011 C 123.544 313.054,123.592 313.043,125.277 315.011 C 130.544 321.162,144.826 313.744,145.441 304.536 C 145.662 301.234,146.675 298.587,148.912 295.473 C 153.629 288.904,155.186 284.619,153.993 281.483 C 152.551 277.690,152.550 277.692,157.898 273.183 L 162.797 269.052 164.499 272.640 C 166.101 276.016,166.102 276.448,164.502 279.995 C 162.390 284.679,163.584 287.511,168.548 289.585 C 175.604 292.533,179.240 290.701,180.317 283.657 C 181.064 278.770,185.292 273.182,188.795 272.452 C 190.283 272.142,194.049 271.226,197.166 270.416 C 202.437 269.046,203.190 269.081,208.006 270.910 C 215.169 273.632,224.732 271.528,228.671 266.364 C 231.118 263.157,231.289 263.098,234.050 264.526 C 239.367 267.275,242.448 266.364,249.903 259.834 C 254.765 255.577,259.733 253.694,261.813 255.322 C 264.437 257.375,273.967 256.263,277.619 253.477 C 284.436 248.278,285.555 241.301,279.946 238.978 C 274.923 236.897,274.429 235.344,278.179 233.423 C 284.406 230.232,284.576 226.832,278.927 218.500 C 271.765 207.936,268.548 206.031,262.939 209.032 C 258.310 211.510,254.563 197.541,255.957 183.000 C 257.056 171.539,256.014 169.845,247.109 168.622 C 234.033 166.827,230.535 159.640,234.885 143.511 C 236.512 137.483,236.915 133.803,236.531 128.511 C 235.752 117.793,235.111 102.557,234.489 80.000 C 233.424 41.354,232.433 36.517,224.290 30.203 C 222.372 28.717,218.935 25.737,216.651 23.581 C 202.956 10.654,199.166 9.291,189.712 13.896 C 186.209 15.603,182.478 17.012,181.421 17.026 C 179.560 17.051,172.166 19.528,161.167 23.811 C 151.983 27.388,148.268 24.528,147.450 13.254 C 146.826 4.635,142.146 0.759,134.994 2.934 M140.180 16.489 C 141.161 31.893,151.221 35.988,169.911 28.592 C 175.086 26.543,181.611 24.604,184.411 24.283 C 187.622 23.913,190.543 22.831,192.327 21.349 C 193.881 20.057,196.205 19.000,197.489 19.000 C 199.960 19.000,211.294 27.813,213.055 31.103 C 213.614 32.147,214.622 33.000,215.295 33.000 C 217.017 33.000,221.719 37.543,223.351 40.782 C 225.607 45.263,225.877 47.648,226.491 68.500 C 226.814 79.500,227.294 92.100,227.556 96.500 C 229.249 124.850,229.086 139.708,227.026 144.935 C 221.260 159.561,231.656 176.000,246.670 176.000 C 248.494 176.000,248.606 176.789,248.554 189.319 C 248.474 208.927,255.278 221.011,263.890 216.557 C 265.546 215.701,267.084 215.000,267.309 215.000 C 267.868 215.000,275.226 226.141,274.823 226.377 C 274.645 226.481,273.212 227.217,271.638 228.012 C 266.119 230.800,266.115 237.073,271.629 242.428 C 275.907 246.583,273.845 249.707,267.545 248.615 C 265.595 248.277,262.440 247.728,260.534 247.395 C 256.219 246.641,249.798 249.552,244.564 254.635 C 241.534 257.578,240.406 258.091,238.191 257.534 C 229.388 255.320,224.981 256.333,223.531 260.902 C 222.726 263.440,214.571 265.378,211.541 263.751 C 209.500 262.655,200.596 262.436,191.863 263.267 C 186.311 263.796,180.242 267.802,176.669 273.296 L 174.200 277.091 172.363 272.296 C 171.352 269.658,170.665 266.877,170.837 266.116 C 172.572 258.426,162.216 255.979,157.520 262.969 C 156.431 264.590,153.401 267.513,150.787 269.465 C 145.425 273.468,144.128 276.700,145.909 281.625 C 147.042 284.758,146.873 285.297,142.930 291.103 C 139.926 295.527,138.574 298.688,138.125 302.342 C 137.433 307.974,132.021 311.575,129.912 307.808 C 127.224 303.003,119.825 303.722,116.965 309.065 C 116.085 310.710,115.572 310.890,114.438 309.948 C 112.868 308.646,102.869 309.472,99.695 311.166 C 98.281 311.921,97.452 311.574,95.872 309.565 C 90.620 302.888,80.119 313.382,83.853 321.577 C 84.694 323.423,84.665 324.020,83.729 324.082 C 70.650 324.958,59.112 334.735,59.022 345.017 C 59.009 346.546,57.253 349.531,54.552 352.620 C 51.696 355.884,49.680 359.383,48.923 362.393 C 47.958 366.224,46.925 367.644,43.271 370.157 C 38.313 373.568,36.000 377.905,36.000 383.788 C 36.000 386.606,35.238 388.493,33.161 390.820 C 28.650 395.873,28.069 395.429,26.511 385.742 C 26.128 383.362,25.053 381.426,23.578 380.459 C 22.297 379.620,20.008 376.586,18.490 373.717 L 15.731 368.500 15.491 341.000 C 15.359 325.875,14.904 312.375,14.479 311.000 C 14.055 309.625,13.531 301.300,13.315 292.500 C 13.099 283.700,12.305 273.800,11.550 270.500 C 10.651 266.568,10.161 257.780,10.130 245.000 C 10.104 234.275,9.867 223.700,9.604 221.500 C 7.847 206.787,7.854 205.475,9.701 203.344 L 11.514 201.255 13.355 203.696 C 18.914 211.065,27.954 204.345,26.399 194.000 C 25.931 190.888,26.212 190.311,28.936 188.797 C 31.616 187.307,32.564 185.489,32.108 182.720 C 31.540 179.268,38.413 172.485,45.000 169.998 C 51.197 167.658,55.940 161.996,55.540 157.415 C 55.417 155.996,56.256 154.932,58.167 154.086 C 62.111 152.341,65.197 147.597,65.251 143.201 C 65.275 141.166,65.152 134.325,64.977 128.000 C 64.623 115.232,65.020 113.913,72.039 104.506 C 77.669 96.961,80.021 91.572,80.677 84.708 C 81.505 76.044,85.482 69.276,100.189 51.500 C 110.887 38.570,113.513 35.427,113.973 35.000 C 114.269 34.725,115.740 32.851,117.241 30.837 C 118.742 28.822,120.548 26.572,121.255 25.837 C 121.962 25.101,124.297 22.083,126.444 19.130 C 134.363 8.233,139.591 7.228,140.180 16.489 M173.344 280.794 C 173.026 282.007,172.379 283.000,171.905 283.000 C 171.431 283.000,171.556 281.873,172.184 280.496 C 173.536 277.527,174.156 277.687,173.344 280.794 " fill-rule="evenodd"></path></g></svg>';
}

function getFiveStar(){
  return '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
       viewBox="0 0 2622.1 512" style="enable-background:new 0 0 2622.1 512;" xml:space="preserve">
        <style type="text/css">
          .st0{fill:#FFFFFF;}
        </style>
        <g>
          <g id="star-rate">
            <polygon class="st0" points="256,386.1 413.9,501.3 354.1,313.6 512,202.7 320,202.7 256,10.7 192,202.7 0,202.7 157.9,313.6 
              98.1,501.3    "/>
          </g>
        </g>
        <g>
          <g id="star-rate_1_">
            <polygon class="st0" points="1310.1,386.1 1467.9,501.3 1408.2,313.6 1566.1,202.7 1374.1,202.7 1310.1,10.7 1246.1,202.7 
              1054.1,202.7 1211.9,313.6 1152.2,501.3    "/>
          </g>
        </g>
        <g>
          <g id="star-rate_2_">
            <polygon class="st0" points="783,386.1 940.9,501.3 881.2,313.6 1039,202.7 847,202.7 783,10.7 719,202.7 527,202.7 684.9,313.6 
              625.2,501.3     "/>
          </g>
        </g>
        <g>
          <g id="star-rate_3_">
            <polygon class="st0" points="1838,386.1 1995.8,501.3 1936.1,313.6 2094,202.7 1902,202.7 1838,10.7 1774,202.7 1582,202.7 
              1739.8,313.6 1680.1,501.3     "/>
          </g>
        </g>
        <g>
          <g id="star-rate_4_">
            <polygon class="st0" points="2366.1,386.1 2524,501.3 2464.2,313.6 2622.1,202.7 2430.1,202.7 2366.1,10.7 2302.1,202.7 
              2110.1,202.7 2268,313.6 2208.2,501.3    "/>
          </g>
        </g>
        </svg>';
}


function buildCustomNav(){
  // initialize $nav_links
  $nav_links = '';

  // add homepage to nav_links
  $page = get_page_by_title('Maine Away');
  $url = get_permalink($page->ID);
  $title = $page->post_title;
  $font = get_field('font');
  $nav_links .= "<a href='{$url}' style='font-family:{$font}'><h1>{$title}</h1>";
  // if on this page, add identifier
  if($title == get_the_title()){
    // add current page identifier
    $nav_links .= getMaineSVG();
  }
  $nav_links .= "</a><div id='pages'>";
  $menu_items = wp_get_nav_menu_items('Navigation');
  /*
  $properties = get_posts(['post_type' => 'property']);
  $all_pages = get_pages();
  
  $pages = array_filter(array_merge($properties, $all_pages), navFilterCallback);
  */

  foreach ($menu_items as $item) {
    $page = get_post($item->object_id);
    $url = get_permalink($page->ID);
    $title = $page->post_title;
    $font = get_post_meta($page->ID, 'font')[0];
    // add link
    $nav_links .= "<a href='{$url}' style='font-family:{$font}'><span>{$title}</span>";
    // if on this page, add identifier
    if($title == get_the_title()){
      // add current page identifier
      $nav_links .= getMaineSVG();
   }
    // add hover svg
    $nav_links .= '<svg class="underline" width="75px" height="7px" xmlns="http://www.w3.org/2000/svg">
    <path stroke="#000000" fill="none" d="m 0,5 H 25 l 6,-4 3,0 6,4 H 100"></path>
    </svg></a>';
    
  }
  $nav_links .= "</div><div id=hamburger class='icon' onclick=\"document.getElementById('pages').classList.toggle('show');\"'>";
  $nav_links .= '<svg viewBox="0 0 32 22.5" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <path d="M25.07,5c0,0.69-0.5,1.25-1.117,1.25H7.266C6.649,6.25,6.148,5.69,6.148,5l0,0c0-0.69,0.5-1.25,1.118-1.25h16.688
                C24.57,3.75,25.07,4.31,25.07,5L25.07,5z"/>
                    <path d="M25.048,11.25c0,0.689-0.501,1.25-1.118,1.25H7.243c-0.618,0-1.118-0.561-1.118-1.25l0,0c0-0.689,0.5-1.25,1.118-1.25
                H23.93C24.547,10,25.048,10.561,25.048,11.25L25.048,11.25z"/>
                    <path d="M25.094,17.5c0,0.689-0.5,1.25-1.117,1.25H7.289c-0.617,0-1.118-0.561-1.118-1.25l0,0c0-0.689,0.5-1.25,1.118-1.25h16.688
                C24.594,16.25,25.094,16.811,25.094,17.5L25.094,17.5z"/>
                  </g>
                </svg>
              </div>';
 
  return $nav_links;
}

function navFilterCallback($page){
  $show_in_nav = get_post_meta($page->ID, 'show_in_navigation');
  if (isset($show_in_nav) && isset($show_in_nav[0]) && $show_in_nav[0] == 1){
    return true;
  }  
  else{
    return false;
  }

}

function fontLoader(){
  $post_font =  get_field('secondary_font') != NULL ? get_field('secondary_font') : get_field('font');
  $fonts = isset($post_font) ? [$post_font] : [];
  // add font for homepage
  $page = get_page_by_title('Maine Away');
  array_push($fonts, get_post_meta($page->ID, 'font')[0]);
  // add fonts for navigation
  $menu_items = wp_get_nav_menu_items('Navigation');

  foreach ($menu_items as $item) {
    $page = get_post($item->object_id);
    $font = get_post_meta($page->ID, 'font')[0];
    // if font not in fonts add it
    if (!in_array($font, $fonts)){
      array_push($fonts, $font); 
    }   
  }
  
  $link_tag = "<link href='https://fonts.googleapis.com/css?family=";
  foreach ($fonts as $font) {
    $encoded_font = str_replace(' ', '+', $font);
    $link_tag .= $encoded_font.'|';
  }
  $link_tag = trim($link_tag, '|');
  $link_tag .= "' rel='stylesheet'>";
  return $link_tag;
}

// register 1080p size for images
add_image_size( 'hd', 1600, 1600, false );

add_filter( 'image_size_names_choose', 'my_custom_sizes' );
 
function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'hd' => __( 'Full HD' ),
    ) );
}

// start image gallery code

function buildImageGallery($class_list, $field_name){
  $string_class_list = implode(' ', $class_list);
  $images_array = [];
  $images = get_field($field_name);
  // make the data a little cleaner
  foreach ($images as $image) {
    array_push($images_array, $image);

  }
  $images_dataset = json_encode($images_array);
  $five_star_class =  $images_array[0]['is_a_review'] ? 'five-star active' : 'five-star';
  echo "<div class='image-gallery {$string_class_list}' data-image-target='0' data-image-set='
  {$images_dataset}'>
          <div class='gal_nav prev'>
            <svg viewbox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'>
              <circle cx='16' cy='16' opacity='.7' r='16'></circle>
              <polyline fill='none' points='21,3.5  8.5,16 21,28.5'></polyline> 
            </svg>
          </div>";
  // add two image div for transition
  echo   "<div class='gallery-image active' style='background-image:url(\"{$images_array[0]['image']['sizes']['hd']}\")'>
           <div class='image-overlay'>
             <div class='review-wrapper'>
               <div class='review'>".
          "<div class='{$five_star_class}'>".getFiveStar()."</div>".
                  '<div class="review-text">'.$images_array[0]['text'].'</div>
                </div>
              </div>
            </div>
          </div>
          <div class="gallery-image inactive">
            <div class="image-overlay">
              <div class="review-wrapper">
               <div class="review">'.
                  "<div class='five-star'>".getFiveStar()."</div>".
                  '<div class="review-text"></div>
                </div>
              </div>
            </div>
          </div>';
  echo    '<div class="gal_nav next">
            <svg viewbox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
              <circle cx="16" cy="16" opacity=".7" r="16"></circle>
              <polyline fill="none" points="11.5,3.5  24,16 11.5,28.5"></polyline> 
            </svg>
          </div>
        </div>'; 
}
// end image gallery code

add_role(
  'Owner',
  'Owner',
  array(
    'edit_others_pages' => true,
    'edit_others_posts' => true,
    'edit_pages' => true,
    'edit_posts' => true,
    'edit_private_pages' => true,
    'edit_private_posts' => true,
    'edit_published_pages' => true,
    'edit_published_posts' => true,
    'edit_plugins' => true,
    'edit_theme_options' => true,
    'read_private_pages' => true,
    'read_private_posts' => true,
    'read' => true,
    'upload_files' => true,
    'activate_plugins' => false,
    'delete_others_pages' => false,
    'delete_others_posts' => false,
    'delete_pages' => false,
    'delete_posts' => false,
    'delete_private_pages' => false,
    'delete_private_posts' => false,
    'delete_published_pages' => false,
    'delete_published_posts' => false,
    'edit_dashboard' => false,
    'export' => false,
    'import' => false,
    'list_users' => false,
    'manage_categories' => false,
    'manage_links' => false,
    'manage_options' => false,
    'moderate_comments' => false,
    'promote_users' => false,
    'publish_pages' => false,
    'publish_posts' => false,
    'remove_users' => false,
    'switch_themes' => false,
    'upload_files' => false,
    'customize' => false,
    'delete_site' => false,
    'update_core' => false,
    'update_plugins' => false,
    'update_themes' => false,
    'install_plugins' => false,
    'install_themes' => false,
    'upload_plugins' => false,
    'upload_themes' => false,
    'delete_themes' => false,
    'delete_plugins' => false,
    'edit_themes' => false,
    'edit_files' => false,
    'edit_users' => false,
    'create_users' => false,
    'delete_users' => false,
    'unfiltered_html' => false
  )
);



/** END OF CUSTOM FUNCTIONS  **/


/* DON'T DELETE THIS CLOSING TAG */ ?>
