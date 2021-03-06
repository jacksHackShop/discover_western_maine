<?php
/*
 Template Name: Rules
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
      <div id="header">
		
				<?php 
          // custom navigation function
          echo buildCustomNav();
        ?>
      </div>
  
			<div id="content">

				<div id="inner-content" class="cf">
					<div id="rules_wrapper">
						<h2><?php echo get_field('property_title'); ?> Rules</h2>
						<div id="rules_text"><?php echo get_field('rules'); ?></div>
					</div>

				</div>

			</div>


<?php get_footer(); ?>
