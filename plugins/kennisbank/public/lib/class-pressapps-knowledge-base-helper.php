<?php

class PAKB_Helper {

	/**
	 * Search function.
	 *
	 * @since 1.0.0
	 */
	public function the_search() {
		global $pakb_loop;

		$search_placeholder = !is_null( get_field( 'pakb_search_placeholder', 'option' ) ) ? trim( strip_tags( get_field( 'pakb_search_placeholder', 'option' ) ) ) : 'Search for answers';
		$live_search = !is_null( get_field( 'pakb_search_live', 'option' ) ) ? get_field( 'pakb_search_live', 'option' ) : true;
		$theme = get_field( 'pakb_theme', 'option' ) ? get_field( 'pakb_theme', 'option' ) : 'origin';
		if ( $theme == 'xxx' ) {
			$flip = ' class="uk-search-icon-flip"';
		} else {
			$flip = '';
		}
		
		$html = '';
		$html .= '<form role="search" class="uk-search uk-search-large uk-search-default" method="post" id="kbsearchform" action="' . home_url( '/' ) . '">';
		$html .= '<button type="submit" id="searchsubmit"' . $flip . ' data-uk-search-icon></button>';
		$html .= '<input type="text" value="' . ( is_search() ? get_search_query() : '') . '" name="s" placeholder="' . ( ! empty( $search_placeholder ) ? $search_placeholder : '' ) . '" id="kb-s" class="uk-search-input' . ( $live_search ? ' autosuggest' : '' ) . '"/>';
		$html .= '<input type="hidden" name="post_type" value="knowledgebase"/>';
		$html .= wp_nonce_field( 'knowedgebase-search', 'search_nonce', false, false );
		$html .= '</form>';

		return $html;
	}

	/**
	 * Includes the file.
	 *
	 * @since 1.0.0
	 *
	 * @param $filename
	 *
	 * @return string
	 */
	public function load_file( $filename ) {
		ob_start();
		include $filename;

		return ob_get_clean();
	}

	/**
	 * Getting template files.
	 *
	 * @since 1.0.0
	 *
	 * @param string $case
	 *
	 * @return string
	 */
	public function get_template_files( $case = 'single' ) {

		$default_path = plugin_dir_path( dirname( __FILE__ ) ) . 'partials/';
		$theme_path   = get_stylesheet_directory() . '/pakb/';

		switch ( $case ) {
			case 'search':
				$filename = 'page/page-search.php';
				break;
			case 'archive':
				$filename = 'page/page-archive.php';
				break;
			case 'single':
			default :
				$filename = 'page/page-single.php';
				break;
			case 'category':
				$filename = 'content/content-category.php';
				break;
			case 'knowledgebase':
				$filename = 'content/content-knowledgebase.php';
				break;
			case 'archive-subcategories':
				$filename = 'page/page-archive-subcategories.php';
				break;
		}

		$default_file = $default_path . $filename;
		$theme_file   = $theme_path . $filename;

		// Modification ref issue #15 to support plugin overrides
		// return ( ( file_exists( $theme_file ) ) ? $theme_file : $default_file );

		$located_file = ( ( file_exists( $theme_file ) ) ? $theme_file : $default_file );
		return apply_filters( 'pakb_load_template', $located_file, $filename );

	}

	/**
	 * Overriding variable.
	 *
	 * @since 1.0.0
	 */
	public function override_is_var() {
		global $wp_query;

		$wp_query->is_tax               = false;
		$wp_query->is_archive           = false;
		$wp_query->is_search            = false;
		$wp_query->is_single            = false;
		$wp_query->is_post_type_archive = false;
		$wp_query->is_404 				= false;
		$wp_query->is_singular 			= true;
		$wp_query->is_page     			= true;
	}

