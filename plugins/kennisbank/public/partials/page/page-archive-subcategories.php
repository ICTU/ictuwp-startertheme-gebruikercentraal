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

// $page_link = get_page_link();

$columns = $pakb_helper->get_columns();

if ( $pakb_helper->get_layout() == 2 ) {
	$class = 'pakb-boxes';
	$gridmatch = ' uk-grid-match';
} else {
	$class = 'pakb-lists';
	$gridmatch = '';
}

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

$pakb_loop->the_breadcrumbs();

$pakb_loop->get_category_desc();

?>
<div class="pakb-section pakb-link <?php echo $class; ?>">
	<div class="<?php echo esc_attr( 'uk-child-width-1-' . $columns . '@m' . $gridmatch ); ?>" data-uk-grid>
		<?php
		foreach ( $pakb_loop->get_cats() as $cat ){
			$pakb_loop->setup_cat( $cat );
			if ( ! $pakb_loop->subcat_have_posts() ) {
				continue;
			}
			$pakb_loop->print_the_cat();
		} ?>
	</div><?php if ( is_pakb_category() ) { ?>
		<ul class="uk-margin-medium-top uk-list uk-list-large pakb-list pakb-secondary-color pakb-link link-icon-right">
			<?php include( plugin_dir_path( __FILE__ ) . '../content/content-children.php' ); ?>
		</ul>
	<?php } ?>
</div>
<?php
