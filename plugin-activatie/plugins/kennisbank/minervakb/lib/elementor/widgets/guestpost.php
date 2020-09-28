<?php
/**
 * MinervaKB Elementor Guest Post Form Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class MinervaKB_ElementorGuestpostWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-guestpost';
    }

    public function get_title() {
        return __( 'KB Guest Posting Form', 'minerva-kb' );
    }

    public function get_icon() {
        return 'far fa-paper-plane';
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
        MKB_TemplateHelper::render_guestpost();
    }
}