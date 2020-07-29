<?php
/**
 * MinervaKB Elementor Info Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class MinervaKB_ElementorInfoWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-info';
    }

    public function get_title() {
        return __( 'KB Info', 'minerva-kb' );
    }

    public function get_icon() {
        return 'fas fa-info-circle';
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
            'content',
            array(
                'label' => __( 'Your info content', 'minerva-kb' ),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 10,
                'default' => __( 'Here is some useful information', 'minerva-kb' ),
                'placeholder' => __( 'Use this widget to highlight some important info', 'minerva-kb' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        echo do_shortcode('[mkb-info]' . $settings['content'] . '[/mkb-info]');
    }
}
