<?php
/*
  Plugin Name: YouTube Block DZS
  Plugin URI: https://digitalzoomstudio.net/
  Description: Add a youtube block.
  Version: 1.0.0
  Author: Digital Zoom Studio
  Author URI: https://digitalzoomstudio.net/
 */


const DZSYTB_VERSION = '1.0.0';

if (function_exists('plugin_dir_url')) {
  define('DZSYTB_BASE_URL', plugin_dir_url(__FILE__));
}
if (function_exists('plugin_dir_path')) {
  define('DZSYTB_BASE_PATH', plugin_dir_path(__FILE__));
}

if (!class_exists('DZSYtBlock')) {
  include_once(DZSYTB_BASE_PATH . '/class-dzsytb.php');
}



/**
 * Returns the main instance of DZSYtBlock.
 *
 */
function DZSYTB(): DZSYtBlock {
  return DZSYtBlock::instance();
}

include_once 'configs/config.php';
$dzsytb = DZSYTB();



if (!function_exists('dzs_read_from_file_ob')) {

  function dzs_read_from_file_ob(string $filepath) {
    // -- @filepath - relative to dzs_functions
    ob_start();
    include($filepath);
    return ob_get_clean();
  }

}