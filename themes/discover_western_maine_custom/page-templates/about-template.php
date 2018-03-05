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
					<?php $text_image_pairs = get_field('image_text_pair'); 
					foreach ($text_image_pairs as $pair){
						echo "<div class='text_image_pair'>";
						
						// set text's class based whether or not we have a image to split the screen with
						// NOTE that $pair == false, not null, if the field isn't set in wp.
						$body_class = $pair['illustration'] ? 'text_body m-all t-all d-1of2' : 'text_body m-all t-all d-all' ;

						echo "<div class='".$body_class."'>";
						// if title is set
						if($pair['text_title']){
							echo '<h2 class>'.$pair['text_title'].'</h2>';
						}
						echo	"<p>".$pair['text_body']."</p></div>";
						// if we have an image, drop it in

						if ($pair['illustration']){
							echo 
							"<div class='illustration_container m-all t-all d-1of2'>".
								"<img src=".$pair['illustration'].">".
							"</div>";
						}
						echo "</div>";
						
					 } ?>
					
					<?php echo do_shortcode('[wpgmza id="'.get_field('map_id').'"]'); ?>

				</div>

			</div>


<?php get_footer(); ?>
