<?php
/*
 Template Name: Home
 *
 * This is your custom page template. You can create as many of these as you need.
 * Simply name is "page-whatever.php" and in add the "Template Name" title at the
 * top, the same way it is here.
 *
 * When you create your page, you can just select the template and viola, you have
 * a custom page template to call your very own. Your mother would be so proud.
 *
 * For more info: http://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>
<body <?php body_class('home_page'); ?> itemscope itemtype="http://schema.org/WebPage">

		<div id="container" class="home_page cf">
      <div id="header" class="home_nav">
		
				<?php 
          // custom navigation function
          echo buildCustomNav();
        ?>
      </div>
  
			<div id="content">

				<div id="inner-content" class="cf">

					<?php buildImageGallery(['fullscreen'], 'image_gallery'); ?>
					

				</div>

			</div>


<?php get_footer(); ?>
