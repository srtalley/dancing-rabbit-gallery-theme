<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/* Add custom functions below */
add_action( 'wp_enqueue_scripts', 'ds_enqueue_assets', 11 );
function ds_enqueue_assets() {

  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css');
  wp_dequeue_style( 'weston-style' );
  wp_enqueue_style( 'child-theme', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));

  wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', '', '', true );

}//end function ds_enqueue_assets

  //Change out of stock to sold on product page
add_filter( 'woocommerce_get_availability', 'fpp_woocommerce_get_availability', 1, 2);
function fpp_woocommerce_get_availability( $availability, $_product ) {
    // Change In Stock Text
    if ( $_product->is_in_stock() ) {
         $availability['availability'] = __('Available!', 'woocommerce');
    }
    // Change Out of Stock Text
    if ( !$_product->is_in_stock() ) {
         $availability['availability'] = __('Sold', 'woocommerce');
    }
    return $availability;
} //end function fpp_woocommerce_get_availability


  //show sold messages on the shop page
add_action( 'woocommerce_shop_loop_item_title', 'fpp_woocommerce_availability_shop_page', 10 );
function fpp_woocommerce_availability_shop_page($availability) {
    //returns an array with 2 items availability and class for CSS
    global $product;
    $product_availability = $product->get_availability();
    //check if availability in the array = string 'Out of Stock'
    //if you want to display the 'in stock' messages as well just leave out this, == 'Out of stock'
    if (  ! $product->is_in_stock() ) {
        echo apply_filters( 'woocommerce_stock_html', '<span class="stock ' . esc_attr( $product_availability['class'] ) . '">' . esc_html( $product_availability['availability'] ) . '</span>', $product_availability['availability'] );
    }
} //end function fpp_woocommerce_availability_shop_page


function alphabetize_filter_pre_get_posts( $query ) {
    if ( ! $query->is_main_query() ) {
        return $query;
    } else {
        if ( is_category( array(75, 76, 78, 79, 83, 84, 87, 88, 90, 91, 93, 94, 95, 96, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 117, 118, 119, 120, 121, 122, 130, 131, 132, 148, 221, 222, 224, 239, 252, 260, 493, 495, 496, 497, 498) ) ) {
            $query->set(  'orderby','title' );
            $query->set(  'order', 'ASC' );
        }
        return $query;
    }
}
add_filter( 'pre_get_posts', 'alphabetize_filter_pre_get_posts' );

// Get Related Products from only the main categories 

// add_filter('woocommerce_related_products_args', 'dst_filter_related_products', 10);

function dst_filter_related_products( $args ) {
    //add category numbers to this list
    $allowed_categories = array( 16, 20, 57, 73, 178, 181, 219, 444);

    global $product;
    global $woocommerce;

    // Related products are found from category only
    $cats_array = array();

    // Get categories
    $terms = wp_get_post_terms($product->get_id(), 'product_cat');
    foreach($terms as $term) {
        if(in_array($term->term_id, $allowed_categories)) {
            $cats_array[] = $term->term_id;
        }
    }

    if(!empty($cats_array)) {
        unset($args['post__in']);
        $args = wp_parse_args( array( 'tax_query'      => array(
            'relation'      => 'OR',
            array(
                'taxonomy'     => 'product_cat',
                'field'        => 'id',
                'terms'        => $cats_array
            )
        ) ), $args );

        return $args;
    } 
}

/**
 * Add the query args for products & blog posts
 */
add_filter( 'query_vars', 'dst_query_args' );

function dst_query_args( $qvars ) {
    $qvars[] = 'dr_category';
    $qvars[] = 'dr_sort';
    return $qvars;
}

/**
 * Add next/prev buttons for woocommerce products
 */
 
add_action( 'woocommerce_before_single_product', 'dst_prev_next_product' , 1);
 
function dst_prev_next_product(){
    
    echo '<div class="prev_next_buttons">';
    
    echo '<div class="previous-button">';
        previous_post_link_for_product('%link', 'Previous', true);
    echo '</div><div class="next-button">';
        next_post_link_for_product('%link', 'Next', true);   
    echo '</div></div>';
         
}

// Generate the next product link
function next_post_link_for_product($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
    adjacent_post_link_product($format, $link, $in_same_cat, $excluded_categories, false);
}

// Generate the previous product link
function previous_post_link_for_product($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
    adjacent_post_link_product($format, $link, $in_same_cat, $excluded_categories, true);
}

