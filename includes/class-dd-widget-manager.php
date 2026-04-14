<?php
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Class DD_Widget_Manager
 * Manages the registration and enqueuing of all custom Elementor widgets and assets.
 */
class DD_Widget_Manager
{

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
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Hooks into Elementor to register widgets and scripts.
	 */
	private function __construct()
	{
		add_action('elementor/widgets/register', [$this, 'register_widgets']);
		add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
		add_action('elementor/frontend/after_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	/**
	 * Registers custom Elementor widgets based on admin settings.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager instance.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		// Retrieve saved options from the database, defaulting to false if never saved
		$active_widgets = get_option( 'dd_addons_active_widgets', false );

		if ( false === $active_widgets ) {
			// Default state on first install: all enabled
			$is_progress_slider_enabled   = true;
			$is_hero_video_slider_enabled = true;
		} else {
			// Strict check: only enabled if the array key exists and equals 'yes'
			$is_progress_slider_enabled   = isset( $active_widgets['progress_slider'] ) && $active_widgets['progress_slider'] === 'yes';
			$is_hero_video_slider_enabled = isset( $active_widgets['hero_video_slider'] ) && $active_widgets['hero_video_slider'] === 'yes';
		}

		if ( $is_progress_slider_enabled ) {
			require_once plugin_dir_path( __DIR__ ) . 'widgets/class-dd-progress-slider.php';
			$widgets_manager->register( new \DD_Progress_Slider_Widget() );
		}

		if ( $is_hero_video_slider_enabled ) {
			require_once plugin_dir_path( __DIR__ ) . 'widgets/class-dd-hero-video-slider.php';
			$widgets_manager->register( new \DD_Hero_Video_Slider_Widget() );
		}
	}

	/**
	 * Enqueues required frontend styles.
	 *
	 * @return void
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style(
			'dd-slider-style',
			plugins_url('assets/css/dd-slider.css', __DIR__),
			[],
			'1.0.0'
		);
	}

	/**
	 * Enqueues required frontend scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script(
			'dd-slider-script',
			plugins_url('assets/js/dd-slider.js', __DIR__),
			['jquery', 'swiper'], // Ensure Swiper is loaded beforehand
			'1.0.0',
			true
		);
	}
}
