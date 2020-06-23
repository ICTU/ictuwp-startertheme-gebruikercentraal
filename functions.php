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
define( 'ID_SKIPLINKS', 'skiplinks' );


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

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function ( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);

	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class GebruikerCentraalTheme extends Timber\Site {
	/** Add timber support. */
	public function __construct() {

		// custom menu locations
		add_action( 'init', array( $this, 'register_my_menu' ) );

		// translation support
		add_action( 'after_setup_theme', array( $this, 'add_translation_support' ) );

		// theme options
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );

		// CSS setup
		add_action( 'wp_enqueue_scripts', array( $this, 'gc_wbvb_add_css' ) );

		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );

		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_action( 'widgets_init', array( $this, 'setup_widgets_init' ) );

		add_action( 'theme_page_templates', array( $this, 'activate_deactivate_page_templates' ) );

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

		$blog_id = get_current_blog_id();

		$context['menu']                = new Timber\Menu( 'primary' );
		$context['footermenu']          = new Timber\Menu( 'footermenu' );
		$context['site']                = $this;
		$context['site_name']           = ( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Gebruiker Centraal' );
		$context['site_slogan']         = ( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : null );
		$context['logo']                = get_stylesheet_directory_uri() . '/theme/img/logo/od.svg';
		$context['sprite_url']          = get_stylesheet_directory_uri() . '/theme/img/sprites/optimaal-digitaal/defs/svg/sprite.defs.svg';
		$context['footer_widget_left']  = Timber::get_widgets( 'footer_widget_left' );
		$context['footer_widget_right'] = Timber::get_widgets( 'footer_widget_right' );

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
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );

		// Yoast Breadcrumbs
		add_theme_support( 'yoast-seo-breadcrumbs' );

		add_image_size( HALFWIDTH, 380, 9999, false );
		add_image_size( BLOG_SINGLE_MOBILE, 120, 9999, false );
		add_image_size( BLOG_SINGLE_TABLET, 250, 9999, false );
		add_image_size( BLOG_SINGLE_DESKTOP, 380, 9999, false );
		add_image_size( IMG_SIZE_HUGE, IMG_SIZE_HUGE_MIN_WIDTH, 9999, false );

		add_image_size( 'thumb-cardv3', 99999, 600, false );    // max  600px hoog, niet croppen


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
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );

		return $twig;
	}

	public function gc_wbvb_add_css() {


		$dependencies = array();
		$versie       = CHILD_THEME_VERSION;
		$infooter     = false;
		$gc_theme = get_option('gc2020_theme_options');


		if ( WP_DEBUG ) {
			$versie = strtotime( "now" );
		}

		wp_enqueue_script( 'main-min', get_stylesheet_directory_uri() . '/theme/dist/js/main-min.js', $dependencies, $versie, $infooter );

		$dependencies = array();


		// TODO : verwijzen naar de relevante CSS

		wp_enqueue_style(
			'gc-fonts',
			get_stylesheet_directory_uri() . '/theme/fonts/fonts.css',
			$dependencies,
			'',
			'all'
		);

		wp_enqueue_style(
			ID_SKIPLINKS,
			get_stylesheet_directory_uri() . '/sites/'.$gc_theme["theme_select"].'/theme/dist/css/od-theme.css',
			$dependencies,
			$versie,
			'all'
		);

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

		wp_add_inline_style( ID_SKIPLINKS, $custom_css );

	}

	/**
	 * Register our sidebars and widgetized areas.
	 *
	 */
	public function setup_widgets_init() {

		register_sidebar( array(
			'name'          => _x( 'Footer widget left', 'Widget area', 'gctheme' ),
			'id'            => 'footer_widget_left',
			'before_widget' => '<section class="widget %s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title widgettitle">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => _x( 'Footer widget right', 'Widget area', 'gctheme' ),
			'id'            => 'footer_widget_right',
			'before_widget' => '<section class="widget %s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title widgettitle">',
			'after_title'   => '</h3>',
		) );

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

}
elseif ( is_singular( GC_TIP_CPT ) ) {

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
	}

	return $classes;

}




/*
 * filter for breadcrumb
 */
add_filter( 'wpseo_breadcrumb_links', 'unbox_yoast_seo_breadcrumb_append_link' );

function unbox_yoast_seo_breadcrumb_append_link( $links ) {
	global $post;

	if ( is_home() || is_front_page() ) {
		// geen breadcrumb op de homepage
		return array();
	}
	elseif ( is_singular( GC_TIP_CPT ) ) {
		// uit siteopties de pagina ophalen die het overzicht is van alle links
		$optionpage = get_field( 'op_welke_pagina_staat_het_overzicht_van_alle_tips', 'option' );

		if ( $optionpage ) {
			// haal de ancestors op voor de huidige pagina

			$ancestors  = get_post_ancestors( $optionpage );
			$currenttip = array_pop( $links );
			$home       = $links[0];
			$parents[]  = array(
				'url'  => get_page_link( $optionpage ),
				'text' => get_the_title( $optionpage ),
			);

			// haal de hele keten aan ancestors op en zet ze in de returnstring
			foreach ( $ancestors as $ancestorid ) {

				if ( $home['url'] !== get_page_link( $ancestorid ) ) {
					// home link staat al in $home, dus niet extra toevoegen

					array_unshift( $parents, array(
						'url'  => get_page_link( $ancestorid ),
						'text' => get_the_title( $ancestorid ),
					) );

				}
			}

			array_unshift( $parents, $links[0] );

			$parents[] = array(
				'url'  => get_page_link( $currenttip['id'] ),
				'text' => get_the_title( $currenttip['id'] ),
			);

			$links = $parents;
		}
	}

	return $links;
}
/**
 * Add our Customizer content
 */
function gc2020_customize_register($wp_customize){

	$wp_customize->add_section('gc2020_theme', array(
		'title'    => __('GC Theme opties', 'ictuwp-theme-gc2020'),
		'description' => 'Selecteer hier het thema voor deze subsite',
		'priority' => 120,
	));
	//  =============================
	//  = Select Box                =
	//  =============================
	$wp_customize->add_setting('gc2020_theme_options[theme_select]', array(
		'default'        => 'GC',
		'capability'     => 'edit_theme_options',
		'type'           => 'option',

	));
	$wp_customize->add_control( 'example_select_box', array(
		'settings' => 'gc2020_theme_options[theme_select]',
		'label'   => 'Selecteer thema:',
		'section' => 'gc2020_theme',
		'type'    => 'select',
		'choices'    => array(
			'GC' => 'Gebruiker Centraal default theme',
			'OD' => 'Optimaal Digitaal theme',

		),
	));




}

add_action('customize_register', 'gc2020_customize_register');
