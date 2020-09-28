<?php
/**
 * Project: MinervaKB.
 * Copyright: 2015-2017 @KonstruktStudio
 */

// breadcrumbs
class MinervaKB_TopicBreadcrumbsTmplShortcode extends KST_Shortcode implements KST_Shortcode_Interface {
    protected $ID = 'tmpl-topic-breadcrumbs';
    protected $name = 'KB Topic Page Breadcrumbs Template';
    protected $description = 'Template part to display breadcrumbs for current topic';
    protected $icon = 'fa fa-code';
    protected $has_content = false;

    public function render($atts, $content = '') {
        if (!MinervaKB_App::getInstance()->info->is_topic()) {
            echo do_shortcode('[mkb-info]<strong>KB Topic Breadcrumbs</strong> will only be displayed inside topic template page[/mkb-info]');
            return;
        }

        $term = get_term_by( 'id', get_queried_object_id(), MKB_Options::option( 'article_cpt_category' ) );

        MKB_TemplateHelper::breadcrumbs( $term, MKB_Options::option( 'article_cpt_category' ) );
    }
}

// title
class MinervaKB_TopicTitleTmplShortcode extends KST_Shortcode implements KST_Shortcode_Interface {
    protected $ID = 'tmpl-topic-title';
    protected $name = 'KB Topic Page Title Template';
    protected $description = 'Template part to display title for current topic';
    protected $icon = 'fa fa-code';
    protected $has_content = false;

    public function render($atts, $content = '') {
        if (!MinervaKB_App::getInstance()->info->is_topic()) {
            echo do_shortcode('[mkb-info]<strong>KB Topic Title</strong> will only be displayed inside topic template page[/mkb-info]');
            return;
        }

        // TODO: maybe text only
        if (MKB_Options::option('topic_customize_title')) {
            ?><h1 class="mkb-page-title"><?php
            single_term_title(MKB_Options::option('topic_custom_title_prefix'));
            ?></h1><?php
        } else {
            the_archive_title('<h1 class="mkb-page-title">', '</h1>');
        }
    }
}

// description
class MinervaKB_TopicDescriptionTmplShortcode extends KST_Shortcode implements KST_Shortcode_Interface {
    protected $ID = 'tmpl-topic-description';
    protected $name = 'KB Topic Page Description Template';
    protected $description = 'Template part to display description for current topic';
    protected $icon = 'fa fa-code';
    protected $has_content = false;

    public function render($atts, $content = '') {
        if (!MinervaKB_App::getInstance()->info->is_topic()) {
            echo do_shortcode('[mkb-info]<strong>KB Topic Description</strong> will only be displayed inside topic template page[/mkb-info]');
            return;
        }

        // Note: can be rendered inside other elements, must not be <div>
        the_archive_description('<span class="mkb-taxonomy-description">', '</span>');
    }
}

// topic children
class MinervaKB_TopicChildrenTmplShortcode extends KST_Shortcode implements KST_Shortcode_Interface {
    protected $ID = 'tmpl-topic-children';
    protected $name = 'KB Topic Page Children Template';
    protected $description = 'Template part to display children for current topic';
    protected $icon = 'fa fa-code';
    protected $has_content = false;

    public function render($atts, $content = '') {
        if (!MinervaKB_App::getInstance()->info->is_topic()) {
            echo do_shortcode('[mkb-info]<strong>KB Topic Children</strong> will only be displayed inside topic template page[/mkb-info]');
            return;
        }

        MKB_TemplateHelper::topic_tmpl_children();
    }
}

// topic search
class MinervaKB_TopicSearchTmplShortcode extends KST_Shortcode implements KST_Shortcode_Interface {
    protected $ID = 'tmpl-topic-search';
    protected $name = 'KB Topic Page Search Template';
    protected $description = 'Template part to display search for current topic';
    protected $icon = 'fa fa-code';
    protected $has_content = false;

    public function render($atts, $content = '') {
        if (!MinervaKB_App::getInstance()->info->is_topic()) {
            echo do_shortcode('[mkb-info]<strong>KB Topic Search</strong> will only be displayed inside topic template page[/mkb-info]');
            return;
        }

        $term = get_queried_object();

        if (!MinervaKB::topic_option($term, 'topic_no_search_switch')) {
            MKB_TemplateHelper::topic_tmpl_search();
        }
    }
}

// loop
class MinervaKB_TopicLoopTmplShortcode extends KST_Shortcode implements KST_Shortcode_Interface {
	protected $ID = 'tmpl-topic-loop';
	protected $name = 'KB Topic Page Loop Template';
	protected $description = 'Template part to display article list for current topic';
	protected $icon = 'fa fa-code';
	protected $has_content = false;

	public function render($atts, $content = '') {
        if (!MinervaKB_App::getInstance()->info->is_topic()) {
            echo do_shortcode('[mkb-info]<strong>KB Topic Articles</strong> will only be displayed inside topic template page[/mkb-info]');
            return;
        }

        MKB_TemplateHelper::topic_tmpl_loop();
	}
}

// pagination
class MinervaKB_TopicPaginationTmplShortcode extends KST_Shortcode implements KST_Shortcode_Interface {
    protected $ID = 'tmpl-topic-pagination';
    protected $name = 'KB Topic Page Pagination Template';
    protected $description = 'Template part to display article list pagination for current topic';
    protected $icon = 'fa fa-code';
    protected $has_content = false;

    public function render($atts, $content = '') {
        if (!MinervaKB_App::getInstance()->info->is_topic()) {
            echo do_shortcode('[mkb-info]<strong>KB Topic Pagination</strong> will only be displayed inside topic template page[/mkb-info]');
            return;
        }

        MKB_TemplateHelper::topic_tmpl_pagination();
    }
}