	/**
	 * Template function for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function page_template( $template ) {

		$templates = array();

		if ( $template && 0 === validate_file( $template ) ) {
			$templates[] = $template;
		}

		//check if skelet option page template has been set and will use that template
		if ( !empty(get_option( 'options_pakb_page_template' ) ) ) {
			$template_name = str_replace( '.php', '', basename( get_option( 'options_pakb_page_template' ) ) );

			$templates[] = get_option( 'options_pakb_page_template' );

			return get_query_template( $template_name, $templates );

		} else {
			$templates[] = 'page.php';

			return get_query_template( 'page', $templates );
		}

	}

	/**
	 * Post data function.
	 *
	 * @since 1.0.0
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public function get_dummy_post_data( $args ) {

		return array_merge( array(
			'ID'                    => 0,
			'post_status'           => 'publish',
			'post_author'           => 0,
			'post_parent'           => 0,
			'post_type'             => 'page',
			'post_date'             => 0,
			'post_date_gmt'         => 0,
			'post_modified'         => 0,
			'post_modified_gmt'     => 0,
			'post_content'          => '',
			'post_title'            => '',
			'post_excerpt'          => '',
			'post_content_filtered' => '',
			'post_mime_type'        => '',
			'post_password'         => '',
			'post_name'             => '',
			'guid'                  => '',
			'menu_order'            => 0,
			'pinged'                => '',
			'to_ping'               => '',
			'ping_status'           => '',
			'comment_status'        => 'closed',
			'comment_count'         => 0,
			'filter'                => 'raw',
		), $args );
	}

	/**
	 * Function for casting votes.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|false $is_ajax
	 */
	public function the_votes( $id = '', $is_ajax = false ) {

		global $post;
		if( !$id ){
			$id = $post->ID;
		}

		$votes_like        = (int) get_post_meta( $id, '_votes_likes', true );
		$votes_dislike     = (int) get_post_meta( $id, '_votes_dislikes', true );
		$voted_like        = sprintf( _n( '%s person found this helpful', '%s people found this helpful', $votes_like, 'pressapps-knowledge-base' ), $votes_like );
		$voted_dislike     = sprintf( _n( '%s person did not find this helpful', '%s people did not find this helpful', $votes_dislike, 'pressapps-knowledge-base' ), $votes_dislike );
		$vote_like_link    = __( "I found this helpful", 'pressapps-knowledge-base' );
		$vote_dislike_link = __( "I did not find this helpful", 'pressapps-knowledge-base' );
		$cookie_vote_count = '';
		$vote_title        = trim( strip_tags( get_option( 'options_pakb_vote_title' ) ) );
		$vote_thanks       = trim( strip_tags( get_option( 'options_pakb_vote_thanks' ) ) );

		if ( isset( $_COOKIE['vote_count'] ) ) {
			$cookie_vote_count = @unserialize( base64_decode( $_COOKIE['vote_count'] ) );
		}

		if ( ! is_array( $cookie_vote_count ) && isset( $cookie_vote_count ) ) {
			$cookie_vote_count = array();
		}

		echo( ( $is_ajax ) ? '' : '<div class="votes uk-margin-large-top">' );

		if ( ! empty ( $vote_title ) ) {
			echo '<div class="uk-text-center mb-l text-l">' . $vote_title . '</div>';
		}

		if ( is_user_logged_in() || get_option( 'options_pakb_vote' ) == 1 ) :

			if ( is_user_logged_in() ) {
				$vote_count = (array) get_user_meta( get_current_user_id(), 'vote_count', true );
			} else {
				$vote_count = $cookie_vote_count;
			}

			$icon_up 		= ( !empty( get_option( 'options_pakb_vote_up_icon' ) ) ? @file_get_contents( PAKB_ICON_DIR . get_option( 'options_pakb_vote_up_icon' ) . '.svg' ) : @file_get_contents( PAKB_ICON_DIR . 'ios-heart.svg' ) );
			$icon_down 	= ( !empty( get_option( 'options_pakb_vote_down_icon' ) ) ? @file_get_contents( PAKB_ICON_DIR . get_option( 'options_pakb_vote_down_icon' ) . '.svg' ) : @file_get_contents( PAKB_ICON_DIR . 'ios-dislike.svg' ) );



			if ( ! in_array( $id, $vote_count ) ) {
				echo '<div class="uk-flex uk-flex-center">';
				echo '<div class="uk-text-right"><a title="' . esc_attr( $vote_like_link ) . '" class="pakb-like-btn pakb-accent-color" data-uk-tooltip href="#" onclick="return false" data-post-id="' . esc_attr( $id ) . '">' . $icon_up . '</a></div>';
				echo '<div class="uk-text-left"><a title="' . esc_attr( $vote_dislike_link ) . '" class="pakb-dislike-btn pakb-accent-color" data-uk-tooltip href="#" onclick="return false" data-post-id="' . esc_attr( $id ) . '">' . $icon_down . '</a></div>';
				echo '</div>';
			} else {
				// already voted
				echo '<div class="uk-flex uk-flex-center">';
				echo '<div title="' . esc_attr( $voted_like ) . '" class="uk-text-right pakb-like-btn" data-uk-tooltip>' . $icon_up . '</div>';
				echo '<div title="' . esc_attr( $voted_dislike ) . '" class="uk-text-left pakb-dislike-btn" data-uk-tooltip>' . $icon_down . '</div>';
				echo '</div>';
				if ( ! empty ( $vote_thanks ) ) {
					echo '<div class="uk-text-center mt-l">' . $vote_thanks . '</div>';
				}
			}

		else :
			// not logged in
			echo '<div class="uk-flex uk-flex-center">';
			echo '<div title="' . esc_attr( $voted_like ) . '" class="uk-text-right pakb-like-btn" data-uk-tooltip>' . $icon_up . '</div>';
			echo '<div title="' . esc_attr( $voted_dislike ) . '" class="uk-text-left pakb-dislike-btn" data-uk-tooltip>' . $icon_down . '</div>';
			echo '</div>';
		endif;

		//echo '</div>';

		echo( ( $is_ajax ) ? '' : '</div>' );
	}

