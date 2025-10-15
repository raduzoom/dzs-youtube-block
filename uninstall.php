<?php
/**
 * Uninstall DZS YouTube Block Plugin
 *
 * This file is executed when the plugin is deleted from WordPress.
 * It cleans up all plugin data, options, and any other traces.
 *
 * @package DZSYouTubeBlock
 * @version 1.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
  exit;
}

// Check if user has permission to uninstall
if (!current_user_can('activate_plugins')) {
  return;
}

// Define plugin constants if not already defined
if (!defined('DZSYTB_VERSION')) {
  define('DZSYTB_VERSION', '1.0.0');
}

// Clean up plugin options
$options_to_delete = array(
  'dzsytb_settings',
  'dzsytb_version',
  'dzsytb_activated',
  'dzsytb_db_version'
);

foreach ($options_to_delete as $option) {
  delete_option($option);
  delete_site_option($option); // For multisite
}

// Clean up user meta (if any)
// Note: Direct query is necessary for bulk deletion during uninstall
global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- bulk cleanup during uninstall; no caching appropriate
$wpdb->query(
  $wpdb->prepare(
    "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
    'dzsytb_%'
  )
);

// Clean up post meta (if any)
// Note: Direct query is necessary for bulk deletion during uninstall
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- bulk cleanup during uninstall; no caching appropriate
$wpdb->query(
  $wpdb->prepare(
    "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
    'dzsytb_%'
  )
);

// Clean up any custom database tables (if created)
// Note: Schema changes are necessary during uninstall to remove plugin tables
$tables_to_drop = array(
  $wpdb->prefix . 'dzsytb_logs',
  $wpdb->prefix . 'dzsytb_analytics'
);

foreach ($tables_to_drop as $table) {
  // Validate identifier (table name) and avoid unsupported %i placeholder
  if (strpos($table, $wpdb->prefix) !== 0) {
    continue;
  }
  if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
    continue;
  }
  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- removing plugin tables during uninstall by design
  $wpdb->query(
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange -- identifier validated above; schema change intentional during uninstall
    "DROP TABLE IF EXISTS `{$table}`"
  );
}

// Clean up any transients
// Note: Direct queries are necessary for bulk deletion during uninstall
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- bulk cleanup during uninstall; no caching appropriate
$wpdb->query(
  $wpdb->prepare(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
    '_transient_dzsytb_%'
  )
);
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- bulk cleanup during uninstall; no caching appropriate
$wpdb->query(
  $wpdb->prepare(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
    '_transient_timeout_dzsytb_%'
  )
);

// Clean up any scheduled events
wp_clear_scheduled_hook('dzsytb_cleanup');
wp_clear_scheduled_hook('dzsytb_analytics');

// Remove any custom capabilities (if added)
$admin_role = get_role('administrator');
if ($admin_role) {
  $capabilities_to_remove = array(
    'manage_dzsytb',
    'edit_dzsytb',
    'delete_dzsytb'
  );

  foreach ($capabilities_to_remove as $cap) {
    $admin_role->remove_cap($cap);
  }
}

// Clean up any uploaded files in wp-content/uploads/dzsytb/ (if exists)
$upload_dir = wp_upload_dir();
$plugin_upload_dir = $upload_dir['basedir'] . '/dzsytb/';

if (is_dir($plugin_upload_dir)) {
  // Recursively remove directory and contents
  function dzsytb_remove_directory($dir) {
    if (is_dir($dir)) {
      $files = array_diff(scandir($dir), array('.', '..'));
      foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
          dzsytb_remove_directory($path);
        } else {
          wp_delete_file($path);
        }
      }

      $wp_filesystem_base = WP_Filesystem_Base();
      $wp_filesystem_base->rmdir(dirname($dir), true);
    }
    return false;
  }

  dzsytb_remove_directory($plugin_upload_dir);
}


// Clear any cached data
if (function_exists('wp_cache_flush')) {
  wp_cache_flush();
}

// Clear object cache if using external caching
if (function_exists('wp_cache_clear_cache')) {
  wp_cache_clear_cache();
}
