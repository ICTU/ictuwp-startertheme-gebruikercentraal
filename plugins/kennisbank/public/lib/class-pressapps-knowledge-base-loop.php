<?php

class PAKB_Loop {

	/**
	 * Initialize PAKB_Loop Class.
	 *
	 */
	public function __construct() {
		// Loop The Content Filter
		add_filter( 'pakb_the_content', 'wptexturize' );
		add_filter( 'pakb_the_content', 'convert_smilies' );
		add_filter( 'pakb_the_content', 'convert_chars' );
		add_filter( 'pakb_the_content', 'wpautop' );
		add_filter( 'pakb_the_content', 'shortcode_unautop' );
		add_filter( 'pakb_the_content', 'prepend_attachment' );
		add_filter( 'pakb_the_content', array( $this, 'the_content_filter' ) );
		add_filter( 'pakb_the_content', array( $GLOBALS['wp_embed'], 'autoembed' ) );

		// Loop The Title Filter
		add_filter( 'pakb_the_title', 'wptexturize' );
		add_filter( 'pakb_the_title', 'convert_chars' );
		add_filter( 'pakb_the_title', 'trim' );
	}

	/**
	 * Extends the have_posts of the $wp_query.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function have_posts() {

		global $pakb_query;

		return $pakb_query->have_posts();
	}

	/**
	 * Extends the in_the_loop of the $wp_query.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function in_the_loop() {
		global $pakb_query;

		return $pakb_query->in_the_loop;
	}

	/**
	 * Extends the the_post loop of the $wp_query.
	 *
	 * @since 1.0.0
	 */
	public function the_post() {
		global $pakb_query;

		$pakb_query->the_post();
	}

	/**
	 * Echo the ID from the get_the_ID function.
	 *
	 * @since 1.0.0
	 */
	public function the_ID() {
		echo $this->get_the_ID();
	}

	/**
	 * Get the current ID.
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 *
	 * @return int|null|string
	 */
	public function get_the_ID( $post_id = 0 ) {
		global $wp_query, $pakb_query;

		if ( ! empty( $post_id ) && is_numeric( $post_id ) ) {
			$knowledgebase_id = $post_id;
		} elseif ( ! empty( $pakb_query->in_the_loop ) && isset( $pakb_query->post->ID ) ) {
			$knowledgebase_id = $pakb_query->post->ID;
		} else {
			$knowledgebase_id = null;
		}

		return $knowledgebase_id;

	}

	/**
	 * Displays the permalink.
	 *
	 * @since 1.0.0
	 */
	public function the_permalink() {
		echo $this->get_the_permalink();
	}

	/**
	 * Get the current permalink.
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 *
	 * @return mixed|void
	 */
	public function get_the_permalink( $post_id = 0 ) {

		if ( $post_id == 0 ) {
			$post_id = get_post( $this->get_the_ID() );
		}

		return apply_filters( 'pakb_the_permalink', get_permalink( $post_id ) );
	}

	/**
	 * Echoes the title.
	 *
	 * @since 1.0.0
	 */
	public function the_title() {
		echo $this->get_the_title();
	}

	/**
	 * Gets the title and run into a filter
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_the_title() {

		$post = get_post( $this->get_the_ID() );

		return apply_filters( 'pakb_the_title', $post->post_title );

	}

	/**
	 * Echoes the content.
	 *
	 * @since 1.0.0
	 */
	public function the_content() {
		echo $this->get_the_content();
	}

	/**
	 * Get the content.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_the_content() {

		$post = get_post( $this->get_the_ID() );

		if ( $this->post_password_required( $post ) ) {
			return get_the_password_form( $post );
		}

		return apply_filters( 'pakb_the_content', $post->post_content );

	}

	/**
	 * Check if password is required for the post.
	 *
	 * @since 1.0.0
	 * @param null $post
	 *
	 * @return bool
	 */
	public function post_password_required( $post = null ) {

		if ( empty( $post ) ) {
			$post = get_post( $this->get_the_ID() );
		}

		return post_password_required( $post );

	}

