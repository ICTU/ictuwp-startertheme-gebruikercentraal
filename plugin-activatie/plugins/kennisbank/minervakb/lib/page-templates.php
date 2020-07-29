<?php
/**
 * Project: MinervaKB.
 * Copyright: 2015-2016 @KonstruktStudio
 */
/**
 * Class MinervaKB_PageTemplates
 * based on https://github.com/wpexplorer/page-templater
 */
class MinervaKB_PageTemplates {

    private static $templates;

    private static $template_files;

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    public function __construct() {

        self::$templates = array();
        self::$template_files = array();

        // Add a filter to the attributes metabox to inject template into the cache.
        if (version_compare(floatval(get_bloginfo( 'version')), '4.7', '<')) {
            // 4.6 and older
            add_filter('page_attributes_dropdown_pages_args', array($this, 'register_templates'));
        } else {
            // Add a filter to the wp 4.7 version attributes metabox
            add_filter('theme_page_templates', array($this, 'add_plugin_page_templates'));
        }

        // Add a filter to the save post to inject out template into the page cache
        add_filter('wp_insert_post_data', array($this, 'register_templates'));

        // Add a filter to the template include to determine if the page has our
        // template assigned and return it's path
        add_filter('template_include', array($this, 'plugin_page_template_filter'));

        // Add your templates to this array.
        self::$templates = array(
            'minervakb-page-template' => 'MinervaKB Page Template',
        );

        self::$template_files = array(
            'minervakb-page-template' => 'lib/templates/page-template.php'
        );
    }

    /**
     * Adds our template to the page dropdown for v4.7+
     */
    public function add_plugin_page_templates( $posts_templates ) {
        $posts_templates = array_merge($posts_templates, self::$templates);

        return $posts_templates;
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     */
    public function register_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();

        if (empty($templates)) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete($cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge($templates, self::$templates);

        // Add the modified cache to allow WordPress to pick it up for listing available templates
        wp_cache_add($cache_key, $templates, 'themes', 1800);

        return $atts;
    }

    public function plugin_page_template_filter( $template ) {
        global $post;

        if (!$post) {
            return $template;
        }

        $template_id = get_post_meta($post->ID, '_wp_page_template', true);

        if (!isset(self::$templates[$template_id])) {
            return $template;
        }

        $file = MINERVA_KB_PLUGIN_DIR . self::$template_files[$template_id];

        $theme_override = MINERVA_THEME_DIR . '/minerva-kb/' . basename(self::$template_files[$template_id]);

        // Just to be safe, we check if the file exist first
        if (file_exists($theme_override)) {
            return $theme_override;
        } else if (file_exists($file)) {
            return $file;
        } else {
            echo $file;
        }

        // Return template
        return $template;
    }

    public static function is_minerva_page_template() {
        global $post;

        if (!$post) {
            return false;
        }

        $template_id = get_post_meta($post->ID, '_wp_page_template', true);

        return isset(self::$templates[$template_id]);
    }
}
