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
                                <?php $summaries = get_field('property_summary');
                                foreach ($summaries as $summary) : ?>
                                    <?php $summary_html = $summary['summary'];
                                    if ($summary['property_link'] != '') {
                                        $summary_html .= "<a class='button' href='{$summary['property_link']}'>Checkout {$summary['title']}</a>";
                                    } ?>
                                    <div class="property-thumbnail" style="background-image:url(<?php echo $summary['thumbnail']['sizes']['thumbnail']; ?>);" data-text="<?php echo $summary_html; ?>">
                                    </div>
                                <?php endforeach;?>
                            </div>
                            <div id="property-text" class="text show-review">
                                <?php echo get_field('general_summary'); ?>
                            </div>

                        </div>
                    </div>
				</div>
			</div>


<?php get_footer(); ?>
