<?php


// Gebruiker Centraal - widgets/widget-over-ons.php
// ----------------------------------------------------------------------------------
// Deze widget kan gebruikt worden in de footer
// ----------------------------------------------------------------------------------
// @package gebruiker-centraal
// @author  Paul van Buuren
// @license GPL-2.0+


class GC_widget_over_ons extends WP_Widget {

	public function __construct() {

		$widget_ops = array(
			'classname'   => 'gc-site-footer-widget ',
			'description' => __( 'Ruimte voor korte informatie over de site en een doorklik.', 'gctheme' ),
		);


		parent::__construct( WBVB_GC_ABOUTUS, WBVB_GC_ABOUTUS, $widget_ops );
	}


	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array(
				'title'                    => '',
				'gc_fw_cta_link'           => '',
				'gc_fw_korte_beschrijving' => '',
				'gc_fw_url_meer_info'      => ''
			)
		);

		$title                    = apply_filters( 'widget_title', $instance['title'] );
		$gc_fw_korte_beschrijving = $instance['gc_fw_korte_beschrijving'];
		$gc_fw_cta_link           = $instance['gc_fw_cta_link'];
		$gc_fw_url_meer_info      = $instance['gc_fw_url_meer_info'];

		if ( intval( $gc_fw_url_meer_info ) > 0 ) {
			$gc_fw_url_meer_info = get_permalink( intval( $gc_fw_url_meer_info ) );
		}


		?>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'gctheme' ) ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
				   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
		<p><label
				for="<?php echo $this->get_field_id( 'gc_fw_korte_beschrijving' ) ?>"><?php _e( "Korte beschrijving van de site:", 'gctheme' ) ?></label><br/><textarea
				cols="33" rows="4" id="<?php echo $this->get_field_id( 'gc_fw_korte_beschrijving' ); ?>"
				name="<?php echo $this->get_field_name( 'gc_fw_korte_beschrijving' ); ?>"><?php echo esc_attr( $gc_fw_korte_beschrijving ); ?></textarea>
		</p>

		<p>
			<label
				for="<?php echo $this->get_field_id( 'gc_fw_cta_link' ); ?>"><?php _e( 'Linktekst', 'gctheme' ) ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'gc_fw_cta_link' ); ?>"
				   name="<?php echo $this->get_field_name( 'gc_fw_cta_link' ); ?>" type="text"
				   value="<?php echo $gc_fw_cta_link; ?>"/>
		</p>

		<p>
			<label
				for="<?php echo $this->get_field_id( 'gc_fw_url_meer_info' ); ?>"><?php _e( 'Link (URL)', 'gctheme' ) ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'gc_fw_url_meer_info' ); ?>"
				   name="<?php echo $this->get_field_name( 'gc_fw_url_meer_info' ); ?>" type="url"
				   value="<?php echo $gc_fw_url_meer_info; ?>"/>
		</p>

		<?php


	}

	function update( $new_instance, $old_instance ) {
		$instance                             = $old_instance;
		$instance['title']                    = strip_tags( $new_instance['title'] );
		$instance['gc_fw_korte_beschrijving'] = $new_instance['gc_fw_korte_beschrijving'];
		$instance['gc_fw_url_meer_info']      = $new_instance['gc_fw_url_meer_info'];
		$instance['gc_fw_cta_link']           = $new_instance['gc_fw_cta_link'];

		return $instance;
	}

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$gc_fw_korte_beschrijving = empty( $instance['gc_fw_korte_beschrijving'] ) ? '' : $instance['gc_fw_korte_beschrijving'];
		$gc_fw_url_meer_info      = empty( $instance['gc_fw_url_meer_info'] ) ? '' : $instance['gc_fw_url_meer_info'];
		$gc_fw_cta_link           = $instance['gc_fw_cta_link'];

		// 't zal vast handiger kunnen, maar ik moet NU een class .gc-site-footer-widget op de <section> hebben
		$args["before_widget"] = preg_replace( '|class="|i', 'class="gc-site-footer-widget ', $args["before_widget"] );


		if ( $gc_fw_url_meer_info && $gc_fw_cta_link ) {

			if ( intval( $gc_fw_url_meer_info ) > 0 ) {
				// we hadden vroeger een page link die een integer terug gaf
				$gc_fw_url_meer_info = get_permalink( intval( $gc_fw_url_meer_info ) );
			}

			$gc_fw_cta_link = '<p><a href="' . $gc_fw_url_meer_info . '" class="btn btn--primary">' . $gc_fw_cta_link . '</a></p>';

		}

		Timber::render( 'widgets/widget-over-ons.twig', array(
			'args'               => $args,
			'instance'           => $instance,
			'korte_beschrijving' => nl2br( $gc_fw_korte_beschrijving ),
			'cta_link'           => $gc_fw_cta_link
		) );
	}
}

//========================================================================================================

function GC_widget_over_ons_init() {
	return register_widget( "GC_widget_over_ons" );
}

add_action( 'widgets_init', 'GC_widget_over_ons_init' );

//========================================================================================================

