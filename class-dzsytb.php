<?php
if ( ! defined( 'ABSPATH' ) ) exit;

include_once DZSYTB_BASE_PATH . 'inc/php/class-admin.php';
include_once DZSYTB_BASE_PATH . 'features/gutenberg/class-gutenberg.php';
include_once DZSYTB_BASE_PATH . 'inc/php/class-view.php';

#[AllowDynamicProperties]
class DZSYtBlock {
  public $db_main_options = '';
  private $isCanSession = true;
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
}
