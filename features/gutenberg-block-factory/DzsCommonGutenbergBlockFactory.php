<?php

if (!class_exists('DzsCommonGutenbergBlockFactory')) {
  /**
   * version v1.00
   * init with
   * array(
   * 'gutenbergBlockName' => 'dzswtl_filters_block',
   * 'gutenbergBlockNameJs' => 'dzswtl/filters_block',
   * 'blockJsUrl' => 'http://something/wpfactory/wp-content/plugins/dzs-wootable/inc/js-gutenberg/filters-block/filters-block.js',
   * 'blockShortcode' => 'dzs_woo_filter_gutenberg',
   * 'actualShortcode' => 'dzs_woo_filter',
   * 'blockOptions' => include(DZSWTL_PATH.'configs/config-filter-options.php'),
   * )
   * Class DzsGutenbergBlockFactory
   */
  class DzsCommonGutenbergBlockFactory {
    public $gutenbergBlockName;
    public $blockJsUrl;
    public $blockShortcode;
    public $gutenbergBlockNameJs;
    /**
     * @var mixed|string generated from json
     */
    public $blockOptions;
    public $actualShortcode;


    /**
     * DzsGutenbergBlockFactory constructor
     * it's important that it is inited with priority on handle_init lower than 20
     * init with
     * array(<br>
     *   'gutenbergBlockName' => 'dzswtl_filters_block',<br>
     *   'gutenbergBlockNameJs' => 'dzswtl/filters_block',<br>
     *   'blockJsUrl' => 'http://something/wpfactory/wp-content/plugins/dzs-wootable/inc/js-gutenberg/filters-block/filters-block.js',<br>
     *   'blockShortcode' => 'dzs_woo_filter_gutenberg',<br>
     *   'actualShortcode' => 'dzs_woo_filter',<br>
     *   'blockOptions' => include(DZSWTL_PATH.'configs/config-filter-options.php'),
     * )
     * @param array $pargs
     */
    function __construct(array $pargs = array()) {
      // Sanitize constructor arguments
      $pargs = $this->sanitize_constructor_args($pargs);
      
      $blockFactoryAtts = array_merge(array(
        'gutenbergBlockName' => '',
        'gutenbergBlockNameJs' => '',
        'blockJsUrl' => '',
        'blockShortcode' => '',
        'blockOptions' => '',
        'actualShortcode' => '',
      ), $pargs);
      $this->gutenbergBlockName = sanitize_key($blockFactoryAtts['gutenbergBlockName']);
      $this->gutenbergBlockNameJs = sanitize_key($blockFactoryAtts['gutenbergBlockNameJs']);
      $this->blockJsUrl = esc_url_raw($blockFactoryAtts['blockJsUrl']);
      $this->blockShortcode = sanitize_key($blockFactoryAtts['blockShortcode']);
      $this->blockOptions = $blockFactoryAtts['blockOptions'];
      $this->actualShortcode = sanitize_key($blockFactoryAtts['actualShortcode']);


      add_action('init', array($this, 'handle_init'), 20);
      add_action('init', array($this, 'add_support_block'), 500);
      add_action('admin_footer', array($this, 'load_script'), 500);
    }

    /**
     * Sanitize constructor arguments
     * 
     * @param array $args The arguments to sanitize
     * @return array The sanitized arguments
     */
    private function sanitize_constructor_args($args) {
      $sanitized = array();
      
      if (is_array($args)) {
        foreach ($args as $key => $value) {
          $key = sanitize_key($key);
          
          if (is_string($value)) {
            $sanitized[$key] = sanitize_text_field($value);
          } elseif (is_array($value)) {
            $sanitized[$key] = $this->sanitize_constructor_args($value);
          } else {
            $sanitized[$key] = $value;
          }
        }
      }
      
      return $sanitized;
    }

    /**
     * @param $argarr
     * @return array converts to array('type' => 'string','default' => $default,) for gutenberg php
     */
    public static function sanitize_config_to_gutenberg_register_block_type($argarr): array {

      $foutarr = array();
      
      if (!is_array($argarr)) {
        return $foutarr;
      }
      
      foreach ($argarr as $lab => $arr) {

        $key = sanitize_key($lab);
        $default = '';

        if (isset($arr['default'])) {
          $default = sanitize_text_field($arr['default']);
        }

        $foutarr[$key] = array(
          'type' => 'string',
          'default' => $default,
        );
      }
      return $foutarr;

    }

    /**
     * called on init start
     */
    function handle_init() {
      // Check if user has permission to register scripts
      if (!current_user_can('edit_posts')) {
        return;
      }

      // -- we store this for loading in the footer once all dependencies are loaded
      if (is_admin() && function_exists('wp_register_script')) {
        wp_register_script(
          $this->gutenbergBlockName,
          $this->blockJsUrl,
          array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor')
        );
      }


      if (is_admin() && class_exists('DZSWTLHelpers')) {
        DZSWTLHelpers::enqueue_ultibox();
      }

      add_shortcode($this->blockShortcode, array($this, 'shortcode_render'));

    }

    /**
     * init end
     */
    function add_support_block() {
      // Check if user has permission to register blocks
      if (!current_user_can('edit_posts')) {
        return;
      }

      // -- in init

      // -- default atrributes gallery

      $atts_gutenberg_block = DzsCommonGutenbergBlockFactory::sanitize_config_to_gutenberg_register_block_type($this->blockOptions);

      if (function_exists('register_block_type')) {

        // -- register gutenberg
        // todo: real atts
        register_block_type($this->gutenbergBlockNameJs, array(
          'attributes' => $atts_gutenberg_block,
          'render_callback' => array($this, 'shortcode_render'),
        ));
      }

    }

    /**
     * called in admin_footer
     */
    function load_script() {
      // Check if user has permission to load scripts
      if (!current_user_can('edit_posts')) {
        return;
      }

      global $post;

//     -- we need to remove gutenberg support if this is avada or wpbakery

      $isLoadScript = true;

      if ($post && $post->post_content && strpos($post->post_content, 'vc_row') !== false) {
        $isLoadScript = false;
      }
      if ((defined('AVADA_VERSION'))) {

        $isLoadScript = false;
      }

      // -- disable if it's not gutenberg
      if (function_exists('get_current_screen')) {
        if (method_exists('get_current_screen', 'is_block_editor') && !get_current_screen()->is_block_editor()) {
          $isLoadScript = false;
        }
      }

      // Sanitize GET parameter
      $post_type = sanitize_text_field($_GET['post_type'] ?? '');
      if ($post_type == 'sp_easy_accordion') {

        $isLoadScript = false;
      }

      if ($isLoadScript) {
        wp_enqueue_script('wp-blocks');
        wp_enqueue_script('wp-element');
        wp_enqueue_script($this->gutenbergBlockName);
      }

    }


    function shortcode_render($attributes) {
      // -- player render

      $fout = '';

      if (is_admin()) {
        // Check if user has permission to render in admin
        if (!current_user_can('edit_posts')) {
          return '';
        }
      }

      // Sanitize attributes
      $attributes = $this->sanitize_render_attributes($attributes);
      $attributes['called_from'] = 'gutenberg_factory_render';
      
      // todo: here
      $fout .= '<div class="gutenberg-dzs-generator-con">';
      $fout .= call_user_func_array($this->actualShortcode, array($attributes));
      $fout .= '</div>';

      return $fout;
    }

    /**
     * Sanitize render attributes
     * 
     * @param array $attributes The attributes to sanitize
     * @return array The sanitized attributes
     */
    private function sanitize_render_attributes($attributes) {
      $sanitized = array();
      
      if (is_array($attributes)) {
        foreach ($attributes as $key => $value) {
          $key = sanitize_key($key);
          
          if (is_string($value)) {
            $sanitized[$key] = sanitize_text_field($value);
          } elseif (is_array($value)) {
            $sanitized[$key] = $this->sanitize_render_attributes($value);
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
}
