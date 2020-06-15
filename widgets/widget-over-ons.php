<?php


// Gebruiker Centraal - widgets/widget-over-ons.php
// ----------------------------------------------------------------------------------
// Deze widget kan gebruikt worden in de footer
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Paul van Buuren
// @license GPL-2.0+

if ( function_exists( 'acf_add_local_field_group' ) ):

	acf_add_local_field_group( array(
		'key'                   => 'group_5ee776d099f23',
		'title'                 => 'Widget over ons',
		'fields'                => array(
			array(
				'key'               => 'field_5ee776d6120e7',
				'label'             => 'Voeg een link toe',
				'name'              => 'overons_link',
				'type'              => 'link',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'return_format'     => 'array',
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'widget',
					'operator' => '==',
					'value'    => sanitize_title( WBVB_GC_ABOUTUS),
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	) );

endif;

class GC_widget_over_ons extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'gc-site-footer-widget ',
			'description' => __( 'Ruimte voor korte informatie over de site en een doorklik.', 'gctheme' ),
		);


		$baseid     = sanitize_title( WBVB_GC_ABOUTUS) ;
		$widgetname = WBVB_GC_ABOUTUS;

		parent::__construct( $baseid, $widgetname, $widget_ops );

	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array(
				'title'              => '',
				'korte_beschrijving' => ''
			)
		);

		$title              = apply_filters( 'widget_title', $instance['title'] );
		$korte_beschrijving = $instance['korte_beschrijving'];

		?>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'gctheme' ) ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
		<p><label
				for="<?php echo $this->get_field_id( 'korte_beschrijving' ) ?>"><?php _e( "Korte beschrijving van de site:", 'gctheme' ) ?></label><br/><textarea
				cols="33" rows="4" id="<?php echo $this->get_field_id( 'korte_beschrijving' ); ?>"
				name="<?php echo $this->get_field_name( 'korte_beschrijving' ); ?>"><?php echo esc_attr( $korte_beschrijving ); ?></textarea>
		</p>

		<?php

	}

	function update( $new_instance, $old_instance ) {
		$instance                       = $old_instance;
		$instance['title']              = strip_tags( $new_instance['title'] );
		$instance['korte_beschrijving'] = $new_instance['korte_beschrijving'];

		return $instance;
	}

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$korte_beschrijving = empty( $instance['korte_beschrijving'] ) ? '' : $instance['korte_beschrijving'];

		// 't zal vast handiger kunnen, maar ik moet NU een class .gc-site-footer-widget op de <section> hebben
		$args["before_widget"] = preg_replace( '|class="|i', 'class="gc-site-footer-widget ', $args["before_widget"] );

		Timber::render( 'widgets/widget-over-ons.twig', array(
			'args'               => $args,
			'instance'           => $instance,
			'korte_beschrijving' => nl2br( $korte_beschrijving ),
		) );
	}
}

//========================================================================================================

function GC_widget_over_ons_init() {
	return register_widget( "GC_widget_over_ons" );
}

add_action( 'widgets_init', 'GC_widget_over_ons_init' );

//========================================================================================================

add_filter( 'dynamic_sidebar_params', 'filter_for_gc_overons_widget' );

function filter_for_gc_overons_widget( $params ) {

	global $post;

	// get widget vars
	$widget_name = $params[0]['widget_name'];
	$widget_id   = $params[0]['widget_id'];

	// bail early if this widget is not a Text widget
	if ( $widget_name != WBVB_GC_ABOUTUS ) {
		return $params;
	}

	$overons_link = get_field( 'overons_link', 'widget_' . $widget_id );

	if ( $overons_link ):

		$params[0]['after_widget'] = '<p><a href="' . $overons_link['url'] . '" class="btn btn--primary">' . $overons_link['title'] . '</a></p>' . $params[0]['after_widget'];

	endif;

	// return
	return $params;

}

//========================================================================================================
