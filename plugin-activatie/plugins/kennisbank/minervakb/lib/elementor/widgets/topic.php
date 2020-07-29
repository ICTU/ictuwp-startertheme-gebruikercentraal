<?php
/**
 * MinervaKB Elementor Topic Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class MinervaKB_ElementorTopicWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-topic';
    }

    public function get_title() {
        return __( 'KB Topic', 'minerva-kb' );
    }

    public function get_icon() {
        return 'far fa-folder';
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
            'id',
            [
                'label' => __('Search & Select KB Topic', 'minerva-kb'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_topics_options(),
                'label_block' => true,
                'multiple' => false
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => __('Child topics view (if any)', 'minerva-kb'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'list' => [
                        'title' => __('List view', 'minerva-kb'),
                        'icon' => 'fa fa-list-ul'
                    ],
                    'box' => [
                        'title' => __('Box view', 'minerva-kb'),
                        'icon' => 'fa fa-th-large'
                    ]
                ],
                'default' => 'box',
                'label_block' => true,
                'toggle' => true
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Child topics layout (if any)', 'minerva-kb'),
                'type' => Controls_Manager::SELECT,
                'default' => '3col',
                'label_block' => true,
                'options' => [
                    '1col' => __('1 column', 'minerva-kb'),
                    '2col' => __('2 columns', 'minerva-kb'),
                    '3col' => __('3 columns', 'minerva-kb'),
                    '4col' => __('4 columns', 'minerva-kb')
                ],
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => __('Number of articles to display', 'minerva-kb'),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 99,
                'step' => 1,
                'default' => 5,
                'description' => __('Use -1 to display all', 'minerva-kb')
            ]
        );

        $this->end_controls_section();
    }

    private function get_topics_options() {
        $options = [];

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

                $options[$term->term_id] = $name;
            endforeach;
        }

        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        MKB_TemplateHelper::render_topic(array(
            'id' => $settings['id'],
            'view' => $settings['view'],
            'columns' => $settings['columns'],
            'limit' => $settings['limit']
        ));
    }
}