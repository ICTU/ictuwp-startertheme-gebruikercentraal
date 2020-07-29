<?php
/**
 * Project: MinervaKB.
 * Copyright: 2015-2017 @KonstruktStudio
 */

class MinervaKB_GlossaryEdit implements KST_EditScreen_Interface {

	/**
	 * Constructor
	 */
	public function __construct($deps) {
		$this->setup_dependencies($deps);

		add_action( 'add_meta_boxes', array($this, 'add_meta_boxes') );
		add_action( 'save_post', array($this, 'save_post') );
		add_action( 'admin_footer', array($this, 'glossary_tmpl'), 30 );
	}

	/**
	 * Sets up dependencies
	 * @param $deps
	 */
	private function setup_dependencies($deps) {
		// just in case
	}

	/**
	 * Register meta box(es).
	 */
	public function add_meta_boxes() {

		// synonyms meta box
		add_meta_box(
			'mkb-glossary-meta-synonyms-id',
			__( 'Glossary synonyms', 'minerva-kb' ),
			array($this, 'synonyms_html'),
			'mkb_glossary',
			'normal',
			'high'
		);
	}


	/**
	 * Synonyms list
	 * @param $post
	 */
	public function synonyms_html( $post ) {

	    $synonyms = get_post_meta(get_the_ID(), '_mkb_glossary_synonyms', true);

	    // NOTE: required
	    wp_nonce_field( 'mkb_save_glossary', 'mkb_save_glossary_nonce' );

		?>
		<label for="mkb_glossary_synonyms"><?php _e('Add comma-separated list of alternative spellings or synonyms:', 'minerva-kb'); ?></label>
        <br>
        <textarea name="mkb_glossary_synonyms" id="mkb_glossary_synonyms" cols="80" rows="3" placeholder="For ex.: video card, graphics card, gpu" style="margin-top: 0.5rem;"><?php esc_html_e($synonyms); ?></textarea>
        <p><?php _e('You can add synonyms to cover different spellings or synonyms of same term.', 'minerva-kb'); ?></p>
	<?php
	}

	/**
	 * Templates
	 */
	public function glossary_tmpl() {
	    // just in case
	}

	/**
	 * Saves meta box fields
	 * @param $post_id
	 * @return mixed|void
	 */
	function save_post( $post_id ) {
		/**
		 * Verify user is indeed user
		 */
		if (
			! isset( $_POST['mkb_save_glossary_nonce'] )
			|| ! wp_verify_nonce( $_POST['mkb_save_glossary_nonce'], 'mkb_save_glossary' )
		) {
			return;
		}

		$post_type = get_post_type($post_id);

		if ($post_type !== 'mkb_glossary') {
			return;
		}

		update_post_meta(
			$post_id,
			'_mkb_glossary_synonyms',
			isset($_POST['mkb_glossary_synonyms']) ?
				trim($_POST['mkb_glossary_synonyms']) :
				''
		);
	}
}
