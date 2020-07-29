<?php
/**
 * Project: MinervaKB.
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MinervaKB_GlossaryShortcode extends KST_Shortcode implements KST_Shortcode_Interface {

	protected $ID = 'glossary';
	protected $name = 'Glossary';
	protected $description = 'List of glossary entries';
	protected $icon = 'fa fa-comment-o';

	public function render($atts, $content = '') {
        $query_args = array(
            'post_type' => 'mkb_glossary',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $topic_loop = new WP_Query( $query_args );
        $glossary_list = array();
        $current_letter = null;

        if ($topic_loop->have_posts()):
            while ( $topic_loop->have_posts() ) : $topic_loop->the_post();

                $title = get_the_title();
                $first_letter = mb_substr($title, 0, 1);
                $first_letter = function_exists('mb_strtolower') ? mb_strtolower($first_letter) : strtolower($first_letter);

                if ($first_letter !== $current_letter) {
                    $current_letter = $first_letter;

                    $glossary_list[$current_letter] = array();
                }

                array_push($glossary_list[$current_letter], get_the_ID());
            endwhile;
        endif;

        wp_reset_postdata();

        if (!sizeof($glossary_list)) {
            return;
        }

        ?>
        <div class="mkb-glossary-list-wrapper">
            <ul id="mkb_glossary_toc" class="mkb-glossary-list-toc"><?php
                foreach ($glossary_list as $letter => $entries):
                    $uppercase_letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
                ?><li><a href="#mkb_glossary_letter_<?php esc_attr_e($letter); ?>"><?php esc_html_e($uppercase_letter); ?></a></li><?php
                endforeach; ?>
            </ul><!--.mkb-glossary-list-toc-->
            <div class="mkb-glossary-list">
            <?php

            // glossary list
            foreach ($glossary_list as $letter => $letter_group):
                $uppercase_letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
                ?>
                <div class="mkb-glossary-letter-group">
                    <h2 id="mkb_glossary_letter_<?php esc_attr_e($letter); ?>"><?php esc_html_e($uppercase_letter); ?></h2><?php
                    foreach ($letter_group as $id): ?>
                    <div class="mkb-glossary-term-entry">
                        <h3><?php esc_html_e(get_the_title($id)); ?></h3>
                        <div class="mkb-glossary-term-entry-content"><?php echo apply_filters('the_content', get_post_field('post_content', $id)); ?></div>
                        <a href="#mkb_glossary_toc"><?php esc_html_e(MKB_Options::option('glossary_back_to_top')); ?></a>
                    </div>
                    <?php
                    endforeach; ?>
                </div>
            <?php endforeach; ?>

            </div><!--.mkb-glossary-list-->
        </div><!--.mkb-glossary-list-wrapper-->
        <?php
	}
}