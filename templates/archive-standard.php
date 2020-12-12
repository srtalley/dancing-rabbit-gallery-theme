
	<main id="main" class="site-main blog" role="main">
		<div class="body-wrap clear">
			<div class="content-main">
			<?php if ( have_posts() ) :
				if(is_category()) {
				}?>

				<div id="posts-scroll">
				<?php  while ( have_posts()) : the_post(); ?>

					<?php get_template_part( 'templates/content', get_post_format() ); ?>
			
				<?php endwhile; ?>

				</div><!-- #posts-scroll -->

				<?php weston_the_paging_nav(); ?>

			<?php else : ?>

				<?php get_template_part( 'templates/content', 'none' ); ?>

			<?php endif; ?>
			</div>
		<?php get_sidebar(); ?>
		</div>
	</main><!-- #main -->
