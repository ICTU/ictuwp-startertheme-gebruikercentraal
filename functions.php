<?php
/**
 * ictuwp-theme-gc2020
 * https://github.com/ICTU/ictuwp-theme-gc2020
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

define( 'CHILD_THEME_VERSION', '5.0.8' );
define( 'ID_MAINCONTENT', 'maincontent' );
define( 'ID_MAINNAV', 'mainnav' );
define( 'ID_ZOEKEN', 'zoeken' );
define( 'ID_SKIPLINKS', 'skiplinksjes' );

define( 'DEFAULTFLAVOR', 'GC' );
define( 'FLAVORSCONFIG', 'flavors_config.json' );

// Custom Post Types
if ( ! defined( 'GC_TIP_CPT' ) ) {
	define( 'GC_TIP_CPT', 'tips' );
}
if ( ! defined( 'GC_SPOTLIGHT_CPT' ) ) {
	define( 'GC_SPOTLIGHT_CPT', 'spotlight' );
}

// Custom Taxonomies
if ( ! defined( 'GC_TIPTHEMA' ) ) {
	define( 'GC_TIPTHEMA', 'tipthema' );
}
if ( ! defined( 'GC_TIPVRAAG' ) ) {
	define( 'GC_TIPVRAAG', 'tipvraag' );
}
if ( ! defined( 'OD_CITAATAUTEUR' ) ) {
	define( 'OD_CITAATAUTEUR', 'tipgever' );
}


// constants for image sizes
define( 'BLOG_SINGLE_MOBILE', 'blog-single-mobile' );
define( 'BLOG_SINGLE_TABLET', 'blog-single-tablet' );
define( 'BLOG_SINGLE_DESKTOP', 'blog-single-desktop' );
define( 'HALFWIDTH', 'halfwidth' );
define( 'IMG_SIZE_HUGE', 'feature-huge' );
define( 'IMG_SIZE_HUGE_MIN_WIDTH', 1200 );

//========================================================================================================

//specific flavours functions

$get_theme_option = get_option( 'gc2020_theme_options' );
$flavor_select    = $get_theme_option['flavor_select'];


if ( $flavor_select == "OD" ) {
	require_once( __DIR__ . '/assets/od.php' );
	add_action( 'init', array( 'ICTUWP_GC_OD_registerposttypes', 'init' ), 1 );
}

// include file for all must-use plugins
require_once( __DIR__ . '/plugin-activatie/must-use-plugins.php' );

// include file for network media
//require_once( __DIR__ . '/network-media-library/network-media-library.php' );


//========================================================================================================


// add the widgets
if ( ! defined( 'WBVB_GC_ABOUTUS' ) ) {
	define( 'WBVB_GC_ABOUTUS', 'GC - Over ons' );
}
require_once( get_template_directory() . '/widgets/widget-over-ons.php' );

// add the gutenberg blocks
require_once( get_template_directory() . '/gutenberg-blocks/gutenberg-settings.php' );
require_once( get_template_directory() . '/gutenberg-blocks/download-block.php' );
require_once( get_template_directory() . '/gutenberg-blocks/cta-block.php' );
require_once( get_template_directory() . '/gutenberg-blocks/related-block.php' );
require_once( get_template_directory() . '/gutenberg-blocks/textimage-block.php' );
require_once( get_template_directory() . '/gutenberg-blocks/links-block.php' );
require_once( get_template_directory() . '/gutenberg-blocks/spotlight-block.php' );
require_once( get_template_directory() . '/gutenberg-blocks/rijksvideo-block.php' );
require_once( get_template_directory() . '/gutenberg-blocks/teaser-block.php' );

// TODO: het block voor de handleiding zou alleen beschikbaar voor pagina's met het template 'template-od-handleiding.php'
require_once( get_template_directory() . '/gutenberg-blocks/handleiding-block.php' );

/**
 * Load other dependencies such as VAR DUMPER :D
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to
 * activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action( 'admin_notices', function () {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
	} );

	add_filter( 'template_include', function ( $template ) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	} );

	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = [ 'templates', 'views' ];

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's
 * autoescape? No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's
 * include("MySite.php")
 */
class GebruikerCentraalTheme extends Timber\Site {

	// contains configuration settings
	protected $configuration;

	/** Add timber support. */
	public function __construct() {

		// custom menu locations
		add_action( 'init', array( $this, 'register_my_menu' ) );

		// custom menu locations
		add_action( 'init', array( $this, 'register_spotlight_cpt' ) );


		// translation support
		add_action( 'after_setup_theme', array( $this, 'add_translation_support' ) );

		// theme options
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );

		// CSS setup
		add_action( 'wp_enqueue_scripts', array( $this, 'gc_wbvb_add_css' ) );

