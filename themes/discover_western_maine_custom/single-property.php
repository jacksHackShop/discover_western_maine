<?php
/*
 * Property Template 
 This is the custom post type post template. If you edit the post type name, you've got
 * to change the name of this template to reflect that name change.
 *
 * For Example, if your custom post type is "register_post_type( 'bookmarks')",
 * then your single template should be single-bookmarks.php
 *
 * Be aware that you should rename 'custom_cat' and 'custom_tag' to the appropiate custom
 * category and taxonomy slugs, or this template will not finish to load properly.
 *
 * For more info: http://codex.wordpress.org/Post_Type_Templates
*/
?>

<?php 
	get_header(); 
	the_post(); 
?>
<body <?php body_class('property_page'); ?> itemscope itemtype="http://schema.org/WebPage">

	<div id="container" class="cf">
    <div id="header" class="property_nav">
	
			<?php 
        // custom navigation function
        echo buildCustomNav();
        // get page font
        $font = get_field('secondary_font') != NULL ? get_field('secondary_font') : get_field('font');
      ?>
    </div>

		<div id="content" style="font-family: <?php echo $font;?>">										
			<?php buildImageGallery(['property'], 'image_gallery');
			?>
			<div id="inner-content" class="cf">
				<h3 id="property_title">The <?php the_field('title');?></h3>
				<div class="stats m-all t-1of2 d-1of2">
					<ul id="stat_list">
					<?php 
						$stats = get_field('stats');
						foreach ( $stats as $stat): ?>
							<li>
								<span class="tooltipped">
									<img class="stat_image" src="<?php echo $stat['icon']; ?>">
									<?php if ($stat['stat_name']):?>
										<span class="tooltip">
											<?php echo $stat['stat_name']; ?>
										</span>	
									<?php endif;?>
								</span>
								<span class="stat_value"><?php echo $stat['stat_info']; ?></span>
								
							</li>

						<?php endforeach; 
					?>
					</ul>
				</div>
				<div class="blurb text_body m-all t-1of2 d-1of2">
					<?php the_field('property_intro'); ?>
				</div>
				<div class="calendar_container">
					<?php if (get_field('alternate_calendar_id')): ?>
						 <input type="checkbox" id="toggle_calendar" name="show_alternate">
						 <label>Check to rent both Lodge and Cabin</label>
					<?php endif; ?>
					<div id="primary_calendar" class="calendar active">
						<?php echo "<h3 class='calendar_title'>".get_field('calendar_display_name')."</h3>";
						echo do_shortcode('[dopbsp id='.get_field('calendar_id').']'); ?>
					</div>
					<?php if (get_field('alternate_calendar_id')): ?>
						 <div id="alternate_calendar" class="calendar hidden">
						 	<?php echo "<h3 class='calendar_title'>".get_field('alternate_calendar_display_name')."</h3>";
						 	echo do_shortcode('[dopbsp id='.get_field('alternate_calendar_id').']'); ?>
						 </div>
					<?php endif; ?>
				</div>
				<div id="property_details">
					<!-- half quote  half about -->	
					<?php 
					$info = get_field('detail_and_testimonials');
					foreach ($info as $info_array) :?>
						<div class='detail_row m-all t-all d-all'>
							<div class='detail text_body m-all t-1of2 d-1of2'><?php echo $info_array['detail']?></div>
							<div class='testimonial m-all t-1of2 d-1of2'>
					  		<div class="testimonial-wrapper">
									<div class='open_quote'>"</div>
					  			<p><?php echo $info_array['testimonial']; echo $_SERVER['DOCUMENT_ROOT']; ?></p>
					  			<div class='close_quote'>"</div>
					  		</div>
					 		</div>
						</div>
						
					<?php endforeach; ?>
				</div>
					
		</div>

	</div>


<?php get_footer(); ?>
