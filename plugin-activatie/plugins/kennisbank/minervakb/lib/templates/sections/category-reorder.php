<?php

$term = get_queried_object();
$category_blocks = explode(',', MKB_Options::option('topic_page_elements_order'));

foreach($category_blocks as $block):
    switch($block) {
        case 'title':
            MKB_TemplateHelper::topic_tmpl_title_description(true);
            break;

        case 'search':
            if (!MinervaKB::topic_option($term, 'topic_no_search_switch')) {
                MKB_TemplateHelper::topic_tmpl_search();
            }
            break;

        case 'breadcrumbs':
            if (!MinervaKB::topic_option($term, 'topic_no_breadcrumbs_switch')) {
                MKB_TemplateHelper::topic_tmpl_breadcrumbs();
            }
            break;

        case 'children':
            MKB_TemplateHelper::topic_tmpl_children();
            break;

        case 'articles':
            MKB_TemplateHelper::topic_tmpl_loop();
            break;

        case 'pagination':
            MKB_TemplateHelper::topic_tmpl_pagination();
            break;

        default:
            break;
    }
endforeach;
