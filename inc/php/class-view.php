<?php
if ( ! defined( 'ABSPATH' ) ) exit;

#[AllowDynamicProperties]
class DZSYTBView {

  public $dzsytb;

  /**
   * @param DZSYtBlock $dzsytb
   */
  function __construct($dzsytb) {

    $this->dzsytb = $dzsytb;

    add_action('init', array($this, 'handle_init'));

  }


  function handle_init(): void {
    add_shortcode(DZSYTB_VIEW_SHORTCODE_NAME, array($this, 'shortcode_player'));


  }


  static function getYoutubeId($url) {
    // Sanitize and validate URL
    $url = esc_url_raw($url);

    if (!str_contains($url, '.com')) {
      return sanitize_text_field($url);
    }

    $YouTubeCheck = preg_match('![?&]{1}v=([^&]+)!', $url . '&', $Data);
    if ($YouTubeCheck) {
      $VideoID = sanitize_text_field($Data[1]);
    }
    return $VideoID;
  }


  /**
   * [zoomsounds_player source="pathto.mp3" artistname="" songname=""]
   * @param array $argsShortcodePlayer
   * @param string $content
   * @return string
   */
  function shortcode_player($argsShortcodePlayer = array(), $content = '') {
    $fout = '';

    $defaultArgs = array('aspectRatio' => '0.65', 'cover'=>'');

    // Sanitize input arguments
    $argsShortcodePlayer = $this->sanitize_shortcode_args($argsShortcodePlayer);
    $argsShortcodePlayer = array_merge($defaultArgs, $argsShortcodePlayer);

    $coverImg = '';

    if($argsShortcodePlayer['cover']){

      if(is_string($argsShortcodePlayer['cover'])){
        $coverImg = esc_url($argsShortcodePlayer['cover']);
      }
      if(is_array($argsShortcodePlayer['cover'])){
        $coverImg = esc_url($argsShortcodePlayer['cover']['url']);
      }
    }
    $fout .= '<div class="dzsytb-con" style="';

    if ($argsShortcodePlayer['max_height']) {
      $fout .= ' max-height: ' . esc_attr($argsShortcodePlayer['max_height']) . 'px';
    }

    $fout .= '"';

    $feArgs = array('autoplay' => $argsShortcodePlayer['autoplay']);
    $fout .= ' data-player_args=\'' . esc_attr(json_encode($feArgs)) . '\'';

    $fout .= '>';
    $fout .= '<div class="dzsytb-video-con" style="';
    if ($argsShortcodePlayer['aspectRatio'] != '0.65') {
      $fout .= 'padding-top: ' . esc_attr((floatval($argsShortcodePlayer['aspectRatio']) * 100)) . '%;';
    }
    $fout .= '">';

    // Escape YouTube URL and title
    $youtube_id = DZSYTBView::getYoutubeId($argsShortcodePlayer['youtubeUrl']);
    $title = esc_attr($argsShortcodePlayer['title']);
    $params = esc_attr($argsShortcodePlayer['youtube_params']);

    $fout .= '<lite-youtube js-api class="dzsytb-fullsize" videoid="' . $youtube_id . '" playlabel="' . $title . '" params="' . $params . '" style=";';

    if ($argsShortcodePlayer['max_height']) {
      $fout .= ' max-height: ' . esc_attr((intval($argsShortcodePlayer['max_height']) + 100)) . 'px';
    }

    $fout .= '"></lite-youtube>';

    if ($coverImg) {

      $fout .= '<figure class="dzsytb-fullsize dzsytb-cover-img visible" style=";';

      $fout .= ' background-image: url(' . $coverImg . ');';

      if ($argsShortcodePlayer['max_height']) {
        $fout .= ' max-height: ' . esc_attr($argsShortcodePlayer['max_height']) . 'px';
      }

      $fout .= '"></figure>';
    }

    if ($argsShortcodePlayer['title'] || $argsShortcodePlayer['subtitle']) {

      $fout .= '<div class="dzsytb-title-subtitle-con dzsytb-fullsize" style="';

      if ($argsShortcodePlayer['max_height']) {
        $fout .= ' max-height: ' . esc_attr($argsShortcodePlayer['max_height']) . 'px';
      }

      $fout .= '">';
      if ($argsShortcodePlayer['title']) {

        $fout .= '<h1 class="dzsytb-title">' . esc_html($argsShortcodePlayer['title']) . '</h1>';
        $fout .= '';
      }
      if ($argsShortcodePlayer['subtitle']) {

        $fout .= '<h3 class="dzsytb-subtitle">' . esc_html($argsShortcodePlayer['subtitle']) . '</h3>';
        $fout .= '';
      }
      $fout .= '</div>';
    }

    $fout .= '</div>';

    $fout .= '</div>';

    DZSYTBView::enqueueFeScripts();

    return $fout;
  }

  /**
   * Sanitize shortcode arguments
   *
   * @param array $args The arguments to sanitize
   * @return array The sanitized arguments
   */
  private function sanitize_shortcode_args($args) {
    $sanitized = array();

    if (is_array($args)) {
      foreach ($args as $key => $value) {
        switch ($key) {
          case 'youtubeUrl':
            $sanitized[$key] = esc_url_raw($value);
            break;
          case 'title':
          case 'subtitle':
            $sanitized[$key] = sanitize_text_field($value);
            break;
          case 'aspectRatio':
            $sanitized[$key] = floatval($value);
            break;
          case 'max_height':
            $sanitized[$key] = intval($value);
            break;
          case 'autoplay':
            $sanitized[$key] = (bool) $value;
            break;
          case 'youtube_params':
            $sanitized[$key] = sanitize_text_field($value);
            break;
          case 'cover':
            if (is_string($value)) {
              $sanitized[$key] = esc_url_raw($value);
            } elseif (is_array($value) && isset($value['url'])) {
              $sanitized[$key] = array('url' => esc_url_raw($value['url']));
            }
            break;
          default:
            $sanitized[$key] = sanitize_text_field($value);
            break;
        }
      }
    }

    return $sanitized;
  }

  static function enqueueFeScripts(): void {
    wp_enqueue_style('frontend-dzsytb', DZSYTB_BASE_URL . 'libs/frontend-dzsytb/frontend-dzsytb.css');
    wp_enqueue_script('frontend-dzsytb', DZSYTB_BASE_URL . 'libs/frontend-dzsytb/frontend-dzsytb.js', array(), DZSYTB_VERSION,
      array(
        'in_footer' => true,
        'strategy' => 'defer',
      ));
  }

}