	/**
	 * Echoes the category.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	public function the_category( $args = array() ) {
		echo $this->get_the_category( $this->get_the_ID(), $args );
	}

	/**
	 * Gets the category.
	 *
	 * @since 1.0.0
	 * @param int   $knowledgebase_id
	 * @param array $args
	 *
	 * @return array|string|null|\WP_Error
	 */
	public function get_the_category( $knowledgebase_id = 0, $args = array() ) {

		$default = array(
			'output'     => 'string',
			'hyperlink'  => true,
			'before_cat' => '&nbsp;',
			'after_cat'  => '',
			'separator'  => ',',
		);

		$args = array_merge( $default, $args );

		if ( $knowledgebase_id == 0 ) {
			$knowledgebase_id = $this->get_the_ID();
		}

		$categories = wp_get_post_terms( $knowledgebase_id, 'knowledgebase_category' );

		switch ( $args['output'] ) {
			case 'string':
				$temp = '<span>' . __( 'in', 'pressapps-knowledge-base' ) . '</span>';

				if ( ! is_array( $categories ) ) {
					return $temp;
				}

				for ( $i = 0; $i < count( $categories ); $i ++ ) {

					$cat = $categories[ $i ];

					$temp .= $args['before_cat'];
					if ( $args['hyperlink'] ) {
						$temp .= "<a href=\"" . get_term_link( $cat, 'knowledgebase_category' ) . "\">";
					}
					$temp .= $cat->name;
					if ( $args['hyperlink'] ) {
						$temp .= "</a>";
					}
					$temp .= $args['after_cat'];

					if ( count( $categories ) != ( $i + 1 ) ) {
						$temp .= $args['separator'];
					}
				}

				return $temp;

				break;
			case 'array':
				return $categories;
				break;
		}

		return null;
	}

	/**
	 * Echoes the tag.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	public function the_tags( $args = array() ) {
		echo $this->get_the_tags( $this->get_the_ID(), $args );
	}

	/**
	 * Gets the tag.
	 *
	 * @since 1.0.0
	 * @param int   $knowledgebase_id
	 * @param array $args
	 *
	 * @return array|string|null|\WP_Error
	 */
	public function get_the_tags( $knowledgebase_id = 0, $args = array() ) {
		$default = array(
			'output'     => 'string',
			'hyperlink'  => true,
			'before_tag' => '&nbsp;',
			'after_tag'  => '',
			'separator'  => ',',
		);

		$args = array_merge( $default, $args );

		if ( $knowledgebase_id == 0 ) {
			$knowledgebase_id = $this->get_the_ID();
		}

		$tags = wp_get_post_terms( $knowledgebase_id, 'knowledgebase_tags' );

		if ( count( $tags ) > 0 ) {

			switch ( $args['output'] ) {
				case 'string':
					$temp = '<span>' . __( 'Tags:', 'pressapps-knowledge-base' ) . '</span>';

					if ( ! is_array( $tags ) ) {
						return $temp;
					}

					for ( $i = 0; $i < count( $tags ); $i ++ ) {

						$tag = $tags[ $i ];

						$temp .= $args['before_tag'];
						if ( $args['hyperlink'] ) {
							$temp .= "<a href=\"" . get_term_link( $tag, 'knowledgebase_tags' ) . "\" >";
						}
						$temp .= $tag->name;
						if ( $args['hyperlink'] ) {
							$temp .= "</a>";
						}
						$temp .= $args['after_tag'];

						if ( count( $tags ) != ( $i + 1 ) ) {
							$temp .= $args['separator'];
						}

					}

					return $temp;
					break;
				case 'array':
					return $tags;
					break;
			}

		}

		return null;
	}

	/**
	 * Echoes the created time.
	 *
	 * @since 1.0.0
	 */
	public function the_created_time() {
		echo $this->get_the_created_time();
	}

	/**
	 * Get the created time.
	 *
	 * @since 1.0.0
	 * @return mixed|void
	 */
	public function get_the_created_time() {
		$post = get_post( $this->get_the_ID() );

		return apply_filters( 'pakb_the_created_time', $post->post_date_gmt );
	}

	/**
	 *Echoes the modified time.
	 *
	 * @since 1.0.0
	 */
	public function the_modified_time() {
		echo $this->get_the_modified_time();
	}

