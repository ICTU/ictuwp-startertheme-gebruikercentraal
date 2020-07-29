<?php
/**
 *
 * Search Widget
 *
 */
if( ! class_exists( 'Pressapps_Knowledge_Base_Widget_Search' ) ) {
  class Pressapps_Knowledge_Base_Widget_Search extends WP_Widget {

    function __construct() {

      $widget_ops     = array(
        'classname'   => 'knowledge_base_search',
        'description' => 'Search knowledge base articles.'
      );

      parent::__construct( 'knowledge_base_search', 'Knowledge Base Search', $widget_ops );

    }

    function widget( $args, $instance ) {

      global $pakb_helper;

      extract( $args );

      $title = get_option( 'widget_' . $args['widget_id'] . '_search_title' );

      echo $before_widget;

      if ( ! empty( $title ) ) {
        echo $before_title . $title . $after_title;
      }

      echo $pakb_helper->the_search();

      echo $after_widget;

    }

    function update( $new_instance, $old_instance ) {

    }

    function form( $instance ) {

    }
  }
}

/**
 *
 * Categories Widget
 *
 */
if( ! class_exists( 'Pressapps_Knowledge_Base_Widget_Categories' ) ) {
  class Pressapps_Knowledge_Base_Widget_Categories extends WP_Widget {

    function __construct() {

      $widget_ops     = array(
        'classname'   => 'knowledge_base_categories',
        'description' => 'Display list of knowledge base categories.'
      );

      parent::__construct( 'knowledge_base_categories', 'Knowledge Base Categories', $widget_ops );

    }

    function widget( $args, $instance ) {

      global $pakb_helper;

      extract( $args );

      $title = get_option( 'widget_' . $args['widget_id'] . '_categories_title' );

      echo $before_widget;

      if ( ! empty( $title ) ) {
        echo $before_title . $title . $after_title;
      }

			$display_count 			= get_option( 'widget_' . $args['widget_id'] . '_categories_count' );

			$terms_args['hide_empty']	= 1;
			$terms_args['order'] 			= get_option( 'widget_' . $args['widget_id'] . '_categories_order' );
			$terms_args['orderby'] 		= get_option( 'widget_' . $args['widget_id'] . '_categories_orderby' );
			$terms_args['number'] 		= get_option( 'widget_' . $args['widget_id'] . '_categories_number' );
			$terms_args['parent'] 		= 0;

			$terms = get_terms( 'knowledgebase_category', $terms_args );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

			    echo '<ul class="uk-list uk-list-large pakb-secondary-color pakb-link pakb-widget-categories">';

			    foreach ( $terms as $term ) {

            $icon = get_term_meta($term->term_id, 'icon', true);

  					$count = ( $display_count ) ? ' (' . $term->count . ')' : '';

			    	echo '<li class="uk-position-relative">';
            if ( !empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
          		echo '<span class="pakb-list-icon">' . @file_get_contents( PAKB_ICON_DIR . $icon . '.svg' ) . '</span>';
          	} elseif ( empty( $icon ) && $pakb_helper->is_category_icon_enabled() ) {
          		echo '<span class="pakb-list-icon">' . @file_get_contents( PAKB_ICON_DIR . 'ios-folder.svg' ) . '</span>';
          	}
            echo '<a href="' . get_term_link( $term ) . '" title="' . sprintf( $term->name ) . '">' . $term->name . $count . '</a></li>';
			    }

			    echo '</ul>';
			}

      echo $after_widget;

    }

    function update( $new_instance, $old_instance ) {

    }

    function form( $instance ) {

    }
  }
}


/**
 *
 * Articles Widget
 *
 */
if( ! class_exists( 'Pressapps_Knowledge_Base_Widget_Articles' ) ) {
  class Pressapps_Knowledge_Base_Widget_Articles extends WP_Widget {

    function __construct() {

      $widget_ops     = array(
        'classname'   => 'knowledge_base_articles',
        'description' => 'Display list of knowledge base articles.'
      );

      parent::__construct( 'knowledge_base_articles', 'Knowledge Base Articles', $widget_ops );

    }

    function widget( $args, $instance ) {

      extract( $args );

      $title = get_option( 'widget_' . $args['widget_id'] . '_articles_title' );

      echo $before_widget;

      if ( ! empty( $title ) ) {
        echo $before_title . $title . $after_title;
      }

      global $post, $pakb_helper;

			$wpq_args['post_type'] 		= 'knowledgebase';
			$wpq_args['post_status'] 	= 'publish';
			$wpq_args['orderby'] 		  = get_option( 'widget_' . $args['widget_id'] . '_articles_orderby' );
			$wpq_args['order'] 			  = get_option( 'widget_' . $args['widget_id'] . '_articles_order' );
			$wpq_args['posts_per_page'] = get_option( 'widget_' . $args['widget_id'] . '_articles_posts_per_page' );

			if ( $wpq_args['orderby'] == 'meta_value_num') {
				$wpq_args['meta_key']   = '_votes_likes';
			}

			if ( get_option( 'widget_' . $args['widget_id'] . '_articles_filter' ) == 'category' ) {
				$wpq_args['tax_query'] 		= array(
					array(
						'taxonomy'         => 'knowledgebase_category',
						'field'            => 'ID',
						'terms'            => get_option( 'widget_' . $args['widget_id'] . '_articles_id' ),
						'include_children' => true
					)
				);
			} elseif ( get_option( 'widget_' . $args['widget_id'] . '_articles_filter' ) == 'tag' ) {
				$wpq_args['tax_query'] 		= array(
					array(
						'taxonomy'         => 'knowledgebase_tags',
						'field'            => 'ID',
						'terms'            => array( get_option( 'widget_' . $args['widget_id'] . '_articles_id' ) ),
						'include_children' => true
					)
				);
			}

			$items = new WP_Query( $wpq_args );

			if ( 0 == $items->found_posts ) {

				_e( 'There are no knowledge base articles.', 'pressapps-knowledge-base' );

			} else { ?>

                <ul class="uk-list uk-list-large pakb-secondary-color pakb-link">
                	<?php foreach ( $items->posts as $item ) { ?>
                		<li><a href="<?php echo get_permalink( $item->ID ); ?>"><?php echo esc_attr( $item->post_title ); ?></a></li>
                	<?php } ?>
                </ul>
			<?php }

			wp_reset_postdata();

      echo $after_widget;

    }

    function update( $new_instance, $old_instance ) {

    }

    function form( $instance ) {

    }
  }
}

if ( ! function_exists( 'pressapps_knowledge_base_widget_init' ) ) {
  function pressapps_knowledge_base_widget_init() {
    register_widget( 'Pressapps_Knowledge_Base_Widget_Search' );
		register_widget( 'Pressapps_Knowledge_Base_Widget_Categories' );
    register_widget( 'Pressapps_Knowledge_Base_Widget_Articles' );
  }
  add_action( 'widgets_init', 'pressapps_knowledge_base_widget_init', 2 );
}
