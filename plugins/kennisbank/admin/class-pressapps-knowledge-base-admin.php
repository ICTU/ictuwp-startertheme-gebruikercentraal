<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://pressapps.co
 * @since      1.0.0
 *
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/admin
 * @author     PressApps
 */
class Pressapps_Knowledge_Base_Admin {

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
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$current_screen = get_current_screen();

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pressapps-knowledge-base-admin.css', array(), $this->version, 'all' );

		if ( ( $current_screen->post_type === 'knowledgebase' && $current_screen->taxonomy !== 'knowledgebase_tags' ) && get_option( 'options_pakb_content_order' ) === 'draganddrop' ) {

			$reorder_css = 'root { display: block; } #the-list tr {cursor: move;} .fixed .column-name, .fixed .column-description { width: 25% }';

			wp_add_inline_style( $this->plugin_name, $reorder_css );

		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();

		if ( $current_screen->post_type === 'knowledgebase' || $current_screen->base === 'knowledge-base_page_pakb-options-voting' ) {

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pressapps-knowledge-base-admin.js', array(
				'jquery',
				'jquery-ui-sortable'
			), $this->version, false );

			//will pass value for reorder over the admin page for checking
			wp_localize_script( $this->plugin_name, 'PAKB_admin',
				array(
					'reorder'                => ( get_option( 'options_pakb_content_order' ) === 'draganddrop' ),
					'reset_confirmation'     => __( 'Are you sure you want to reset votes for this article?', 'pressapps-knowledge-base' ),
					'reset_all_confirmation' => __( 'Are you sure you want to reset votes for all articles?', 'pressapps-knowledge-base' ),
					'reset_success'          => __( 'Success! Votes are now reset!', 'pressapps-knowledge-base' )
				)
			);

		}
	}

	/**
	 * Initiate CPT and Taxonomy for the plugin and attached to the init hook.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt_tax() {
		$kb_slug 	= get_option( 'options_pakb_category_slug' ) ? get_option( 'options_pakb_article_slug' ) : 'knowledgebase';
		$kbcat_slug = get_option( 'options_pakb_category_slug' ) ? get_option( 'options_pakb_category_slug' ) : 'kb';

		// Knowledgebase Post Type
		register_post_type( 'knowledgebase', array(
			'description'         => __( 'Knowledge Base', 'pressapps-knowledge-base' ),
			'labels'              => array(
				'name'               => __( 'Knowledge Base', 'pressapps-knowledge-base' ),
				'singular_name'      => __( 'Article', 'pressapps-knowledge-base' ),
				'add_new'            => __( 'Add New', 'pressapps-knowledge-base' ),
				'add_new_item'       => __( 'Add New Article', 'pressapps-knowledge-base' ),
				'edit_item'          => __( 'Edit Article', 'pressapps-knowledge-base' ),
				'new_item'           => __( 'New Article', 'pressapps-knowledge-base' ),
				'view_item'          => __( 'View Article', 'pressapps-knowledge-base' ),
				'search_items'       => __( 'Search Articles', 'pressapps-knowledge-base' ),
				'not_found'          => __( 'No Articles found', 'pressapps-knowledge-base' ),
				'not_found_in_trash' => __( 'No Articles found in Trash', 'pressapps-knowledge-base' ),
				'all_items'          => __( 'All Articles', 'pressapps-knowledge-base' ),
			),
			'menu_position'       => 5,
			'menu_icon'						=> 'dashicons-format-aside',
			'rewrite'             => array(
				'slug'       => $kb_slug,
				'with_front' => false,
			),
			'supports'            => array(
				'title',
				'editor',
				'author',
				'comments',
				'page-attributes',
				'thumbnail',
				'wpcom-markdown',
				'revisions'
			),
			'public'              => true,
			'show_ui'             => true,
			'publicly_queryable'  => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'show_in_rest'				=> ( get_option( 'options_pakb_block_editor' ) ? true : false ),
		) );

		// Knowledgebase Category Taxonomy
		register_taxonomy( 'knowledgebase_category', array( 'knowledgebase' ), array(
			'labels'       => array(
				'name'              => __( 'Categories', 'pressapps-knowledge-base' ),
				'singular_name'     => __( 'Category', 'pressapps-knowledge-base' ),
				'search_items'      => __( 'Search Categories', 'pressapps-knowledge-base' ),
				'all_items'         => __( 'All Categories', 'pressapps-knowledge-base' ),
				'parent_item'       => __( 'Parent Category', 'pressapps-knowledge-base' ),
				'parent_item_colon' => __( 'Parent Category:', 'pressapps-knowledge-base' ),
				'edit_item'         => __( 'Edit Category', 'pressapps-knowledge-base' ),
				'update_item'       => __( 'Update Category', 'pressapps-knowledge-base' ),
				'add_new_item'      => __( 'Add New Category', 'pressapps-knowledge-base' ),
				'new_item_name'     => __( 'New Category Name', 'pressapps-knowledge-base' ),
				'popular_items'     => null,
				'menu_name'         => __( 'Categories', 'pressapps-knowledge-base' )
			),
			'show_ui'      => true,
			'public'       => true,
			'query_var'    => true,
			'hierarchical' => true,
			'show_in_rest'				=> ( get_option( 'options_pakb_block_editor' ) ? true : false ),
			'rewrite'      => array(
				'slug' => $kbcat_slug,
				'with_front' => false
			)
		) );

		// Knowledgebase Tags Taxonomy
		register_taxonomy( 'knowledgebase_tags', array( 'knowledgebase' ), array(
			'labels'       => array(
				'name'              => __( 'Tags', 'pressapps-knowledge-base' ),
				'singular_name'     => __( 'Tag', 'pressapps-knowledge-base' ),
				'search_items'      => __( 'Search Tags', 'pressapps-knowledge-base' ),
				'all_items'         => __( 'All Tags', 'pressapps-knowledge-base' ),
				'parent_item'       => __( 'Parent Tag', 'pressapps-knowledge-base' ),
				'parent_item_colon' => __( 'Parent Tag:', 'pressapps-knowledge-base' ),
				'edit_item'         => __( 'Edit Tag', 'pressapps-knowledge-base' ),
				'update_item'       => __( 'Update Tag', 'pressapps-knowledge-base' ),
				'add_new_item'      => __( 'Add New Tag', 'pressapps-knowledge-base' ),
				'new_item_name'     => __( 'New Tag Name', 'pressapps-knowledge-base' ),
				'popular_items'     => null,
				'menu_name'         => __( 'Tags', 'pressapps-knowledge-base' )
			),
			'show_ui'      => true,
			'public'       => true,
			'query_var'    => true,
			'hierarchical' => false,
			'show_in_rest'				=> ( get_option( 'options_pakb_block_editor' ) ? true : false ),
			'rewrite'      => array( 'slug' => 'knowledgebase_tags' )
		) );

		if ( get_option( 'PAKB_FLUSH_REWRITE_RULE' ) ) {
			flush_rewrite_rules();
			update_option( 'PAKB_FLUSH_REWRITE_RULE', false );
		}
	}

	/**
	 * Register knowledge base block category
	 */
	public function add_block_category( $categories, $post ) {
	    return array_merge(
	        $categories,
	        array(
	            array(
	                'slug' => 'pakb',
	                'title' => __( 'Knowledge Base', 'pressapps-knowledge-base' ),
	                'icon'  => 'dashicons-screenoptions',
	            ),
	        )
	    );
	}

	/**
	 * Register blocks
	 */
	public function acf_register_blocks() {

	    if( function_exists('acf_register_block') ) {

					acf_register_block(array(
	            'name'              => 'pakb-knowledgebase',
	            'title'             => __('Knowledge Base', 'pressapps-knowledge-base'),
	            'description'       => __('Display main knowledge base content.', 'pressapps-knowledge-base'),
	            'render_callback'   => array($this, 'acf_block_render_callback'),
	            'category'          => 'pakb',
	            'icon'              => 'screenoptions',
	            'mode'              => 'preview',
	            'keywords'          => array( 'knowledgebase', 'categories' ),
	        ));

					acf_register_block(array(
	            'name'              => 'pakb-search',
	            'title'             => __('Search', 'pressapps-knowledge-base'),
	            'description'       => __('Live search for knowledge base plugin.', 'pressapps-knowledge-base'),
	            'render_callback'   => array($this, 'acf_block_render_callback'),
	            'category'          => 'pakb',
	            'icon'              => 'search',
	            'mode'              => 'preview',
	            'keywords'          => array( 'search' ),
	        ));

					acf_register_block(array(
	            'name'              => 'pakb-widget-area',
	            'title'             => __('Widget Area', 'pressapps-knowledge-base'),
	            'description'       => __('Display horizontal knowledge base widget area.', 'pressapps-knowledge-base'),
	            'render_callback'   => array($this, 'acf_block_render_callback'),
	            'category'          => 'pakb',
	            'icon'              => 'welcome-widgets-menus',
	            'mode'              => 'preview',
	            'keywords'          => array( 'widget', 'sidebar' ),
	        ));

					acf_register_block(array(
	            'name'              => 'pakb-attachments',
	            'title'             => __('Attachments', 'pressapps-knowledge-base'),
	            'description'       => __('Display article file attachments.', 'pressapps-knowledge-base'),
	            'render_callback'   => array($this, 'acf_block_render_callback'),
							'post_types'				=> array('knowledgebase'),
	            'category'          => 'pakb',
	            'icon'              => 'paperclip',
	            'mode'              => 'preview',
	            'keywords'          => array( 'attachments', 'files' ),
	        ));

	    }
	}

	/**
	 * Render blocks
	 */
	 public function acf_block_render_callback( $block ) {

		// convert name ("acf/block_name") into path friendly slug ("block_name")
		$slug = str_replace('acf/pakb-', '', $block['name']);

		// include a template part from within the "public/partials/block/" folder
		if( file_exists( PAKB_PLUGIN_DIR . "public/partials/block/block-{$slug}.php" ) ) {
			include( PAKB_PLUGIN_DIR . "public/partials/block/block-{$slug}.php" );
		}
	}

	/**
	 * Adds a link to the plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function settings_link( $links ) {

		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=pakb-options' ), __( 'Settings', 'pressapps-knowledge-base' ) );

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Adds links to the plugin links row
	 *
	 * @since    1.0.0
	 */
	public function row_links( $links, $file ) {

		if ( strpos( $file, $this->plugin_name . '.php' ) !== false ) {

			$link = '<a href="https://docs.unbound.studio/knowledgebase-wordpress-plugin/" target="_blank">' . __( 'Help', 'pressapps-knowledge-base' ) . '</a>';

			array_push( $links, $link );

		}

		return $links;

	}

	/**
	 * Initiate functions for the admin page.
	 *
	 * @since    1.0.0
	 */
	public function admin_init_action() {
		global $pagenow;

		if ( isset( $_GET['page'] ) && isset( $_GET['settings-updated'] ) ) {
			if ( $_GET['page'] == 'knowledgebase-options' && $_GET['settings-updated'] == 'true' ) {
				flush_rewrite_rules( true );
			}
		}

		$reorder = ( get_option( 'options_pakb_content_order' ) === 'draganddrop' );
		if ( ( ! $reorder ) ) {
			return;
		}

		if ( $pagenow == 'edit.php' ) {
			if ( isset( $_GET['post_type'] ) && 'knowledgebase' == $_GET['post_type'] ) {

				add_filter( 'pre_get_posts', array( $this, 'order_reorder_list' ) );
			}
		} elseif ( $pagenow == 'edit-tags.php' ) {
			if ( isset( $_GET['post_type'] ) && 'knowledgebase' == $_GET['post_type'] ) {

				add_filter( 'get_terms_orderby', array( $this, 'order_reorder_taxonomies_list' ), 10, 2 );
			}
		}
	}

	/**
	 * Attached to the pre_get_posts hook for the admin.
	 *
	 * @since    1.0.0
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	public function order_reorder_list( $query ) {
		$reorder = ( ( get_option( 'options_pakb_content_order' ) == 'draganddrop' ) ? true : false );
		if ( isset( $query->query_vars['post_type'] ) ) {
			if ( $reorder && is_admin() && $query->query_vars['post_type'] === 'knowledgebase' ) {
				$query->set( 'orderby', 'menu_order' );
				$query->set( 'order', 'ASC' );

				return $query;
			}
		}
	}

	/**
	 * Filter hook for ordering taxonomy list.
	 *
	 * @since    1.0.0
	 *
	 * @param $orderby
	 * @param $args
	 *
	 * @return string
	 */
	public function order_reorder_taxonomies_list( $orderby, $args ) {
		$orderby = "t.term_group";

		return $orderby;
	}

	/**
	 * Custom column content for the Knowledgebase CPT.
	 *
	 * @since    1.0.0
	 *
	 * @param $column
	 */
	public function manage_custom_knowledgebase_posts_column_action( $column, $post_id ) {

		switch ( $column ) {
			case 'category':
				$terms = wp_get_object_terms( $post_id, 'knowledgebase_category' );
				foreach ( $terms as $term ) {
					$temp = " <a href=\"" . admin_url( 'edit-tags.php?action=edit&taxonomy=knowledgebase_category&tag_ID=' . $term->term_id . '&post_type=knowledgebase' ) . "\" ";
					$temp .= " class=\"row-title\">{$term->name}</a><br/>";
					echo $temp;
				}
				break;
			case 'likes':
				echo '<p>' . intval( get_post_meta( $post_id, '_votes_likes', true ) ) . '</p>';
				break;
			case 'dislikes':
				echo '<p>' . intval( get_post_meta( $post_id, '_votes_dislikes', true ) ) . '</p>';
				break;
			case 'reset':
				echo '<a href="#" class="pakb-reset-vote button" data-reset-nonce="' . wp_create_nonce( 'reset_vote_' . $post_id ) . '" data-post-id="' . esc_attr( $post_id ) . '">' . __( 'Reset', 'pressapps-knowledge-base' ) . '</a>';
				break;
		}
	}

	/**
	 * Attached to the restrict_manage_posts hook for the admin.
	 *
	 * @since    1.0.0
	 */
	public function restrict_manage_posts_action() {
		global $typenow;

		if ( $typenow == 'knowledgebase' ) {
			?>
			<select name="knowledgebase_category">
				<option value="0"><?php _e( 'View all categories' ); ?></option>
				<?php
				$categories = get_terms( 'knowledgebase_category' );
				if ( count( $categories ) > 0 ) {
					foreach ( $categories as $cat ) {
						if ( isset( $_GET['knowledgebase_category'] ) && $_GET['knowledgebase_category'] == $cat->slug ) {
							echo "<option value={$cat->slug} selected=\"selected\">{$cat->name}</option>";
						} else {
							echo "<option value={$cat->slug} >{$cat->name}</option>";
						}
					}
				}
				?>
			</select>
			<?php
		}

	}

	/**
	 * Attached to the pre_get_posts hook for the admin.
	 *
	 * @since    1.0.0
	 *
	 * @param $query
	 */
	public function pre_get_posts_action( $query ) {
		// if ( is_object( $pakb ) ) {
		$reorder = get_option( 'options_pakb_content_order' ) === 'draganddrop' ? true : false;

		if ( ! $reorder ) {
			return;
		}
		if ( isset( $query->query_vars['post_type'] ) ) {
			if ( ( is_admin() && $query->query_vars['post_type'] === 'knowledgebase' ) &&
			     ( $query->is_post_type_archive( 'knowledgebase' )
			       || $query->is_tax( 'knowledgebase_category' ) || $query->is_tax( 'knowledgebase_tags' )
			       || ( $query->is_search() && ( isset( $_REQUEST['post_type'] ) ? ( $_REQUEST['post_type'] == 'knowledgebase' ) : false ) )
			     ) && $query->is_main_query()
			) {
				$query->set( 'orderby', 'menu_order' );
				$query->set( 'order', 'ASC' );
			}
		}
		// }

	}

	/**
	 * Ajax request for saving the order on the admin page.
	 *
	 * @since    1.0.0
	 */
	public function order_save_order() {

		global $wpdb;

		$action          = $_POST['action'];
		$posts_array     = $_POST['post'];
		$listing_counter = 1;
		foreach ( $posts_array as $post_id ) {

			$wpdb->update(
				$wpdb->posts,
				array( 'menu_order' => $listing_counter ),
				array( 'ID' => $post_id )
			);

			$listing_counter ++;
		}

		die();
	}

	/**
	 * Save taxonomies order on the admin page.
	 *
	 * @since    1.0.0
	 */
	public function order_save_taxonomies_order() {
		global $wpdb;

		$action          = $_POST['action'];
		$tags_array      = $_POST['tag'];
		$listing_counter = 1;

		foreach ( $tags_array as $tag_id ) {

			$wpdb->update(
				$wpdb->terms,
				array( 'term_group' => $listing_counter ),
				array( 'term_id' => $tag_id )
			);

			$listing_counter ++;
		}

		die();
	}

	/**
	 * Columns for the Knowledgebase CPT on the admin page.
	 *
	 * @since    1.0.0
	 *
	 * @param $columns
	 *
	 * @return array $columns
	 */
	public function manage_knowledgebase_posts_columns_filter( $columns ) {

		$new_columns['cb']       = $columns['cb'];
		$new_columns['title']    = __( 'Title', 'pressapps-knowledge-base' );
		$new_columns['category'] = __( 'Category', 'pressapps-knowledge-base' );
		$new_columns['date']     = $columns['date'];

		//check if voting is enabled and will add addition column on admin page
		if ( get_option( 'options_pakb_vote' ) >= 1 ) {
			$new_columns['likes']    = __( 'Likes', 'pressapps-knowledge-base' );
			$new_columns['dislikes'] = __( 'Dislikes', 'pressapps-knowledge-base' );
			$new_columns['reset']    = __( 'Reset', 'pressapps-knowledge-base' );
		}


		return $new_columns;
	}

	/**
	 * Columns for the Knowledgebase category in the admin page.
	 */
	function manage_knowledgebase_category_columns_filter( $columns ) {

		global $pakb_helper;

		if ( !$pakb_helper->is_category_icon_enabled() ) {
			return $columns;
		}

		$columns['icon'] = __('Icon');

		return $columns;
	}

	function manage_custom_knowledgebase_category_column_action( $content, $column_name, $term_id ) {

		global $pakb_helper;
		
		if ( !$pakb_helper->is_category_icon_enabled() ) {
			return $content;
		}

    if ( 'icon' == $column_name ) {
			$icon = get_term_meta($term_id, 'icon', true);
			if ( !empty( $icon ) ) {
				$content = @file_get_contents( PAKB_ICON_DIR . $icon . '.svg' );
			} else {
				$content = @file_get_contents( PAKB_ICON_DIR . 'folder.svg' );
			}
    }
		return $content;
	}
	/**
	 * Reset all votes on the knowledgebase CPT
	 */
	public function reset_vote_all_admin() {

		if ( is_user_logged_in() ) {
			delete_user_meta( get_current_user_id(), 'vote_count' );
		} elseif ( isset( $_COOKIE['vote_count'] ) ) {
			setcookie( 'vote_count', '', time() - 3600, '/' );
		}

		//get all knowledgebase CPT based on IDs
		$args = array(
			'post_type'      => 'knowledgebase',
			'posts_per_page' => - 1,
			'fields'         => 'ids'
		);

		$vote_queries  = get_posts( $args );
		$total_queries = count( $vote_queries );
		$vote          = 1;

		foreach ( $vote_queries as $post_id ) {
			if ( $vote >= $total_queries ) {
				echo json_encode( array( 'success' => 'true' ) );
			}
			//remove all meta that is attached to post
			delete_post_meta( $post_id, '_votes_likes' );
			delete_post_meta( $post_id, '_votes_dislikes' );
			$vote ++;
		}
		die;
	}

	/**
	 * Ajax Call for resetting votes in admin page
	 */
	public function reset_vote_admin() {
		$post_id = intval( $_REQUEST['post_id'] );

		//will check if nonce was sent was valid
		check_ajax_referer( 'reset_vote_' . $post_id, 'reset_nonce' );

		$post_likes_update    = update_post_meta( $post_id, '_votes_likes', 0 );
		$post_dislikes_update = update_post_meta( $post_id, '_votes_dislikes', 0 );

		$cookie_vote_count = '';
		if ( isset( $_COOKIE['vote_count'] ) ) {
			$cookie_vote_count = @unserialize( base64_decode( $_COOKIE['vote_count'] ) );
		}

		//will check if user is logged - for voting that was set for loggedin users
		if ( is_user_logged_in() ) {
			$vote_count = (array) get_user_meta( get_current_user_id(), 'vote_count', true );
		} else {
			$vote_count = $cookie_vote_count;
		}

		//will look for the post if it exist on vote_count either through $_COOKIE or get_user_meta()
		$post_vote_key = array_search( $post_id, $vote_count );

		if ( $post_vote_key ) {
			//if we are able to find the key and will remove it
			unset( $vote_count[ $post_vote_key ] );

			if ( is_user_logged_in() ) {
				update_user_meta( get_current_user_id(), 'vote_count', $vote_count );
			} elseif ( isset( $_COOKIE['vote_count'] ) ) {
				setcookie( 'vote_count', $vote_count, time() + ( 10 * 365 * 24 * 60 * 60 ), '/' );
			}
		}

		echo json_encode( compact( 'post_likes_update', 'post_dislikes_update', 'post_id' ) );
		die;
	}

	/**
	 * Assigned orderby value for sortable column of likes and dislikes under Knowledgebase CPT
	 * attached to manage_edit-knowledgebase_sortable_columns filter hook
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function kb_votes_sortable( $columns ) {
		$columns['likes']    = 'likes';
		$columns['dislikes'] = 'dislikes';

		return $columns;
	}

	/**
	 * Custom query to manipulate orderby sorting based on meta_value for likes and dislikes
	 */
	public function kb_votes_orderby( $query ) {

		if ( is_admin() ){
			$orderby = $query->get( 'orderby' );
			if ( $orderby == 'likes' ) {
				$query->set( 'meta_key', '_votes_likes' );
				$query->set( 'orderby', 'meta_value_num' );
			} elseif ( $orderby == 'dislikes' ) {
				$query->set( 'meta_key', '_votes_dislikes' );
				$query->set( 'orderby', 'meta_value_num' );
			}
		}
	}
}
