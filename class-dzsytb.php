<?php
include_once DZSYTB_BASE_PATH . 'inc/php/class-admin.php';
include_once DZSYTB_BASE_PATH . 'features/gutenberg/class-gutenberg.php';
include_once DZSYTB_BASE_PATH . 'inc/php/class-view.php';

#[AllowDynamicProperties]
class DZSYtBlock {
  public $db_main_options = '';
  private $isCanSession = true;
  public $classAdmin;
  public $classFrontendBot;
  /**
   * The single instance of the class.
   *
   * @var DZSYtBlock
   * @since 1.0.0
   */
  protected static $_instance = null;

  /**
   * @var DZSYTBView
   *8 - update
   */
  public $classView;
  /**
   * @var DZSYTBGutenberg
   */
  public $classGutenberg;
  public $classElementor = null;

  function __construct() {

    $this->classGutenberg = new DZSYTBGutenberg($this);
    $this->classView = new DZSYTBView($this);

    add_action('init', array($this, 'handle_init'));

    add_action('plugins_loaded', array($this, 'handle_plugins_loaded'));



  }


  /**
   * Main WooCommerce Instance.
   * @return DZSYtBlock - Main instance.
   */
  public static function instance(): ?DZSYtBlock {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  function handle_init(): void {
    // Add security checks for admin actions
    if (is_admin() && isset($_POST['dzsytb_action'])) {
      $this->handle_admin_actions();
    }
  }

  function handle_plugins_loaded() {
    // Check if Elementor is active before including integration
    if (function_exists('is_plugin_active') && is_plugin_active('elementor/elementor.php')) {
      // Verify the file exists before including it
      $elementor_file = DZSYTB_BASE_PATH . 'inc/integrations/elementor/DZSYTB_Elementor.php';
      if (file_exists($elementor_file)) {
        include_once($elementor_file);
        $this->classElementor = new DZSYTB_Elementor($this);
      }
    }
  }

  /**
   * Handle admin actions with security checks
   */
  private function handle_admin_actions() {
    // Verify nonce for admin actions
    if (!isset($_POST['dzsytb_nonce']) || !wp_verify_nonce(wp_unslash($_POST['dzsytb_nonce']), 'dzsytb_action')) {
      wp_die(esc_html__('Security check failed. Please try again.', 'dzs-youtube-block'));
    }

    // Check user capabilities
    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('You do not have sufficient permissions to perform this action.', 'dzs-youtube-block'));
    }

    // Sanitize action type
    $action = sanitize_text_field($_POST['dzsytb_action'] ?? '');

    switch ($action) {
      case 'save_settings':
        $this->handle_save_settings();
        break;
      case 'reset_settings':
        $this->handle_reset_settings();
        break;
      default:
        wp_die(esc_html__('Invalid action specified.', 'dzs-youtube-block'));
    }
  }

  /**
   * Handle saving plugin settings
   */
  private function handle_save_settings() {
    // Sanitize and validate settings data
    $settings = $this->sanitize_settings($_POST['dzsytb_settings'] ?? array());

    // Save settings (implement your save logic here)
    update_option('dzsytb_settings', $settings);

    // Redirect with success message
    wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=dzsytb-mo')));
    exit;
  }

  /**
   * Handle resetting plugin settings
   */
  private function handle_reset_settings() {
    // Delete settings
    delete_option('dzsytb_settings');

    // Redirect with success message
    wp_redirect(add_query_arg('settings-reset', 'true', admin_url('admin.php?page=dzsytb-mo')));
    exit;
  }

  /**
   * Sanitize plugin settings
   *
   * @param array $settings The settings to sanitize
   * @return array The sanitized settings
   */
  private function sanitize_settings($settings) {
    $sanitized = array();

    if (is_array($settings)) {
      foreach ($settings as $key => $value) {
        if (is_string($value)) {
          $sanitized[$key] = sanitize_text_field($value);
        } elseif (is_array($value)) {
          $sanitized[$key] = $this->sanitize_settings($value);
        } elseif (is_numeric($value)) {
          $sanitized[$key] = intval($value);
        } elseif (is_bool($value)) {
          $sanitized[$key] = (bool) $value;
        } else {
          $sanitized[$key] = $value;
        }
      }
    }

    return $sanitized;
  }
}
