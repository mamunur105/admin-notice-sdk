<?php

namespace TinySolutions\SDK\Admin;

use TinySolutions\SDK\Common\Loader;
use TinySolutions\SDK\Traits\SingletonTrait;

/**
 * Review class
 */
class Review {
	/**
	 * @var object
	 */
	protected $loader;

	/**
	 * Singleton
	 */
	use SingletonTrait;

	/**
	 * Template builder post type
	 *
	 * @var string
	 */
	public string $textdomain = 'tiny_sdk';

	/**
	 * Init
	 *
	 * @return void
	 */
	private function __construct() {
		$this->loader = Loader::instance();
		$this->tiny_sdk_check_installation_time();
		$this->loader->add_action( 'admin_init', $this, 'tiny_sdk_spare_me', 5 );
	}

	/**
	 * Check if review notice should be shown or not
	 *
	 * @return void
	 */
	public function tiny_sdk_check_installation_time() {

		// Added Lines Start
		$nobug = get_option( 'tiny_sdk_spare_me' );

		$rated = get_option( 'tiny_sdk_rated' );

		if ( '1' == $nobug || 'yes' == $rated ) {
		//	return;
		}

		$now = strtotime( 'now' );

		$install_date = get_option( 'tiny_sdk_plugin_activation_time' );

		$past_date = strtotime( '+10 days', $install_date );

		$remind_time = get_option( 'tiny_sdk_remind_me' );

		if ( ! $remind_time ) {
			$remind_time = $install_date;
		}

		$remind_time = $remind_time ? $remind_time : $past_date;

		$remind_due = strtotime( '+10 days', $remind_time );

		if ( ! $now > $past_date || $now < $remind_due ) {
		//	return;
		}
  
		$this->loader->add_action( 'admin_notices', $this, 'tiny_sdk_display_admin_notice' );
	}

