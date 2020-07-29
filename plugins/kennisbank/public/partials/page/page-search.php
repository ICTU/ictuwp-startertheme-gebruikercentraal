<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Setting global variables.
 *
 */
global $pakb_helper, $pakb_loop;  ?>

<?php
echo '<div class="uk-margin-medium-bottom">';
echo $pakb_helper->the_search();
echo '</div>';
?>
<div class="uk-margin-large-top">
	<?php $pakb_loop->the_breadcrumbs(); ?>
	<ul class="uk-margin-medium-top uk-list uk-list-large pakb-list pakb-secondary-color link-icon-right">
	<?php
		while ( $pakb_loop->have_posts() ) : $pakb_loop->the_post();
			//will skip if the post type is not knowledgebase
			if ( get_post_type( $pakb_loop->get_the_ID() ) !== "knowledgebase" ) {
				continue;
			}
			do_action( 'pakb_search_loop' ); // action inside the loop for search ?>
			<li id="<?php echo esc_attr( 'kb-' . $pakb_loop->get_the_ID() ); ?>"><a href="<?php $pakb_loop->the_permalink(); ?>"><?php $pakb_loop->the_title(); ?></a></li>
			<?php
		endwhile;
	?>
	</ul>
	<?php if ( ! $pakb_loop->have_posts() ) : ?>
		<div class="uk-alert-primary" uk-alert>
			<?php _e( 'Sorry, no results were found.', 'pressapps-knowledge-base' ); ?>
		</div>
	<?php endif; ?>
</div>
