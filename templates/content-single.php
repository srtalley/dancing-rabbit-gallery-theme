<?php
/**
 * @package weston
 */

$show_featured_img = get_post_meta( $id, '_weston_post_show_featured_img', true );
?>


<div class="prev_next_buttons">
<div class="previous-button">
<?php previous_post_link('%link', 'Previous', true); ?>
</div>
<div class="next-button">
<?php next_post_link('%link', 'Next', true); ?>  
</div></div>
	

		<div class="entry-content">
			<?php if(has_post_thumbnail() && $show_featured_img == "yes") { ?>
				<div class="featured-image">
					<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail( 'weston_post_thumb', array( 'class' => 'post-thumb', 'alt' => ''. the_title_attribute( 'echo=0' ) .'', 'title' => ''. the_title_attribute( 'echo=0' ) .'' ) ); ?></a>
				</div>
			<?php } ?>
			
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'weston' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->
