<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://pressapps.co
 * @since      1.0.0
 *
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/public
 * @author     PressApps
 */
class Pressapps_Knowledge_Base_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles_base() {

		$theme = wp_get_theme(); // gets the current theme

		if ( 'Knowledge Base' == $theme->name || 'Knowledge Base' == $theme->parent_theme ) {
			wp_enqueue_style( 'base', plugin_dir_url( __FILE__ ) . 'css/pressapps-knowledge-base-base.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$style_theme = get_option( 'options_pakb_theme' ) ? sanitize_text_field( get_option( 'options_pakb_theme' ) ) : 'origin';

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pressapps-knowledge-base-' . $style_theme . '.css', array(), $this->version, 'all' );

		wp_add_inline_style( $this->plugin_name, wp_kses( $this->custom_css(), array( '\"', "\'" ) ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$search_category = !is_null( get_field( 'pakb_search_category', 'option' ) ) ? get_field( 'pakb_search_category', 'option' ) : true;

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pressapps-knowledge-base-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'PAKB', array(
			'base_url' => esc_url( home_url() ),
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'category' => $search_category,
			'noresult_placeholder' => __( 'No Results Found', 'pressapps-knowledge-base' ),
			'tocscrolloffset' => !is_null( get_field( 'pakb_toc_offset', 'option' ) ) ? get_field( 'pakb_toc_offset', 'option' ) : '100'
		) );
	}

	function add_body_class( $classes ) {

		if ( is_tax( 'knowledgebase_category' ) ) {
	        $classes[] = 'pakb-template-category';
		} elseif ( is_singular( 'knowledgebase' ) ) {
	        $classes[] = 'pakb-template-single';
		}

	    return $classes;

	}

	/**
	 * Registers all shortcodes at once
	 */
	public function register_shortcodes() {
		add_shortcode( 'pakb_articles', array( $this, 'articles_shortcode' ) );
		add_shortcode( 'pakb_knowledgebase', array( $this, 'knowledgebase_shortcode' ) );
		add_shortcode( 'pakb_search', array( $this, 'search_shortcode' ) );
		add_shortcode( 'pakb_attachments', array( $this, 'attachments_shortcode' ) );
		add_shortcode( 'pakb_categories', array( $this, 'categories_shortcode' ) );
	}

	/**
	 * Articles shortcode
	 */
	public function articles_shortcode( $atts ) {

		ob_start();

		$defaults['posts_per_page']	= '10';
		$defaults['orderby']		= 'date';
		$defaults['order']			= 'DESC';
		$defaults['filter']			= '';
		$defaults['category']		= '';
		$defaults['tag']			= '';

		$args					= shortcode_atts( $defaults, $atts, 'pakb_articles' );
		$items 					= $this->get_knowledgebase_posts( $args );

		if ( is_array( $items ) || is_object( $items ) ) {

			include( plugin_dir_path( __FILE__ ) . 'partials/pressapps-knowledge-base-list-article.php' );

		} else {

			echo $items;

		}

		$output = ob_get_contents();

		ob_end_clean();

		return $output;

	}

	/**
	 * Categories shortcode
	 */
	public function categories_shortcode( $atts ) {

		global $pakb_helper;

		$defaults['count']			= false;
		$defaults['order']			= 'ASC';
		$defaults['orderby']		= 'name';
		$defaults['number']			= 10;

		$params = shortcode_atts( $defaults, $atts, 'pakb_categories' );

		$args = array(
			'hide_empty'	=> true,
			'order' 			=> $params['order'],
			'orderby' 		=> $params['orderby'],
			'number' 			=> $params['number'],
			'parent' 			=> 0,
		);

		$terms = get_terms( 'knowledgebase_category', $args );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
	
			$html = '';
				$html .= '<ul class="uk-list uk-list-large pakb-secondary-color pakb-link pakb-widget-categories">';
	
				foreach ( $terms as $term ) {
	
					$icon = get_term_meta($term->term_id, 'icon', true);
	
					$count = ( $params['count'] == 'true' ) ? ' (' . $term->count . ')' : '';
	
					$html .= '<li class="uk-position-relative">';
					if ( !empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
						$html .= '<span class="pakb-list-icon">' . @file_get_contents( PAKB_ICON_DIR . $icon . '.svg' ) . '</span>';
					} elseif ( empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
						$html .= '<span class="pakb-list-icon">' . @file_get_contents( PAKB_ICON_DIR . 'ios-folder.svg' ) . '</span>';
					}
					$html .= '<a href="' . get_term_link( $term ) . '" title="' . sprintf( $term->name ) . '">' . $term->name . $count . '</a></li>';
				}
	
				$html .= '</ul>';

				return $html;
		}
	
	}

	/**
	 * Main page shortcode
	 */
	public function knowledgebase_shortcode( $atts ) {

		global $pakb_loop, $pakb_helper;

		$include = isset( $atts['categories'] ) ? $atts['categories'] : [];

		$pakb_loop->process_kbpage( $include );
		return $pakb_helper->load_file( $pakb_helper->get_template_files( 'knowledgebase' ) );

	}

	/**
	 * Search shortcode
	 */
	public function search_shortcode( $atts ) {

		global $pakb_helper;
		return $pakb_helper->the_search();

	}

	/**
	 * Attachments shortcode
	 */
	public function attachments_shortcode() {

		global $pakb_helper;

		return $pakb_helper->attachments();

	}

	/**
	 * Returns a post object of knowledgebase posts
	 */
	private function get_knowledgebase_posts( $params ) {

		$return = '';

		$args['post_type'] 		= 'knowledgebase';
		$args['post_status'] 	= 'publish';
		$args['orderby'] 		= $params['orderby'];
		$args['order'] 			= $params['order'];
		$args['posts_per_page'] = $params['posts_per_page'];

		if ( $args['orderby'] == 'meta_value_num') {
			$args['meta_key']   = '_votes_likes';
		}


		if ( $params['filter'] == 'category' ) {
			$args['tax_query'] 		= array(
				array(
					'taxonomy'         => 'knowledgebase_category',
					'field'            => 'ID',
					'terms'            => $params['category'],
					'include_children' => true
				)
			);
		} elseif ( $params['filter'] == 'tag' ) {
			$args['tax_query'] 		= array(
				array(
					'taxonomy'         => 'knowledgebase_tags',
					'field'            => 'ID',
					'terms'            => $params['tag'],
					'include_children' => true
				)
			);
		}


		$query = new WP_Query( $args );

		if ( 0 == $query->found_posts ) {

			$return = '<p>There are no knowledge base articles.</p>';

		} else {

			$return = $query;

		}

		return $return;

	}

	/**
	 * Register sidebars
	 */
	public function sidebars_init() {

	    register_sidebar(array(
		    'name'          => __('Knowledge Base Widget Area', 'pressapps'),
		    'id'            => 'pakb-main',
		    'before_widget' => '<div class="widget %1$s %2$s">',
		    'after_widget'  => '</div>',
		    'before_title'  => '<h3>',
		    'after_title'   => '</h3>',
	    ));

	}

	/**
	 * Filters query on the public and attached to pre_get_posts filter
	 *
	 * @since 1.0.0
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	public function pre_get_posts_filter( $query ) {

		if ( ! is_admin() &&
			( $query->is_post_type_archive( 'knowledgebase' )
			  || $query->is_tax( 'knowledgebase_tags' ) || $query->is_tax( 'knowledgebase_category' )
			  || ( $query->is_search() &&
			       ( ( isset( $query->query_vars['post_type'] ) ) ? ( $query->query_vars['post_type'] == 'knowledgebase' ) : false )
			  )
			) && $query->is_main_query()
		) {
			$query->set( 'posts_per_page', - 1 );
			$query->set( 'posts_per_archive_page', - 1 );
		}

		return $query;
	}

	/**
	 * Filters template attached to template_include filter.
	 *
	 * @since 1.0.0
	 * @global WP_Query $wp_query
	 *
	 * @param           $template
	 *
	 * @return mixed
	 */
	public function template_include_filter( $template ) {

		global $wp_query, $pakb_query, $post, $pakb_cat, $pakb_tag, $pakb_loop, $pakb_helper;

		$pakb_query = $wp_query;

		if ( get_option( 'kb_search_query' ) == "true" && ! is_search() ) {
			delete_option( 'kb_search_query' );
		}

		$is_search_query = ( get_option( 'kb_search_query' ) === "true" );

		if ( ( $wp_query->post_count >= 1 && //will check if there are any post to be displayed
			(
				is_post_type_archive( 'knowledgebase' ) || is_singular( 'knowledgebase' )
				|| is_tax( 'knowledgebase_category' ) || is_tax( 'knowledgebase_tags' )
				|| ( is_search() && ( isset( $_REQUEST['post_type'] ) ? ( $_REQUEST['post_type'] == 'knowledgebase' ) : false ) )
			) )
		     || $is_search_query // will override default theme search if it is a kb search
		) {

			if ( ( is_post_type_archive( 'knowledgebase' ) ) || ( is_search() && $is_search_query )  ) {

				if ( is_search() && $is_search_query ) {
					$post = new WP_Post( (object) $pakb_helper->get_dummy_post_data( array(
						'ID'           => isset( $wp_query->post ) ? $wp_query->post->ID : '',
						'post_content' => $pakb_helper->load_file( $pakb_helper->get_template_files( 'search' ) ),
						'post_title'   => sprintf( __( 'Search Result for "%s"', 'pressapps-knowledge-base' ), get_search_query() ),
					) ) );
					//pakb_override_is_var();
				} else {

					$post = new WP_Post( (object) $pakb_helper->get_dummy_post_data( array(
						'ID'           => isset( $wp_query->post->ID ) ? $wp_query->post->ID : '',
						'post_content' => $pakb_helper->load_file( $pakb_helper->get_template_files( 'archive' ) ),
						'post_title'   => __( 'KB Archive', 'pressapps-knowledge-base' ),
					) ) );
				}

				/**
				 * @todo this is redundant Code we need to get this updated with 1 function call like pakb_override_is_var
				 */
				$wp_query->posts      = array( $post );
				$wp_query->post       = $post;
				$wp_query->post_count = 1;

			} elseif ( is_tax( 'knowledgebase_category' ) ) {

				$pakb_loop->process_cat();

				if ( $pakb_loop->has_sub_cat() ) {

					$post = new WP_Post( (object) $pakb_helper->get_dummy_post_data( array(
						'ID'           => $wp_query->post->ID,
						'post_content' => $pakb_helper->load_file( $pakb_helper->get_template_files( 'archive-subcategories' ) ),
						'post_title'   => $pakb_cat['main']->name,
					) ) );

				} else {
					$post = new WP_Post( (object) $pakb_helper->get_dummy_post_data( array(
						'ID'           => $wp_query->post->ID,
						'post_content' => $pakb_helper->load_file( $pakb_helper->get_template_files( 'archive' ) ),
						'post_title'   => $pakb_cat['main']->name,
					) ) );
				}

				$wp_query->posts      = array( $post );
				$wp_query->post       = $post;
				$wp_query->post_count = 1;


			} elseif ( is_tax( 'knowledgebase_tags' ) ) {

				$pakb_loop->process_tag();

				$post = new WP_Post( (object) $pakb_helper->get_dummy_post_data( array(
					'ID'           => $wp_query->post->ID,
					'post_content' => $pakb_helper->load_file( $pakb_helper->get_template_files( 'archive' ) ),
					'post_title'   => $pakb_tag['main']->name,
				) ) );

				$wp_query->posts      = array( $post );
				$wp_query->post       = $post;
				$wp_query->post_count = 1;


			} elseif ( is_post_type_archive( 'knowledgebase' ) && $overridekb ) {

				$pakb_loop->process_kbpage();

					$post = new WP_Post( (object) $pakb_helper->get_dummy_post_data( array(
						'ID'           => $wp_query->post->ID,
						'post_content' => $pakb_helper->load_file( $pakb_helper->get_template_files( 'archive-subcategories' ) ),
						'post_title'   => $wp_query->post->post_title,
					) ) );

				$wp_query->posts      = array( $post );
				$wp_query->post       = $post;
				$wp_query->post_count = 1;

			} elseif ( is_singular( 'knowledgebase' ) ) {

				$post = new WP_Post( (object) $pakb_helper->get_dummy_post_data( array(
					'ID'           => $wp_query->post->ID,
					'post_content' => $pakb_helper->load_file( $pakb_helper->get_template_files( 'single' ) ),
					'post_title'   => $wp_query->post->post_title,
				) ) );

				$wp_query->posts      = array( $post );
				$wp_query->post       = $post;
				$wp_query->post_count = 1;
			}

			return $pakb_helper->page_template( $template );
		}


		return $template;
	}

	/**
	 * Attached to public pre_get_posts
	 *
	 * @param $query
	 */
	public function public_pre_get_posts( $query ) {

		switch ( get_option( 'options_pakb_content_order' ) ) {
			case 'default':
				$orderby = 'date';
				$order = 'DESC';
				break;
			case 'draganddrop':
				$orderby = 'menu_order';
				$order = 'ASC';
				break;
			case 'alphabetical':
				$orderby = 'title';
				$order = 'ASC';
				break;
			default:
				$orderby = 'date';
				$order = 'DESC';
				break;
		}


		if ( ! is_admin() &&
		     ( $query->is_post_type_archive( 'knowledgebase' )
		       || $query->is_tax( 'knowledgebase_category' ) || $query->is_tax( 'knowledgebase_tags' )
		       || ( $query->is_search() && ( isset( $_REQUEST['post_type'] ) ? ( $_REQUEST['post_type'] == 'knowledgebase' ) : false ) )
		     ) && $query->is_main_query()
		) {
			$query->set( 'orderby', $orderby );
			$query->set( 'order', $order );
		}

	}

	/**
	 * Ajax live search function attached to wp_ajax_search_title & wp_ajax_nopriv_search_title.
	 *
	 * @since 1.0.0
	 * @global WPDB $wpdb
	 */
	public function live_search() {
		global $wpdb, $pakb_helper;

		$search_input = $_REQUEST['query'];

		$live_search = !is_null( get_field( 'pakb_search_live', 'option' ) ) ? get_field( 'pakb_search_live', 'option' ) : true;
		$search_category = !is_null( get_field( 'pakb_search_category', 'option' ) ) ? get_field( 'pakb_search_category', 'option' ) : true;

		if ( !$live_search ) {
			return;
		}

		if ( term_exists( $search_input, 'knowledgebase_tags' ) ) {
			$term_check = term_exists( $search_input, 'knowledgebase_tags' );
		} else {
			$term_check = false;
		}

			// $search_qry = intval( $term_check['term_id'] );
			//
			// $qry = " SELECT pa.ID, pa.post_title, pa.post_type, pa.post_content as post_content, pa.post_name ";
			// $qry .= " FROM {$wpdb->posts} AS pa INNER JOIN {$wpdb->term_relationships} AS pt ";
			// $qry .= " ON pa.ID = pt.object_id AND pt.term_taxonomy_id = %s ";
			//
			// $sql_qry = $wpdb->prepare( $qry, $search_qry );

		$search_qry   = "%" . $search_input . "%";

		$qry = ' SELECT ID, post_title, post_type, post_content as post_content, post_name ';
		$qry .= " FROM {$wpdb->posts} WHERE post_status = %s ";
		$qry .= " AND post_type = 'knowledgebase'  ";
		if ( get_option( 'options_pakb_search_in' ) === 'title' ) {
			$qry .= " AND (post_title like %s) ";
		} else {
			$qry .= " AND (post_title like %s or post_content like %s) ";
		}

		$sql_qry = $wpdb->prepare( $qry, 'publish', $search_qry, $search_qry );


		$search_json = array(
			"query"       => "Unit",
			"suggestions" => array(),
		);

		if ( $wpdb->get_results( $sql_qry ) ) {

			$results = $wpdb->get_results( $sql_qry );
			foreach ( $results as $result ) {

				if ( $search_category ) {
					$query_cats = wp_get_post_terms( $result->ID, 'knowledgebase_category' );
					$cat_output = array();

					//will display category
					foreach ( $query_cats as $query_cat ) {
						if ( $query_cat->parent ) {
							$cat_parent = get_term( $query_cat->parent, 'knowledgebase_category' );
							$cat_output[] = $cat_parent->name . ' <span>/</span> ' . $query_cat->name;
						} else {
							$cat_output[] = $query_cat->name;
						}
					}

					$search_json["suggestions"][] = array(
						"value" => $result->post_title,
						"data" => array( 'category' => implode( ' ', $cat_output ) ),
						"url"   => get_permalink( $result->ID ),
						"post_id"  => $result->ID
					);
				} else {
					$search_json["suggestions"][] = array(
						"value" => $result->post_title,
						"url"   => get_permalink( $result->ID ),
						"post_id"  => $result->ID
					);
				}

			}
		}



		echo json_encode( $search_json );
		die();
	}

	/**
	 * Voting function pass to init hook
	 *
	 * @since 1.0.0
	 */
	public function voting_init() {
		global $post, $pakb_helper;

		if ( is_user_logged_in() ) {

			$vote_count = (array) get_user_meta( get_current_user_id(), 'vote_count', true );

			if ( isset( $_GET['pakb_vote_like'] ) && $_GET['pakb_vote_like'] > 0 ) :

				$post_id  = (int) $_GET['pakb_vote_like'];
				$the_post = get_post( $post_id );

				if ( $the_post && ! in_array( $post_id, $vote_count ) ) :
					$vote_count[] = $post_id;
					update_user_meta( get_current_user_id(), 'vote_count', $vote_count );
					$post_votes = (int) get_post_meta( $post_id, '_votes_likes', true );
					$post_votes ++;
					update_post_meta( $post_id, '_votes_likes', $post_votes );
					$post = get_post( $post_id );
					$pakb_helper->the_votes( true );
					die( '' );
				endif;

			elseif ( isset( $_GET['pakb_vote_dislike'] ) && $_GET['pakb_vote_dislike'] > 0 ) :

				$post_id  = (int) $_GET['pakb_vote_dislike'];
				$the_post = get_post( $post_id );

				if ( $the_post && ! in_array( $post_id, $vote_count ) ) :
					$vote_count[] = $post_id;
					update_user_meta( get_current_user_id(), 'vote_count', $vote_count );
					$post_votes = (int) get_post_meta( $post_id, '_votes_dislikes', true );
					$post_votes ++;
					update_post_meta( $post_id, '_votes_dislikes', $post_votes );
					$post = get_post( $post_id );
					$pakb_helper->the_votes( true );
					die( '' );

				endif;

			endif;

		} elseif ( ! is_user_logged_in() && get_option( 'options_pakb_vote' ) == 1 ) {

			// ADD VOTING FOR NON LOGGED IN USERS USING COOKIE TO STOP REPEAT VOTING ON AN ARTICLE
			$vote_count = '';

			if ( isset( $_COOKIE['vote_count'] ) ) {
				$vote_count = @unserialize( base64_decode( $_COOKIE['vote_count'] ) );
			}

			if ( ! is_array( $vote_count ) && isset( $vote_count ) ) {
				$vote_count = array();
			}

			if ( isset( $_GET['pakb_vote_like'] ) && $_GET['pakb_vote_like'] > 0 ) :

				$post_id  = (int) $_GET['pakb_vote_like'];
				$the_post = get_post( $post_id );

				if ( $the_post && ! in_array( $post_id, $vote_count ) ) :
					$vote_count[]          = $post_id;
					$_COOKIE['vote_count'] = base64_encode( serialize( $vote_count ) );
					setcookie( 'vote_count', $_COOKIE['vote_count'], time() + ( 10 * 365 * 24 * 60 * 60 ), '/' );
					$post_votes = (int) get_post_meta( $post_id, '_votes_likes', true );
					$post_votes ++;
					update_post_meta( $post_id, '_votes_likes', $post_votes );
					$post = get_post( $post_id );
					$pakb_helper->the_votes( true );
					die( '' );
				endif;

			elseif ( isset( $_GET['pakb_vote_dislike'] ) && $_GET['pakb_vote_dislike'] > 0 ) :

				$post_id  = (int) $_GET['pakb_vote_dislike'];
				$the_post = get_post( $post_id );

				if ( $the_post && ! in_array( $post_id, $vote_count ) ) :
					$vote_count[]          = $post_id;
					$_COOKIE['vote_count'] = base64_encode( serialize( $vote_count ) );
					setcookie( 'vote_count', $_COOKIE['vote_count'], time() + ( 10 * 365 * 24 * 60 * 60 ), '/' );
					$post_votes = (int) get_post_meta( $post_id, '_votes_dislikes', true );
					$post_votes ++;
					update_post_meta( $post_id, '_votes_dislikes', $post_votes );
					$post = get_post( $post_id );
					$pakb_helper->the_votes( true );
					die( '' );

				endif;

			endif;

		} elseif ( ! is_user_logged_in() && get_option( 'options_pakb_vote' ) == 2 ) {

			return;

		}

	}

	/**
	 * Custom CSS option for the plugin.
	 *
	 * @since 1.0.0
	 * @return string css style
	 */
	public function custom_css() {

		$custom_css = '';
		// Custom CSS
		if ( get_option( 'options_pakb_custom_css' ) ) {
			$custom_css .= get_option( 'options_pakb_custom_css' );
		}

		$theme = !is_null( get_field( 'pakb_theme', 'option' ) ) ? get_field( 'pakb_theme', 'option' ) : 'origin';

		if ($theme == 'origin' ) {
		// Origin primary color
			$origin_primary = get_option( 'options_pakb_origin_primary' ) ? sanitize_text_field( get_option( 'options_pakb_origin_primary' ) ) : '#3384FF';
			if ( $origin_primary ) {
				$custom_css .= '.uk-card-body:hover, .uk-search-default .uk-search-input:focus { border-color: ' . $origin_primary . " }\n";
				$custom_css .= '.pakb-toc { border-left-color: ' . $origin_primary . " }\n";
				$custom_css .= '.pakb-accent-color, .pakb-accent-color a, .pakb-accent-color a:hover, .pakb-accent-color a:visited, .autocomplete-suggestion, .uk-card-body:hover .uk-card-title.pakb-secondary-color { color: ' . $origin_primary . " }\n";
				$custom_css .= '.uk-button-primary, .uk-button-primary:focus, .styled-ol li:before { background-color: ' . $origin_primary . " }\n";
				$custom_css .= '.pakb-list-icon svg, .pakb-box-icon svg, .pakb-like-btn svg, .pakb-dislike-btn svg { fill:  ' . $origin_primary . " !important }\n";
				// $custom_css .= '.uk-search .uk-search-icon { color:  ' . $origin_primary . " }\n";
			}

			// Origin secondary color
			$origin_secondary = get_option( 'options_pakb_origin_secondary' ) ? sanitize_text_field( get_option( 'options_pakb_origin_secondary' ) ) : '#222222';
			if ( $origin_secondary ) {
				$custom_css .= '.pakb-secondary-color, .pakb-secondary-color a, .pakb-secondary-color a:hover, .pakb-secondary-color a:visited { color: ' . $origin_secondary . " }\n";
			}
			
			// Origin muted color
			$origin_muted = get_option( 'options_pakb_origin_muted' ) ? sanitize_text_field( get_option( 'options_pakb_origin_muted' ) ) : '#AEB4BB';
			if ( $origin_muted ) {
				$custom_css .= '.pakb-muted-color, .pakb-muted-color a, .pakb-muted-color a:hover, .pakb-muted-color a:visited, .uk-breadcrumb :last-child * { color: ' . $origin_muted . " }\n";
			}

			// Origin category font size
			$category_font_size = !is_null( get_field( 'pakb_origin_category_font_size', 'option' ) ) ? get_field( 'pakb_origin_category_font_size', 'option' ) : '24';
			if ( $category_font_size ) {
				$custom_css .= '.pakb-boxes .uk-card-title, .pakb-lists h2 { font-size: ' . sanitize_text_field( $category_font_size ) . "px;}\n";
			}

			// Origin box layout category icon size
			$box_icon_size = !is_null( get_field( 'pakb_origin_box_icon_size', 'option' ) ) ? get_field( 'pakb_origin_box_icon_size', 'option' ) : '54';
			if ( $box_icon_size ) {
				$custom_css .= '.pakb-boxes svg { height: ' . sanitize_text_field( $box_icon_size ) . "px;}\n";
			}

		}

		//sanitize on output
		return $custom_css;
	}

	/**
	 * Filters the title of the post based on condition.
	 *
	 * @since 1.0.0
	 *
	 * @param      $title
	 * @param null $id
	 *
	 * @return string Title
	 */
	public function the_title_filter( $title, $id = null ) {

		return $title;

	}

	public function search_post_query(  ) {

		if ( isset( $_POST['post_type'] ) && isset( $_POST['s'] ) && $_POST['search_nonce'] ) {
			wp_verify_nonce( $_POST['post_type'].'-search' );
			update_option( 'kb_search_query', 'true' );
		}
	}
}
