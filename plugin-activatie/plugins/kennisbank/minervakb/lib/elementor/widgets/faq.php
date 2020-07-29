<?php
/**
 * MinervaKB Elementor FAQ Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;

class MinervaKB_ElementorFAQWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-faq';
    }

    public function get_title() {
        return __( 'FAQ', 'minerva-kb' );
    }

    public function get_icon() {
        return 'far fa-question-circle';
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
            'title',
            [
                'label' => __('FAQ title', 'minerva-kb'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Frequently Asked Questions', 'minerva-kb'),
                'placeholder' => __('Type your title here', 'minerva-kb'),
            ]
        );

        $this->add_control(
            'titleSize',
            [
                'label' => __('FAQ title font size', 'minerva-kb'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 30,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 30,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .mkb-section-title h3' => 'font-size: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'titleColor',
            [
                'label' => __( 'FAQ title color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .mkb-section-title h3' => 'color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'limitWidth',
            [
                'label' => __('Limit FAQ container width?', 'minerva-kb'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'width',
            [
                'label' => __('FAQ container maximum width', 'minerva-kb'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 120,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 120,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 60,
                ],
                'condition' => [
                    'limitWidth' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mkb-home-faq' => 'width: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'controlsMarginTop',
            [
                'label' => __('FAQ controls top margin', 'minerva-kb'),
                'type' => Controls_Manager::SLIDER,
                'description' => __('Distance between FAQ controls and title', 'minerva-kb'),
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 60,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 60,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-faq__controls' => 'margin-top: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'controlsMarginBottom',
            [
                'label' => __('FAQ controls bottom margin', 'minerva-kb'),
                'type' => Controls_Manager::SLIDER,
                'description' => __('Distance between FAQ controls and questions', 'minerva-kb'),
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 60,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 60,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-faq__controls' => 'margin-bottom: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'showFilter',
            [
                'label' => __('Show FAQ live filter?', 'minerva-kb'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'showToggleAll',
            [
                'label' => __('Show FAQ toggle all button?', 'minerva-kb'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'category_id',
            [
                'label' => __('Search & Select FAQ Category', 'minerva-kb'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_all_categories(),
                'label_block' => true,
                'multiple' => false
            ]
        );

        $this->add_control(
            'categories',
            [
                'label' => __( 'Select FAQ categories to display', 'minerva-kb' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'title_field' => '{{{ MinervaKBElementor && MinervaKBElementor.faqCategories && MinervaKBElementor.faqCategories[category_id] }}}',
            ]
        );

        $this->add_control(
            'showCategories',
            [
                'label' => __('Show FAQ categories?', 'minerva-kb'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'showCount',
            [
                'label' => __('Show FAQ category question count?', 'minerva-kb'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->end_controls_section();
    }

    private function get_all_categories() {
        $options = [];

        $categories = get_terms('mkb_faq_category', array(
            'hide_empty' => false
        ));

        if (isset($categories) && !is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $term):
                $name = $term->name;

                if ($term->parent) {
                    $parent = get_term_by('id', $term->parent, 'mkb_faq_category');

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

        $ids = array();

        foreach($settings['categories'] as $category) {
            if (isset($category['category_id']) && $category['category_id']) {
                array_push($ids, (int)$category['category_id']);
            }
        }

        MKB_TemplateHelper::render_faq(array(
            'home_faq_title' => $settings['title'],
            'home_faq_title_size' => $settings['titleSize'],
            'home_faq_title_color' => $settings['titleColor'],
            'home_faq_margin_top' => array('unit' => 'em', 'size' => 0),
            'home_faq_margin_bottom' => array('unit' => 'em', 'size' => 0),
            'home_faq_limit_width_switch' => (bool)$settings['limitWidth'],
            'home_faq_width_limit' => $settings['width'],
            'home_faq_controls_margin_top' => $settings['controlsMarginTop'],
            'home_faq_controls_margin_bottom' => $settings['controlsMarginBottom'],
            'home_show_faq_filter' => (bool)$settings['showFilter'],
            'home_show_faq_toggle_all' => (bool)$settings['showToggleAll'],
            'home_faq_categories' => !empty($ids) ? implode(',', $ids) : '',
            'home_show_faq_categories' => (bool)$settings['showCategories'],
            'home_show_faq_category_count' => (bool)$settings['showCount']
        ));
    }
}