<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DD_Widget_Manager
 * Manages the registration and enqueuing of all custom Elementor widgets and assets.
 */
class DD_Widget_Manager {

	/**
	 * Instance of the class.
	 *
	 * @var DD_Widget_Manager
	 */
	private static $instance = null;

	/**
	 * Singleton pattern implementation.
	 *
	 * @return DD_Widget_Manager
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Hooks into Elementor to register widgets and scripts.
	 */
	private function __construct() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Registers custom Elementor widgets.
	 * * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager instance.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		// Future expansion: Add logic here to check `get_option()` if a widget is enabled/disabled via a settings page.
		require_once plugin_dir_path( __DIR__ ) . 'widgets/class-dd-progress-slider.php';
		$widgets_manager->register( new \DD_Progress_Slider_Widget() );
	}

	/**
	 * Enqueues required frontend styles.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'dd-slider-style',
			plugins_url( 'assets/css/dd-slider.css', __DIR__ ),
			[],
			'1.0.0'
		);
	}

	/**
	 * Enqueues required frontend scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'dd-slider-script',
			plugins_url( 'assets/js/dd-slider.js', __DIR__ ),
			[ 'jquery', 'swiper' ], // Ensure Swiper is loaded beforehand
			'1.0.0',
			true
		);
	}
}