		add_action( 'timber/context', array( $this, 'add_to_context' ) );
		add_action( 'timber/twig', array( $this, 'add_to_twig' ) );

		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_action( 'widgets_init', array( $this, 'setup_widgets_init' ) );

		add_action( 'theme_page_templates', array( $this, 'activate_deactivate_page_templates' ) );

		add_action( 'admin_init', array( $this, 'add_adminstyles' ) );

		parent::__construct();

	}

	// define menu location and name
	public function register_my_menu() {
		register_nav_menu( 'primary', _x( 'Menu in header', 'menu location', 'gctheme' ) );
		register_nav_menu( 'footermenu', _x( 'Menu in footer', 'menu location', 'gctheme' ) );
	}

	function add_translation_support() {
		load_theme_textdomain( 'gctheme', get_template_directory() . '/languages' );
	}


	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function read_configuration_files() {

		// read configuration json file
		$configfile    = file_get_contents( trailingslashit( get_stylesheet_directory() ) . FLAVORSCONFIG );
		$configfile    = json_decode( $configfile, true );
		$theme_options = get_option( 'gc2020_theme_options' );
		$flavor        = DEFAULTFLAVOR; // default, tenzij er een smaakje is gekozen
		if ( isset( $theme_options['flavor_select'] ) ) {
			$flavor = $theme_options['flavor_select'];
		}
		if ( isset( $configfile[ DEFAULTFLAVOR ] ) ) {
			$defaultsettings = $configfile[ DEFAULTFLAVOR ];
		} else {
			// iemand heeft een typvaud gemaakt en de in dit bestand hier
			// gedefinieerde default staat niet in het json-bestand.
			// Beetje jammer, maar dan nemen we -op hoop van zegen- het
			// eerste setje configuratieregels
			$defaultsettings = reset( $configfile );
		}

		if ( isset( $configfile[ $flavor ] ) ) {
			// admin has chosen a flavor (any flavor), so let's
			// merge the configuration of chosen flavor with the default settings
			$this->configuration = wp_parse_args( $configfile[ $flavor ], $defaultsettings );

		} else {
			// no flavor chosen, so set the configuration to the default settings
			$this->configuration = $defaultsettings;
		}

	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {

		$this->read_configuration_files();

		$context['menu']                    = new Timber\Menu( 'primary' );
		$context['footermenu']              = new Timber\Menu( 'footermenu' );
		$context['site']                    = $this;
		$context['site_name']               = ( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Gebruiker Centraal' );
		$context['alt_logo']                = sprintf( _x( 'Logo %s', 'Alt-tekst op logo', 'gctheme' ), get_bloginfo( 'name' ) );
		$context['sprite_od']               = get_stylesheet_directory_uri() . '/assets/images/sprites/optimaal-digitaal/defs/svg/sprite.defs.svg';
		$context['sprite_steps']            = get_stylesheet_directory_uri() . '/assets/images/sprites/stepchart/defs/svg/sprite.defs.svg';
		$context['footer_widget_left']      = Timber::get_widgets( 'footer_widget_left' );
		$context['footer_widget_right']     = Timber::get_widgets( 'footer_widget_right' );
		$context['site_logo']               = get_stylesheet_directory_uri() . $this->configuration['site_logo'];
		$context['skiplinks_id']            = ID_SKIPLINKS;
		$context['maincontent_id']          = 'maincontent';
		$context['maincontent_id_linktext'] = _x( 'Jump to main content', 'skiplinks', 'gctheme' );
		$context['mainnav_id']              = 'menu-primary';
		$context['mainnav_id_linktext']     = _x( 'Jump to main navigation', 'skiplinks', 'gctheme' );

		// Additional vars for archives
		if ( is_archive() ) {
			$context['archive_term']['tid']   = get_queried_object()->term_id;
			$context['archive_term']['descr'] = get_queried_object()->description;
			$context['pagetype']              = 'archive_' . get_queried_object()->taxonomy;
		}

		return $context;
	}

	public function theme_supports() {

		$this->read_configuration_files();

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		$theme_options = get_option( 'gc2020_theme_options' );
		$flavor        = DEFAULTFLAVOR; // default, tenzij er een smaakje is gekozen
		if ( isset( $theme_options['flavor_select'] ) ) {
			$flavor = $theme_options['flavor_select'];
		}

		// these are the only allowed colors for the editor
		// keys in the flavors_config.json should match the keys here
		$arr_acceptable_colors = array(
			'white'                => array(
				'name'  => __( 'Wit', 'gctheme' ),
				'slug'  => 'white',
				'color' => '#fff'
			),
			'black'                => array(
				'name'  => __( 'Zwart', 'gctheme' ),
				'slug'  => 'black',
				'color' => '#000',
			),
			'gc-green'             => array(
				'name'  => __( 'GC Groen', 'gctheme' ),
				'slug'  => 'gc-green',
				'color' => '#25b34b',
			),
			'gc-dark-blue'         => array(
				'name'  => __( 'GC Dark Blue', 'gctheme' ),
				'slug'  => 'gc-dark-blue',
				'color' => '#004152',
			),
			'gc_pantybrown'        => array(
				'name'  => __( 'GC Pantybrown', 'gctheme' ),
				'slug'  => 'gc_pantybrown',
				'color' => '#e8d8c7',
			),
			'gc-dark-purple'       => array(
				'name'  => __( 'GC Dark Purple', 'gctheme' ),
				'slug'  => 'gc-dark-purple',
				'color' => '#4c2974',
			),
			'gc-blue'              => array(
				'name'  => __( 'GC Blue', 'gctheme' ),
				'slug'  => 'gc-blue',
				'color' => '#0095da',
			),
			'gc-pink'              => array(
				'name'  => __( 'GC Pink', 'gctheme' ),
				'slug'  => 'gc-pink',
				'color' => '#c42c76',
			),
			'gc-orange'            => array(
				'name'  => __( 'GC Orange', 'gctheme' ),
				'slug'  => 'gc-orange',
				'color' => '#f99d1c',
			),
			'gc-cyan'              => array(
				'name'  => __( 'GC Cyan', 'gctheme' ),
				'slug'  => 'gc-cyan',
				'color' => '#00b4ac',
			),
			'inc_orange'           => array(
				'name'  => __( 'Inclusie Orange', 'gctheme' ),
				'slug'  => 'nlds-orange',
				'color' => '#D94721',
			),
			'inc_a11y_orange'      => array(
				'name'  => __( 'Inclusie Orange', 'gctheme' ),
				'slug'  => 'inc-a11y-orange',
				'color' => '#c73d19',
			),
			'nlds_purplish'        => array(
				'name'  => __( 'NLDS Purplish', 'gctheme' ),
				'slug'  => 'nlds-purplish',
				'color' => '#74295f',
			),
			'gc_blue'              => array(
				'name'  => __( 'GC Blue', 'gctheme' ),
				'slug'  => 'gc-blue',
				'color' => '#0095da',
			),
			'gc_a11y-blue'         => array(
				'name'  => __( 'GC Blue Safe', 'gctheme' ),
				'slug'  => 'gc-a11y-blue',
				'color' => '#007BB0',
			),
			'gc_a11y_green'        => array(
				'name'  => __( 'gc_a11y_green', 'gctheme' ),
				'slug'  => 'gc-a11y-green',
				'color' => '#148839',
			),
			'od_orange'            => array(
				'name'  => __( 'od_orange', 'gctheme' ),
				'slug'  => 'gc-orange',
				'color' => '#BA4F0C',
			),
			'od_orange_darker'     => array(
				'name'  => __( 'od_orange_darker', 'gctheme' ),
				'slug'  => 'gc-orange',
				'color' => '#983A00',
			),
			'gc_pantybrown_xlight' => array(
				'name'  => __( 'GC Pantybrown Xtra Light', 'gctheme' ),
				'slug'  => 'gc-pantybrown-xlight',
				'color' => '#f9f6f3',
			)
		);


		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Enable excerpts for pages.
		 *
		 */
		add_post_type_support( 'page', 'excerpt' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', [
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		] );

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', [
			'aside',
			'image',
			'video',
			'quote',
			'link',
			'gallery',
			'audio',
		] );

		add_theme_support( 'menus' );

		// Yoast Breadcrumbs
		add_theme_support( 'yoast-seo-breadcrumbs' );

		add_image_size( HALFWIDTH, 380, 9999, false );
		add_image_size( BLOG_SINGLE_MOBILE, 120, 9999, false );
		add_image_size( BLOG_SINGLE_TABLET, 250, 9999, false );
		add_image_size( BLOG_SINGLE_DESKTOP, 380, 9999, false );
		add_image_size( IMG_SIZE_HUGE, IMG_SIZE_HUGE_MIN_WIDTH, 9999, false );

		add_image_size( 'thumb-cardv3', 99999, 600, false );    // max  600px hoog, niet croppen

		// Enable and load CSS for admin editor
		add_theme_support( 'editor-styles' );

		// Allow for responsive embedding
		add_theme_support( 'responsive-embeds' );

		// Disable Custom Colors
		add_theme_support( 'disable-custom-colors' );

		$colors_editor = array(
			// these colors should always be available
			// any other should be defined in flavors_config.json
			'white'                => array(
				'name'  => __( 'Wit', 'gctheme' ),
				'slug'  => 'white',
				'color' => '#fff',
			),
			'black'                => array(
				'name'  => __( 'Zwart', 'gctheme' ),
				'slug'  => 'black',
				'color' => '#000',
			),
			'gc_pantybrown_xlight' => array(
				'name'  => __( 'GC Pantybrown Xtra Light', 'gctheme' ),
				'slug'  => 'gc-pantybrown-xlight',
				'color' => '#f9f6f3',
			)
		);

		if ( $this->configuration['palette'] ) {
			// there are extra colors for the current flavor
			foreach ( $this->configuration['palette'] as $key => $value ) {
				if ( isset( $arr_acceptable_colors[ $key ] ) ) {
					// the color is allowed
					$colors_editor[ $key ] = array(
						'name'  => $arr_acceptable_colors[ $key ]['name'],
						'color' => $arr_acceptable_colors[ $key ]['color'],
						'slug'  => $key
					);
				}
			}
		}

		// Restrict Editor Color Palette
		add_theme_support( 'editor-color-palette', $colors_editor );

		// options for font size: off!
		add_theme_support( 'disable-custom-font-sizes' );

		// forces the dropdown for font sizes to only contain "normal"
		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' => __( 'Larger', 'gctheme' ),
				'size' => 24,
				'slug' => 'larger'
			),
			array(
				'name' => __( 'Extra large', 'gctheme' ),
				'size' => 32,
				'slug' => 'extra-large'
			)
		) );


	}

	/*
	 * add admin styles
	 */
	public function add_adminstyles() {

		// Load CSS for admin editor
		$cachebuster = '?v=' . CHILD_THEME_VERSION;
		if ( WP_DEBUG ) {
			$cachebuster = '?v=' . filemtime( dirname( __FILE__ ) . '/assets/fonts/editor-fonts.css' );
		}
		add_editor_style( get_stylesheet_directory_uri() . '/assets/fonts/editor-fonts.css' . $cachebuster );
		add_editor_style( get_stylesheet_directory_uri() . '/assets/css/editor-styles.css' . $cachebuster );
		/*
		*/
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';

		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {


		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', [ $this, 'myfoo' ] ) );

		return $twig;
	}

	public function gc_wbvb_add_css() {

		$versie = CHILD_THEME_VERSION;

		if ( WP_DEBUG ) {
			$versie = strtotime( "now" );
		}

		if ( is_array( $this->configuration['jsfiles'] ) ) {

			foreach ( $this->configuration['jsfiles'] as $key => $value ) {

				$dependencies = $value['dependencies'];

				if ( $value['version'] ) {
					$versie = $value['version'];
				}

				wp_enqueue_script( $value['handle'], get_stylesheet_directory_uri() . $value['file'], $dependencies, $versie, $value['infooter'] );
			}
		}

		$skiplinkshandle = ID_SKIPLINKS;

		if ( is_array( $this->configuration['cssfiles'] ) ) {

			foreach ( $this->configuration['cssfiles'] as $key => $value ) {

				$dependencies = $value['dependencies'];

				if ( $value['version'] ) {
					$versie = $value['version'];
				}

				wp_enqueue_style( $value['handle'], get_stylesheet_directory_uri() . $value['file'], $dependencies, $versie, 'all' );
				$skiplinkshandle = $value['handle'];

			}

			$custom_css = '
				ul#' . ID_SKIPLINKS . ', ul#' . ID_SKIPLINKS . ' li {
					list-style-type: none;
					list-style-image: none;
					padding: 0;
					margin: 0;
				}
				ul#' . ID_SKIPLINKS . ' li {
					background: none;
				}
				#' . ID_SKIPLINKS . ' li a {
					position: absolute;
					top: -1000px;
					left: 50px;
				}
				#' . ID_SKIPLINKS . ' li a:focus {
					left: 6px;
					top: 7px;
					height: auto;
					width: auto;
					display: block;
					font-size: 14px;
					font-weight: 700;
					padding: 15px 23px 14px;
					background: #f1f1f1;
					color: #21759b;
					z-index: 100000;
					line-height: normal;
					text-decoration: none;
					-webkit-box-shadow: 0 0 2px 2px rgba(0,0,0,.6);
					box-shadow: 0 0 2px 2px rgba(0,0,0,.6)
				}

				#' . ID_MAINNAV . ':focus {
					position: relative;
					z-index: 100000;
				}

				#' . ID_MAINNAV . ' a:focus {
					position: relative;
					z-index: 100000;
					color: #fff;
				}

				#' . ID_ZOEKEN . ':focus label {
					position: relative;
					left: 0;
					top: 0;
				}';

			if ( $this->configuration['site_logo'] ) {
				$custom_css .= "
				 .gc-site-footer-widget {
					background-image: url('" . get_stylesheet_directory_uri() . $this->configuration['site_logo'] . "');
				}";
			}


			wp_add_inline_style( $skiplinkshandle, $custom_css );

		}
	}

	/**
	 * Register our sidebars and widgetized areas.
	 *
	 */
	public function setup_widgets_init() {

		register_sidebar( [
			'name'          => _x( 'Footer widget left', 'Widget area', 'gctheme' ),
			'id'            => 'footer_widget_left',
			'before_widget' => '<section class="widget %s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title widgettitle">',
			'after_title'   => '</h3>',
		] );

		register_sidebar( [
			'name'          => _x( 'Footer widget right', 'Widget area', 'gctheme' ),
			'id'            => 'footer_widget_right',
			'before_widget' => '<section class="widget %s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title widgettitle">',
			'after_title'   => '</h3>',
		] );

	}

	/**
	 * Only allow some page templates based on the chosen flavor
	 *
	 * @param array $page_templates List of currently active page templates.
	 *
	 * @return array Modified list of page templates.
	 */
	public function activate_deactivate_page_templates( $page_templates ) {

		$allowed_templates = array(
			"template-landingspagina.php"   => "Landingspagina",
			"template-overzichtspagina.php" => "Overzichtspagina",
			"template-sitemap.php"          => "Sitemap"
		);

		// check the flavor
		$theme_options = get_option( 'gc2020_theme_options' );
		if ( isset( $theme_options['flavor_select'] ) ) {
			// flavor is available
			$flavor = $theme_options['flavor_select'];

			switch ( $flavor ) {
				case 'OD':
					// for Optimaal Digitaal, add tip templates
					$allowed_templates["template-overzicht-tipgevers.php"] = "[OD] Overzicht alle tipgevers";
					$allowed_templates["template-alle-tips.php"]           = "[OD] Overzicht alle tips";
					$allowed_templates["template-tips.php"]                = "[OD] Template tips-pagina";
					$allowed_templates["template-od-home.php"]             = "[OD] Template Home";
					$allowed_templates["template-od-handleiding.php"]      = "[OD] Template Handleiding";

					break;

				default:
					break;
			}

		}

		return $allowed_templates;
	}

	public function register_spotlight_cpt() {

		$labels = [
			"name"               => __( "Spotlight-blokken", 'gctheme' ),
			"singular_name"      => __( "Spotlight-blok", 'gctheme' ),
			"menu_name"          => __( "Spotlight", 'gctheme' ),
			"menu_name"          => __( "Spotlight", 'gctheme' ),
			"all_items"          => __( "Alle spotlight-blokken", 'gctheme' ),
			"add_new"            => __( "Toevoegen", 'gctheme' ),
			"add_new_item"       => __( "Spotlight-blok toevoegen", 'gctheme' ),
			"edit"               => __( "Spotlight-blok bewerken", 'gctheme' ),
			"edit_item"          => __( "Bewerk spotlight-blok", 'gctheme' ),
			"new_item"           => __( "Nieuwe spotlight-blok", 'gctheme' ),
			"view"               => __( "Bekijk", 'gctheme' ),
			"view_item"          => __( "Bekijk spotlight-blok", 'gctheme' ),
			"search_items"       => __( "Spotlight-blokken zoeken", 'gctheme' ),
			"not_found"          => __( "Geen spotlight-blokken gevonden", 'gctheme' ),
			"not_found_in_trash" => __( "Geen spotlight-blokken in de prullenbak", 'gctheme' ),
		];

		$args = [
			"labels"              => $labels,
			"description"         => __( "Hier voer je spotlight-blokken in.", 'gctheme' ),
			"public"              => false,
			"hierarchical"        => false,
			"exclude_from_search" => true,
			"publicly_queryable"  => false,
			"show_ui"             => true,
			"show_in_menu"        => true,
			"show_in_rest"        => true,
			"capability_type"     => __( "post", 'gctheme' ),
			"supports"            => [
				"title",
				"excerpt",
				"revisions",
				"thumbnail",
			],
			"has_archive"         => false,
			"can_export"          => true,
			"delete_with_user"    => false,
			"map_meta_cap"        => true,
			"query_var"           => true,
		];
		register_post_type( GC_SPOTLIGHT_CPT, $args );


	}


}

