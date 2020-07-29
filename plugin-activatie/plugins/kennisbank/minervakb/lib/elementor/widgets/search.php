<?php
/**
 * MinervaKB Elementor Search Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;

class MinervaKB_ElementorSearchWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-search';
    }

    public function get_title() {
        return __( 'KB Search', 'minerva-kb' );
    }

    public function get_icon() {
        return 'fas fa-search';
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
                'label' =>  __( 'Search title', 'minerva-kb' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'Need some help?', 'minerva-kb' ),
                'placeholder' => __('Type your title here', 'minerva-kb'),
            ]
        );

        $this->add_control(
            'placeholder',
            [
                'label' =>  __( 'Search placeholder', 'minerva-kb' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'ex.: Installation', 'minerva-kb' ),
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
                'label' => __( 'Optional: you can limit search to specific topics', 'minerva-kb' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'description' => __( 'You can leave it empty to search all topics (default).', 'minerva-kb' ),
                'title_field' => '{{{ MinervaKBElementor && MinervaKBElementor.topics && MinervaKBElementor.topics[topic_id] && MinervaKBElementor.topics[topic_id].title }}}',
            ]
        );

        $this->add_control(
            'noFocus',
            [
                'label' => __( 'Disable search field autofocus?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'minerva-kb'),
                'label_off' => __('No', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'showTip',
            [
                'label' => __( 'Show search tip?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tip',
            [
                'label' =>  __( 'Search tip (under the input)', 'minerva-kb' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'Tip: Use arrows to navigate results, ESC to focus search input', 'minerva-kb' ),
                'placeholder' => __('Type tip here', 'minerva-kb'),
                'condition' => [
                    'showTip' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'showTopic',
            [
                'label' => __( 'Show topic in results?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'minerva-kb'),
                'label_off' => __('Hide', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'topicLabel',
            [
                'label' =>  __( 'Search result topic label', 'minerva-kb' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( 'Topic', 'minerva-kb' ),
                'placeholder' => __('Type label here', 'minerva-kb'),
                'condition' => [
                    'showTopic' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'layout_section',
            array(
                'label' => __('Layout', 'minerva-kb'),
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'titleSize',
            [
                'label' => __( 'Search title font size', 'minerva-kb' ),
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
                'condition' => [
                    'title!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search__title' => 'font-size: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'theme',
            [
                'label' => __( 'Which search input theme to use?', 'minerva-kb' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'minerva',
                'label_block' => true,
                'options' => [
                    'minerva' => __( 'Minerva', 'minerva-kb' ),
                    'clean' => __( 'Clean', 'minerva-kb' ),
                    'mini' => __( 'Mini', 'minerva-kb' ),
                    'bold' => __( 'Bold', 'minerva-kb' ),
                    'invisible' => __( 'Invisible', 'minerva-kb' ),
                    'thick' => __( 'Thick', 'minerva-kb' ),
                    '3d' => __( '3d', 'minerva-kb' ),
                ],
                'description' => __( 'Use predefined styles for search bar', 'minerva-kb' )
            ]
        );

        $this->add_control(
            'minWidth',
            [
                'label' => __( 'Search input minimum width', 'minerva-kb' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
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
                        'max' => 100,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 38,
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search__input-wrap' => 'width: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'topPadding',
            [
                'label' => __( 'Search container top padding', 'minerva-kb' ),
                'type' => Controls_Manager::SLIDER,
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
                        'max' => 50,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-header' => 'padding-top: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'bottomPadding',
            [
                'label' => __( 'Search container bottom padding', 'minerva-kb' ),
                'type' => Controls_Manager::SLIDER,
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
                        'max' => 50,
                    ],
                    'rem' => [
                        'min' => 0,
                        'step' => 1,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-header' => 'padding-bottom: {{SIZE}}{{UNIT}}!important;',
                ],
            ]
        );

        $this->add_control(
            'resultsMultiline',
            [
                'label' => __( 'Allow multiline titles in results?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'minerva-kb'),
                'label_off' => __('Off', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __( 'By default, results are fit in one line. You can change this to allow multiline titles', 'minerva-kb' )
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
                'label' => __( 'Search title color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'condition' => [
                    'title!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search__title' => 'color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'borderColor',
            [
                'label' => __( 'Search wrap border color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .kb-search__input-wrap' => 'border-color: {{VALUE}}!important; background-color: {{VALUE}}!important;',
                    '{{WRAPPER}} .kb-search__input-wrap:after' => 'background: {{VALUE}}!important;',
                ],
                'description' => __( 'Not available in some themes', 'minerva-kb' ),
            ]
        );

        $this->add_control(
            'bg',
            [
                'label' => __( 'Search container background color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .kb-header' => 'background-color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'imageBgType',
            [
                'label' => __( 'Background image type', 'minerva-kb' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'none' => [
                        'title' => __('None', 'minerva-kb'),
                        'icon' => 'fa fa-ban'
                    ],
                    'media' => [
                        'title' => __('Media library', 'minerva-kb'),
                        'icon' => 'fa fa-picture-o'
                    ],
                    'url' => [
                        'title' => __('URL', 'minerva-kb'),
                        'icon' => 'fa fa-external-link-square'
                    ]
                ],
                'default' => 'none',
                'label_block' => true,
                'toggle' => true
            ]
        );

        $this->add_control(
            'imageBgMedia',
            [
                'label' => __( 'Select background image', 'minerva-kb' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'imageBgType' => 'media',
                ],
            ]
        );

        $this->add_control(
            'imageBgUrl',
            [
                'label' => __( 'Paste image URL', 'minerva-kb' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com/image.png', 'minerva-kb' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'imageBgType' => 'url',
                ],
            ]
        );

        $this->add_control(
            'addGradient',
            [
                'label' => __( 'Add gradient overlay?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'minerva-kb'),
                'label_off' => __('No', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'gradientFrom',
            [
                'label' => __( 'Container gradient from', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#00c1b6',
                'condition' => [
                    'addGradient' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search-gradient' => 'background: linear-gradient(45deg, {{VALUE}} 0%, {{gradientTo.VALUE}} 100%)!important;',
                ],
            ]
        );

        $this->add_control(
            'gradientTo',
            [
                'label' => __( 'Container gradient to', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#136eb5',
                'condition' => [
                    'addGradient' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search-gradient' => 'background: linear-gradient(45deg, {{gradientFrom.VALUE}} 0%, {{VALUE}} 100%)!important;',
                ],
            ]
        );

        $this->add_control(
            'gradientOpacity',
            [
                'label' => __( 'Background gradient opacity', 'minerva-kb' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1,
                'step' => 0.05,
                'default' => 1,
                'condition' => [
                    'addGradient' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search-gradient' => 'opacity: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'addPattern',
            [
                'label' => __( 'Add pattern overlay?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'minerva-kb'),
                'label_off' => __('No', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'patternType',
            [
                'label' => __( 'Pattern type', 'minerva-kb' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'media' => [
                        'title' => __('Media library', 'minerva-kb'),
                        'icon' => 'fa fa-picture-o'
                    ],
                    'url' => [
                        'title' => __('URL', 'minerva-kb'),
                        'icon' => 'fa fa-external-link-square'
                    ]
                ],
                'default' => 'media',
                'label_block' => true,
                'toggle' => true,
                'condition' => [
                    'addPattern' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'patternMedia',
            [
                'label' => __( 'Select pattern image', 'minerva-kb' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'addPattern' => 'yes',
                    'patternType' => 'media',
                ],
            ]
        );

        $this->add_control(
            'patternUrl',
            [
                'label' => __( 'Paste pattern image URL', 'minerva-kb' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com/image.png', 'minerva-kb' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'addPattern' => 'yes',
                    'patternType' => 'url',
                ],
            ]
        );

        $this->add_control(
            'patternOpacity',
            [
                'label' => __( 'Background pattern opacity', 'minerva-kb' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1,
                'step' => 0.05,
                'default' => 1,
                'condition' => [
                    'addPattern' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search-pattern' => 'opacity: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'tipColor',
            [
                'label' => __( 'Search tip color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
                'condition' => [
                    'showTip' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search__tip' => 'color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'topicBg',
            [
                'label' => __( 'Search results topic background', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#4a90e2',
                'condition' => [
                    'showTopic' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search__result-topic-name' => 'color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'topicColor',
            [
                'label' => __( 'Search results topic color', 'minerva-kb' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'condition' => [
                    'showTopic' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kb-search__result-topic-name' => 'color: {{VALUE}}!important;',
                ],
            ]
        );

        $this->add_control(
            'topicCustomColors',
            [
                'label' => __( 'Custom topic colors in results?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'minerva-kb'),
                'label_off' => __('No', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __( 'Topic custom color will be used as background color for topic label', 'minerva-kb' ),
                'condition' => [
                    'showTopic' => 'yes',
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
                'label' => __('Icons', 'minerva-kb'),
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'iconsLeft',
            [
                'label' => __( 'Show icons on the left?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'minerva-kb'),
                'label_off' => __('No', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'showSearchIcon',
            [
                'label' => __( 'Show search icon?', 'minerva-kb' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'minerva-kb'),
                'label_off' => __('No', 'minerva-kb'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'searchIcon',
            [
                'label' => __( 'Search icon', 'minerva-kb' ),
                'type' => Controls_Manager::ICON,
                'label_block' => true,
                'default' => 'fa fa-search',
                'condition' => [
                    'showSearchIcon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'clearIcon',
            [
                'label' => __( 'Search clear icon', 'minerva-kb' ),
                'type' => Controls_Manager::ICON,
                'label_block' => true,
                'default' => 'fa fa-times-circle',
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

    private function get_image_url($type, $media, $url) {
        if ($type === 'media') {
            return $media['url'];
        } else if ($type === 'url') {
            return $url['url'];
        }

        return '';
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $ids = array();

        foreach($settings['topics'] as $topic) {
            if (isset($topic['topic_id']) && $topic['topic_id']) {
                array_push($ids, (int)$topic['topic_id']);
            }
        }

        $container_image = $this->get_image_url(
            $settings['imageBgType'],
            $settings['imageBgMedia'],
            $settings['imageBgUrl']
        );

        $pattern_image = $this->get_image_url(
            (bool)$settings['addPattern'] ? $settings['patternType'] : 'none',
            $settings['patternMedia'],
            $settings['patternUrl']
        );

        MKB_TemplateHelper::render_search(array(
            'search_title' => $settings['title'],
            'search_title_size' => $settings['titleSize'],
            'search_theme' => $settings['theme'],
            'search_min_width' => $settings['minWidth'],
            'search_container_padding_top' => $settings['topPadding'],
            'search_container_padding_bottom' => $settings['bottomPadding'],
            'search_placeholder' => $settings['placeholder'],
            'search_topics' => !empty($ids) ? implode(',', $ids) : '',
            'disable_autofocus' => (bool)$settings['noFocus'],
            'show_search_tip' => (bool)$settings['showTip'],
            'search_tip' => $settings['tip'],
            'show_topic_in_results' => (bool)$settings['showTopic'],
            'search_results_multiline' => (bool)$settings['resultsMultiline'],
            'search_result_topic_label' => $settings['topicLabel'],
            'search_title_color' => $settings['titleColor'],
            'search_border_color' => $settings['borderColor'],
            'search_container_bg' => $settings['bg'],
            'search_container_image_bg' => $container_image,
            'add_gradient_overlay' => (bool)$settings['addGradient'],
            'search_container_gradient_from' => $settings['gradientFrom'],
            'search_container_gradient_to' => $settings['gradientTo'],
            'search_container_gradient_opacity' => $settings['gradientOpacity'],
            'add_pattern_overlay' => (bool)$settings['addPattern'],
            'search_container_image_pattern' => $pattern_image,
            'search_container_image_pattern_opacity' => $settings['patternOpacity'],
            'search_tip_color' => $settings['tipColor'],
            'search_results_topic_bg' => $settings['topicBg'],
            'search_results_topic_color' => $settings['topicColor'],
            'search_results_topic_use_custom' => (bool)$settings['topicCustomColors'],
            'search_icons_left' => (bool)$settings['iconsLeft'],
            'show_search_icon' => (bool)$settings['showSearchIcon'],
            'search_icon' => $settings['searchIcon'],
            'search_clear_icon' => $settings['clearIcon']
        ));
    }
}
