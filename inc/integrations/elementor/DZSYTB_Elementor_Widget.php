<?php


if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

// Check if Elementor is active
if (!class_exists('\Elementor\Widget_Base')) {
  return;
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
    // Check if user has permission to edit
    if (!current_user_can('edit_posts')) {
      return;
    }

    global $dzsytb;

    $this->start_controls_section(
      'content_section',
      [
        'label' => esc_html__('Content', 'dzs-youtube-block'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    // Verify config file exists before including it
    $config_file = DZSYTB_BASE_PATH . 'configs/config-gutenberg-player.php';
    if (!file_exists($config_file)) {
      error_log('DZS YouTube Block: Config file not found: ' . $config_file);
      return;
    }

    $playerOptions = include($config_file);

    $arrOptions = array();

    $arrOptions = self::mapToElementor($this, $playerOptions, 'main');

    foreach ($arrOptions as $arrOption){
      $this->add_control($arrOption['controlName'], $arrOption['controlArgs']);
    }

    $this->end_controls_section();
  }

  static function mapToElementor($elm, $optionsItemMeta, $seekedCategory) {
    $arrOptions = array();

    if (!is_array($optionsItemMeta)) {
      return $arrOptions;
    }

    foreach ($optionsItemMeta as $key => $configOption) {
      // Sanitize the key
      $key = sanitize_key($key);

      if (isset($configOption['category']) && $configOption['category'] === $seekedCategory) {

//        if (!dzs_is_option_for_this($configOption, 'elementor')) {
//          continue;
//        }

        $controlName = sanitize_key($configOption['name']);

        $placeholder = '';
        $default = '';

        if (isset($configOption['default'])) {
          $default = sanitize_text_field($configOption['default']);
        }
        if (isset($configOption['default'])) {
          $placeholder = sanitize_text_field($configOption['default']);
        }
        $controlArgs = [
          'label' => sanitize_text_field($configOption['title']),
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

            $controlArgs['media_type'] = sanitize_text_field($configOption['upload_type']);
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
              $controlArgs['label_on'] = esc_html__('Enable', 'dzs-youtube-block');;
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
    return esc_html__('YouTube Block', 'dzs-youtube-block');
  }

  public function get_icon() {
    return 'eicon-headphones';
  }

  public function get_categories() {
    return ['general'];
  }

  protected function render() {
    // Check if user has permission to view
    if (!current_user_can('read')) {
      return;
    }

    global $dzsytb;

    $settings = $this->get_settings_for_display();

    // Sanitize settings before passing to shortcode
    $settings = $this->sanitize_widget_settings($settings);

    echo '<div class="dzsytb-con-con">';

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is already sanitized by shortcode_player method
    echo DZSYTB_Elementor__Widget::sanitizeShortcode($dzsytb->classView->shortcode_player($settings));

    echo '</div>';
  }

static function sanitizeShortcode($arg) {
  return $arg;
}

  /**
   * Sanitize widget settings
   *
   * @param array $settings The settings to sanitize
   * @return array The sanitized settings
   */
  private function sanitize_widget_settings($settings) {
    $sanitized = array();

    if (is_array($settings)) {
      foreach ($settings as $key => $value) {
        $key = sanitize_key($key);

        if (is_string($value)) {
          $sanitized[$key] = sanitize_text_field($value);
        } elseif (is_array($value)) {
          $sanitized[$key] = $this->sanitize_widget_settings($value);
        } elseif (is_numeric($value)) {
          $sanitized[$key] = floatval($value);
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
