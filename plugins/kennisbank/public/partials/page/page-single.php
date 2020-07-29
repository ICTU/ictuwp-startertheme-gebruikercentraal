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

$meta = !is_null( get_field( 'pakb_meta_info', 'option' ) ) ? get_field( 'pakb_meta_info', 'option' ) : true;
$toc = !is_null( get_field( 'pakb_toc', 'option' ) ) ? get_field( 'pakb_toc', 'option' ) : true;
$related = !is_null( get_field( 'pakb_related_articles', 'option' ) ) ? get_field( 'pakb_related_articles', 'option' ) : true;

if ( !is_null( get_field( 'pakb_meta', 'option' ) ) ) {
	$meta_display = get_field( 'pakb_meta', 'option' );
	$meta = get_field( 'pakb_meta_info', 'option' );
} else {
	$meta_display = true;
	$meta = ['updated', 'category', 'tags'];
}

if ( is_null( get_field( 'pakb_search', 'option' ) ) ) {
	$search_display = true;
} elseif ( !is_null( get_field( 'pakb_search', 'option' ) ) && in_array( 'single', get_field( 'pakb_search', 'option' ) ) ) {
	$search_display = true;
} else {
	$search_display = false;
}

if ( $search_display ) {
	echo '<div class="uk-margin-medium-bottom">';
	echo $pakb_helper->the_search();
	echo '</div>';
}

while ( $pakb_loop->have_posts() ) : $pakb_loop->the_post();
	do_action( 'pakb_single_loop' ); // action inside the loop for single page ?>
	<article class="uk-article pakb-link">
		<?php $pakb_loop->the_breadcrumbs(); ?>

		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail();
		}
		?>

		<?php if ( $toc ) { 
			$toc_title = !is_null( get_field( 'pakb_toc_title', 'option' ) ) ? get_field( 'pakb_toc_title', 'option' ) : __( 'Article sections', 'pressapps-knowledge-base' );
			?>
			<div class="pakb-toc-wrap uk-margin-large-bottom">
				<?php if ( $toc_title ) { ?>
					<h3 class="uk-margin-medium-bottom"><?php echo $toc_title; ?></h3>
				<?php } ?>
				<ul class="pakb-toc uk-nav pakb-accent-color" data-toc=".pakb-article-content" data-toc-headings="<?php echo get_option( 'options_pakb_toc_selectors' ); ?>"></ul></div>
		<?php } ?>
		<?php
		$style_ordered_list = get_post_meta( get_the_ID(), 'style_ordered_list', true );
		// $attachments = get_post_meta( get_the_ID(), 'attachments', true );

		if ( !empty( $style_ordered_list ) && $style_ordered_list ) {
			$style = ' styled-ol';
		} else {
			$style = '';
		}

		echo '<div class="pakb-article-content' . $style . '">';
		$pakb_loop->the_content();
		echo '</div>';

		// check if the repeater field has rows of data
		if( get_option('options_pakb_attachments') ) {
			echo $pakb_helper->attachments();
		}
		
		if ( $meta_display ) {
			echo '<div class="pakb-muted-color uk-margin-medium-top">';
				if ( !empty($meta) && in_array( 'updated', $meta ) ) {
					echo '<time class="updated published" datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . sprintf( __( 'Last Updated: %s ago', 'pressapps-knowledge-base' ), human_time_diff( get_the_modified_date( 'U' ), current_time( 'timestamp' ) ) ) . '</time> ';
				}
				if ( !empty($meta) && in_array( 'category', $meta ) ) {
					$pakb_loop->the_category();
				}
				if ( !empty($meta) && in_array( 'tags', $meta ) ) {
					echo ' ';
					$pakb_loop->the_tags();
				}
			echo '</div>';
		}

		if ( get_option( 'options_pakb_vote' ) != 0 && ! $pakb_loop->post_password_required() ) {
			echo '<div id="pakb-vote">';
			$this->the_votes();
			echo '</div>';
		} ?>
		<?php
		if ( $related ) {
			$pakb_helper->display_related_articles( $pakb_loop->get_the_ID() );
		}
		?>
		<?php
		if ( get_option( 'options_pakb_comments' ) ) {
			$theme = wp_get_theme();
			if ( 'Knowledge Base' == $theme->name || 'Knowledge Base' == $theme->parent_theme ) {
				comments_template( '/templates/comments.php' );
			} else {
				comments_template();
			}
		}
		?>
	</article>
	<?php
endwhile;
?>
