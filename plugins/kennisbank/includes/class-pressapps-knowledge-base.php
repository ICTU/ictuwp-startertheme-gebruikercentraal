<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://pressapps.co
 * @since      1.0.0
 *
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pressapps_Knowledge_Base
 * @subpackage Pressapps_Knowledge_Base/includes
 * @author     PressApps
 */
class Pressapps_Knowledge_Base {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pressapps_Knowledge_Base_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'pressapps-knowledge-base';
		$this->version     = '2.4.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pressapps_Knowledge_Base_Loader. Orchestrates the hooks of the plugin.
	 * - Pressapps_Knowledge_Base_i18n. Defines internationalization functionality.
	 * - Pressapps_Knowledge_Base_Admin. Defines all hooks for the admin area.
	 * - Pressapps_Knowledge_Base_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pressapps-knowledge-base-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pressapps-knowledge-base-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pressapps-knowledge-base-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pressapps-knowledge-base-public.php';

		/**
		 * Loads widget for the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/class-pressapps-knowledge-base-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/class-pressapps-knowledge-base-loop.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/pressapps-knowledge-base-functions.php';

		/**
		 * Loads widgets for the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pressapps-knowledge-base-widgets.php';

		$this->loader = new Pressapps_Knowledge_Base_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pressapps_Knowledge_Base_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pressapps_Knowledge_Base_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Pressapps_Knowledge_Base_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'plugin_row_meta', $plugin_admin, 'row_links', 10, 2 );
		$this->loader->add_action( 'plugin_action_links_' . $this->get_plugin_name() . "/" . $this->get_plugin_name() . ".php", $plugin_admin, 'settings_link' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init_action' );
		$this->loader->add_action( 'manage_knowledgebase_posts_custom_column', $plugin_admin, 'manage_custom_knowledgebase_posts_column_action', 10, 2 );
		$this->loader->add_action( 'manage_knowledgebase_category_custom_column', $plugin_admin, 'manage_custom_knowledgebase_category_column_action', 10, 3 );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'restrict_manage_posts_action' );
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'pre_get_posts_action' );
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin, 'order_reorder_list' );

		// Register CPT and Tax
		$this->loader->add_filter( 'init', $plugin_admin, 'register_cpt_tax' );

		$this->loader->add_action( 'acf/init', $plugin_admin, 'acf_register_blocks' );
		$this->loader->add_filter( 'block_categories', $plugin_admin, 'add_block_category', 10, 2 );

		// Ajax for ordering post
		$this->loader->add_action( 'wp_ajax_pakb_order_update_posts', $plugin_admin, 'order_save_order' );
		$this->loader->add_action( 'wp_ajax_nopriv_pakb_order_update_posts', $plugin_admin, 'order_save_order' );

		$this->loader->add_action( 'wp_ajax_pakb_order_update_taxonomies', $plugin_admin, 'order_save_taxonomies_order' );
		$this->loader->add_action( 'wp_ajax_nopriv_pakb_order_update_taxonomies', $plugin_admin, 'order_save_taxonomies_order' );

		$this->loader->add_action( 'wp_ajax_pakb_reset_vote_admin', $plugin_admin, 'reset_vote_admin' );
		$this->loader->add_action( 'wp_ajax_nopriv_pakb_reset_vote_admin', $plugin_admin, 'reset_vote_admin' );

		$this->loader->add_action( 'wp_ajax_pakb_reset_vote_all_admin', $plugin_admin, 'reset_vote_all_admin' );
		$this->loader->add_action( 'wp_ajax_nopriv_pakb_reset_vote_all_admin', $plugin_admin, 'reset_vote_all_admin' );

		$this->loader->add_filter( 'manage_knowledgebase_posts_columns', $plugin_admin, 'manage_knowledgebase_posts_columns_filter' );
		$this->loader->add_filter( 'manage_edit-knowledgebase_category_columns', $plugin_admin, 'manage_knowledgebase_category_columns_filter' );

		//Knowledgebase CPT votes sortable
		$this->loader->add_filter( 'manage_edit-knowledgebase_sortable_columns', $plugin_admin, 'kb_votes_sortable', 10, 1 );

		//Custom query for orderby value on sorting likes and dislikes on knowledgebase CPT
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'kb_votes_orderby', 10, 1 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Pressapps_Knowledge_Base_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles_base' );
		$this->loader->add_action( 'enqueue_block_assets', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'enqueue_block_assets', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_action( 'widgets_init', $plugin_public, 'sidebars_init' );

		$this->loader->add_filter( 'pre_get_posts', $plugin_public, 'pre_get_posts_filter' );
		$this->loader->add_filter( 'template_include', $plugin_public, 'template_include_filter' );
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'public_pre_get_posts' );

		//Ajax request for live search function
		$this->loader->add_action( 'wp_ajax_search_title', $plugin_public, 'live_search' );
		$this->loader->add_action( 'wp_ajax_nopriv_search_title', $plugin_public, 'live_search' );

		$this->loader->add_action( 'init', $plugin_public, 'voting_init' );

		$this->loader->add_filter( 'body_class', $plugin_public, 'add_body_class' );
		//filters the title based on condition on the public not on admin
		$this->loader->add_filter( 'the_title', $plugin_public, 'the_title_filter',10,2 );
		$this->loader->add_action( 'init', $plugin_public, 'search_post_query' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Pressapps_Knowledge_Base_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
