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

/** START OF CUSTOM FUNCTIONS  **/
function buildCustomNav(){
  // initialize $nav_links
  $nav_links = '';

  // add homepage to nav_links
  $page = get_page_by_title('Maine Away');
  $url = get_permalink($page->ID);
  $title = $page->post_title;
  $font = get_field('font');
  $nav_links .= "<a href='{$url}' style='font-family:{$font}'><h1>{$title}</h1></a><div id='pages'>";
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
    $nav_links .= "<a href='{$url}' style='font-family:{$font}'><span>{$title}</span>";
    $nav_links .= '<svg width="75px" height="7px" xmlns="http://www.w3.org/2000/svg">
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
  trim($link_tag, '|');
  $link_tag .= "' rel='stylesheet'>";
  return $link_tag;
}

// start image gallery code

function buildImageGallery($class_list, $field_name){
  $string_class_list = implode(' ', $class_list);
  echo '<div class="image-gallery '.$string_class_list.'" data-image-target="0">
          <div class="gal_nav prev">
            <svg viewbox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
              <circle cx="16" cy="16" opacity=".7" r="16"></circle>
              <polyline fill="none" points="21,3.5  8.5,16 21,28.5"></polyline> 
            </svg>
          </div>
          <ul class="gallery_images">';

        $images = get_field($field_name); 
        foreach( $images as $image ){
        
          echo "<li class='gallery_image' style='background-image:url(\"{$image['image']}\")'></li>";  
        }
    echo '</ul>
          <div class="gal_nav next">
            <svg viewbox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
              <circle cx="16" cy="16" opacity=".7" r="16"></circle>
              <polyline fill="none" points="11.5,3.5  24,16 11.5,28.5"></polyline> 
            </svg>
          </div>
        </div>';
}
// end image gallery code



/** END OF CUSTOM FUNCTIONS  **/


/* DON'T DELETE THIS CLOSING TAG */ ?>
