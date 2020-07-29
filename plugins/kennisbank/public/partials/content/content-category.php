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

$description = $pakb_loop->get_cat_description();
$icon = get_term_meta($pakb_loop->get_cat_id(), 'icon', true);
$view_all = !is_null( get_field( 'pakb_view_all', 'option' ) ) ? get_field( 'pakb_view_all', 'option' ) : true;

if ( $pakb_helper->get_layout() == 2 ) {

	$html  = '<div>';
	$html .= '<div class="uk-card uk-card-small uk-card-body uk-card-default uk-border-rounded uk-inline uk-text-center">';
	$html .= '<div><a class="card-link uk-position-cover" href="' . esc_url( $pakb_loop->get_cat_link() ) . '"></a></div>';
	if ( !empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
		$html .= '<div class="pakb-box-icon">' . @file_get_contents( PAKB_ICON_DIR . $icon . '.svg' ) . '</div>';
	} elseif ( empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
		$html .= '<div class="pakb-box-icon">' . @file_get_contents( PAKB_ICON_DIR . 'ios-folder.svg' ) . '</div>';
	}
	$html .= '<h2 class="uk-card-title pakb-secondary-color">' . $pakb_loop->get_cat_name() . '</h2>';
	$html .= ( !empty( $description ) ? '<p class="pakb-muted-color">' .$pakb_loop->get_cat_description() . '</p>' : '' );
	if ( $view_all ) {
		$html .= '<p class="pakb-muted-color">' . __( 'View All', 'pressapps-knowledge-base') . ( $pakb_loop->is_view_all_count_enabled() ? $pakb_loop->get_the_cat_count(' ','') : '' ) . '</p>';
	}
	$html .= '</div>';
	$html .= '</div>';
	echo $html;

} else {

	$html  = '<div>';
	$html .= '<h2 class="pakb-accent-color">';
	if ( !empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
		$html .= '<span class="pakb-list-icon">' . @file_get_contents( PAKB_ICON_DIR . $icon . '.svg' ) . '</span>';
	} elseif ( empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
		$html .= '<span class="pakb-list-icon">' . @file_get_contents( PAKB_ICON_DIR . 'ios-folder.svg' ) . '</span>';
	}
	$html .= '<a href="' . esc_url( $pakb_loop->get_cat_link() ) . '">' . $pakb_loop->get_cat_name() . ( $pakb_loop->is_cat_count_enabled() ? $pakb_loop->get_the_cat_count(' (',')') : '' ) . '</a></h2>';
	$html .= '<ul class="uk-list uk-list-large">';
		while ($pakb_loop->subcat_have_posts() ) {
			$pakb_loop->subcat_the_post();
			do_action( 'pakb_category_loop' ); // action inside the loop for category page
			$html .= '<li><a class="pakb-secondary-color" href="' . esc_url( $pakb_loop->subcat_get_permalink() ) . '">' . $pakb_loop->subcat_get_title() . '</a></li>';
		}
	$html .= '</ul>';
	if ( $view_all ) {
		$html .= '<div><a class="pakb-muted-color uk-margin-small-top uk-margin-remove-bottom" href="' . $pakb_loop->get_cat_link() . '">' . __( 'View All', 'pressapps-knowledge-base') . ( $pakb_loop->is_view_all_count_enabled() ? $pakb_loop->get_the_cat_count(' ','') : '' ) . '</a></div>';
	}
	$html .= '</div>';

	echo $html;

}
