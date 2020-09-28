<?php
/**
 * Elementor blocks
 * Copyright: 2015-2020 @KonstruktStudio
 */

if (!defined( 'ABSPATH')) {
    exit; // Exit if accessed directly.
}

final class MinervaKB_Elementor_Extension {

    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init() {
        if (!did_action('elementor/loaded')) {
            return;
        }

        // check for required Elementor version
        if (!version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' )) {
            add_action( 'admin_notices', array($this, 'admin_notice_minimum_elementor_version'));

            return;
        }

        // load Elementor blocks & controls
        add_action('elementor/elements/categories_registered', array($this, 'add_elementor_widget_categories'));
        add_action('elementor/widgets/widgets_registered', array($this, 'init_widgets'));
        add_action('elementor/controls/controls_registered', array($this, 'init_controls'));

        add_action('elementor/editor/after_enqueue_scripts', array($this, 'load_assets'));
    }

    public function add_elementor_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'minerva-support',
            array(
                'title' => __( 'Minerva KB', 'minerva-kb' ),
                'icon' => 'fa fa-university',
            )
        );
    }

    public function admin_notice_minimum_elementor_version() {
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'minerva-kb'),
            '<strong>' . esc_html__('MinervaKB', 'minerva-kb') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'minerva-kb') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function init_widgets() {
        // include widgets
        require_once( __DIR__ . '/widgets/tip.php' );
        require_once( __DIR__ . '/widgets/info.php' );
        require_once( __DIR__ . '/widgets/warning.php' );
        require_once( __DIR__ . '/widgets/article-content.php' );
        require_once( __DIR__ . '/widgets/guestpost.php' );
        require_once( __DIR__ . '/widgets/related.php' );
        require_once( __DIR__ . '/widgets/faq.php' );
        require_once( __DIR__ . '/widgets/topic.php' );
        require_once( __DIR__ . '/widgets/topics.php' );
        require_once( __DIR__ . '/widgets/search.php' );

        // register widgets
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorTopicsWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorSearchWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorFAQWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorTopicWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorRelatedWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorGuestpostWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorTipWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorInfoWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorWarningWidget() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new MinervaKB_ElementorArticleContentWidget() );
    }

    public function init_controls() {}

    public function load_assets() {
        wp_enqueue_script( 'minerva-kb/admin-elementor-main-js', MINERVA_KB_PLUGIN_URL . 'assets/js/minerva-kb-elementor.js', array(), MINERVA_KB_VERSION, true );

        wp_localize_script('minerva-kb/admin-elementor-main-js', 'MinervaKBElementor', $this->get_elementor_editor_data());
    }

    public function get_elementor_editor_data() {
        // articles
        $articles = get_posts([
            'post_type' => MKB_Options::option('article_cpt'),
            'post_status' => 'publish',
            'posts_per_page' => '-1'
        ]);
        $articles_list = [];

        if (!empty($articles)) {

            foreach($articles as $article) {
                $articles_list[$article->ID] = array(
                    'title' => $article->post_title,
                    'permalink' => get_permalink($article->ID),
                );
            }
        }

        // topics
        $topics_list = [
            'recent' => array(
                'title' => __('[Dynamic] Recent', 'minerva-kb')
            ),
            'updated' => array(
                'title' => __('[Dynamic] Recently updated', 'minerva-kb')
            ),
            'top_views' => array(
                'title' => __('[Dynamic] Most viewed', 'minerva-kb')
            ),
            'top_likes' => array(
                'title' => __('[Dynamic] Most liked', 'minerva-kb')
            ),
        ];

        $topics = get_terms(MKB_Options::option('article_cpt_category'), array(
            'hide_empty' => false
        ));

        if (isset($topics) && !is_wp_error($topics) && !empty($topics)) {
            foreach ($topics as $term):
                $name = $term->name;

                if ($term->parent) {
                    $parent = get_term_by('id', $term->parent, MKB_Options::option('article_cpt_category'));

                    if ($parent && isset($parent->name)) {
                        $name = $parent->name . ' - ' . $name;
                    }
                }

                $topics_list[$term->term_id] = array(
                    'title' => $name
                );
            endforeach;
        }

        // FAQ categories
        $faq_category_list = [];

        $faq_categories = get_terms('mkb_faq_category', array(
            'hide_empty' => false
        ));

        if (isset($faq_categories) && !is_wp_error($faq_categories) && !empty($faq_categories)) {
            foreach ($faq_categories as $term):
                $name = $term->name;

                if ($term->parent) {
                    $parent = get_term_by('id', $term->parent, 'mkb_faq_category');

                    if ($parent && isset($parent->name)) {
                        $name = $parent->name . ' - ' . $name;
                    }
                }

                $faq_category_list[$term->term_id] = $name;
            endforeach;
        }

        return array(
            'articles' => $articles_list,
            'topics' => $topics_list,
            'faqCategories' => $faq_category_list
        );
    }
}

MinervaKB_Elementor_Extension::instance();
