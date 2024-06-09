<?php
/**
 * Main initialization class.
 *
 * @package TinySolutions\tiny_sdk
 */

namespace TinySolutions\SDK;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}


use TinySolutions\ANCENTER\Admin\Dependencies;
use TinySolutions\SDK\Common\Assets;
use TinySolutions\SDK\Common\Loader;
use TinySolutions\SDK\Traits\SingletonTrait;
use TinySolutions\SDK\Admin\Review;

/**
 * Main initialization class.
 */
final class Plugin {
	/**
	 * Singleton
	 */
	use SingletonTrait;

	/**
	 * Nonce id
	 *
	 * @var string
	 */
	public $nonceId = 'tiny_sdk_wpnonce';

	/**
	 * Post Type.
	 *
	 * @var string
	 */
	public $current_theme;
	/**
	 * Post Type.
	 *
	 * @var string
	 */
	public $category = 'tiny_sdk_category';

	/**
	 * @var object
	 */
	protected $loader;
	/**
	 * Class Constructor
	 */
	private function __construct() {
//		$this->loader = Loader::instance();
//		$this->run();
	}
	/**
	 * Load Text Domain
	 */
	public function plugins_loaded() {
	}

	/**
	 * Assets url generate with given assets file
	 *
	 * @param string $file File.
	 *
	 * @return string
	 */
	public function get_assets_uri( $file ) {
		$file = ltrim( $file, '/' );
		return trailingslashit( SDK_URL . '/assets' ) . $file;
	}

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init() {
//		Review::instance();
//		// Include File.
//		Assets::instance();
	}
	
	/**
	 * @return void
	 */
	private function run() {
		$this->init();
		$this->loader->run();
	}
	
}
