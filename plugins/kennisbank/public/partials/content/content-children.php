<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $pakb_helper;

$cat = get_queried_object()->term_id;;

if ( get_option( 'options_pakb_content_order' ) !== 'default' ) {
	$orderby = $pakb_helper->reorder_option();

	$args = array(
		'post_type' => 'knowledgebase',
		'orderby'   => $orderby,
		'order'     => 'ASC',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy'         => 'knowledgebase_category',
				'field'            => 'id',
				'terms'            => $cat,
				'include_children' => false
			)
		),
	);
} else {
	$args = array(
		'post_type'   => 'knowledgebase',
		'posts_per_page' => -1,
		'tax_query'   => array(
			array(
				'taxonomy'         => 'knowledgebase_category',
				'field'            => 'id',
				'terms'            => $cat,
				'include_children' => false
			)
		),
	);
}

$query_children = new WP_Query( $args );

while( $query_children->have_posts() ) : $query_children->the_post(); ?>
  <li id="<?php echo esc_attr( 'kb-' . get_the_ID() ); ?>"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?><?php //$pakb_helper->vote_ui(); ?></a></li>
<?php
endwhile;
