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
	 * Registers the widget controls.
	 * Optimized to include Source Switching and Button Repeaters.
	 */
	protected function register_controls() {

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
			]
		);

		$repeater->add_control(
			'source_type',
			[
				'label'   => esc_html__( 'Carousel Source', 'dd-addons' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'template',
				'options' => [
					'template' => esc_html__( 'Elementor Template', 'dd-addons' ),
					'custom'   => esc_html__( 'Custom Fields', 'dd-addons' ),
				],
			]
		);

		// --- Template Option ---
		$repeater->add_control(
			'template_id',
			[
				'label'     => esc_html__( 'Select Template', 'dd-addons' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_elementor_templates(),
				'condition' => [ 'source_type' => 'template' ],
			]
		);

		// --- Custom Fields Option ---
		$repeater->add_control(
			'heading',
			[
				'label'     => esc_html__( 'Heading', 'dd-addons' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'condition' => [ 'source_type' => 'custom' ],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'     => esc_html__( 'Description', 'dd-addons' ),
				'type'      => \Elementor\Controls_Manager::TEXTAREA,
				'condition' => [ 'source_type' => 'custom' ],
			]
		);

		// Nested Button Repeater
		$button_repeater = new \Elementor\Repeater();
		
		$button_repeater->add_control(
			'btn_text',
			[
				'label' => esc_html__( 'Button Text', 'dd-addons' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$button_repeater->add_control(
			'btn_link',
			[
				'label' => esc_html__( 'Link', 'dd-addons' ),
				'type'  => \Elementor\Controls_Manager::URL,
			]
		);

		$repeater->add_control(
			'buttons',
			[
				'label'       => esc_html__( 'Buttons', 'dd-addons' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $button_repeater->get_controls(),
				'condition'   => [ 'source_type' => 'custom' ],
				'title_field' => '{{{ btn_text }}}',
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Carousel Slides', 'dd-addons' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ nav_label }}}',
			]
		);

		$this->end_controls_section();
        
        // Settings Section (Unchanged for Autoplay/Speed)
        $this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'dd-addons' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        // ... (Refer to previous code for Autoplay Delay and Speed controls)
        $this->end_controls_section();
	}

	/**
	 * Renders the widget output on the frontend.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( empty( $settings['slides'] ) ) return;

		$this->add_render_attribute( 'wrapper', [
			'class'           => 'dd-progress-slider-wrapper',
			'data-dd-options' => wp_json_encode([
				'autoplay_delay' => absint( $settings['autoplay_delay'] ),
				'speed'          => absint( $settings['transition_speed'] ),
			]),
		] );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="swiper dd-swiper-container">
				<div class="swiper-wrapper">
					<?php foreach ( $settings['slides'] as $slide ) : ?>
						<div class="swiper-slide dd-swiper-slide">
							<?php if ( 'template' === $slide['source_type'] ) : 
								echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $slide['template_id'] );
							else : ?>
								<div class="dd-custom-slide-content">
									<?php if ( $slide['heading'] ) : ?>
										<h2 class="dd-slide-title"><?php echo esc_html( $slide['heading'] ); ?></h2>
									<?php endif; ?>
									
									<?php if ( $slide['description'] ) : ?>
										<p class="dd-slide-desc"><?php echo esc_html( $slide['description'] ); ?></p>
									<?php endif; ?>

									<?php if ( ! empty( $slide['buttons'] ) ) : ?>
										<div class="dd-slide-buttons">
											<?php foreach ( $slide['buttons'] as $index => $btn ) : 
												// Index 0 = Solid, Index 1+ = Outline
												$btn_class = ( 0 === $index ) ? 'dd-btn-solid' : 'dd-btn-outline';
												$link_url  = ! empty( $btn['btn_link']['url'] ) ? $btn['btn_link']['url'] : '#';
												?>
												<a href="<?php echo esc_url( $link_url ); ?>" class="dd-btn <?php echo esc_attr( $btn_class ); ?>">
													<?php echo esc_html( $btn['btn_text'] ); ?>
												</a>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="dd-slider-navigation">
				<?php foreach ( $settings['slides'] as $index => $slide ) : ?>
					<div class="dd-nav-item" data-index="<?php echo esc_attr( $index ); ?>">
						<div class="dd-nav-progress-fill"></div>
						<span class="dd-nav-label"><?php echo esc_html( $slide['nav_label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}