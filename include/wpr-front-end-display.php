<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPR_Comments_Categories_Front_End' ) ) {
	/**
	 * Class WPR_Comments_Categories_Front_End
	 */
	class WPR_Comments_Categories_Front_End {
		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;
		/**
		 * @var Categories
		 */
		private $categories;

		/**
		 * WPR_Comments_Categories_Front_End constructor.
		 */
		public function __construct() {
			add_action( 'comment_form_logged_in_after', array( $this, 'wpr_add_additional_fields' ) );
			add_action( 'comment_form_after_fields', array( $this, 'wpr_add_additional_fields' ) );
			add_action( 'wp_footer', array( $this, 'wpr_display_comments_filter' ) );
			add_action( 'comment_post', array( $this, 'wpr_save_comment_category' ) );
			add_action( 'init', array( $this, 'wpr_front_init' ) );
			add_filter( 'comment_class', array( $this, 'wpr_add_category_class' ), 10, 5 );
			add_action( 'wp_enqueue_scripts', array( $this, 'wpr_front_end_scripts' ) );
		}

		/**
		 * Enqueue scripts in front end
		 */
		public function wpr_front_end_scripts() {
			wp_enqueue_style( 'wpr-front-plugin', plugin_dir_url( __FILE__ ) . '../assets/css/style.css', array(), '1.0.0' );
			wp_enqueue_script( 'wpr-front-plugin-script', plugin_dir_url( __FILE__ ) . '../assets/js/wpr-comments-categories.js', array( 'jquery' ), '1.0.0', true );
		}

		/**
		 * Add category as class
		 *
		 * @param $classes
		 * @param $class
		 * @param $comment_ID
		 * @param $comment
		 * @param $post_id
		 *
		 * @return mixed
		 */
		public function wpr_add_category_class( $classes, $class, $comment_ID, $comment, $post_id ) {
			$wpr_category = get_comment_meta( $comment_ID, 'wpr_comment_category', true );
			if ( $wpr_category ) {
				array_push( $classes, sanitize_title( $wpr_category ) );
			}

			return $classes;
		}

		/**
		 * Init
		 */
		public function wpr_front_init() {
			$this->categories = get_option( 'wpr_current_categories' );
			$this->options    = get_option( 'wpr_comments_cat' );
		}

		/**
		 * Display Filter above comments list
		 */
		public function wpr_display_comments_filter() {
			$comments_list        = '.comment-list';
			$filter_buttons_style = ' style="';
			if ( ! empty( $this->options ) ) {
				if ( ! empty( $this->options['wpr_comments_css_class'] ) ) {
					$comments_list = $this->options['wpr_comments_css_class'];
				}
				
				if ( ! empty( $this->options['wpr_filter_comments_background_color'] ) ) {
					$filter_buttons_style .= sprintf( 'background-color: %s;', sanitize_hex_color( $this->options['wpr_filter_comments_background_color'] ) );
				}

				if ( ! empty( $this->options['wpr_filter_comments_text_color'] ) ) {
					$filter_buttons_style .= sprintf( 'color: %s;', sanitize_hex_color( $this->options['wpr_filter_comments_text_color'] ) );
				}

				if ( ! empty( $this->options['wpr_filter_comments_border_color'] ) ) {
					$filter_buttons_style .= sprintf( 'border: 1px %s solid;', sanitize_hex_color( $this->options['wpr_filter_comments_border_color'] ) );
				}

				if ( ! empty( $this->options['wpr_filter_comments_padding'] ) ) {
					$filter_buttons_style .= sprintf( 'padding: %spx;', absint( $this->options['wpr_filter_comments_padding'] ) );
				}

				if ( ! empty( $this->options['wpr_filter_font_size'] ) ) {
					$filter_buttons_style .= sprintf( 'font-size: %spx;', absint( $this->options['wpr_filter_font_size'] ) );
				}

				$filter_buttons_style .= 'border-radius: 5px;';
			}
			$filter_buttons_style .= '"';
			?>
            <script>
                jQuery(document).ready(function ($) {
                    $default_comment = $('<?php echo $comments_list; ?>');
                    $filter_html = '';
                    if ($default_comment.length > 0) {
						<?php
						if ( ! empty( $this->categories ) ) {
						?>
                        $filter_html += '<ul id="wpr-comments-categories">';
                        $filter_html += '<li><a href="#" class="wpr-comment-cat"<?php echo $filter_buttons_style; ?> data-cat-name=""><?php esc_html_e( 'All Comments' ); ?></a></li>';
						<?php
						foreach ( $this->categories as $wpr_cat ) {
						?>
                        $filter_html += '<li><a href="#" class="wpr-comment-cat"<?php echo $filter_buttons_style; ?> data-cat-name="<?php echo sanitize_title( $wpr_cat ); ?>"><?php echo esc_attr( $wpr_cat ); ?></a></li>';
						<?php }
						}
						?>
                        $filter_html += '</ul>';
                        $($filter_html).insertBefore($default_comment);
                    }
                });
            </script>
			<?php
		}

		/**
		 * Add additional fields to comment form
		 */
		public function wpr_add_additional_fields() {
			echo '<p class="comment-form-category">' .
			     '<label for="comment-category">' . __( 'Comment Category' ) . '</label>';
			if ( ! empty( $this->categories ) ) {
				echo '<select name="comment-category">';
				foreach ( $this->categories as $wpr_cat ) {
					echo sprintf( '<option value="%1$s">%1$s</option>', esc_attr( $wpr_cat ) );
				}
				echo '</select>';
			}
			echo '</p>';
		}

		/**
		 * Save Comment Category
		 *
		 * @param $comment_id
		 */
		public function wpr_save_comment_category( $comment_id ) {
			if ( ( isset( $_POST['comment-category'] ) ) && ( $_POST['comment-category'] != '' ) ) {
				$comment_category = wp_filter_nohtml_kses( $_POST['comment-category'] );
			}
			add_comment_meta( $comment_id, 'wpr_comment_category', $comment_category );
		}
	}
}

$comments_categories_front_end = new WPR_Comments_Categories_Front_End();
