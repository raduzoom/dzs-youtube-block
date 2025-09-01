<?php
use Elementor\Plugin;

#[AllowDynamicProperties]
class DZSYTB_Elementor {

  /**
   * @param DZSYtBlock $dzsytb
   */
  function __construct(DZSYtBlock $dzsytb) {
    $this->dzsytb = $dzsytb;

    // Check if user has permission to use Elementor
    if (!current_user_can('edit_posts')) {
      return;
    }

    // Add Plugin actions
    add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
    add_action('elementor/controls/controls_registered', [$this, 'init_controls']);

    add_action('elementor/preview/enqueue_styles', function () {
      DZSYTBView::enqueueFeScripts();
//      wp_enqueue_script('dzsap-elementor-preview-refresh-dzsap', DZSAP_BASE_URL . 'inc/php/compatibilities/elementor-preview-refresh-dzsap.js');
    });


  }

  function init_widgets() {
    // Check if user has permission to register widgets
    if (!current_user_can('edit_posts')) {
      return;
    }

    $widget_file = DZSYTB_BASE_PATH . 'inc/integrations/elementor/DZSYTB_Elementor_Widget.php';

    // Verify the widget file exists before including it
    if (!file_exists($widget_file)) {
      return;
    }

    include_once($widget_file);

    // Check if the widget class exists before instantiating
    if (class_exists('DZSYTB_Elementor__Widget')) {
      Plugin::instance()->widgets_manager->register_widget_type(new DZSYTB_Elementor__Widget());
    }
  }

  function init_controls() {
    // Check if user has permission to register controls
    if (!current_user_can('edit_posts')) {
      return;
    }

    // Add custom controls if needed
  }
}
