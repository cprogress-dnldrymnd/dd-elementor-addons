<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DD_Hero_Video_Slider_Widget
 * Elementor widget that outputs a centralized Swiper cards carousel surrounded by quadrant text fields.
 * * @author Digitally Disruptive - Donald Raymundo
 */
class DD_Hero_Video_Slider_Widget extends \Elementor\Widget_Base {

	/**
	 * Retrieves the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'dd_hero_video_slider';
	}

	/**
	 * Retrieves the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Hero Video Cards', 'dd-addons' );
	}

	/**
	 * Retrieves the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-slider-video';
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
	 * Retrieves the dependencies required for the widget.
	 *
	 * @return array Array of script handles.
	 */
	public function get_script_depends() {
		return [ 'swiper' ];
	}

	/**
	 * Registers the widget controls via a tabbed interface.
	 *
	 * @return void
	 */
	protected function register_controls() {

		// ==============================
		// TAB: CONTENT -> TEXT FIELDS
		// ==============================
		$this->start_controls_section(
			'section_text_content',
			[
				'label' => esc_html__( 'Hero Texts', 'dd-addons' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'text_1',
			[
				'label'   => esc_html__( 'Text 1 (Top Left)', 'dd-addons' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Find', 'dd-addons' ),
			]
		);

		$this->add_control(
			'text_2',
			[
				'label'   => esc_html__( 'Text 2 (Top Right)', 'dd-addons' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'your', 'dd-addons' ),
			]
		);

		$this->add_control(
			'text_3',
			[
				'label'   => esc_html__( 'Text 3 (Bottom Left)', 'dd-addons' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'perfect', 'dd-addons' ),
			]
		);

		$this->add_control(
			'text_4',
			[
				'label'   => esc_html__( 'Text 4 (Bottom Right)', 'dd-addons' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'creator', 'dd-addons' ),
			]
		);

		$this->end_controls_section();

		// ==============================
		// TAB: CONTENT -> VIDEOS
		// ==============================
		$this->start_controls_section(
			'section_videos',
			[
				'label' => esc_html__( 'Video Slides', 'dd-addons' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		// Tabbed interface implementation for repeater configuration
		$repeater->start_controls_tabs( 'video_configuration_tabs' );

		$repeater->start_controls_tab(
			'tab_video_media',
			[
				'label' => esc_html__( 'Video Media', 'dd-addons' ),
			]
		);

		$repeater->add_control(
			'video_file',
			[
				'label'      => esc_html__( 'Upload Video', 'dd-addons' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'media_types'=> [ 'video' ],
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'video_slides',
			[
				'label'       => esc_html__( 'Carousel Videos', 'dd-addons' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'video_file' => [ 'url' => '' ] ],
					[ 'video_file' => [ 'url' => '' ] ],
				],
				'title_field' => esc_html__( 'Video Slide', 'dd-addons' ),
			]
		);

		$this->end_controls_section();

		// ==============================
		// TAB: STYLE -> TEXT STYLING
		// ==============================
		$this->start_controls_section(
			'section_style_texts',
			[
				'label' => esc_html__( 'Text Global Styling', 'dd-addons' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Text Color', 'dd-addons' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dd-hero-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .dd-hero-text',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label'      => esc_html__( 'Grid Gap / Spacing', 'dd-addons' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dd-hero-text-wrap' => 'gap: {{SIZE}}{{UNIT}};',
				],
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

		?>
		<div class="dd-hero-video-container">
			<div class="dd-hero-text-wrap">
				<span class="dd-hero-text dd-text-1"><?php echo esc_html( $settings['text_1'] ); ?></span>
				<span class="dd-hero-text dd-text-2"><?php echo esc_html( $settings['text_2'] ); ?></span>
				<span class="dd-hero-text dd-text-3"><?php echo esc_html( $settings['text_3'] ); ?></span>
				<span class="dd-hero-text dd-text-4"><?php echo esc_html( $settings['text_4'] ); ?></span>
			</div>

			<div class="swiper-hero-vids">
				<div class="swiper swiper-hero-vids-wrapper">
					<div class="swiper-wrapper">
						<?php if ( ! empty( $settings['video_slides'] ) ) : ?>
							<?php foreach ( $settings['video_slides'] as $slide ) : ?>
								<?php if ( ! empty( $slide['video_file']['url'] ) ) : ?>
									<div class="swiper-slide elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ); ?>">
										<video class="elementor-video"
											src="<?php echo esc_url( $slide['video_file']['url'] ); ?>" 
											muted playsinline controlslist="nodownload"></video>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}