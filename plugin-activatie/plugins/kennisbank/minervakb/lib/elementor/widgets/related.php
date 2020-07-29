<?php
/**
 * MinervaKB Elementor Related Articles Widget
 * Copyright: 2015-2020 @KonstruktStudio
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;

class MinervaKB_ElementorRelatedWidget extends Widget_Base {

    public function get_name() {
        return 'minervakb-related';
    }

    public function get_title() {
        return __( 'KB Related Articles', 'minerva-kb' );
    }

    public function get_icon() {
        return 'fas fa-network-wired';
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

        $repeater = new Repeater();

        $repeater->add_control(
            'article_id',
            [
                'label' => __('Search & Select KB Article', 'minerva-kb'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_all_articles(),
                'label_block' => true,
                'multiple' => false
            ]
        );

        $this->add_control(
            'articles',
            [
                'label' => __( 'Related Articles List', 'minerva-kb' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'title_field' => '{{{ MinervaKBElementor && MinervaKBElementor.articles && MinervaKBElementor.articles[article_id] && MinervaKBElementor.articles[article_id].title }}}',
            ]
        );

        $this->end_controls_section();
    }

    private function get_all_articles() {
        $posts = get_posts([
            'post_type' => MKB_Options::option('article_cpt'),
            'post_status' => 'publish',
            'posts_per_page' => '-1'
        ]);

        if (!empty($posts)) {
            return wp_list_pluck($posts, 'post_title', 'ID');
        }

        return [];
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $ids = array();

        foreach($settings['articles'] as $article) {
            if (isset($article['article_id']) && $article['article_id']) {
                array_push($ids, (int)$article['article_id']);
            }
        }

        if ($ids && is_array($ids) && !empty($ids)):
            ?>
            <div class="mkb-related-content">
                <div class="mkb-related-content-title"><?php echo esc_html(MKB_Options::option('related_content_label')); ?></div>
                <ul class="mkb-related-content-list">
                    <?php foreach($ids as $index => $id):
                        if ( empty($id) || !is_string( get_post_status( $id )) ) {
                            continue;
                        }
                        ?>
                        <li><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php echo esc_html(get_the_title($id)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php
        endif;
    }

    protected function _content_template() {
        ?>
        <# if (settings.articles) { #>
            <div class="mkb-related-content">
                <div class="mkb-related-content-title"><?php esc_html_e(MKB_Options::option('related_content_label')); ?></div>
                <ul class="mkb-related-content-list">
                <# _.each(settings.articles, function(item, index) { #>
                    <# if (item && item.article_id) { #>
                        <li><a href="{{{ MinervaKBElementor.articles[item.article_id].permalink }}}">{{{ MinervaKBElementor.articles[item.article_id].title }}}</a></li>
                    <# } #>
                <# }); #>
                </ul>
            </div>
        <# } #>
        <?php
    }
}