<?php
/**
 * ictuwp-theme-gc2020
 * https://github.com/ICTU/ictuwp-theme-gc2020
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

define( 'CHILD_THEME_VERSION', '5.0.1' );
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


// add the widgets
if ( ! defined( 'WBVB_GC_ABOUTUS' ) ) {
	define( 'WBVB_GC_ABOUTUS', 'GC - Over ons' );
}
require_once( get_template_directory() . '/widgets/widget-over-ons.php' );

// add the gutenberg blocks
require_once( get_template_directory() . '/gutenberg-blocks/gutenberg-settings.php' );
require_once( get_template_directory() . '/gutenberg-blocks/download-block.php' );


/**
 * If you are installing Timber as a Composer dependency in your theme, you'll
 * need this block to load your dependencies and initialize Timber. If you are
 * using Timber via the WordPress.org plug-in, you can safely delete this
 * block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
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
Timber::$autoescape = FALSE;


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
		add_action( 'init', [ $this, 'register_my_menu' ] );

		// translation support
		add_action( 'after_setup_theme', [ $this, 'add_translation_support' ] );

		// theme options
		add_action( 'after_setup_theme', [ $this, 'theme_supports' ] );

		// CSS setup
		add_action( 'wp_enqueue_scripts', [ $this, 'gc_wbvb_add_css' ] );

		add_filter( 'timber/context', [ $this, 'add_to_context' ] );
		add_filter( 'timber/twig', [ $this, 'add_to_twig' ] );

		add_action( 'init', [ $this, 'register_taxonomies' ] );

		add_action( 'widgets_init', [ $this, 'setup_widgets_init' ] );

		add_action( 'theme_page_templates', [
			$this,
			'activate_deactivate_page_templates',
		] );

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
	public function add_to_context( $context ) {

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

			$context['pagetype'] = 'archive_' . get_queried_object()->taxonomy;
		}

		// Additional vars for archives

		if ( is_archive() ) {
			$context['archive_term']['tid']   = get_queried_object()->term_id;
			$context['archive_term']['descr'] = get_queried_object()->description;

			$context['pagetype'] = 'archive_' . get_queried_object()->taxonomy;
		}

		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

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

		add_image_size( HALFWIDTH, 380, 9999, FALSE );
		add_image_size( BLOG_SINGLE_MOBILE, 120, 9999, FALSE );
		add_image_size( BLOG_SINGLE_TABLET, 250, 9999, FALSE );
		add_image_size( BLOG_SINGLE_DESKTOP, 380, 9999, FALSE );
		add_image_size( IMG_SIZE_HUGE, IMG_SIZE_HUGE_MIN_WIDTH, 9999, FALSE );

		add_image_size( 'thumb-cardv3', 99999, 600, FALSE );    // max  600px hoog, niet croppen

		// Enable and load CSS for admin editor
		add_theme_support( 'editor-styles' );
		$cachebuster = '';
		if ( WP_DEBUG ) {
			$cachebuster = '?v=' . filemtime( dirname( __FILE__ ) . '/assets/fonts/editor-fonts.css' );
		}
		add_editor_style( get_stylesheet_directory_uri() . '/assets/fonts/editor-fonts.css' . $cachebuster );
		add_editor_style( get_stylesheet_directory_uri() . '/assets/css/editor-styles.css' . $cachebuster );




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

				wp_enqueue_script(
					$value['handle'],
					get_stylesheet_directory_uri() . $value['file'],
					$dependencies,
					$versie,
					$value['infooter']
				);
			}
		}

		$skiplinkshandle = ID_SKIPLINKS;

		if ( is_array( $this->configuration['cssfiles'] ) ) {

			foreach ( $this->configuration['cssfiles'] as $key => $value ) {

				$dependencies = $value['dependencies'];

				if ( $value['version'] ) {
					$versie = $value['version'];
				}

				wp_enqueue_style(
					$value['handle'],
					get_stylesheet_directory_uri() . $value['file'],
					$dependencies,
					$versie,
					'all'
				);
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
	 * Remove page templates inherited from the parent theme.
	 *
	 * @param array $page_templates List of currently active page templates.
	 *
	 * @return array Modified list of page templates.
	 */
	public function activate_deactivate_page_templates( $page_templates ) {

		// Remove the templates we don’t need, based on which site we look at

		// do not use sitemap
		// unset( $page_templates['template-sitemap.php'] );

		return $page_templates;
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

	$classes[] = 'meh';

	if ( is_page() ) {

		$template = basename( get_page_template() );
		if ( 'template-alle-tips.php' === $template ) {
			$classes[] = 'page--type-overview page--overview-archive';
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

		switch(get_queried_object()->taxonomy){
			case 'tipthema':
				$classes[] = 'page--overview-header-lg';
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
		$title = single_cat_title( '', FALSE );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', FALSE );
	} elseif ( is_author() ) {
		$title = '<span class="vcard">' . get_the_author() . '</span>';
	} elseif ( is_tax() ) { //for custom post types
//		$title = sprintf( __( '%1$s' ), single_term_title( '', FALSE ) );
		$title = single_term_title( '', FALSE );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', FALSE );
	}

	return $title;
} );

/**
 * Add our Customizer content
 */
function gc2020_customize_register( $wp_customize ) {

	$wp_customize->add_section( 'gc2020_theme', array(
		'title'       => _x( 'Gebruiker Centraal Instellingen', 'customizer', 'gctheme' ),
		'description' => _x( 'Selecteer hier het kleurenschema voor deze site. Je wijzigt hiermee de styling, logo en sommige functionaliteit van de site.', 'customizer', 'gctheme' ),
		'priority'    => 60,
	) );
	//  =============================
	//  = Select Box                =
	//  =============================
	$wp_customize->add_setting( 'gc2020_theme_options[flavor_select]', array(
		'default'    => DEFAULTFLAVOR,
		'capability' => 'edit_theme_options',
		'type'       => 'option',
	) );

	$flavors = array();

	// read configuration json file
	$configfile   = file_get_contents( trailingslashit( get_stylesheet_directory() ) . FLAVORSCONFIG );
	$flavorsource = json_decode( $configfile, true );
	foreach ( $flavorsource as $key => $value ) {
		$flavors[ strtoupper( $key ) ] = $value['name'];
	}

	$wp_customize->add_control( 'example_select_box', array(
		'settings' => 'gc2020_theme_options[flavor_select]',
		'label'    => _x( 'Kies het kleurenschema', 'customizer', 'gctheme' ),
		'section'  => 'gc2020_theme',
		'type'     => 'select',
		'choices'  => $flavors,
	) );


}

add_action( 'customize_register', 'gc2020_customize_register' );


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
