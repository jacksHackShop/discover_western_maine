<?php
/*
 Template Name: About
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
<?php 
	get_header(); 
	the_post(); 
?>
<body <?php body_class('about_page'); ?> itemscope itemtype="http://schema.org/WebPage">

	<div id="container" class="cf">
	    <div id="header" class="about_nav">
			
					<?php 
	          // custom navigation function
	          echo buildCustomNav();
	        ?>
	    </div>
	  
			<div id="content">

				<div id="inner-content" class="cf">
					<div id="section-nav">
						<?php if( get_field('show_map') && get_field('visit_map') && count( get_field('visit_map') ) > 0 ): ?>
							<div class="selector">Map</div>
						<?php endif; ?>
						<?php $sections = get_field('page_section');
						foreach ($sections as $section) : ?>
							<div class="selector"><?php echo $section['section_title'];?></div>
						<?php endforeach;?>
					</div>
					<div id="section-wrapper">
						<div id="active-section"></div>
					</div>
					<div id="overflow-hider">
						<?php if( get_field('show_map') && get_field('visit_map') && count( get_field('visit_map') ) > 0 ): ?>
							<div id="Map" class="section">
								<div class="gmap_listing_explorer_wrapper" data-markers='<?php echo htmlspecialchars( json_encode( get_field('visit_map') ), ENT_QUOTES, 'UTF-8'); ?>' data-initial-zoom='</php echo get_field("initial_map_zoom"); ?>'>
									<h2 class="listings_header">LOCATIONS</h2>
									<div class="listings"></div>
									<div class="map"></div>
								</div>
							</div>
						<?php endif; ?>
						<?php foreach ($sections as $section) : ?>
							<div id="<?php echo $section['section_title']; ?>" class="section">
								<?php echo $section['section_content']; ?>
							</div>
						<?php endforeach;?>
					</div>
				</div>

			</div>


<?php get_footer(); ?>
