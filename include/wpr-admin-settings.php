<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 *
 */
if ( ! class_exists( 'WPR_Comments_Settings' ) ) {
	/**
	 * Class WPR_Comments_Settings
	 */
	class WPR_Comments_Settings {
		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;
		/**
		 * @var Categories
		 */
		private $categories;

		/**
		 * WPR_Comments_Settings constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'wpr_add_settings_sub_menu' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wpr_backend_enqueue_scripts' ) );
			add_action( 'wp_ajax_wpr_add_category', array( $this, 'wpr_add_category' ) );
			add_action( 'wp_ajax_wpr_delete_category', array( $this, 'wpr_delete_comment_category' ) );
		}

		/**
		 * Enqueue admin script
		 */
		public function wpr_backend_enqueue_scripts() {
			/**
			 * Load Color Picker
			 */
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_style( 'wpr-admin-style', plugin_dir_url( __FILE__ ) . '../assets/css/admin-style.css', array(), '1.0.0' );
			wp_enqueue_script( 'wpr-backend-plugin-script', plugin_dir_url( __FILE__ ) . '../assets/js/wpr-admin-settings.js', array( 'jquery' ), '1.0.0', true );
			$args = array(
				'nonce'    => wp_create_nonce( 'wpr-load-more-nonce' ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			);
			wp_localize_script( 'wpr-backend-plugin-script', 'ajax_backend', $args );
		}

		/**
		 * Add new category
		 */
		public function wpr_add_category() {
			check_ajax_referer( 'wpr-load-more-nonce', 'nonce' );

			$cat_name = esc_attr( $_POST['cat_name'] );

			if ( empty( $this->categories ) ) {
				update_option( 'wpr_current_categories', array( $cat_name ) );
			} else {
				if ( ! in_array( $cat_name, $this->categories ) ) {
					array_push( $this->categories, $cat_name );
					update_option( 'wpr_current_categories', $this->categories );
				}
			}

			wp_die();
		}

		/**
		 * Remove category name
		 */
		public function wpr_delete_comment_category() {
			check_ajax_referer( 'wpr-load-more-nonce', 'nonce' );

			$cat_name = esc_attr( $_POST['cat_name'] );

			if ( ! empty( $this->categories ) && in_array( $cat_name, $this->categories ) ) {
				$index = array_search( $cat_name, $this->categories );

				unset( $this->categories[ $index ] );
				update_option( 'wpr_current_categories', $this->categories );
			}

			wp_die();
		}

		/**
		 *
		 */
		public function wpr_add_settings_sub_menu() {
			add_options_page(
				__( 'Comments Categories' ),
				__( 'Comments Categories' ),
				'manage_options',
				'wpr-comments-category',
				array( $this, 'wpr_settings_callback' )
			);
		}

		/**
		 * Add settings tabs
		 *
		 * @param string $current
		 */
		function wpr_settings_tabs( $current = 'categories' ) {
			$tabs = array( 'categories' => 'Categories', 'styles' => 'Styles' );
			echo '<div id="icon-themes" class="icon32"><br></div>';
			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $tabs as $tab => $name ) {
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=wpr-comments-category&tab=$tab'>$name</a>";
			}
			echo '</h2>';
		}

		/**
		 * Display settings
		 */
		public function wpr_settings_callback() {
			// Set class property
			$this->options = get_option( 'wpr_comments_cat' );
			?>
            <div class="wrap">
                <h1><?php echo __( 'Comments Categories' ); ?></h1>
				<?php
				if ( isset ( $_GET['tab'] ) ) {
					$this->wpr_settings_tabs( $_GET['tab'] );
				} else {
					$this->wpr_settings_tabs( 'categories' );
				}

				$tab = 'categories';
				if ( isset ( $_GET['tab'] ) ) {
					$tab = $_GET['tab'];
				}

				switch ( $tab ) {
					case "categories": ?>
                        <h3><?php echo __( 'Current Categories' ); ?></h3>
						<?php
						echo '<ol id="wpr-current-categories">';
						// Current Categories.
						if ( ! empty( $this->categories ) ) {
							foreach ( $this->categories as $wpr_cat ) {
								echo sprintf( '<li>%1$s <a href="#" class="wpr-delete-cat" data-cat-name="%1$s">[X]</a></li>', esc_attr( $wpr_cat ) );
							}
						}
						echo '</ol>';
						?>
                        <h3><?php echo __( 'Add new Category' ); ?></h3>
                        <form id="wpr-add-new-category">
                            <p>
                                <input name="wpr-new-category"/>
                            </p>
                            <p>
								<?php submit_button( 'Create new Category' ); ?>
                            </p>
                        </form>
						<?php
						break;
					case "styles": ?>
						<?php settings_errors(); ?>
                        <form method="POST" action="options.php">
							<?php settings_fields( 'wpr_comments_category' ); ?>
							<?php do_settings_sections( 'wpr-comments-category' ) ?>
							<?php submit_button(); ?>
                        </form>
						<?php
						break;
				}
				?>
            </div>
			<?php
		}

		/**
		 * Register and add settings
		 */
		public function page_init() {
			$this->categories = get_option( 'wpr_current_categories' );
			register_setting(
				'wpr_comments_category',
				'wpr_comments_cat',
				array( $this, 'wpr_sanitize' )
			);

			add_settings_section(
				'wpr_setting_section',
				'Custom Settings',
				array( $this, 'wpr_print_section_info' ),
				'wpr-comments-category'
			);

			add_settings_field(
				'wpr-comments-class',
				'Comments List CSS Class/ID',
				array( $this, 'wpr_comments_css_class' ),
				'wpr-comments-category',
				'wpr_setting_section'
			);

			add_settings_field(
				'wpr-filter-buttons-background-color',
				'Style buttons background color',
				array( $this, 'wpr_filter_buttons_background_color' ),
				'wpr-comments-category',
				'wpr_setting_section'
			);

			add_settings_field(
				'wpr-filter-buttons-text-color',
				'Style buttons text color',
				array( $this, 'wpr_filter_buttons_text_color' ),
				'wpr-comments-category',
				'wpr_setting_section'
			);

			add_settings_field(
				'wpr-filter-buttons-border-color',
				'Style buttons border color',
				array( $this, 'wpr_filter_buttons_border_color' ),
				'wpr-comments-category',
				'wpr_setting_section'
			);

			add_settings_field(
				'wpr-filter-buttons-font-size',
				'Style buttons font size (px size)',
				array( $this, 'wpr_filter_buttons_font_size' ),
				'wpr-comments-category',
				'wpr_setting_section'
			);

			add_settings_field(
				'wpr-filter-buttons-padding',
				'Style buttons padding (px size)',
				array( $this, 'wpr_filter_buttons_padding' ),
				'wpr-comments-category',
				'wpr_setting_section'
			);
		}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public function wpr_sanitize( $input ) {
			$new_input = array();
			if ( isset( $input['wpr_comments_css_class'] ) ) {
				$new_input['wpr_comments_css_class'] = esc_attr( $input['wpr_comments_css_class'] );
			}

			if ( isset( $input['wpr_filter_comments_background_color'] ) ) {
				$new_input['wpr_filter_comments_background_color'] = sanitize_hex_color( $input['wpr_filter_comments_background_color'] );
			}

			if ( isset( $input['wpr_filter_comments_text_color'] ) ) {
				$new_input['wpr_filter_comments_text_color'] = sanitize_hex_color( $input['wpr_filter_comments_text_color'] );
			}

			if ( isset( $input['wpr_filter_comments_border_color'] ) ) {
				$new_input['wpr_filter_comments_border_color'] = sanitize_hex_color( $input['wpr_filter_comments_border_color'] );
			}

			if ( isset( $input['wpr_filter_comments_padding'] ) ) {
				$new_input['wpr_filter_comments_padding'] = absint( $input['wpr_filter_comments_padding'] );
			}

			if ( isset( $input['wpr_filter_font_size'] ) ) {
				$new_input['wpr_filter_font_size'] = absint( $input['wpr_filter_font_size'] );
			}

			return $new_input;
		}

		/**
		 * Print the Section text
		 */
		public function wpr_print_section_info() {
			echo 'Enter your Comments List Element CSS Class/ID. Default is <b>.comment-list</b>';
		}

		/**
		 * Get the settings option array and print one of its values
		 */
		public function wpr_comments_css_class() {
			printf(
				'<input type="text" id="wpr_comments_css_class" name="wpr_comments_cat[wpr_comments_css_class]" value="%s" placeholder=".comment-list" />',
				isset( $this->options['wpr_comments_css_class'] ) ? esc_attr( $this->options['wpr_comments_css_class'] ) : ''
			);
		}

		/**
		 * Add Filter Buttons background color
		 */
		public function wpr_filter_buttons_background_color() {
			printf(
				'<input type="text" class="wpr-color-field" id="wpr_filter_comments_background_color" name="wpr_comments_cat[wpr_filter_comments_background_color]" value="%s" placeholder="#FFF" />',
				isset( $this->options['wpr_filter_comments_background_color'] ) ? esc_attr( $this->options['wpr_filter_comments_background_color'] ) : ''
			);
		}

		/**
		 * Add Filter Buttons text color
		 */
		public function wpr_filter_buttons_text_color() {
			printf(
				'<input type="text" class="wpr-color-field" id="wpr_filter_comments_text_color" name="wpr_comments_cat[wpr_filter_comments_text_color]" value="%s" placeholder="#000" />',
				isset( $this->options['wpr_filter_comments_text_color'] ) ? esc_attr( $this->options['wpr_filter_comments_text_color'] ) : ''
			);
		}

		/**
		 * Add Filter Buttons border color
		 */
		public function wpr_filter_buttons_border_color() {
			printf(
				'<input type="text" class="wpr-color-field" id="wpr_filter_comments_border_color" name="wpr_comments_cat[wpr_filter_comments_border_color]" value="%s" placeholder="#000" />',
				isset( $this->options['wpr_filter_comments_border_color'] ) ? esc_attr( $this->options['wpr_filter_comments_border_color'] ) : ''
			);
		}

		/**
		 * Add Filter Font size
		 */
		public function wpr_filter_buttons_font_size() {
			printf(
				'<input type="text" id="wpr_filter_font_size" name="wpr_comments_cat[wpr_filter_font_size]" value="%s" placeholder="11" />',
				isset( $this->options['wpr_filter_font_size'] ) ? esc_attr( $this->options['wpr_filter_font_size'] ) : ''
			);
        }

		/**
		 * Add Filter Buttons padding
		 */
		public function wpr_filter_buttons_padding() {
			printf(
				'<input type="text" id="wpr_filter_comments_padding" name="wpr_comments_cat[wpr_filter_comments_padding]" value="%s" placeholder="8" />',
				isset( $this->options['wpr_filter_comments_padding'] ) ? esc_attr( $this->options['wpr_filter_comments_padding'] ) : ''
			);
		}
	}
}

if ( is_admin() ) {
	$comments_categories_settings = new WPR_Comments_Settings();
}