	/**
	 * Get the modified time.
	 *
	 * @since 1.0.0
	 * @return mixed|void
	 */
	public function get_the_modified_time() {
		$post = get_post( $this->get_the_ID() );

		return apply_filters( 'pakb_the_created_time', $post->post_modified_gmt );
	}

	/**
	 * Check if archive, extends the is_archive of the $wp_query.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_archive() {
		global $pakb_query;
		if ( is_admin() ) {
			return false;
		}

		return $pakb_query->is_archive();
	}

	/**
	 * Extends the is_single function of the $wp_query.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_single() {
		global $pakb_query;

		if ( is_admin() ) {
			return false;
		}

		return $pakb_query->is_single();
	}

	/**
	 * Checks if it is under knowledgebase_category
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_category() {
		global $pakb_query;

		return $pakb_query->is_tax( 'knowledgebase_category' );
	}

	/**
	 * Check if it is under knowledgebase_tags
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_tag() {
		global $pakb_query;

		return $pakb_query->is_tax( 'knowledgebase_tags' );
	}

	/**
	 * Extends the get_queried_object function of the $wp_query.
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public function get_queried_object() {
		global $pakb_query;

		return $pakb_query->get_queried_object();
	}

	/**
	 * Gets the ID of the global $pakb_comment.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_the_update_ID() {
		global $pakb_comment;

		return ( ! empty( $pakb_comment ) ) ? $pakb_comment->ID : '0';
	}

	/**
	 * Setsup the comment.
	 *
	 * @since 1.0.0
	 * @param $comment
	 */
	public function setup_comment( $comment ) {

		global $pakb_comment;

		$pakb_comment = $comment;

	}

	/**
	 * Echoes the update list.
	 *
	 * @since 1.0.0
	 */
	// public function the_update() {
	// 	global $pakb_helper;
	// 	echo $pakb_helper->load_file( $pakb_helper->get_template_files( 'update-list' ) );
	// }

	/**
	 * Echoes the update content.
	 *
	 * @since 1.0.0
	 */
	public function the_update_content() {

		echo $this->get_the_update_content();
	}

	/**
	 * Get the updated content.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_the_update_content() {

		global $pakb_comment;

		return ( ( isset( $pakb_comment->comment_content ) ) ? $pakb_comment->comment_content : '' );
	}

	/**
	 * Echoes the avatar.
	 *
	 * @since 1.0.0
	 * @param null $size
	 */
	public function the_avatar( $size = null ) {
		$size = ( ( is_null( $size ) ) ? 64 : $size );
		echo $this->get_the_avatar( $size );
	}

	/**
	 * Gets the avatar, extends the get_avatar function of wordpress.
	 *
	 * @since 1.0.0
	 * @param int $size
	 *
	 * @return false|string
	 */
	public function get_the_avatar( $size = 64 ) {
		global $pakb_comment;

		if ( empty( $pakb_comment ) ) {
			return '';
		}

		return get_avatar( $pakb_comment->user_id, $size );
	}

	/**
	 * Process category.
	 *
	 * @since 1.0.0
	 */
	public function process_cat() {
		global $pakb_cat, $pakb_helper;

		$pakb_cat['main'] = get_term_by( 'slug', get_query_var( 'knowledgebase_category' ), 'knowledgebase_category' );

		if ( get_option( 'options_pakb_content_order' ) !== 'default' ) {
			$orderby = $pakb_helper->reorder_option( true );

			$pakb_cat['child'] = get_terms( 'knowledgebase_category', array(
				'parent'     => $pakb_cat['main']->term_id,
				'orderby'    => $orderby,
				'order'      => 'ASC',
				'hide_empty' => 0
			) );
		} else {
			$pakb_cat['child'] = get_terms( 'knowledgebase_category', array(
				'parent'     => $pakb_cat['main']->term_id,
				'hide_empty' => 0
			) );
		}

	}

	/**
	 * Process tag.
	 *
	 * @since 1.0.0
	 */
	public function process_tag() {
		global $pakb_tag;

		$pakb_tag['main'] = get_term_by( 'slug', get_query_var( 'knowledgebase_tags' ), 'knowledgebase_tags' );

	}

