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
	// enqueue slick js
	function enqueueSlick(){
		wp_enqueue_script('slickjs', get_template_directory_uri()."/library/js/libs/slick/slick.min.js", ['jquery']);
	}	
	add_action('wp_enqueue_scripts', 'enqueueSlick');
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
			<div class="slick-gallery">
				<?php $raw_image_gallery = get_field('image_gallery'); 
					$size = 'hd';
					foreach ($raw_image_gallery as $image) : ?>
						<?php $ratio = $image['image']['width'] > $image['image']['height'] ? 'landscape' : 'portrait'; ?>
					<div>
						<img src="<?php echo $image['image']['sizes'][$size]; ?>" class="<?php echo $ratio; ?>">
					</div>
				<?php endforeach;?>
			</div>
			<div id="gallery-nav">
				<div id="slick-custom-prev" class="slick-custom-arrow"><</div>
				<div class="slick-nav">
					<?php $size = 'thumbnail'; 
					foreach ($raw_image_gallery as $image) : ?>
						<div class="slick-nav-img">
							<img src="<?php echo $image['image']['sizes'][$size]; ?>">
						</div>
					<?php endforeach;?>
				</div>
				<div id="slick-custom-next" class="slick-custom-arrow">></div>
			</div>
			<!-- load jquery for slick gallery.  needs to be here for some reason
			<script
			  src="https://code.jquery.com/jquery-3.3.1.min.js">
	        </script>
	        <script
			  src="https://code.jquery.com/jquery-migrate-3.0.1.min.js">
	        </script>-->
			<script src="<?php echo get_template_directory_uri(); ?>/library/js/libs/slick/slick.min.js">
	        </script>
			<div id="inner-content" class="cf">
				<div class="stats m-all t-1of2 d-1of3">
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
				<div class="blurb m-all t-1of2 d-2of3">
					<h3 id="property_title">The <?php the_field('title');?></h3>
					<div class="text_body">
						<?php the_field('property_intro'); ?>
					</div>
				</div>
				<?php
					$cal_parser = new CalFileParser();
					$events = [];
					if( null !== get_field('calendars_to_sync') ):
						$icals = get_field('calendars_to_sync');
						
						foreach( $icals as $ical ){
							$events = array_merge( $events, $cal_parser->parse( $ical['ical_link'] ) );
						}
					?>
					<div class="calendar_container text_body m-all t-1of3 d-1of3">
						<h1>Availability</h1>
						<div class="calendars_wrapper">
							<?php 
								for($month_num = date('m'); $month_num <= date('m') + 11; $month_num++): 
							?>
								<div class="calendar_month" data-month="<?php echo ( ( $month_num - 1 ) % 12 + 1) ?>">
									<a onclick="month_filter_click(this, <?php echo ( ( $month_num - 1 ) % 12 + 1) ?>);"><?php echo date('F', mktime( 0, 0, 0, $month_num, 1)); ?></a>
									<table class="calendar">
										<tbody>
											<tr>
												<?php 
													for( $day_of_month = 0; $day_of_month < date('w', mktime( 0, 0, 0, $month_num, 1 )); $day_of_month++ ){ 
														echo '<td></td>';
													}//Create empty days at start of month
													for( $day_of_month = 1; $day_of_month <= date('d', mktime( 0, 0, 0, $month_num + 1, 0)); $day_of_month++ ){
														if( ( date('w', mktime( 0, 0, 0, $month_num, 1) - 1 ) + $day_of_month ) % 7 == 0 ){ echo '</tr><tr>';}
														$style_classes_for_day = '';
														if( date("m.d.y",  mktime( 0, 0, 0, $month_num, $day_of_month ) ) == date("m.d.y") ){ //Is it today??
															$style_classes_for_day .= ' today'; 
														}
														if( mktime( 0, 0, 0, $month_num, $day_of_month ) < mktime( 0, 0, 0 ) ){ //Is it a date in the past??
															$style_classes_for_day .= ' past';
														}
														$day_to_check = DateTime::createFromFormat ( "U" , mktime( 0,0,0, $month_num, $day_of_month ));
														foreach( $events as $event ){//Check if there is an event on this day
															if( $event["DTSTART"] <= $day_to_check &&
																	$event["DTEND"] >= $day_to_check ){
																$style_classes_for_day .= ' booked';
																break;
															}
														}
														echo '<td data-date="'.$day_to_check->format('Y-m-d H:i:s').'" class="'.$style_classes_for_day.'">'.$day_of_month.'</td>';
													}//Create the rest of the month, new rows every 7(with the empty cell offset) ?>
											</tr>
										</tbody>
									</table>
								</div>
							<?php endfor; ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="detail text_body m-all t-1of2 d-1of2">
					<h1>Booking Details</h1>
					<?php the_field('booking_details'); ?>
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
									<div class='open quote'>
										<svg transform="scale(-1,1)" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve" ><g><path d="M76.729,44.54c0.005-0.138,0.021-0.273,0.021-0.413c0-0.047-0.007-0.092-0.007-0.138c0.001-0.092,0.007-0.183,0.007-0.275    l-0.021,0.014c-0.212-6.166-5.265-11.103-11.481-11.103c-6.353,0-11.502,5.149-11.502,11.502c0,5.796,4.292,10.577,9.869,11.372    c-1.387,4.595-5.646,7.938-10.695,7.938v4.106C65.813,67.542,76.293,57.325,76.729,44.54z"/><path d="M46.893,44.54c0.005-0.138,0.021-0.273,0.021-0.413c0-0.047-0.007-0.092-0.007-0.138c0.001-0.092,0.007-0.183,0.007-0.275    l-0.021,0.014c-0.212-6.166-5.265-11.103-11.481-11.103c-6.353,0-11.502,5.149-11.502,11.502c0,5.796,4.292,10.577,9.869,11.372    c-1.386,4.595-5.645,7.938-10.694,7.938v4.106C35.978,67.542,46.456,57.325,46.893,44.54z"/></g></svg>
									</div>
					  			<p><?php echo $info_array['testimonial']; ?></p>
					  			<div class='close quote'>
					  				<svg transform="rotate(180) scale(-1,1)" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve" ><g><path d="M76.729,44.54c0.005-0.138,0.021-0.273,0.021-0.413c0-0.047-0.007-0.092-0.007-0.138c0.001-0.092,0.007-0.183,0.007-0.275    l-0.021,0.014c-0.212-6.166-5.265-11.103-11.481-11.103c-6.353,0-11.502,5.149-11.502,11.502c0,5.796,4.292,10.577,9.869,11.372    c-1.387,4.595-5.646,7.938-10.695,7.938v4.106C65.813,67.542,76.293,57.325,76.729,44.54z"/><path d="M46.893,44.54c0.005-0.138,0.021-0.273,0.021-0.413c0-0.047-0.007-0.092-0.007-0.138c0.001-0.092,0.007-0.183,0.007-0.275    l-0.021,0.014c-0.212-6.166-5.265-11.103-11.481-11.103c-6.353,0-11.502,5.149-11.502,11.502c0,5.796,4.292,10.577,9.869,11.372    c-1.386,4.595-5.645,7.938-10.694,7.938v4.106C35.978,67.542,46.456,57.325,46.893,44.54z"/></g></svg>
					  			</div>
					  		</div>
					 		</div>
						</div>
						
					<?php endforeach; ?>
				</div>
					
		</div>

	</div>


<?php get_footer(); ?>