new GebruikerCentraalTheme();


function insert_breadcrumb() {

	// print out Yoast Breadcrumb
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div class="breadcrumb"><nav aria-label="Breadcrumb" class="breadcrumb__list">', '</nav></div>' );
	}

}


/*
 * filter body class
 */

add_filter( 'body_class', 'my_body_classes' );

function my_body_classes( $classes ) {

	global $post;

	$classes[] = '';

	if ( is_page() ) {

		$template = basename( get_page_template() );
		if (
			( 'template-alle-tips.php' === $template ) ||
			( 'template-overzicht-tipgevers.php' === $template ) ) {
			$classes[] = 'page--type-overview page--overview-archive';
		}
		if ( 'template-landingspagina.php' === $template ) {
			$classes[] = 'page--type-landing page--overview-archive entry--type-landing';
		}

	} elseif ( is_singular( GC_TIP_CPT ) ) {

		$classes[] = 'page page--type-tipkaart';
		$taxonomie = get_the_terms( $post, GC_TIPTHEMA );

		if ( $taxonomie && ! is_wp_error( $taxonomie ) ) {
			$counter = 0;
			// tip slug
			foreach ( $taxonomie as $term ) {

				$themakleur = get_field( 'kleur_en_icoon_tipthema', GC_TIPTHEMA . '_' . $term->term_id );

				if ( $themakleur ) {
					$classes[] = 'page-tipkaart--' . $themakleur;
				}
			}
		}
	} elseif ( is_archive() ) {
		$classes[] = 'page--type-overview page--overview-archive';

		//print_r(get_queried_object()->taxonomy);

		switch ( get_queried_object()->taxonomy ) {
			case 'tipthema':
				//$classes[] = 'page--overview-header-lg';
				break;

		}
	}

	return $classes;

}

