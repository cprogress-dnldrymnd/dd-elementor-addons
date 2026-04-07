<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DD_Progress_Slider_Widget
 * Elementor widget that outputs a Swiper carousel consisting of Elementor Templates with progress-bar navigation.
 */
class DD_Progress_Slider_Widget extends \Elementor\Widget_Base {

	/**
	 * Retrieves the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'dd_progress_slider';
	}

	/**
	 * Retrieves the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Progress Slider', 'dd-addons' );
	}

	/**
	 * Retrieves the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-slides';
	}

	/**
	 * Retrieves the widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Retrieves saved Elementor templates for the dropdown selection.
	 *
	 * @return array Array of templates formatted for a Select control.
	 */
	protected function get_elementor_templates() {
		$templates = get_posts( [
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		] );

		$options = [ '' => esc_html__( '— Select Template —', 'dd-addons' ) ];

		foreach ( $templates as $template ) {
			$options[ $template->ID ] = $template->post_title;
		}

		return $options;
	}

	/**
	 * Registers the widget controls via a tabbed interface.
	 *
	 * @return void
	 */
	protected function register_controls() {

		// ==============================
		// TAB: CONTENT
		// ==============================
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides Configuration', 'dd-addons' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'nav_label',
			[
				'label'       => esc_html__( 'Navigation Label', 'dd-addons' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Slide Item', 'dd-addons' ),
				'label_block' => true,
				'description' => esc_html__( 'The text displayed on the bottom progress navigation.', 'dd-addons' ),
			]
		);

		$repeater->add_control(
			'template_id',
			[
				'label'       => esc_html__( 'Select Elementor Template', 'dd-addons' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $this->get_elementor_templates(),
				'label_block' => true,
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Carousel Slides', 'dd-addons' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'nav_label' => esc_html__( 'Bespoke', 'dd-addons' ) ],
					[ 'nav_label' => esc_html__( 'Display Models', 'dd-addons' ) ],
				],
				'title_field' => '{{{ nav_label }}}',
			]
		);

		$this->end_controls_section();

		// ==============================
		// TAB: SETTINGS (Content)
		// ==============================
		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'dd-addons' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'autoplay_delay',
			[
				'label'   => esc_html__( 'Autoplay Delay (ms)', 'dd-addons' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 5000,
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label'   => esc_html__( 'Transition Speed (ms)', 'dd-addons' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Renders the widget output on the frontend.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['slides'] ) ) {
			return;
		}

		// Prepare configuration payload for the frontend JS
		$swiper_options = [
			'autoplay_delay' => absint( $settings['autoplay_delay'] ),
			'speed'          => absint( $settings['transition_speed'] ),
		];

		$this->add_render_attribute( 'wrapper', [
			'class'           => 'dd-progress-slider-wrapper',
			'data-dd-options' => wp_json_encode( $swiper_options ),
		] );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="swiper dd-swiper-container">
				<div class="swiper-wrapper">
					<?php
					// Loop through repeater items
					foreach ( $settings['slides'] as $index => $slide ) :
						?>
						<div class="swiper-slide dd-swiper-slide">
							<?php
							// Render the selected Elementor template
							if ( ! empty( $slide['template_id'] ) ) {
								echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $slide['template_id'] );
							} else {
								echo '<div class="dd-placeholder">' . esc_html__( 'Please select a template.', 'dd-addons' ) . '</div>';
							}
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="dd-slider-navigation">
				<?php foreach ( $settings['slides'] as $index => $slide ) : ?>
					<div class="dd-nav-item" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="dd-nav-progress-bg"></div>
						<div class="dd-nav-progress-fill"></div>
						<span class="dd-nav-label"><?php echo esc_html( $slide['nav_label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}