<?php
/**
 * @package weston
 */

$shop_count = get_theme_mod( 'weston_shop_count' );
$price      = esc_attr( get_post_meta( get_the_ID(), '_price', true ) );
$reg_price  = esc_attr( get_post_meta( get_the_ID(), '_regular_price', true) );
$sale       = $price < $reg_price ? TRUE : FALSE;
global $product;
$product_availability = $product->get_availability();
$currency   = get_woocommerce_currency_symbol();
$hover_color = "#000";
$sold_sticker = '';
// Add a query arg if necessary
$product_link = get_the_permalink();
if(is_product_category()){
	$product_link = add_query_arg('dr_category', get_queried_object_id(), $product_link);
}
?>

<li class="product small" id="<?php echo $post->ID; ?>">
	<div class="inside">
	<div class="thumb-container">
		<a href="<?php echo $product_link; ?>" rel="bookmark" alt="<?php the_title_attribute(); ?>">
			
			<div class="product-thumb">
				
				<?php
				if( has_post_thumbnail() ) {
					woocommerce_template_loop_product_thumbnail();					
				} else { ?>
					<span class="blank-product"></span>
				<?php } ?>
        <?php if (  ! $product->is_in_stock() ) {
            $sold_sticker = '<span class="stock ' . esc_attr( $product_availability['class'] ) . '">' . esc_html( $product_availability['availability'] ) . '</span>';

            echo $sold_sticker;
        } ?>
				<?php if( $sale ) {?>

					<span class="sale"><?php _e( 'Sale', 'weston' ); ?></span>

				<?php } ?>

			</div>
		</a>
		
	</div>


	<div class="details">
		<span class="title"><?php the_title(); ?></span>
		<span class="price"><?php echo $sale ? '<span class="old-price">' . $currency . $reg_price . '</span> ' . $currency . $price : $currency . $price;?></span>
	</div>
	
	</div>
</li>