/*
 * Generate archive titles
 */

add_filter( 'get_the_archive_title', function ( $title ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = '<span class="vcard">' . get_the_author() . '</span>';
	} elseif ( is_tax() ) { //for custom post types
//		$title = sprintf( __( '%1$s' ), single_term_title( '', FALSE ) );
		$title = single_term_title( '', false );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	}

	return $title;
} );

/**
 * Add our Customizer content
 */
function gc2020_customize_register( $wp_customize ) {

	$wp_customize->add_section( 'gc2020_theme', [
		'title'       => _x( 'Gebruiker Centraal Instellingen', 'customizer', 'gctheme' ),
		'description' => _x( 'Selecteer hier het kleurenschema voor deze site. Je wijzigt hiermee de styling, logo en sommige functionaliteit van de site.', 'customizer', 'gctheme' ),
		'priority'    => 60,
	] );
	//  =============================
	//  = Select Box                =
	//  =============================
	$wp_customize->add_setting( 'gc2020_theme_options[flavor_select]', [
		'default'    => DEFAULTFLAVOR,
		'capability' => 'edit_theme_options',
		'type'       => 'option',
	] );

	$flavors = [];

	// read configuration json file
	$configfile   = file_get_contents( trailingslashit( get_stylesheet_directory() ) . FLAVORSCONFIG );
	$flavorsource = json_decode( $configfile, true );
	foreach ( $flavorsource as $key => $value ) {
		$flavors[ strtoupper( $key ) ] = $value['name'];
	}

	$wp_customize->add_control( 'example_select_box', [
		'settings' => 'gc2020_theme_options[flavor_select]',
		'label'    => _x( 'Kies het kleurenschema', 'customizer', 'gctheme' ),
		'section'  => 'gc2020_theme',
		'type'     => 'select',
		'choices'  => $flavors,
	] );


}

