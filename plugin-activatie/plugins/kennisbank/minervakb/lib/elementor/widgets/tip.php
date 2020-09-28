<?php
/**
 * MinervaKB Elementor Tip Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class MinervaKB_ElementorTipWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-tip';
    }

    public function get_title() {
        return __( 'KB Tip', 'minerva-kb' );
    }

    public function get_icon() {
        return 'far fa-lightbulb';
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
                'label' => __( 'Your tip content', 'minerva-kb' ),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 10,
                'default' => __( 'Here is a helpful tip', 'minerva-kb' ),
                'placeholder' => __( 'Use this widget to display tips to readers', 'minerva-kb' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        echo do_shortcode('[mkb-tip]' . $settings['content'] . '[/mkb-tip]');
    }
}
