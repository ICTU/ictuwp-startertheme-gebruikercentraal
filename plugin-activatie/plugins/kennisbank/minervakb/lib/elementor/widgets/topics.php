<?php
/**
 * MinervaKB Elementor Topics List Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;

class MinervaKB_ElementorTopicsWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-topics';
    }

    public function get_title() {
        return __( 'KB Topics List', 'minerva-kb' );
    }

    public function get_icon() {
        return 'fas fa-th';
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
                'label' => __( 'Topics title', 'minerva-kb' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'Popular topics', 'minerva-kb' ),
                'placeholder' => __('Type your title here', 'minerva-kb'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'topic_id',
            [
                'label' => __('Search & Select KB Topic', 'minerva-kb'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_topics_options(),
                'label_block' => true,
                'multiple' => false
            ]
        );

        $this->add_control(
            'topics',
            [
                'label' => __( 'Select topics to display', 'minerva-kb' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'description' => __( 'You can leave it empty to display all recent topics. NOTE: dynamic topics only work for list view', 'minerva-kb' ),
                'title_field' => '{{{ MinervaKBElementor && MinervaKBElementor.topics && MinervaKBElementor.topics[topic_id] && MinervaKBElementor.topics[topic_id].title }}}',
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => __( 'Topics view', 'minerva-kb' ),
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
                'default' => 'list',
                'label_block' => true,
                'toggle' => true
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __( 'Topics layout', 'minerva-kb' ),
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
            'showCount',
            [
                'label' => __( 'Show articles count?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'showDescription',
            [
                'label' => __( 'Show description?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'view' => 'box',
                ],
            ]
        );

        $this->add_control(
            'showAll',
            [
                'label' => __( 'Add "Show all" link?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'showAllLabel',
            [
                'label' => __( 'Show all link label', 'minerva-kb' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'Show all', 'minerva-kb' ),
                'placeholder' => __('Type label here', 'minerva-kb'),
                'condition' => [
                    'showAll' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hideChildren',
            [
                'label' => __( 'Hide child topics?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Hide', 'minerva-kb'),
                'label_off' => __('Show', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __('This option has no effect when specific topics are selected', 'minerva-kb')
            ]
        );

        $this->add_control(
            'articlesLimit',
            [
                'label' => __( 'Number of article to display', 'minerva-kb' ),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 99,
                'step' => 1,
                'default' => 5,
                'description' => __('Use -1 to display all', 'minerva-kb')
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => __( 'Number of topics to display', 'minerva-kb' ),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 99,
                'step' => 1,
                'default' => -1,
                'description' => __('Use -1 to display all. Has no effect when specific topics are selected', 'minerva-kb')
            ]
        );

        $this->end_controls_section();

        /**
         * Colors
         */
        $this->start_controls_section(
            'colors_section',
            array(
                'label' => __('Style & Colors', 'minerva-kb'),
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'titleColor',
            [
                'label' => __( 'Topics title color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#333',
                'condition' => [
                    'title!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mkb-section-title' => 'color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'titleSize',
            [
                'label' => __( 'Topics title font size', 'minerva-kb' ),
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
                    'size' => 2,
                ],
                'condition' => [
                    'title!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .mkb-section-title' => 'font-size: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'countBg',
            [
                'label' => __( 'Articles count background', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#4a90e2',
                'condition' => [
                    'view' => 'list',
                    'showCount' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-topic .kb-topic__count' => 'background: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'countColor',
            [
                'label' => __( 'Articles count color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'condition' => [
                    'view' => 'list',
                    'showCount' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-topic .kb-topic__count' => 'color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'topicColor',
            [
                'label' => __( 'Topic color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#4a90e2',
                'description' => __( 'Note, that topic color can be changed for each topic individually on topic edit page', 'minerva-kb' ),
            ]
        );

        $this->add_control(
            'forceTopicColor',
            [
                'label' => __( 'Force topic color?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'minerva-kb'),
                'label_off' => __('Off', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __('Override topic custom colors', 'minerva-kb')
            ]
        );

        $this->add_control(
            'boxItemBg',
            [
                'label' => __( 'Box view background', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'condition' => [
                    'view' => 'box',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-topic.kb-topic--box-view .kb-topic__inner' => 'background: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'boxItemHoverBg',
            [
                'label' => __( 'Box view hover background', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#f8f8f8',
                'condition' => [
                    'view' => 'box',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-topic.kb-topic--box-view:hover .kb-topic__inner' => 'background: {{VALUE}}!important;',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Icons
         */
        $this->start_controls_section(
            'icons_section',
            array(
                'label' => __('Icons & Images', 'minerva-kb'),
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'showTopicIcons',
            [
                'label' => __( 'Show topic icons?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'showArticleIcons',
            [
                'label' => __( 'List view only: Show article icons?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'view' => 'list',
                ],
            ]
        );

        $this->add_control(
            'articleIcon',
            [
                'label' => __( 'Article icon', 'minerva-kb' ),
                'type' => Controls_Manager::ICON,
                'label_block' => true,
                'default' => 'fa fa-book',
                'condition' => [
                    'view' => 'list',
                    'showArticleIcons' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'topicIcon',
            [
                'label' => __( 'Topic icon', 'minerva-kb' ),
                'type' => Controls_Manager::ICON,
                'label_block' => true,
                'default' => 'fa fa-list-alt',
                'condition' => [
                    'showTopicIcons' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'forceTopicIcon',
            [
                'label' => __( 'Force topic icon?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'minerva-kb'),
                'label_off' => __('Off', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __('Override topic custom icons', 'minerva-kb')
            ]
        );

        $this->add_control(
            'useTopicImage',
            [
                'label' => __( 'Box view only: Show image instead of icon?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_block' => true,
                'label_on' => __('On', 'minerva-kb'),
                'label_off' => __('Off', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'view' => 'box',
                ],
                'description' => __('Image URL can be added on each topic page', 'minerva-kb')

            ]
        );

        $this->add_control(
            'imageSize',
            [
                'label' => __( 'Topic image size', 'minerva-kb' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
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
                    'size' => 10,
                ],
                'condition' => [
                    'view' => 'box',
                    'useTopicImage' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-topic__icon-image' => 'width: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'iconPaddingTop',
            [
                'label' => __( 'Topic icon/image top padding', 'minerva-kb' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 120,
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
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 0,
                ],
                'condition' => [
                    'view' => 'box',
                    'showTopicIcons' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-topic__icon-holder' => 'padding-top: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'iconPaddingBottom',
            [
                'label' => __( 'Topic icon/image bottom padding', 'minerva-kb' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 120,
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
                        'max' => 20,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 0,
                ],
                'condition' => [
                    'view' => 'box',
                    'showTopicIcons' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-topic__icon-holder' => 'padding-bottom: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Topics list options
     * @return array
     */
    private function get_topics_options() {
        $options = [
            'recent' => __('[Dynamic] Recent', 'minerva-kb'),
            'updated' => __('[Dynamic] Recently updated', 'minerva-kb'),
            'top_views' => __('[Dynamic] Most viewed', 'minerva-kb'),
            'top_likes' => __('[Dynamic] Most liked', 'minerva-kb'),
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

                $options[$term->term_id] = $name;
            endforeach;
        }

        return $options;
    }

    /**
     * Render
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $ids = array();

        foreach($settings['topics'] as $topic) {
            if (isset($topic['topic_id']) && $topic['topic_id']) {
                array_push($ids, $topic['topic_id']); // NOTE: parse int breaks dynamic values
            }
        }

        MKB_TemplateHelper::render_topics(array(
            'topics_title' => $settings['title'],
            'topics_title_color' => $settings['titleColor'],
            'topics_title_size' => $settings['titleSize'],
            'home_topics' => !empty($ids) ? implode(',', $ids) : '',
            'home_view' => $settings['view'],
            'home_layout' => $settings['columns'],
            'show_articles_count' => (bool)$settings['showCount'],
            'home_topics_show_description' => (bool)$settings['showDescription'],
            'show_all_switch' => (bool)$settings['showAll'],
            'show_all_label' => $settings['showAllLabel'],
            'home_topics_hide_children' => (bool)$settings['hideChildren'],
            'home_topics_articles_limit' => $settings['articlesLimit'],
            'home_topics_limit' => $settings['limit'],
            'articles_count_bg' => $settings['countBg'],
            'articles_count_color' => $settings['countColor'],
            'show_topic_icons' => (bool)$settings['showTopicIcons'],
            'show_article_icons' => (bool)$settings['showArticleIcons'],
            'article_icon' => preg_replace('/^fa /i', '', $settings['articleIcon']),
            'topic_color' => $settings['topicColor'],
            'force_default_topic_color' => (bool)$settings['forceTopicColor'],
            'force_default_topic_icon' => (bool)$settings['forceTopicIcon'],
            'box_view_item_bg' => $settings['boxItemBg'],
            'box_view_item_hover_bg' => $settings['boxItemHoverBg'],
            'topic_icon' => preg_replace('/^fa /i', '', $settings['topicIcon']),
            'use_topic_image' => (bool)$settings['useTopicImage'],
            'image_size' => $settings['imageSize'],
            'topic_icon_padding_top' => $settings['iconPaddingTop'],
            'topic_icon_padding_bottom' => $settings['iconPaddingBottom']
        ));
    }
}