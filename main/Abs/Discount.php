<?php
/**
 * Discount
 */
namespace TinySolutions\SDK\Abs;

// Do not allow directly accessing this file.
use TinySolutions\SDK\Common\Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Black Friday Offer.
 */
abstract class Discount {
	/**
	 * @var
	 */
	protected $options = [];
	/**
	 * @var object
	 */
	protected $loader;
	
	/**
	 * Class Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->loader = Loader::instance();
		$this->loader->add_action( 'admin_init', $this, 'show_notice' );
	}

	/**
	 * @return array
	 */
	abstract public function the_options(): array;

	/**
	 * @return void
	 */
	public function show_notice() {
		$defaults      = [
			'is_condition'   => true,
			'check_pro'      => true,
			'download_link'  => 'https://www.wptinysolutions.com/tiny-products/admin-notice-centralization/',
			'plugin_name'    => 'Custom Post Type Woocommerce Integration Pro',
			'image_url'      => tiny_sdk()->get_assets_uri( 'images/cpt-woo-icon-150x150.png' ),
			'option_name'    => '',
			'start_date'     => '',
			'end_date'       => '',
			'notice_for'     => 'Cyber Monday Deal!!',
			'notice_message' => '',
			'show_by_button' => true,
		];
		$options       = apply_filters( 'tiny_sdk_offer_notice', $this->the_options() );
		$this->options = wp_parse_args( $options, $defaults );
		$current       = time();
		$start         = strtotime( $this->options['start_date'] );
		$end           = strtotime( $this->options['end_date'] );
		if ( ! ( $this->options['is_condition'] ?? false ) ) {
			return;
		}
		if ( ( $this->options['check_pro'] ?? false ) && tiny_sdk()->has_pro() ) {
			return;
		}
		// Black Friday Notice.
		if ( $start <= $current && $current <= $end ) {
			if ( get_option( $this->options['option_name'] ) != '1' ) {
				if ( ! isset( $GLOBALS['tiny_sdk__notice'] ) ) {
					$GLOBALS['tiny_sdk__notice'] = 'tiny_sdk__notice';
					$this->offer_notice();
				}
			}
		}
	}

	/**
	 * Black Friday Notice.
	 *
	 * @return void
	 */
	private function offer_notice() {
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_script( 'jquery' );
			}
		);

		add_action(
			'admin_notices',
			function () {
				?>
				<style>
					.tiny_sdkoffer-notice {
						--e-button-context-color: #2179c0;
						--e-button-context-color-dark: #2271b1;
						--e-button-context-tint: rgb(75 47 157/4%);
						--e-focus-color: rgb(75 47 157/40%);
						display: grid;
						grid-template-columns: 100px auto;
						padding-top: 12px;
						padding-bottom: 12px;
						column-gap: 15px;
					}

					.tiny_sdkoffer-notice img {
						grid-row: 1 / 4;
						align-self: center;
						justify-self: center;
					}

					.tiny_sdkoffer-notice h3,
					.tiny_sdkoffer-notice p {
						margin: 0 !important;
					}

					.tiny_sdkoffer-notice .notice-text {
						margin: 0 0 2px;
						padding: 5px 0;
						max-width: 100%;
						font-size: 14px;
					}

					.tiny_sdkoffer-notice .button-primary,
					.tiny_sdkoffer-notice .button-dismiss {
						display: inline-block;
						border: 0;
						border-radius: 3px;
						background: var(--e-button-context-color-dark);
						color: #fff;
						vertical-align: middle;
						text-align: center;
						text-decoration: none;
						white-space: nowrap;
						margin-right: 5px;
						transition: all 0.3s;
					}

					.tiny_sdkoffer-notice .button-primary:hover,
					.tiny_sdkoffer-notice .button-dismiss:hover {
						background: var(--e-button-context-color);
						border-color: var(--e-button-context-color);
						color: #fff;
					}

					.tiny_sdkoffer-notice .button-primary:focus,
					.tiny_sdkoffer-notice .button-dismiss:focus {
						box-shadow: 0 0 0 1px #fff, 0 0 0 3px var(--e-button-context-color);
						background: var(--e-button-context-color);
						color: #fff;
					}

					.tiny_sdkoffer-notice .button-dismiss {
						border: 1px solid;
						background: 0 0;
						color: var(--e-button-context-color);
						background: #fff;
					}
				</style>
				<div class="tiny_sdkoffer-notice notice notice-info is-dismissible"
					 data-tiny_sdkdismissable="tiny_sdk_offer">
					<img alt="<?php echo esc_attr( $this->options['plugin_name'] ); ?>"
						 src="<?php echo esc_url( $this->options['image_url'] ); ?>"
						 width="100px"
						 height="100px"/>
					<h3><?php echo sprintf( '%s – %s', esc_html( $this->options['plugin_name'] ), esc_html( $this->options['notice_for'] ) ); ?></h3>

					<p class="notice-text">
						<?php echo wp_kses_post( $this->options['notice_message'] ); ?>
					</p>
					<p>
						<?php if ( ( $this->options['show_by_button'] ?? false ) ) { ?>
						<a class="button button-primary"
						   href="<?php echo esc_url( $this->options['download_link'] ); ?>" target="_blank">Buy Now</a>
						<?php } ?>
						<a class="button button-dismiss" href="#">Dismiss</a>
					</p>
				</div>
				<?php
			}
		);

		add_action(
			'admin_footer',
			function () {
				?>
				<script type="text/javascript">
					(function ($) {
						$(function () {
							setTimeout(function () {
								$('div[data-tiny_sdkdismissable] .notice-dismiss, div[data-tiny_sdkdismissable] .button-dismiss')
									.on('click', function (e) {
										e.preventDefault();
										$.post(ajaxurl, {
											'action': 'tiny_sdk_dismiss_offer_admin_notice',
											'nonce': <?php echo wp_json_encode( wp_create_nonce( 'tiny_sdkoffer-dismissible-notice' ) ); ?>
										});
										$(e.target).closest('.is-dismissible').remove();
									});
							}, 1000);
						});
					})(jQuery);
				</script>
				<?php
			}
		);

		add_action(
			'wp_ajax_tiny_sdk_dismiss_offer_admin_notice',
			function () {
				check_ajax_referer( 'tiny_sdkoffer-dismissible-notice', 'nonce' );
				if ( ! empty( $this->options['option_name'] ) ) {
					update_option( $this->options['option_name'], '1' );
				}
				wp_die();
			}
		);
	}
}
