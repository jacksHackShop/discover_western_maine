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
<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

		<div id="container" class="cf about_page">
      <div id="header" class="about_nav">
		
				<?php 
          // custom navigation function
          echo buildCustomNav();
        ?>
      </div>
  
			<div id="content">

				<div id="inner-content" class="cf">
					<?php var_dump(get_field('font')); ?>
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<?php endwhile; else : ?>

							<article id="post-not-found" class="hentry cf">
									<header class="article-header">
										<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
								</header>
									<section class="entry-content">
										<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
								</section>
								<footer class="article-footer">
										<p><?php _e( 'This is the error message in the page-custom.php template.', 'bonestheme' ); ?></p>
								</footer>
							</article>

					<?php endif; ?>


				</div>

			</div>


<?php get_footer(); ?>
