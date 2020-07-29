<?php
/**
 * Project: MinervaKB.
 * Copyright: 2015-2016 @KonstruktStudio
 */

class MinervaKB_PageEdit implements KST_EditScreen_Interface {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array($this, 'add_meta_boxes') );
		add_action( 'save_post', array($this, 'save_post') );
		add_action( 'save_post', array($this, 'save_post_handler') );
	}

	/**
	 * Register meta box(es).
	 */
	public function add_meta_boxes() {

		// page builder switch
		add_meta_box(
			'mkb-page-meta-switch-box-id',
			__( 'Enable MinervaKB Page Builder (you can create multiple pages)', 'minerva-kb' ),
			array($this, 'builder_switch_html'),
			'page',
			'normal',
			'default'
		);

		// page builder options
		add_meta_box(
			'mkb-page-meta-box-id',
			__( 'MinervaKB Builder Page Settings', 'minerva-kb' ),
			array($this, 'builder_html'),
			'page',
			'normal',
			'default'
		);

        // page template options
        add_meta_box(
            'mkb-page-template-meta-box-id',
            __( 'MinervaKB Page Settings', 'minerva-kb' ),
            array($this, 'page_settings_html'),
            'page',
            'side',
            'default'
        );
	}

	/**
	 * Meta box with switch to builder for page
	 * @param $post
	 */
	public function builder_switch_html( $post ) {

		$switch_value = MKB_PageOptions::is_builder_page();

		$settings_helper = new MKB_SettingsBuilder(array(
			'no_tabs' => true
		));

		?>
		<div class="mkb-page-switch-settings-container mkb-clearfix fn-mkb-settings-container">
			<?php wp_nonce_field( 'mkb_save_page_settings', 'mkb_save_page_settings_nonce' ); ?>
			<div id="mkb-home-page-switch-settings" class="mkb-loading mkb-form">
				<?php

				$options = array(
					array(
						'id'      => 'enable_home_page',
						'type'    => 'checkbox',
						'label'   => __( 'Turn on this switch and save page to display KB Home editor', 'minerva-kb' ),
						'default' => false,
                        'description' => __( 'Please note, that MinervaKB Page Template must NOT be selected for page builder to display, because it has it\'s own page settings.', 'minerva-kb' )
					)
				);

				?>
				<div class="mkb-settings-content">
					<?php
					foreach ( $options as $option ):
						$settings_helper->render_option(
							$option["type"],
							(bool) $switch_value,
							$option
						);
					endforeach;
					?>
				</div>
				<?php
				?>
			</div>
		</div>
	<?php
	}
	/**
	 * Page builder meta box
	 * @param WP_Post $post Current post object.
	 */
	public function builder_html( $post ) {

		$switch_value = MKB_PageOptions::is_builder_page();
		$settings = MKB_PageOptions::get_page_settings();

		if (!$switch_value) {
			?>
			<div class="mkb-page-settings-off-message">
				<?php _e( 'Currently disabled. Turn on the switch above to display KB Home editor', 'minerva-kb'); ?>
			</div>
			<?php
			return;
		}

		$settings_helper = new MKB_SettingsBuilder();

		?>
		<div class="mkb-page-settings-container mkb-clearfix fn-mkb-settings-container">
			<div id="mkb-page-settings" class="mkb-loading mkb-clearfix mkb-form fn-mkb-page-settings">
				<div class="mkb-plugin-settings-preloader">
					<div class="mkb-loader">
						<span class="inner1"></span>
						<span class="inner2"></span>
						<span class="inner3"></span>
					</div>
				</div>
				<div class="mkb-settings-content-holder">
					<?php

					$options = MKB_PageOptions::get_options();

					$settings_helper->render_tab_links( $options );

					?>
					<div class="mkb-settings-content">
						<?php
						foreach ( $options as $option ):
							$settings_helper->render_option(
								$option["type"],
								isset( $settings[ $option["id"] ] ) ? $settings[ $option["id"] ] : null,
								$option
							);
						endforeach;

						$settings_helper->close_tab_container();
						?>
					</div>
				</div>
				<?php
				?>
			</div>
		</div>
	<?php
	}

    /**
     * Page settings meta box
     */
	public function page_settings_html() {
        global $post;

        $settings = array(
            "add_container" => false,
            "show_title" => false,
            "page_top_padding" => array("unit" => 'em', "size" => "0"),
            "page_bottom_padding" => array("unit" => 'em', "size" => "0")
        );

        if ($post) {
            $stored_settings = get_post_meta($post->ID, '_mkb_page_template_settings', true);

            if ($stored_settings) {
                $settings = wp_parse_args($stored_settings, $settings);
            }
        }

        $settings_helper = new MKB_SettingsBuilder();

        ?>
        <div class="mkb-page-settings-container mkb-clearfix fn-mkb-settings-container">
            <?php wp_nonce_field( 'mkb_save_page_template_settings', 'mkb_save_page_template_settings_nonce' ); ?>
            <div id="mkb-page-settings" class="mkb-clearfix mkb-form fn-mkb-page-settings">
                <div class="mkb-settings-content-holder">
                    <?php

                    $options = array(
                        array(
                            'id' => 'template_add_container',
                            'type' => 'checkbox',
                            'label' => __( 'Add container to page?', 'minerva-kb' ),
                            'default' => false,
                            'description' => __( 'You can add container to limit content width. Page will be fullwidth otherwise (unless theme adds custom paddings or margins)', 'minerva-kb' )
                        ),
                        array(
                            'id' => 'template_show_title',
                            'type' => 'checkbox',
                            'label' => __( 'Show page title?', 'minerva-kb' ),
                            'default' => false,
                            'description' => __( 'You can show or hide page title', 'minerva-kb' )
                        ),
                        array(
                            'id' => 'template_page_top_padding',
                            'type' => 'css_size',
                            'label' => __( 'Page top padding', 'minerva-kb' ),
                            'default' => array("unit" => 'em', "size" => "0"),
                            'description' => __( 'Distance between header and page content', 'minerva-kb' )
                        ),
                        array(
                            'id' => 'template_page_bottom_padding',
                            'type' => 'css_size',
                            'label' => __( 'Page bottom padding', 'minerva-kb' ),
                            'default' => array("unit" => 'em', "size" => "0"),
                            'description' => __( 'Distance between page content and footer', 'minerva-kb' )
                        ),
                    );

                    ?>
                    <div class="mkb-settings-content">
                        <?php
                        foreach ( $options as $option ):
                            $option_save_key = str_replace('template_', '', $option["id"]);

                            $settings_helper->render_option(
                                $option["type"],
                                isset( $settings[ $option_save_key ] ) ? $settings[$option_save_key] : null,
                                $option
                            );
                        endforeach;
                        ?>
                    </div>
                </div>
                <?php
                ?>
            </div>
        </div>
        <?php
    }

	/**
	 * Save meta box content.
     * TODO: this is used for template settings as well
	 */
	public function save_post( $post_id ) {
		/**
		 * Verify user is indeed user
		 */
		if (
			! isset( $_POST['mkb_save_page_settings_nonce'] )
			|| ! wp_verify_nonce( $_POST['mkb_save_page_settings_nonce'], 'mkb_save_page_settings' )
		) {
			return;
		}

		$post_type = get_post_type($post_id);

		if ($post_type !== 'page') {
			return;
		}

		update_post_meta($post_id, '_mkb_enable_home_page', isset($_POST['mkb_option_enable_home_page']));

		if (isset($_POST['mkb_page_section'])) {
			update_post_meta( $post_id, '_mkb_page_sections', $_POST['mkb_page_section']);
		} else {
			update_post_meta($post_id, '_mkb_page_sections', array());
		}

		// TODO: refactor page options save
		$page_settings = array(
			"add_container" => false,
			"show_title" => false,
			"page_top_padding" => array("unit" => 'em', "size" => "0"),
			"page_bottom_padding" => array("unit" => 'em', "size" => "0"),
		);

		if (isset($_POST['mkb_option_add_container'])) {
			$page_settings["add_container"] = true;
		}

		if (isset($_POST['mkb_option_show_title'])) {
			$page_settings["show_title"] = true;
		}

		if (isset($_POST['mkb_option_page_top_padding']) && isset($_POST['mkb_option_page_top_padding_unit'])) {
			$page_settings["page_top_padding"] = array(
				"size" => $_POST['mkb_option_page_top_padding'],
				"unit" => $_POST['mkb_option_page_top_padding_unit']
			);
		}

		if (isset($_POST['mkb_option_page_bottom_padding']) && isset($_POST['mkb_option_page_bottom_padding_unit'])) {
			$page_settings["page_bottom_padding"] = array(
				"size" => $_POST['mkb_option_page_bottom_padding'],
				"unit" => $_POST['mkb_option_page_bottom_padding_unit']
			);
		}

		update_post_meta($post_id, '_mkb_page_settings', $page_settings);
	}

    /**
     * New page save handler for page template settings
     * @param $post_id
     */
    public function save_post_handler( $post_id ) {
        $post_type = get_post_type($post_id);

        if ($post_type !== 'page') {
            return;
        }

        /**
         * Verify user is indeed user
         */
        if (
            ! isset( $_POST['mkb_save_page_template_settings_nonce'] )
            || ! wp_verify_nonce( $_POST['mkb_save_page_template_settings_nonce'], 'mkb_save_page_template_settings' )
        ) {
            return;
        }

        // TODO: refactor page options save
        $page_settings = array(
            "add_container" => false,
            "show_title" => false,
            "page_top_padding" => array("unit" => 'em', "size" => "0"),
            "page_bottom_padding" => array("unit" => 'em', "size" => "0"),
        );

        if (isset($_POST['mkb_option_template_add_container'])) {
            $page_settings["add_container"] = true;
        }

        if (isset($_POST['mkb_option_template_show_title'])) {
            $page_settings["show_title"] = true;
        }

        if (isset($_POST['mkb_option_template_page_top_padding']) && isset($_POST['mkb_option_template_page_top_padding_unit'])) {
            $page_settings["page_top_padding"] = array(
                "size" => $_POST['mkb_option_template_page_top_padding'],
                "unit" => $_POST['mkb_option_template_page_top_padding_unit']
            );
        }

        if (isset($_POST['mkb_option_template_page_bottom_padding']) && isset($_POST['mkb_option_template_page_bottom_padding_unit'])) {
            $page_settings["page_bottom_padding"] = array(
                "size" => $_POST['mkb_option_template_page_bottom_padding'],
                "unit" => $_POST['mkb_option_template_page_bottom_padding_unit']
            );
        }

        update_post_meta($post_id, '_mkb_page_template_settings', $page_settings);
    }
}