	/**
	 * Remove the notice for the user if review already done or if the user does not want to
	 *
	 * @return void
	 */
	public function tiny_sdk_spare_me() {

		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'tiny_sdk_notice_nonce' ) ) {
			return;
		}

		if ( isset( $_GET['tiny_sdk_spare_me'] ) && ! empty( $_GET['tiny_sdk_spare_me'] ) ) {
			$spare_me = absint( $_GET['tiny_sdk_spare_me'] );
			if ( 1 == $spare_me ) {
				update_option( 'tiny_sdk_spare_me', '1' );
			}
		}

		if ( isset( $_GET['tiny_sdk_remind_me'] ) && ! empty( $_GET['tiny_sdk_remind_me'] ) ) {
			$remind_me = absint( $_GET['tiny_sdk_remind_me'] );
			if ( 1 == $remind_me ) {
				$get_activation_time = strtotime( 'now' );
				update_option( 'tiny_sdk_remind_me', $get_activation_time );
			}
		}

		if ( isset( $_GET['tiny_sdk_rated'] ) && ! empty( $_GET['tiny_sdk_rated'] ) ) {
			$tiny_sdk_rated = absint( $_GET['tiny_sdk_rated'] );
			if ( 1 == $tiny_sdk_rated ) {
				update_option( 'tiny_sdk_rated', 'yes' );
			}
		}
	}

	protected function tiny_sdk_current_admin_url() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

		if ( ! $uri ) {
			return '';
		}

		return remove_query_arg(
			[
				'_wpnonce',
				'_wc_notice_nonce',
				'wc_db_update',
				'wc_db_update_nonce',
				'wc-hide-notice',
				'tiny_sdk_spare_me',
				'tiny_sdk_remind_me',
				'tiny_sdk_rated',
			],
			admin_url( $uri )
		);
	}

	/**
	 * Display Admin Notice, asking for a review
	 **/
	public function tiny_sdk_display_admin_notice() {
	
		// WordPress global variable
		global $pagenow;
		$exclude = [
//			'themes.php',
//			'users.php',
//			'tools.php',
//			'options-general.php',
//			'options-writing.php',
//			'options-reading.php',
//			'options-discussion.php',
//			'options-media.php',
//			'options-permalink.php',
//			'options-privacy.php',
//			'admin.php',
//			'import.php',
//			'export.php',
//			'site-health.php',
//			'export-personal-data.php',
//			'erase-personal-data.php',
		];
		
		if ( ! in_array( $pagenow, $exclude ) ) {
			
			$args = [ '_wpnonce' => wp_create_nonce( 'tiny_sdk_notice_nonce' ) ];

			$dont_disturb = add_query_arg( $args + [ 'tiny_sdk_spare_me' => '1' ], $this->tiny_sdk_current_admin_url() );
			$remind_me    = add_query_arg( $args + [ 'tiny_sdk_remind_me' => '1' ], $this->tiny_sdk_current_admin_url() );
			$rated        = add_query_arg( $args + [ 'tiny_sdk_rated' => '1' ], $this->tiny_sdk_current_admin_url() );
			$reviewurl    = 'https://wordpress.org/support/plugin/tiny_sdk/reviews/?filter=5#new-post';
			$plugin_name  = 'Modules For Woocommerce';
			?>
			<div class="notice tiny_sdk-review-notice tiny_sdk-review-notice--extended">
				<div class="tiny_sdk-review-notice_content">
					<h3>Enjoying "<?php echo $plugin_name; ?>"? </h3>
					<p>Thank you for choosing "<string><?php echo $plugin_name; ?></string>". If you found our plugin useful, please consider giving us a 5-star rating on WordPress.org. Your feedback will motivate us to grow.</p>
					<div class="tiny_sdk-review-notice_actions">
						<a href="<?php echo esc_url( $reviewurl ); ?>" class="tiny_sdk-review-button tiny_sdk-review-button--cta" target="_blank"><span>⭐ Yes, You Deserve It!</span></a>
						<a href="<?php echo esc_url( $rated ); ?>" class="tiny_sdk-review-button tiny_sdk-review-button--cta tiny_sdk-review-button--outline"><span>😀 Already Rated!</span></a>
						<a href="<?php echo esc_url( $remind_me ); ?>" class="tiny_sdk-review-button tiny_sdk-review-button--cta tiny_sdk-review-button--outline"><span>🔔 Remind Me Later</span></a>
						<!-- <a href="--><?php // echo esc_url( $dont_disturb ); ?><!--" class="tiny_sdk-review-button tiny_sdk-review-button--cta tiny_sdk-review-button--error tiny_sdk-review-button--outline"><span>😐 No Thanks </span></a>-->
					</div>
				</div>
			</div>
			<style>
				.tiny_sdk-review-button--cta {
					--e-button-context-color: #5d3dfd;
					--e-button-context-color-dark: #5d3dfd;
					--e-button-context-tint: rgb(75 47 157/4%);
					--e-focus-color: rgb(75 47 157/40%);
				}

				.tiny_sdk-review-notice {
					position: relative;
					margin: 5px 20px 5px 2px;
					border: 1px solid #ccd0d4;
					background: #fff;
					box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
					font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
					border-inline-start-width: 4px;
				}

				.tiny_sdk-review-notice.notice {
					padding: 0;
				}

				.tiny_sdk-review-notice:before {
					position: absolute;
					top: -1px;
					bottom: -1px;
					left: -4px;
					display: block;
					width: 4px;
					background: -webkit-linear-gradient(bottom, #5d3dfd 0%, #6939c6 100%);
					background: linear-gradient(0deg, #5d3dfd 0%, #6939c6 100%);
					content: "";
				}

				.tiny_sdk-review-notice_content {
					padding: 20px;
				}

				.tiny_sdk-review-notice_actions > * + * {
					margin-inline-start: 8px;
					-webkit-margin-start: 8px;
					-moz-margin-start: 8px;
				}

				.tiny_sdk-review-notice p {
					margin: 0;
					padding: 0;
					line-height: 1.5;
				}

				p + .tiny_sdk-review-notice_actions {
					margin-top: 1rem;
				}

				.tiny_sdk-review-notice h3 {
					margin: 0;
					font-size: 1.0625rem;
					line-height: 1.2;
				}

				.tiny_sdk-review-notice h3 + p {
					margin-top: 8px;
				}

				.tiny_sdk-review-button {
					display: inline-block;
					padding: 0.4375rem 0.75rem;
					border: 0;
					border-radius: 3px;;
					background: var(--e-button-context-color);
					color: #fff;
					vertical-align: middle;
					text-align: center;
					text-decoration: none;
					white-space: nowrap;
				}

				.tiny_sdk-review-button:active {
					background: var(--e-button-context-color-dark);
					color: #fff;
					text-decoration: none;
				}

				.tiny_sdk-review-button:focus {
					outline: 0;
					background: var(--e-button-context-color-dark);
					box-shadow: 0 0 0 2px var(--e-focus-color);
					color: #fff;
					text-decoration: none;
				}

				.tiny_sdk-review-button:hover {
					background: var(--e-button-context-color-dark);
					color: #fff;
					text-decoration: none;
				}

				.tiny_sdk-review-button.focus {
					outline: 0;
					box-shadow: 0 0 0 2px var(--e-focus-color);
				}

				.tiny_sdk-review-button--error {
					--e-button-context-color: #682e36;
					--e-button-context-color-dark: #ae2131;
					--e-button-context-tint: rgba(215, 43, 63, 0.04);
					--e-focus-color: rgba(215, 43, 63, 0.4);
				}

				.tiny_sdk-review-button.tiny_sdk-review-button--outline {
					border: 1px solid;
					background: 0 0;
					color: var(--e-button-context-color);
				}

				.tiny_sdk-review-button.tiny_sdk-review-button--outline:focus {
					background: var(--e-button-context-tint);
					color: var(--e-button-context-color-dark);
				}

				.tiny_sdk-review-button.tiny_sdk-review-button--outline:hover {
					background: var(--e-button-context-tint);
					color: var(--e-button-context-color-dark);
				}
			</style>
			<?php
		}
	}

}