add_action( 'customize_register', 'gc2020_customize_register' );


/**
 *  Dequeue unwanted CSS from plugins
 */

add_action( 'wp_enqueue_scripts', 'gc_ho_dequeue_css', 999 );

function gc_ho_dequeue_css() {
	include_once( 'wp-admin/includes/plugin.php' );

	/*
	if(is_plugin_active('ictuwp-plugin-rijksvideo/ictuwp-plugin-rijksvideo.php')) {
		wp_dequeue_script('rhswp_video_collapsible');
		wp_dequeue_style( 'rhswp-frontend');
	}
	*/

	// geen styling van events manager
	wp_deregister_style( 'events-manager' );
	wp_dequeue_style( 'events-manager' );

	// of events manager pro
	wp_deregister_style( 'events-manager-pro' );
	wp_dequeue_style( 'events-manager' );

	// geen css van newsletterplugin
	wp_deregister_style( 'newsletter' );
	wp_dequeue_style( 'newsletter-css' );

	/*
	 *
		// Add kennisbank CSS if subsite is kennisbank
		$get_theme_option = get_option( 'gc2020_theme_options' );
		$flavor_select    = $get_theme_option['flavor_select'];
		if ( $flavor_select == "KB" ) {
			wp_enqueue_style( 'gc-kennisbank-style', get_template_directory_uri() . '/flavors/kennisbank/assets/css/gc-kennisbank.css' );
		}

	 */


}


