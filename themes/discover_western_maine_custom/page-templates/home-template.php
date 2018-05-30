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
					<?php buildImageGallery(['fullscreen', 'attributed'], 'image_gallery'); ?>
                    <div id="about-properties-wrapper">
                        <div id="about-properties">
                            <div id="property-thumbnails">
                                <?php $summeries = get_field('property_summery');
                                foreach ($summeries as $summery) : ?>
                                <div class="property-thumbnail" style="background-image:url(<?php echo $summery['thumbnail']['sizes']['thumbnail']; ?>);" data-text="<?php echo $summery['summery']; ?>">
                                </div>
                                <?php endforeach;?>
                            </div>
                            <div id="property-text" class="text" data-text="<?php echo get_field('general_summery'); ?>">
                                <?php echo get_field('general_summery'); ?>
                            </div>

                        </div>
                    </div>
					

				</div>

			</div>


<?php get_footer(); ?>