	/**
	 * Process Knowledgebase.
	 *
	 * @since 1.0.0
	 */
	public function process_kbpage( $include = [] ) {
		global $pakb_cat, $pakb_helper;

		$pakb_cat['main'] = '';

		if ( get_option( 'options_pakb_content_order' ) !== 'default' ) {
			$orderby = $pakb_helper->reorder_option( true );

			$pakb_cat['child'] = get_terms( 'knowledgebase_category', array(
				'parent'     => 0,
				'hide_empty' => 0,
				'orderby'    => $orderby,
				'order'      => 'ASC',
				'include'		 => $include
			) );
		} else {
			$pakb_cat['child'] = get_terms( 'knowledgebase_category', array(
				'parent' 		 => 0,
				'hide_empty' => 0,
				'include'		 => $include
			) );
		}
	}

	/**
	 * Get Categories.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_cats() {
		global $pakb_cat;

		return $pakb_cat['child'];
	}

	/**
	 * Check if has sub category.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function has_sub_cat() {

		global $pakb_cat;

		return ( count( (array) $pakb_cat['child'] ) != 0 ) ? true : false;
	}

	/**
	 * Setup Category.
	 *
	 * @since 1.0.0
	 * @param $cat
	 */
	public function setup_cat( $cat, $children = true ) {

		global $pakb_cat_obj, $pakb_helper;

		$pakb_cat_obj['cat'] = $cat;

		if ( get_option( 'options_pakb_content_order' ) !== 'default' ) {
			$orderby = $pakb_helper->reorder_option();

			$args = array(
				'post_type' => 'knowledgebase',
				'orderby'   => $orderby,
				'order'     => 'ASC',
				'tax_query' => array(
					array(
						'taxonomy'         => 'knowledgebase_category',
						'field'            => 'id',
						'terms'            => $cat->term_id,
						'include_children' => $children
					)
				),
			);
		} else {
			$args = array(
				'post_type'   => 'knowledgebase',
				'numberposts' => - 1,
				'tax_query'   => array(
					array(
						'taxonomy'         => 'knowledgebase_category',
						'field'            => 'id',
						'terms'            => $cat->term_id,
						'include_children' => $children
					)
				),
			);
		}

		if ( is_search() ) {
			$args['posts_per_page'] = - 1;
		} else {
			$args['posts_per_page'] = get_option( 'options_pakb_posts_per_cat' );
		}

		$pakb_cat_obj['posts']        = new WP_Query( $args );
		$pakb_cat_obj['actual_count'] = $pakb_cat_obj['posts']->found_posts;

	}

	/**
	 * Echoes the category.
	 *
	 * @since 1.0.0
	 */
	public function print_the_cat() {
		global $pakb_helper;
		echo $pakb_helper->load_file( $pakb_helper->get_template_files( 'category' ) );

	}

	/**
	 * Echoes the category link.
	 *
	 * @since 1.0.0
	 */
	public function the_cat_link() {
		global $pakb_cat_obj;

		echo get_term_link( $pakb_cat_obj['cat'] );
	}

	/**
	 * Get the category link.
	 *
	 * @since 1.0.0
	 * @return string|\WP_Error
	 */
	public function get_cat_link() {
		global $pakb_cat_obj;

		return get_term_link( $pakb_cat_obj['cat'] );
	}

	/**
	 * Get the category term_id.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_cat_id() {

		global $pakb_cat_obj;

		return $pakb_cat_obj['cat']->term_id;
	}

	/**
	 * Echoes the category name.
	 *
	 * @since 1.0.0
	 */
	public function the_cat_name() {

		global $pakb_cat_obj;

		echo $pakb_cat_obj['cat']->name;
	}

	/**
	 * Get the category name.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_cat_name() {

		global $pakb_cat_obj;

		return $pakb_cat_obj['cat']->name;
	}

	/**
	 * Echoes the category slug.
	 *
	 * @since 4.0.0
	 */
	public function the_cat_slug() {

		global $pakb_cat_obj;

		echo $pakb_cat_obj['cat']->slug;
	}

