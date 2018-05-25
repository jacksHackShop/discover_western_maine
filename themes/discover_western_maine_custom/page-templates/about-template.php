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
						<?php $sections = get_field('page_section');
						foreach ($sections as $section) : ?>
							<div class="selector"><?php echo $section['section_title'];?></div>
						<?php endforeach;?>
					</div>
					<div id="section-wrapper">
						<div id="active-section"></div>
					</div>
					<div id="overflow-hider">
						<?php foreach ($sections as $section) : ?>
							<div id="<?php echo $section['section_title']; ?>" class="section">
								<?php echo $section['section_content']; ?>
							</div>
						<?php endforeach;?>
					</div>
				</div>

			</div>


<?php get_footer(); ?>
