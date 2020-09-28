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
	    // NOTE: required
	    wp_nonce_field( 'mkb_save_glossary', 'mkb_save_glossary_nonce' );

        $settings_helper = new MKB_SettingsBuilder(array(
            'post' => true,
            'no_tabs' => true
        ));

        $options = array(
            array(
                'id' => 'mkb_glossary_synonyms', // legacy
                'type' => 'textarea',
                'label' => __( 'Add comma-separated list of alternative spellings or synonyms:', 'minerva-kb' ),
                'height' => 3,
                'width' => 80,
                'placeholder' => __( 'For ex.: video card, graphics card, gpu', 'minerva-kb' ),
                'default' => '',
                'description' => __('You can add synonyms to cover different spellings or synonyms of same term.', 'minerva-kb')
            ),
            array(
                'id' => 'mkb_exclude_from_kb',
                'type' => 'checkbox',
                'label' => __( 'Exclude this term from client-side highlight?', 'minerva-kb' ),
                'default' => false
            )
        );

        ?>
        <div class="mkb-clearfix">
            <div class="mkb-settings-content">
                <?php
                foreach ($options as $option):
                    $value = $option["default"];
                    $meta_key = '_' . $option['id'];

                    if (metadata_exists('post', get_the_ID(), $meta_key)) {
                        $value = get_post_meta(get_the_ID(), $meta_key, true);

                        if ($option['type'] === 'checkbox') {
                            $value = (bool)$value;
                        }
                    }

                    $settings_helper->render_option(
                        $option["type"],
                        $value,
                        $option
                    );
                endforeach;
                ?>
            </div>
        </div>
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

		// synonyms
		update_post_meta(
			$post_id,
			'_mkb_glossary_synonyms',
			isset($_POST['mkb_glossary_synonyms']) ?
				trim($_POST['mkb_glossary_synonyms']) :
				''
		);

        // exclude
        update_post_meta(
            $post_id,
            '_mkb_exclude_from_kb',
            (bool)($_POST['mkb_exclude_from_kb'])
        );
	}
}
