<?php
/**
 * @package weston
 */

// Modify the search query to ignore results in between HTML tags
$search_query = get_search_query();

if(!preg_match('/^(["\']).*\1$/m', html_entity_decode($search_query))) {
    $search_query = explode(' ', $search_query);
} else {
    $new_search_query =  str_replace('&quot;', "", $search_query);
    $search_query = array($new_search_query);
}

$show_post = true;
$post_content_html_check = wp_strip_all_tags(get_the_content());
$post_excerpt_html_check = wp_strip_all_tags(get_the_excerpt());

foreach ($search_query as $query_item) {
	if(stripos($post_content_html_check, $query_item) === false && stripos($post_excerpt_html_check, $query_item) === false) {
		$show_post = false;
	} 
} 
if($show_post) { 
?>
<div class="entry-content bozo">
<article id="post-<?php the_ID(); ?>" <?php post_class('clear'); ?> >
	<?php if(has_post_thumbnail()) : ?>			
		<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('weston_thumb_square', array('class' => 'post-thumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>
	<?php endif; ?>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->
	<div class="entry-summary">
		<p><?php the_excerpt(); ?></p>
	</div><!-- .entry-summary -->
	
</article><!-- #post-## -->
</div>
<?php } // end $show_post