	/**
	 * Get the category slug.
	 *
	 * @since 4.0.0
	 * @return mixed
	 */
	public function get_cat_slug() {

		global $pakb_cat_obj;

		return $pakb_cat_obj['cat']->slug;
	}

	/**
	 * Echoes the category description.
	 *
	 * @since 1.0.0
	 */
	public function the_cat_description() {

		global $pakb_cat_obj;

		echo $pakb_cat_obj['cat']->description;
	}

	/**
	 * Get the category description.
	 *
	 * @since 1.0.0
	 */
	public function get_cat_description() {

		global $pakb_cat_obj;

		return $pakb_cat_obj['cat']->description;
	}

	/**
	 * Echoes the category count.
	 *
	 * @since 1.0.0
	 */
	public function the_cat_count() {

		global $pakb_cat_obj;

		echo $pakb_cat_obj['actual_count'];
	}

	/**
	 * Return the category count.
	 *
	 * @since 1.0.0
	 *
	 * @param string $before - character before the count
	 * @param string $after - character after the count
	 *
	 * @return string
	 */
	public function get_the_cat_count( $before = '', $after = '' ) {

		global $pakb_cat_obj;

		return  $before . $pakb_cat_obj['actual_count'] . $after;
	}

	/**
	 * Extends the have_posts function for the $pakb_cat_obj.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function subcat_have_posts() {

		global $pakb_cat_obj;

		return $pakb_cat_obj['posts']->have_posts();
	}

	/**
	 * Extends the in_the_loop function for the $pakb_cat_obj.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function subcat_in_the_loop() {
		global $pakb_cat_obj;

		return $pakb_cat_obj['posts']->in_the_loop;
	}

	/**
	 * Extends the_post function for the $pakb_cat_obj.
	 *
	 * @since 1.0.0
	 */
	public function subcat_the_post() {
		global $pakb_cat_obj;

		$pakb_cat_obj['posts']->the_post();
	}

	/**
	 * Echoes the sub category ID.
	 *
	 * @since 1.0.0
	 */
	public function subcat_the_ID() {
		echo $this->subcat_get_the_ID();
	}

	/**
	 * Gets the sub category ID.
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 *
	 * @return int|string
	 */
	public function subcat_get_the_ID( $post_id = 0 ) {
		global $wp_query, $pakb_cat_obj;


		if ( ! empty( $post_id ) && is_numeric( $post_id ) ) {
			$knowledgebase_id = $post_id;
		} elseif ( ! empty( $pakb_cat_obj['posts']->in_the_loop ) && isset( $pakb_cat_obj['posts']->post->ID ) ) {
			$knowledgebase_id = $pakb_cat_obj['posts']->post->ID;
		} else {
			$knowledgebase_id = null;
		}

		return $knowledgebase_id;

	}

	/**
	 * Echoes the sub category permalink.
	 *
	 * @since 1.0.0
	 */
	public function subcat_the_permalink() {
		echo $this->subcat_get_permalink();
	}

	/**
	 * Gets the sub category permalink.
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 *
	 * @return mixed|void
	 */
	public function subcat_get_permalink( $post_id = 0 ) {

		if ( $post_id == 0 ) {
			$post_id = get_post( $this->subcat_get_the_ID() );
		}

		return apply_filters( 'pakb_the_permalink', get_permalink( $post_id ) );
	}

	/**
	 * Echoes the sub category title.
	 *
	 * @since 1.0.0
	 */
	public function subcat_the_title() {
		echo $this->subcat_get_title();
	}

	/**
	 * Gets the sub category title.
	 *
	 * @since 1.0.0
	 * @return mixed|void
	 */
	public function subcat_get_title() {

		$post = get_post( $this->subcat_get_the_ID() );

		return apply_filters( 'pakb_the_title', $post->post_title );

	}

	/**
	 * Attached to the hook for the content.
	 *
	 * @since 1.0.0
	 * @param $content
	 *
	 * @return string
	 */
	public function the_content_filter( $content ) {
		return the_content();
	}