//========================================================================================================
// ervoor zorgen dat specifieke Optimaal Digitaal-termen op de juiste manier afgebroken kunnen worden

if ( ! function_exists( 'od_wbvb_custom_post_title' ) ) {

	function od_wbvb_custom_post_title( $title ) {

		$pattern     = '/erantwoordelijkh/i'; // verantwoordelijkheid
		$replacement = 'erant&shy;woorde&shy;lijkh';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/emeenscha/i'; // gemeenschappelijk,  gemeenschap
		$replacement = 'emeen&shy;scha';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/ersoonsge/i'; // persoonsgegevens
		$replacement = 'ersoons&shy;ge';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/informatiev/i'; // informatieveiligheid
		$replacement = 'informatie&shy;v';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/ortermijnd/i'; // kortetermijndenken
		$replacement = 'ortermijn&shy;d';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/ebruiksvrien/i';
		$replacement = 'ebruiks&shy;vrien';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/gebruikssituatie/i';
		$replacement = 'gebruiks&shy;situatie';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/laaggeletterde/i';
		$replacement = 'laag&shy;geletterde';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/belangenbehartig/i';
		$replacement = 'belangen&shy;behartig';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/ijvingsform/i';
		$replacement = 'ijvings&shy;form';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/evensgebeurtenis/i';
		$replacement = 'evens&shy;gebeurtenis';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/gemeenschapp/i';
		$replacement = 'gemeen&shy;schap&shy;p';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/kortetermijndoel/i';
		$replacement = 'kortetermijn&shy;doel';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/toptakenprincipe/i';
		$replacement = 'toptaken&shy;principe';
		$title       = preg_replace( $pattern, $replacement, $title );

		$pattern     = '/verwachtingsmanagement/i';
		$replacement = 'verwachtings&shy;management';
		$title       = preg_replace( $pattern, $replacement, $title );

		return $title;

	}

}