// Generate the post link that is next to the current product
function adjacent_post_link_product( $format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true ) {
    if ( $previous && is_attachment() )
        $post = get_post( get_post()->post_parent );
    else
        $post = get_adjacent_post_product( $in_same_cat, $excluded_categories, $previous );
    if ( ! $post ) {
        $output = '';
    } else {
        $title = $post->post_title;

        if ( empty( $post->post_title ) )
            $title = $previous ? __( 'Previous Post' ) : __( 'Next Post' );

        $title = apply_filters( 'the_title', $title, $post->ID );
        $date = mysql2date( get_option( 'date_format' ), $post->post_date );
        $rel = $previous ? 'prev' : 'next';

        $string = '<a href="' .  get_permalink( $post ) . '" rel="'.$rel.'">';
        $inlink = str_replace( '%title', $title, $link );
        $inlink = str_replace( '%date', $date, $inlink );
        $inlink = $string . $inlink . '</a>';

        $output = str_replace( '%link', $inlink, $format );
    }

    $adjacent = $previous ? 'previous' : 'next';

    echo apply_filters( "{$adjacent}_post_link", $output, $format, $link, $post, $adjacent );
}

// sorts by alphabetical 

function get_adjacent_post_product( $in_same_cat = false, $excluded_categories = '', $previous = true ) {
    global $wpdb;

    if ( ! $post = get_post() )
        return null;

    $join = '';
    $posts_in_ex_cats_sql = '';

    if ( $in_same_cat || ! empty( $excluded_categories ) ) {
        $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
        // current product_cat_id 
        // get the query var
        $product_cat_id = get_query_var( 'dr_category', '' );

        if ( $in_same_cat ) {
            if ( ! is_object_in_taxonomy( $post->post_type, 'product_cat' ) )
                return '';
            $cat_array = wp_get_object_terms($post->ID, 'product_cat', array('fields' => 'ids'));
            if ( ! $cat_array || is_wp_error( $cat_array ) )
                return '';

            
            // see if a category id is set for the product category, but also make sure that
            // matches an id for the post we're viewing because we could run into a situation 
            // where someone opens a page in one tab, then views another category, but 
            // then goes back to that original page.

            if($product_cat_id != '' && in_array($product_cat_id, $cat_array)) {

                $join .= " AND tt.taxonomy = 'product_cat' AND tt.term_id IN (" . $product_cat_id . ")";
            } else {
                $join .= " AND tt.taxonomy = 'product_cat' AND tt.term_id IN (" . implode(',', $cat_array) . ")";

            }
        }

        $posts_in_ex_cats_sql = "AND tt.taxonomy = 'product_cat'";
        if ( ! empty( $excluded_categories ) ) {
            if ( ! is_array( $excluded_categories ) ) {
                // back-compat, $excluded_categories used to be IDs separated by " and "
                if ( strpos( $excluded_categories, ' and ' ) !== false ) {
                    _deprecated_argument( __FUNCTION__, '3.3', sprintf( __( 'Use commas instead of %s to separate excluded categories.' ), "'and'" ) );
                    $excluded_categories = explode( ' and ', $excluded_categories );
                } else {
                    $excluded_categories = explode( ',', $excluded_categories );
                }
            }

            $excluded_categories = array_map( 'intval', $excluded_categories );

            if ( ! empty( $cat_array ) ) {
                $excluded_categories = array_diff($excluded_categories, $cat_array);
                $posts_in_ex_cats_sql = '';
            }

            if ( !empty($excluded_categories) ) {
                $posts_in_ex_cats_sql = " AND tt.taxonomy = 'product_cat' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
            }
        }
    }

    $adjacent = $previous ? 'previous' : 'next';
    $op = $previous ? '<' : '>';
    $order = $previous ? 'DESC' : 'ASC';

    $join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );

    $where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare( "WHERE p.post_title $op %s AND p.post_type = %s AND p.post_status = 'publish'", $post->post_title, $post->post_type ), $in_same_cat, $excluded_categories );

    $sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_title $order LIMIT 1" );

    $query = "SELECT p.id FROM $wpdb->posts AS p $join $where $sort";

    $query_key = 'adjacent_post_' . md5($query);
    $result = wp_cache_get($query_key, 'counts');
    if ( false !== $result ) {
        if ( $result )
            $result = get_post( $result );
        return $result;
    }

    $result = $wpdb->get_var( $query );
    if ( null === $result )
        $result = '';

    wp_cache_set($query_key, $result, 'counts');

    if ( $result )
        $result = get_post( $result );

    return $result;
}

/**
 * Writes logs to the log file
 */
function wl ( $log )  {
	if ( is_array( $log ) || is_object( $log ) ) {
		error_log( print_r( $log, true ) );
	} else {
		error_log( $log );
	}
} // end write_log

/**
 * Add filtering for the permalink
 */