	/**
	 * Check if breadcrumb is enabled.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_breadcrumbs_enabled() {
		return !is_null( get_field( 'pakb_breadcrumbs', 'option' ) ) ? get_field( 'pakb_breadcrumbs', 'option' ) : true;
	}

	/**
	 * Displays the breadcrumb.
	 *
	 * @since 1.0.0
	 */
	public function the_breadcrumbs() {
		if ( ! $this->is_breadcrumbs_enabled() ) {
			return;
		}

		$links            = array();
		$pages            = $this->get_breadcrumbs();
		$breadcrumb_title = !is_null( get_field( 'pakb_breadcrumbs_text', 'option' ) ) ? get_field( 'pakb_breadcrumbs_text', 'option' ) : __( 'Knowledge Base', 'pressapps-knowledge-base' );
		$page_i           = 0;

		if ( is_search() ) {
			$pages[ get_search_link() ] = __( 'Search', 'pressapps-knowledge-base' );
		}

		$page_count = count( $pages );

		echo "<ul class=\"uk-breadcrumb pakb-link pakb-muted-color uk-margin-medium-bottom\">";
		foreach ( $pages as $link => $title ) {
			$page_i ++;

			if ( $page_count === $page_i ) {
				$links[] = "<li><span>" . ( $title === 'Knowledge Base' ? $breadcrumb_title : $title ) . "</span></li>";
			} else {

				$links[] = "<li><a href=\"" . esc_url( $link ) . "\">" . ( $title === 'Knowledge Base' ? $breadcrumb_title : $title ) . "</a></li>";
			}

		}
		echo implode( "", $links );
		echo "</ul>";
	}

	/**
	 * Category description
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_category_desc() {
		if ( $this->is_category() ) {
			$queried_object = $this->get_queried_object();
			if($queried_object->description != ''){
				echo '<div class="uk-margin-medium-bottom pakb-muted-color pakb-category-decs pakb-link">' . $queried_object->description . '</div>';
			}
		}
	}

	/**
	 * Gets the breadcrumb.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_breadcrumbs() {

		$parent_pages   = array();
		$queried_object = $this->get_queried_object();
		$breadcrumb_url = get_option( 'options_pakb_breadcrumbs_url' ) ? get_permalink( get_option( 'options_pakb_breadcrumbs_url' ) ) : get_home_url();
		$pages          = array( $breadcrumb_url => __( 'Knowledge Base', 'pressapps-knowledge-base' ) );

		if ( $this->is_category() ) {
			$obj = $queried_object;
			/**
			 * Process n Level of Parents
			 */
			while ( $obj->parent != 0 ) {
				$obj                                   = get_term_by( 'id', $obj->parent, 'knowledgebase_category' );
				$parent_pages[ get_term_link( $obj ) ] = $obj->name;

			}
			$pages                                     = array_merge( $pages, array_reverse( $parent_pages ) );
			$pages[ get_term_link( $queried_object ) ] = $queried_object->name;
		} elseif ( $this->is_single() ) {
			$parent_cat = wp_get_post_terms( $queried_object->ID, 'knowledgebase_category' );
			$term = $parent_cat[0];
			$parent_pages[ get_term_link( $term->term_id ) ] = $term->name;

			while ( $term->parent != 0 ) {
				$term                                   = get_term_by( 'id', $term->parent, 'knowledgebase_category' );
				$parent_pages[ get_term_link( $term ) ] = $term->name;
			}
			$pages                                     			= array_merge( $pages, array_reverse( $parent_pages ) );
			$pages[ get_permalink( $queried_object->ID ) ] 	= $queried_object->post_title;
		} elseif ( $this->is_tag() ) {
			$pages[ get_term_link( $queried_object ) ] 			= $queried_object->name;
		}

		return $pages;
	}

	/**
	 * Check if category count is enabled.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_cat_count_enabled() {
		if ( ! get_option( 'options_pakb_category_count' ) ) {
			return false;
		} else {
			return (bool) get_option( 'options_pakb_category_count' );
		}

	}

	/**
	 * Check if view all link count is enabled.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_view_all_count_enabled() {
		
		return !is_null( get_field( 'pakb_view_all_count', 'option' ) ) ? get_field( 'pakb_view_all_count', 'option' ) : true;

	}
}