//========================================================================================================

function gc_wbvb_get_human_filesize( $bytes, $decimals = 2 ) {
	$sz     = 'BKMGTP';
	$factor = floor( ( strlen( $bytes ) - 1 ) / 3 );

	return sprintf( "%.{$decimals}f", $bytes / pow( 1024, $factor ) ) . @$sz[ $factor ] . 'B';
}

//========================================================================================================

function get_themakleuren() {

	$themakleuren = [];

	// alle tipthema's langs om de kleuren op te halen
	$args  = [
		'taxonomy'   => GC_TIPTHEMA,
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	];
	$terms = get_terms( $args );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$count = count( $terms );

		foreach ( $terms as $term ) {

			$themakleur = get_field( 'kleur_en_icoon_tipthema', GC_TIPTHEMA . '_' . $term->term_id );

			if ( $themakleur ) {

				$themakleuren[ $term->term_id ] = $themakleur;

			} else {
				// kleur ontbreekt
			}
		}
	}

	return $themakleuren;

}

// Hernoem menu naam Kennisbank
function rename_minervakb() {

	global $menu;

	foreach ( $menu as $key => $item ) {
		if ( $item[0] === 'minervakb' ) {
			$menu[ $key ][0] = __( 'GC Kennisbank', 'gctheme' );     //change name

		}
	}

	return false;
}

add_action( 'admin_menu', 'rename_minervakb', 999 );

//========================================================================================================

function append_block_wrappers( $block_content, $block ) {

	$pagetemplate = basename( get_page_template() );
//	$block_content = '<strong>' . $pagetemplate . ' / ' . $block['blockName']  . '</strong><br>' . $block_content;

	if ( ( $block['blockName'] === 'core/paragraph' ||
	       $block['blockName'] === 'acf/gc-ctalink' ) && 'template-landingspagina.php' === $pagetemplate ) {

		$content = '<div class="section section--paragraph">';
		$content .= $block_content;
		$content .= '</div>';

		return $content;

	} elseif ( $block['blockName'] === 'core/heading' ) {
		$content = '<div class="section section--heading">';
		$content .= $block_content;
		$content .= '</div>';

		return $content;
	} elseif ( $block['blockName'] === 'acf/gc-handleiding' ) {
		global $handleidingcounter;
		$handleidingcounter ++;
		$content = '<li class="manual-item manual-item--item-' . $handleidingcounter . '">';
		$content .= $block_content;
		$content .= '</li>';

		return $content;
		/*
	} elseif ( $block['blockName'] ) {
		$content = '<div style="border-top: 1px solid #dadada;">';
		$content .= '<p><strong>Block: ' . $block['blockName'] . '</strong></p>';
		$content .= $block_content;
		$content .= '</div>';

		return $content;
		*/
	}

	return $block_content;
}