add_filter('post_link', 'dst_filter_blog_permalink', 10, 3);
function dst_filter_blog_permalink($url, $post, $leavename=false) {
    if(is_category() && get_queried_object_id() != 446) {
        $url = add_query_arg('dr_sort', get_queried_object_id(), $url);
    }
    return $url;
}
/*
* Sort Next/Previous Post Links Alphabetically for posts
*/

add_filter('get_next_post_sort',  'filter_next_and_prev_post_sort');
add_filter('get_previous_post_sort',  'filter_next_and_prev_post_sort');
function filter_next_and_prev_post_sort($sort) {
    global $post;
    // get the category query var
    $post_cat_id = get_query_var( 'dr_sort', '' );
    $cat_array = wp_get_object_terms($post->ID, 'category', array('fields' => 'ids'));
    // if(in_array('446', $cat_array)){
    if (get_post_type($post) == 'post' && $post_cat_id != '446' && !in_array('446', $cat_array)) {
        // make sure we are not in the blog (446) category which should be sorted by date
        $op = ('get_previous_post_sort' == current_filter()) ? 'DESC' : 'ASC';
        $sort = "ORDER BY p.post_title ".$op ." LIMIT 1";
    } 
    return $sort;
}

add_filter( 'get_next_post_join', 'navigate_in_same_taxonomy_join', 20);
add_filter( 'get_previous_post_join', 'navigate_in_same_taxonomy_join', 20 );
function navigate_in_same_taxonomy_join($join) {
    global $wpdb, $post;
    // get the category query var
    $post_cat_id = get_query_var( 'dr_sort', '' );

    if (get_post_type($post) == 'post' && $post_cat_id != '') {

        $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

        $cat_array = wp_get_object_terms($post->ID, 'category', array('fields' => 'ids'));
            if ( ! $cat_array || is_wp_error( $cat_array ) )
                return '';
                

        if(in_array('446', $cat_array)){
            // if this is the blog category return only blogs
            $join .= " AND tt.taxonomy = 'category' AND tt.term_id IN (446)";
        } else if($post_cat_id != '') {
            // if a dr_sort category is specified return those
            $join .= " AND tt.taxonomy = 'category' AND tt.term_id IN (" . $post_cat_id . ")";
        } else {
            // return the original join
            return $join;
        }

    }

    return $join;
}

add_filter( 'get_next_post_where' , 'filter_next_and_prev_post_where' );
add_filter( 'get_previous_post_where' , 'filter_next_and_prev_post_where' );
function filter_next_and_prev_post_where( $original ) {

    global $wpdb, $post;
    // get the category query var
    $post_cat_id = get_query_var( 'dr_sort', '' );
    
    if (get_post_type($post) == 'post') {

        $where = '';
        $taxonomy   = 'category';
        $op = ('get_previous_post_where' == current_filter()) ? '<' : '>';

        if ( ! is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
            return $original ;
        }

        $term_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

        $term_array = array_map( 'intval', $term_array );
        if ( ! $term_array || is_wp_error( $term_array ) ) {
            return $original;
        }
        if(in_array('446', $term_array)){
            // don't modify the query (which includes sorting by post title) if a blog post
            return $original;
        } else if($post_cat_id != '') {     
            // get items in the specified dr_sort category  
            $where = " AND tt.term_id IN (" . $post_cat_id . ")";
            return $wpdb->prepare( "WHERE p.post_title $op %s AND p.post_type = %s AND p.post_status = 'publish' $where", $post->post_title, $post->post_type );
        } else {
            // just get items matching any of the post terms
            $where = " AND tt.term_id IN (" . implode( ',', $term_array ) . ")";
            return $wpdb->prepare( "WHERE p.post_title $op %s AND p.post_type = %s AND p.post_status = 'publish' $where", $post->post_title, $post->post_type );
        }
    } else {
        return $original;
    }
}


/**
 * Add the query to the post next / previous links
*/
function dst_next_prev_link_add_query_args( $output, $format, $link, $post, $adjacent) {

    $original_link = get_permalink( $post );
    $post_link = $original_link;

    // get the category query var
    $post_cat_id = get_query_var( 'dr_sort', '' );
    if ( $post_cat_id != '' ) {
        $post_link = add_query_arg('dr_sort', $post_cat_id, $post_link);
    }
    $product_cat_id = get_query_var( 'dr_category', '' );
    if ( $product_cat_id != '' ) {
        $post_link = add_query_arg('dr_category', $product_cat_id, $post_link);
    }

    $output = str_replace($original_link, $post_link, $output);
    // return add_query_arg( $query_args, $url );
    return $output;
}

add_filter('previous_post_link','dst_next_prev_link_add_query_args', 100, 5);
add_filter('next_post_link','dst_next_prev_link_add_query_args', 100, 5);