	public function attachments() {

		if ( is_admin() && $_POST["post_id"] !== NULL ) {
			$id = $_POST["post_id"];
		} elseif ( !is_admin() && get_the_ID() !== NULL ) {
			$id = get_the_ID();
		} else {
			_e( 'Something went wrong, insure you are using this block/shortcode only in knowledge base posts.', 'pressapps-knowledge-base' );
			return;
		}

		if( have_rows('attachments', $id ) ) {
			if ( !empty( get_option('options_pakb_attachment_info' ) ) ) {
				$display = get_option('options_pakb_attachment_info');
			} else {
				$display = ['filename', 'modified', 'size'];
			}


			$html = '';
			$html .= '<table class="uk-table uk-table-divider uk-table-justify">';
			$html .= '<tbody>';

			// loop through the rows of data
				while ( have_rows('attachments', $id ) ) : the_row();
					$file = get_sub_field('file');

					$html .= '<tr>';

					foreach ($display as $info) {

						switch ($info) {
							case 'icon':
								$html .= '<td><img src="' . $file['icon'] . '" /></td>';
								break;
							case 'author':
								$html .= '<td>' . get_the_author_meta( 'display_name', $file['author'] ) . '</td>';
								break;
							case 'size':
								$html .= '<td>' . size_format( $file['filesize'] ) . '</td>';
								break;
							case 'modified':
								$html .= '<td>' . mysql2date( get_option( 'date_format' ), $file['modified'] ) . '</td>';
								break;
							case 'type':
								$html .= '<td>' . $file['subtype'] . '</td>';
								break;
							case 'filename':
								$html .= '<td><a href="' . $file['url'] . '" title="' . $file['filename'] . '" target="_blank">' . $file['filename'] . '</a></td>';
								break;
							case 'description':
								$html .= '<td>' . $file['description'] . '</td>';
								break;
						}

					}
					$html .= '</tr>';
				endwhile;
			$html .= '</tbody>';
			$html .= '</table>';

			return $html;

		} else {
			if ( is_admin() ) {
				_e( 'No files attached to this article.', 'pressapps-knowledge-base' );
			}
		}
	}

