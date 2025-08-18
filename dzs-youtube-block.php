<?php
/*
  Plugin Name: YouTube Block DZS
  Plugin URI: https://digitalzoomstudio.net/
  Description: Add a youtube block.
  Version: 1.0.0
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
    wp_die(__('Required plugin file not found. Please reinstall the plugin.', 'dzsytb'));
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
    wp_die(__('Configuration file not found. Please reinstall the plugin.', 'dzsytb'));
}

// Initialize the plugin
try {
    $dzsytb = DZSYTB();
} catch (Exception $e) {
    // Log error and display user-friendly message
    error_log('DZS YouTube Block initialization error: ' . $e->getMessage());
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>' . 
             __('DZS YouTube Block failed to initialize. Please check the error logs or reinstall the plugin.', 'dzsytb') . 
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