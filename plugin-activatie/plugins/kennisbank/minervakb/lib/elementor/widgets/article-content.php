<?php
/**
 * MinervaKB Elementor Article Content Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class MinervaKB_ElementorArticleContentWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-article-content';
    }

    public function get_title() {
        return __( 'KB Article Content', 'minerva-kb' );
    }

    public function get_icon() {
        return 'far fa-file-alt';
    }

    public function get_categories() {
        return array('minerva-support');
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            array(
                'label' => __('Content', 'minerva-kb'),
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'empty_note',
            [
                'label' => __( 'This widget has no settings', 'minerva-kb' ),
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        if (defined( 'REST_REQUEST' ) && REST_REQUEST) {
            return;
        }

        if (!is_singular(MKB_Options::option('article_cpt'))) {
            echo __('<p>Warning! Article content block should be used only in single article custom templates</p>', 'minerva-kb');

            return;
        }

        do_action('minerva_single_article_content');
    }
}