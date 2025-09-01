<?php
/*
  Plugin Name: Video Block Cover for YouTube DZS, Elementor
  Plugin URI: https://github.com/raduzoom/
  Description: Add a youtube block.
  Version: 1.0.0
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Author: Digital Zoom Studio
  Author URI: https://digitalzoomstudio.net/
 */

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

const DZSYTB_VERSION = '1.0.0';

// Define plugin constants with proper checks
if (function_exists('plugin_dir_url')) {
  define('DZSYTB_BASE_URL', plugin_dir_url(__FILE__));
}
if (function_exists('plugin_dir_path')) {
  define('DZSYTB_BASE_PATH', plugin_dir_path(__FILE__));
}

// Include main class file with existence check
$main_class_file = DZSYTB_BASE_PATH . 'class-dzsytb.php';
if (!file_exists($main_class_file)) {
    wp_die(esc_html__('Required plugin file not found. Please reinstall the plugin.', 'dzs-youtube-block'));
}

if (!class_exists('DZSYtBlock')) {
  include_once($main_class_file);
}

/**
 * Returns the main instance of DZSYtBlock.
 *
 */
function DZSYTB(): DZSYtBlock {
  return DZSYtBlock::instance();
}

// Include configuration file with existence check
$config_file = DZSYTB_BASE_PATH . 'configs/config.php';
if (file_exists($config_file)) {
    include_once($config_file);
} else {
    wp_die(esc_html__('Configuration file not found. Please reinstall the plugin.', 'dzs-youtube-block'));
}

// Initialize the plugin
try {
    $dzsytb = DZSYTB();
} catch (Exception $e) {
    // Log error and display user-friendly message
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>' .
             esc_html__('DZS YouTube Block failed to initialize. Please check the error logs or reinstall the plugin.', 'dzs-youtube-block') .
             '</p></div>';
    });
    return;
}

if (!function_exists('dzs_read_from_file_ob')) {

  function dzs_read_from_file_ob(string $filepath) {
    // -- @filepath - relative to dzs_functions

    // Security check: ensure filepath is within plugin directory
    $real_filepath = realpath($filepath);
    $plugin_path = realpath(DZSYTB_BASE_PATH);

    if ($real_filepath === false || strpos($real_filepath, $plugin_path) !== 0) {
        return '';
    }

    ob_start();
    include($filepath);
    return ob_get_clean();
  }

}
