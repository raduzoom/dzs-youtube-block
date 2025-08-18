<?php


if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}


if (!function_exists('dzs_is_option_for_this')) {
  function dzs_is_option_for_this($oim, $seekedTag) {

    if (isset($oim['it_is_for']) && $oim['it_is_for']) {
      if (is_array($oim['it_is_for'])) {
        if (in_array($seekedTag, $oim['it_is_for'])) {
          return true;
        }
        return false;
      } else {
        if ($oim['it_is_for'] == $seekedTag) {
          return true;
        }
        return false;
      }
    }
    return true;
  }
}
class DZSYTB_Elementor__Widget extends \Elementor\Widget_Base {

  public static $slug = 'elementor-dzsytb-playlist';
  public static $controlsId = 'elementor-dzsytb-playlist';
  protected function _register_controls() {

    global $dzsytb;




    $this->start_controls_section(
      'content_section',
      [
        'label' => esc_html__('Content', DZSYTB_ID),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );


    $playerOptions = include(DZSYTB_BASE_PATH . 'configs/config-gutenberg-player.php');


    $arrOptions = array();






    $arrOptions = self::mapToElementor($this, $playerOptions, 'main');



    foreach ($arrOptions as $arrOption){
      $this->add_control($arrOption['controlName'], $arrOption['controlArgs']);
    }


    $this->end_controls_section();




  }

  static function mapToElementor($elm, $optionsItemMeta, $seekedCategory) {
    $arrOptions = array();
    foreach ($optionsItemMeta as $key => $configOption) {

      if (isset($configOption['category']) && $configOption['category'] === $seekedCategory) {

//        if (!dzs_is_option_for_this($configOption, 'elementor')) {
//          continue;
//        }

        $controlName = $configOption['name'];


        $placeholder = '';
        $default = '';

        if (isset($configOption['default'])) {
          $default = $configOption['default'];
        }
        if (isset($configOption['default'])) {
          $placeholder = $configOption['default'];
        }
        $controlArgs = [
          'label' => $configOption['title'],
          'placeholder' => $placeholder,
          'default' => $default,
        ];

        if ($configOption['type'] === 'text') {
          $controlArgs['type'] = \Elementor\Controls_Manager::TEXT;
        }
        if ($configOption['type'] === 'textarea') {
          $controlArgs['type'] = \Elementor\Controls_Manager::TEXTAREA;
          if (isset($configOption['extra_type']) && $configOption['extra_type'] === 'WYSIWYG') {
            $controlArgs['type'] = \Elementor\Controls_Manager::WYSIWYG;
          }
        }
        if ($configOption['type'] === 'attach') {
          $controlArgs['type'] = \Elementor\Controls_Manager::MEDIA;

          if (isset($configOption['upload_type'])){

            $controlArgs['media_type'] = $configOption['upload_type'];
            if($configOption['upload_type'] == 'audio') {
              $controlArgs['media_type'] = 'audio';
            }
            if($configOption['upload_type'] == 'image') {
              $controlArgs['media_type'] = 'image';
            }
          }
          unset($controlArgs['default']);
        }
        if ($configOption['type'] === 'select') {
          $controlArgs['type'] = \Elementor\Controls_Manager::SELECT;


          if (!isset($configOption['options'])) {
            if (isset($configOption['choices'])) {
              $configOption['options'] = $configOption['choices'];
            }
          }

          if (isset($configOption['options'])) {

            if (isset($configOption['extra_type']) && $configOption['extra_type'] === 'switcher') {
              $controlArgs['type'] = \Elementor\Controls_Manager::SWITCHER;
              $controlArgs['label_on'] = esc_html__('Enable', DZSYTB_ID);
              $controlArgs['return_value'] = 'on';
            } else {
              $controlArgs['options'] = $elm->mapChoicesToFlatArray($configOption['options']);
            }

          }
        }

        $controlArgs['label_block'] = true;

        $arrOptions[] = array(
          'controlName' => $controlName,
          'controlArgs' => $controlArgs
        );

      }
    }

    return $arrOptions;
  }


  public function get_name() {
    return 'dzsytb_widget';
  }

  public function get_title() {
    return esc_html__('YouTube Block', DZSYTB_ID);
  }

  public function get_icon() {
    return 'eicon-headphones';
  }

  public function get_categories() {
    return ['general'];
  }

  protected function render() {
    global $dzsytb;

    $settings = $this->get_settings_for_display();


    echo '<div class="dzsytb-con-con">';




    echo $dzsytb->classView->shortcode_player($settings);

    echo '</div>';

  }
}