<?php

namespace TinySolutions\SDK\Common;

use TinySolutions\SDK\Traits\SingletonTrait;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * AssetsController
 */
class Assets {

	/**
	 * Singleton
	 */
	use SingletonTrait;

	/**
	 * @var object
	 */
	protected $loader;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Ajax URL
	 *
	 * @var string
	 */
	private $ajaxurl;

	/**
	 * Class Constructor
	 */

	/**
	 * @return void
	 */
	private function __construct() {
		$this->loader  = Loader::instance();
		$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : SDK_VERSION;
		/**
		 * Admin scripts.
		 */
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'backend_assets', 1 );
	}


	/**
	 * Registers Admin scripts.
	 *
	 * @return void
	 */
	public function backend_assets( $hook ) {

		$styles = [
			[
				'handle' => 'tiny_sdk-settings',
				'src'    => tiny_sdk()->get_assets_uri( 'css/backend/admin-settings.css' ),
			],
		];

		// Register public styles.
		foreach ( $styles as $style ) {
			wp_register_style( $style['handle'], $style['src'], '', $this->version );
		}

		$scripts = [
			[
				'handle' => 'tiny_sdk-settings',
				'src'    => tiny_sdk()->get_assets_uri( 'js/backend/admin-settings.js' ),
				'deps'   => [],
				'footer' => true,
			],
		];

		// Register public scripts.
		foreach ( $scripts as $script ) {
			wp_register_script( $script['handle'], $script['src'], $script['deps'], $this->version, $script['footer'] );
		}

		// WPml Create Issue
		wp_dequeue_style( 'wpml-tm-styles' );
		wp_dequeue_script( 'wpml-tm-scripts' );

		wp_enqueue_style( 'tiny_sdk-settings' );
		wp_enqueue_script( 'tiny_sdk-settings' );

		wp_localize_script(
			'tiny_sdk-settings',
			'tiny_sdkParams',
			[
				'ajaxUrl'                => esc_url( admin_url( 'admin-ajax.php' ) ),
				'adminUrl'               => esc_url( admin_url() ),
				'restApiUrl'             => esc_url_raw( rest_url() ), // site_url(rest_get_url_prefix()),
				'rest_nonce'             => wp_create_nonce( 'wp_rest' ),
				tiny_sdk()->nonceId => wp_create_nonce( tiny_sdk()->nonceId ),
			]
		);
	}
}