add_filter( 'render_block', 'append_block_wrappers', 10, 2 );

//========================================================================================================
/*
 * only allow these Gutenberg blocks to be used
 */

function gc_restrict_gutenberg_blocks( $allowed_blocks ) {

	/*
		these are most of the core blocks. We will allow only some of them
		---------------------------------------------
		Standard block: core-embed/facebook
		Standard block: core-embed/instagram
		Standard block: core-embed/twitter
		Standard block: core-embed/wordpress
		Standard block: core-embed/vimeo
		Standard block: core-embed/youtube
		Standard block: core/audio
		Standard block: core/button
		Standard block: core/categories
		Standard block: core/code
		Standard block: core/columns
		Standard block: core/cover
		Standard block: core/file
		Standard block: core/gallery
		Standard block: core/heading
		Standard block: core/html
		Standard block: core/image
		Standard block: core/latest-posts
		Standard block: core/media-text
		Standard block: core/list
		Standard block: core/nextpage
		Standard block: core/paragraph
		Standard block: core/preformatted
		Standard block: core/pullquote
		Standard block: core/quote
		Standard block: core/separator
		Standard block: core/spacer
		Standard block: core/subhead
		Standard block: core/table
		Standard block: core/text-columns
		Standard block: core/verse
		Standard block: core/video


		these are our custom Gutenberg blocks
		see folder: /gutenberg-blocks
		---------------------------------------------
		GC specific block: acf/gc-links
		GC specific block: acf/gc-related
		GC specific block: acf/gc-downloads
		GC specific block: acf/gc-ctalink
		GC specific block: acf/gc-textimage


	*/
	return array(
		'core/image',
		'core/heading',
		'core/table',
		'core/audio',
		'core/gallery',
		'core/list',
		'core/media-text',
		'core/paragraph',
		'core/pullquote',
		'core/subhead',
		'core-embed/youtube',
		'core-embed/vimeo',

		'acf/gc-ctalink',
		'acf/gc-downloads',
		'acf/gc-handleiding',
		'acf/gc-links',
		'acf/gc-related',
		'acf/gc-rijksvideo',
		'acf/gc-textimage'


	);

}

//add_filter( 'allowed_block_types', 'gc_restrict_gutenberg_blocks' );

//========================================================================================================

add_filter( 'acf/fields/relationship/result', 'my_acf_fields_relationship_result', 10, 4 );

function my_acf_fields_relationship_result( $text, $post, $field, $post_id ) {

	if ( GC_TIP_CPT === get_post_type( $post ) ) {
		$tipnummer = get_field( 'tip-nummer', $post->ID );
		$text      = sprintf( _x( 'Tip %s', 'Label tip-nummer', 'gctheme' ), $tipnummer ) . ' - ' . $text;
	}

	return $text;
}

//========================================================================================================

function tipgever_archive_modify_query( $query ) {

	global $query_vars;

	if ( ! is_admin() && $query->is_main_query() ) {

		if ( is_tax( OD_CITAATAUTEUR ) ) {
			// geen pagination voor tipgevers overzichten
			$query->set( 'posts_per_page', - 1 );

			return $query;

		}

	}

	return $query;
}

add_action( 'pre_get_posts', 'tipgever_archive_modify_query' );

//========================================================================================================

function get_hero_image() {
	$return = array();

	if ( get_field( 'hero_image' ) ) {
		$data           = get_field( 'hero_image' );
		$return['src']  = $data['url'];
		$return['alt']  = $data['alt'];
		$return['mime'] = get_post_mime_type( $data['ID'] );
	}

	return $return;

}


//========================================================================================================

function translate_mime_type( $fullmimetype ) {

	$return = '';

	switch ( strtolower( $fullmimetype ) ) {

		case "png":
			$return = 'PNG';
			break;

		case "jpeg":
			$return = 'JPG';
			break;

		case "jpg":
			$return = 'JPG';
			break;

		case "vnd.openxmlformats-officedocument.wordprocessingml.document":
			$return = 'Word';
			break;

		case "msword":
			$return = 'Word';
			break;

		case "vnd.openxmlformats-officedocument.presentationml.presentation":
			$return = 'Powerpoint';
			break;

		case "vnd.ms-excel":
			$return = 'Excel';
			break;

		case "vnd.openxmlformats-officedocument.spreadsheetml.sheet":
			$return = 'Excel';
			break;

		case "plain":
			$return = 'TXT';
			break;

		default:
			$return = 'document: (' . $fullmimetype . ')';
			break;
	}
	return $return;

}

//========================================================================================================


