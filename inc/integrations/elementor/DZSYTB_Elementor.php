<?php
use Elementor\Plugin;

#[AllowDynamicProperties]
class DZSYTB_Elementor {

  /**
   * @param DZSYtBlock $dzsytb
   */
  function __construct(DZSYtBlock $dzsytb) {
    $this->dzsytb = $dzsytb;
    // Add Plugin actions
    add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);
    add_action('elementor/controls/controls_registered', [$this, 'init_controls']);

    add_action('elementor/preview/enqueue_styles', function () {
      DZSYTBView::enqueueFeScripts();
//      wp_enqueue_script('dzsap-elementor-preview-refresh-dzsap', DZSAP_BASE_URL . 'inc/php/compatibilities/elementor-preview-refresh-dzsap.js');
    });


  }

  function init_widgets() {

    include_once(DZSYTB_BASE_PATH . 'inc/integrations/elementor/DZSYTB_Elementor_Widget.php');
    Plugin::instance()->widgets_manager->register_widget_type(new DZSYTB_Elementor__Widget());
  }

  function init_controls() {

  }
}