<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class DD_Progress_Slider_Widget
 * Elementor widget that outputs a Swiper carousel consisting of Elementor Templates or Custom Content with progress-bar navigation.
 */
class DD_Progress_Slider_Widget extends \Elementor\Widget_Base
{

    /**
     * Retrieves the widget name.
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'dd_progress_slider';
    }

    /**
     * Retrieves the widget title.
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return esc_html__('Progress Slider', 'dd-addons');
    }

    /**
     * Retrieves the widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-slides';
    }

    /**
     * Retrieves the widget categories.
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Retrieves saved Elementor templates for the dropdown selection.
     *
     * @return array Array of templates formatted for a Select control.
     */
    protected function get_elementor_templates()
    {
        $templates = get_posts([
            'post_type'      => 'elementor_library',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        $options = ['' => esc_html__('— Select Template —', 'dd-addons')];

        foreach ($templates as $template) {
            $options[$template->ID] = $template->post_title;
        }

        return $options;
    }

    /**
     * Registers the widget controls via a tabbed interface.
     * Includes grouped tabs for Content, Background, and Background Overlay.
     *
     * @return void
     */
    protected function register_controls()
    {

        // ==============================
        // TAB: CONTENT
        // ==============================
        $this->start_controls_section(
            'section_slides',
            [
                'label' => esc_html__('Slides Configuration', 'dd-addons'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->start_controls_tabs('slide_configuration_tabs');

        // ------------------------------
        // REPEATER SUB-TAB: CONTENT
        // ------------------------------
        $repeater->start_controls_tab(
            'tab_slide_content',
            [
                'label' => esc_html__('Content', 'dd-addons'),
            ]
        );

        $repeater->add_control(
            'nav_label',
            [
                'label'       => esc_html__('Navigation Label', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Slide Item', 'dd-addons'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'source_type',
            [
                'label'   => esc_html__('Carousel Source', 'dd-addons'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'template',
                'options' => [
                    'template' => esc_html__('Elementor Template', 'dd-addons'),
                    'custom'   => esc_html__('Custom Content', 'dd-addons'),
                ],
            ]
        );

        $repeater->add_control(
            'template_id',
            [
                'label'       => esc_html__('Select Elementor Template', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'options'     => $this->get_elementor_templates(),
                'label_block' => true,
                'condition'   => [
                    'source_type' => 'template',
                ],
            ]
        );

        $repeater->add_control(
            'text_align',
            [
                'label'   => esc_html__('Text Alignment', 'dd-addons'),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left'   => [
                        'title' => esc_html__('Left', 'dd-addons'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'dd-addons'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'dd-addons'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'condition' => [
                    'source_type' => 'custom',
                ],
            ]
        );

        $repeater->add_control(
            'custom_heading',
            [
                'label'       => esc_html__('Heading', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Custom Slide Heading', 'dd-addons'),
                'label_block' => true,
                'condition'   => [
                    'source_type' => 'custom',
                ],
            ]
        );

        $repeater->add_control(
            'custom_description',
            [
                'label'       => esc_html__('Description', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => esc_html__('Provide a brief description for this slide.', 'dd-addons'),
                'condition'   => [
                    'source_type' => 'custom',
                ],
            ]
        );

        $repeater->add_control(
            'button_1_text',
            [
                'label'       => esc_html__('Button 1 Text (Solid)', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__('Learn More', 'dd-addons'),
                'condition'   => [
                    'source_type' => 'custom',
                ],
            ]
        );

        $repeater->add_control(
            'button_1_link',
            [
                'label'       => esc_html__('Button 1 Link', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::URL,
                'condition'   => [
                    'source_type' => 'custom',
                ],
            ]
        );

        $repeater->add_control(
            'button_2_text',
            [
                'label'       => esc_html__('Button 2 Text (Outline)', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'condition'   => [
                    'source_type' => 'custom',
                ],
            ]
        );

        $repeater->add_control(
            'button_2_link',
            [
                'label'       => esc_html__('Button 2 Link', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::URL,
                'condition'   => [
                    'source_type' => 'custom',
                ],
            ]
        );

        $repeater->end_controls_tab();

        // ------------------------------
        // REPEATER SUB-TAB: BACKGROUND
        // ------------------------------
        $repeater->start_controls_tab(
            'tab_slide_background',
            [
                'label' => esc_html__('Background', 'dd-addons'),
            ]
        );

        $repeater->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'slide_bg',
                'label'    => esc_html__('Slide Background', 'dd-addons'),
                'types'    => ['classic', 'gradient', 'video'],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}',
            ]
        );

        $repeater->end_controls_tab();

        // ------------------------------
        // REPEATER SUB-TAB: OVERLAY
        // ------------------------------
        $repeater->start_controls_tab(
            'tab_slide_overlay',
            [
                'label' => esc_html__('Overlay', 'dd-addons'),
            ]
        );

        $repeater->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'slide_bg_overlay',
                'label'    => esc_html__('Background Overlay', 'dd-addons'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .dd-slide-overlay',
            ]
        );

        $repeater->add_control(
            'slide_bg_overlay_opacity',
            [
                'label'   => esc_html__('Opacity', 'dd-addons'),
                'type'    => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.5,
                ],
                'range' => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .dd-slide-overlay' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'slides',
            [
                'label'       => esc_html__('Carousel Slides', 'dd-addons'),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    ['nav_label' => esc_html__('Bespoke', 'dd-addons'), 'source_type' => 'template'],
                    ['nav_label' => esc_html__('Display Models', 'dd-addons'), 'source_type' => 'custom'],
                ],
                'title_field' => '{{{ nav_label }}}',
            ]
        );

        $this->end_controls_section();

        // ==============================
        // TAB: SETTINGS
        // ==============================
        $this->start_controls_section(
            'section_settings',
            [
                'label' => esc_html__('Slider Settings', 'dd-addons'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'slider_min_height',
            [
                'label'      => esc_html__('Minimum Height', 'dd-addons'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', 'em', 'rem'],
                'range'      => [
                    'px' => ['min' => 200, 'max' => 1200],
                    'vh' => ['min' => 10, 'max' => 100],
                ],
                'default'    => ['unit' => 'px', 'size' => 600],
                'selectors'  => [
                    '{{WRAPPER}} .dd-swiper-container' => 'min-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dd-custom-slide-content' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'autoplay_delay',
            [
                'label'   => esc_html__('Autoplay Delay (ms)', 'dd-addons'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 5000,
            ]
        );

        $this->add_control(
            'transition_speed',
            [
                'label'   => esc_html__('Transition Speed (ms)', 'dd-addons'),
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
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['slides'])) {
            return;
        }

        $swiper_options = [
            'autoplay_delay' => absint($settings['autoplay_delay']),
            'speed'          => absint($settings['transition_speed']),
        ];

        $this->add_render_attribute('wrapper', [
            'class'           => 'dd-progress-slider-wrapper',
            'data-dd-options' => wp_json_encode($swiper_options),
        ]);
?>
        <div <?php $this->print_render_attribute_string('wrapper'); ?>>
            <div class="swiper dd-swiper-container">
                <div class="swiper-wrapper">
                    <?php
                    foreach ($settings['slides'] as $index => $slide) :
                        $repeater_class = 'elementor-repeater-item-' . esc_attr($slide['_id']);
                        $align_class    = ! empty($slide['text_align']) ? 'dd-align-' . esc_attr($slide['text_align']) : 'dd-align-center';

                        // Intercept and generate raw HTML payload for video backgrounds
                        $bg_type    = $slide['slide_bg_background'] ?? '';
                        $video_html = '';
                        if ('video' === $bg_type && ! empty($slide['slide_bg_video_link'])) {
                            $video_url = $slide['slide_bg_video_link'];
                            $video_html .= '<div class="dd-bg-video-wrapper">';

                            if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $video_url, $match);
                                $yt_id = $match[1] ?? '';
                                if ($yt_id) {
                                    $video_html .= '<iframe src="https://www.youtube.com/embed/' . esc_attr($yt_id) . '?autoplay=1&mute=1&loop=1&controls=0&showinfo=0&rel=0&playsinline=1&playlist=' . esc_attr($yt_id) . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
                                }
                            } elseif (strpos($video_url, 'vimeo.com') !== false) {
                                preg_match('/vimeo\.com\/([0-9]+)/i', $video_url, $match);
                                $vimeo_id = $match[1] ?? '';
                                if ($vimeo_id) {
                                    $video_html .= '<iframe src="https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '?autoplay=1&loop=1&muted=1&background=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                                }
                            } else {
                                $video_html .= '<video src="' . esc_url($video_url) . '" autoplay muted loop playsinline></video>';
                            }
                            $video_html .= '</div>';
                        }
                    ?>
                        <div class="swiper-slide dd-swiper-slide <?php echo $repeater_class; ?>">

                            <?php echo $video_html; // Injects behind content plane 
                            ?>

                            <div class="dd-slide-overlay"></div>

                            <div class="dd-slide-inner-content">
                                <?php
                                if ('template' === $slide['source_type']) {
                                    if (! empty($slide['template_id'])) {
                                        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($slide['template_id']);
                                    } else {
                                        echo '<div class="dd-placeholder">' . esc_html__('Please select a template.', 'dd-addons') . '</div>';
                                    }
                                } else {
                                ?>
                                    <div class="dd-custom-slide-content <?php echo esc_attr($align_class); ?>">
                                        <?php if (! empty($slide['custom_heading'])) : ?>
                                            <h2 class="dd-slide-heading"><?php echo esc_html($slide['custom_heading']); ?></h2>
                                        <?php endif; ?>

                                        <?php if (! empty($slide['custom_description'])) : ?>
                                            <div class="dd-slide-description">
                                                <?php echo wp_kses_post(nl2br($slide['custom_description'])); ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="dd-slide-actions">
                                            <?php if (! empty($slide['button_1_text'])) : ?>
                                                <a href="<?php echo esc_url($slide['button_1_link']['url'] ?? '#'); ?>" class="dd-btn dd-btn-solid">
                                                    <?php echo esc_html($slide['button_1_text']); ?>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (! empty($slide['button_2_text'])) : ?>
                                                <a href="<?php echo esc_url($slide['button_2_link']['url'] ?? '#'); ?>" class="dd-btn dd-btn-outline">
                                                    <?php echo esc_html($slide['button_2_text']); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="dd-slider-navigation">
                <?php foreach ($settings['slides'] as $index => $slide) : ?>
                    <div class="dd-nav-item" data-index="<?php echo esc_attr($index); ?>">
                        <div class="dd-nav-progress-bg"></div>
                        <div class="dd-nav-progress-fill"></div>
                        <span class="dd-nav-label"><?php echo esc_html($slide['nav_label']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<?php
    }
}