	/**
	 * Filter function to fixed Array to string conversion notice
	 *
	 * @param  string $string
	 *
	 * @param string  $default
	 *
	 * @return string
	 */
	public function filtered_string( $string, $default = "" ) {
		if ( is_string( $string ) && strtolower( $string ) === 'array' ) {
			empty( $default ) ? $string = "" : $string = $default;
		} elseif ( is_array( $string ) ) {
			empty( $default ) ? $string = "" : $string = $default;
		}

		return $string;
	}

	/**
	 * Display related articles on single post
	 *
	 * @param $id
	 */
	public function display_related_articles( $id ) {

		$taxonomy   = 'knowledgebase_category';
		$post_terms = wp_get_post_terms( $id, $taxonomy );
		$post_array = array();

		if ( ! is_wp_error( $post_terms ) ) {

			foreach ( $post_terms as $post_term ) {
				$args = $args = array(
					'post_type'      => 'knowledgebase',
					'posts_per_page' => 6,
					'exclude'        => $id,
					'tax_query'      => array(
						array(
							'taxonomy' => $taxonomy,
							'field'    => 'term_id',
							'terms'    => $post_term->term_id
						)
					)
				);
				$post_array_objects = get_posts( $args );

				foreach ( $post_array_objects as $post_array_object ) {
					$post_array[] = $post_array_object->ID;
				}
			}

			$i = 0;
			$count = get_option( 'options_pakb_related_count' ) ? get_option( 'options_pakb_related_count' ) : 10;

			echo '<div class="uk-margin-large-top pakb-primary">';
			printf( '<h3>%s</h3>', __( 'Related Articles', 'pressapps-knowledge-base' ) );
			//will check if the post id exist in the array and will remove
			$post_array = array_unique($post_array);
			echo '<ul class="uk-list uk-list-large pakb-list pakb-secondary-color link-icon-right">';
			foreach ( $post_array as $index => $post_id ) {
				$post_object = get_post( $post_id ); ?>
				<li><?php printf( '<a id="%s" href="%s">%s</a>', $post_object->ID, get_permalink( $post_object->ID ), esc_html( $post_object->post_title ) ); ?></li>
				<?php
				if ( ++$i == $count ) break;
			}
			echo '</ul></div>';
		}
	}

	/**
	 * Helper function to check on the reorder option and return a specific orderby
	 *
	 * @param bool|false $is_category
	 *
	 * @return string
	 */
	public function reorder_option( $is_category = false ) {

		switch ( get_option( 'options_pakb_content_order' ) ) {
			case 'default':
				$orderby = 'date';
				break;
			case 'draganddrop':
				$orderby = ( $is_category ) ? 'term_group' : 'menu_order';
				break;
			case 'alphabetical':
				$orderby = ( $is_category ) ? 'name' : 'title';
				break;
			default:
				$orderby = 'date';
				break;
		}

		return $orderby;
	}

	public function get_layout() {

		$theme = get_option( 'options_pakb_theme' ) ? get_option( 'options_pakb_theme' ) : 'origin';
		$layout = get_option( 'options_pakb_' . $theme . '_layout' ) ? get_option( 'options_pakb_' . $theme . '_layout' ) : 2;

		if ( !empty( $layout ) ) {
			return $layout;
		} else {
			return 2;
		}	
	
	}

	public function get_columns() {

		$theme = get_option( 'options_pakb_theme' ) ? get_option( 'options_pakb_theme' ) : 'origin';
		$columns = get_option( 'options_pakb_' . $theme . '_columns' ) ? get_option( 'options_pakb_' . $theme . '_columns' ) : 3;

		if ( !empty( $columns ) ) {
			return $columns;
		} else {
			return 2;
		}	
	
	}

	public function is_category_icon_enabled() {

		if ( is_null( get_field( 'pakb_theme', 'option' ) ) ) {
			return true;
		} else {
			$theme = get_field( 'pakb_theme', 'option' ) ? get_field( 'pakb_theme', 'option' ) : 'origin';
			return get_field( 'pakb_' . $theme . '_category_icon', 'option' );
		}	
	
	}

}