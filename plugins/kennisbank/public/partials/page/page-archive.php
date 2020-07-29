<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
/**
 * Setting global variables.
 *
 */
global $pakb_helper, $pakb_loop;

if ( is_null( get_field( 'pakb_search', 'option' ) ) ) {
	$search_display = true;
} elseif ( !is_null( get_field( 'pakb_search', 'option' ) ) && in_array( 'category', get_field( 'pakb_search', 'option' ) ) ) {
	$search_display = true;
} else {
	$search_display = false;
}

if ( $search_display ) {
	echo '<div class="uk-margin-medium-bottom">';
	echo $pakb_helper->the_search();
	echo '</div>';
}
?>

<div class="pakb-link">
    <?php $pakb_loop->the_breadcrumbs(); ?>
    <?php $pakb_loop->get_category_desc(); ?>
    <ul class="uk-margin-medium-top uk-list uk-list-large pakb-list pakb-secondary-color link-icon-right">
    <?php
        while( $pakb_loop->have_posts() ) : $pakb_loop->the_post();
            do_action( 'pakb_archive_loop' ); // action inside the loop for archive page
    ?>
        <li id="<?php echo esc_attr( 'kb-' . $pakb_loop->get_the_ID() ); ?>"><a href="<?php echo esc_url( $pakb_loop->get_the_permalink() ); ?>"><?php $pakb_loop->the_title(); ?><?php //$pakb_helper->vote_ui(); ?></a></li>
    <?php
        endwhile;
    ?>
    </ul>
</div>
