<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DD_Admin_Settings
 * Handles the creation of the backend settings page to enable or disable specific widgets.
 */
class DD_Admin_Settings {

	/**
	 * Instance of the class.
	 *
	 * @var DD_Admin_Settings
	 */
	private static $instance = null;

	/**
	 * Singleton pattern implementation.
	 *
	 * @return DD_Admin_Settings
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Hooks into WordPress admin actions.
	 */
	private function __construct() {
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Adds the settings page to the WordPress admin menu under 'Settings'.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		add_options_page(
			esc_html__( 'DD Elementor Addons', 'dd-addons' ),
			esc_html__( 'DD Addons', 'dd-addons' ),
			'manage_options',
			'dd-elementor-addons',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Registers the plugin settings and fields using the WordPress Settings API.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'dd_addons_settings_group',
			'dd_addons_active_widgets'
		);

		add_settings_section(
			'dd_addons_widgets_section',
			esc_html__( 'Widget Manager', 'dd-addons' ),
			[ $this, 'render_section_description' ],
			'dd-elementor-addons'
		);

		add_settings_field(
			'progress_slider',
			esc_html__( 'Progress Slider Widget', 'dd-addons' ),
			[ $this, 'render_checkbox_field' ],
			'dd-elementor-addons',
			'dd_addons_widgets_section',
			[
				'id' => 'progress_slider',
			]
		);
	}

	/**
	 * Renders the description for the settings section.
	 *
	 * @return void
	 */
	public function render_section_description() {
		echo '<p>' . esc_html__( 'Check the boxes below to enable specific Elementor widgets. Uncheck to disable them and improve editor performance.', 'dd-addons' ) . '</p>';
	}

	/**
	 * Renders a checkbox input field for a specific setting.
	 *
	 * @param array $args Arguments passed from add_settings_field, containing the field 'id'.
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$options = get_option( 'dd_addons_active_widgets', [] );
		$id      = $args['id'];
		
		// Default to enabled if the option hasn't been saved yet
		$checked = ! isset( $options[ $id ] ) || $options[ $id ] === 'yes' ? 'checked' : '';

		printf(
			'<input type="checkbox" name="dd_addons_active_widgets[%1$s]" value="yes" %2$s />',
			esc_attr( $id ),
			esc_attr( $checked )
		);
	}

	/**
	 * Renders the HTML output for the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'dd_addons_settings_group' );
				do_settings_sections( 'dd-elementor-addons' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}