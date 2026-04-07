<?php

/**
 * Plugin Name: Digitally Disruptive Elementor Addons
 * Description: An OOP-based Elementor extension featuring a template-driven Swiper slider with progress navigation.
 * Plugin URI:  https://digitallydisruptive.co.uk/
 * Version:     1.0.0
 * Author:      Digitally Disruptive - Donald Raymundo
 * Author URI:  https://digitallydisruptive.co.uk/
 * Text Domain: dd-addons
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main Plugin Class
 * Responsible for bootstrapping the plugin and loading required dependencies.
 */
final class DD_Elementor_Addons
{

    /**
     * Plugin instance.
     *
     * @var DD_Elementor_Addons
     */
    private static $instance = null;

    /**
     * Retrieves the main instance of the plugin.
     *
     * @return DD_Elementor_Addons
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
     * Initializes hooks for loading the widget manager.
     */
    public function __construct()
    {
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Initializes the plugin by requiring core files once Elementor is ready.
     *
     * @return void
     */
    public function init()
    {
        // Initialize admin settings if in the backend
        if (is_admin()) {
            require_once plugin_dir_path(__FILE__) . 'includes/class-dd-admin-settings.php';
            DD_Admin_Settings::instance();
        }

        // Check if Elementor installed and activated
        if (! did_action('elementor/loaded')) {
            return;
        }

        require_once plugin_dir_path(__FILE__) . 'includes/class-dd-widget-manager.php';

        // Initialize the widget manager
        DD_Widget_Manager::instance();
    }
}

DD_Elementor_Addons::instance();
