<?php


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

    if (!str_contains($url, '.com')) {
      return $url;
    }

    $YouTubeCheck = preg_match('![?&]{1}v=([^&]+)!', $url . '&', $Data);
    if ($YouTubeCheck) {
      $VideoID = $Data[1];
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


    $argsShortcodePlayer = array_merge($defaultArgs, $argsShortcodePlayer);


    $coverImg = '';

    if($argsShortcodePlayer['cover']){

      if(is_string($argsShortcodePlayer['cover'])){
        $coverImg = $argsShortcodePlayer['cover'];
      }
      if(is_array($argsShortcodePlayer['cover'])){
        $coverImg = $argsShortcodePlayer['cover']['url'];
      }
    }
    $fout .= '<div class="dzsytb-con" style="';


    if ($argsShortcodePlayer['max_height']) {
      $fout .= ' max-height: ' . $argsShortcodePlayer['max_height'] . 'px';
    }

    $fout .= '"';

    $feArgs = array('autoplay' => $argsShortcodePlayer['autoplay']);
    $fout .= ' data-player_args=\'' . json_encode($feArgs) . '\'';


    $fout .= '>';
    $fout .= '<div class="dzsytb-video-con" style="';
    if ($argsShortcodePlayer['aspectRatio'] != '0.65') {
      $fout .= 'padding-top: ' . (floatval($argsShortcodePlayer['aspectRatio']) * 100) . '%;';
    }
    $fout .= '">';
    $fout .= '<lite-youtube js-api class="dzsytb-fullsize" videoid="' . DZSYTBView::getYoutubeId($argsShortcodePlayer['youtubeUrl']) . '" playlabel="' . $argsShortcodePlayer['title'] . '" params="' . $argsShortcodePlayer['youtube_params'] . '" style=";';


    if ($argsShortcodePlayer['max_height']) {
      $fout .= ' max-height: ' . (intval($argsShortcodePlayer['max_height']) + 100) . 'px';
    }

    $fout .= '"></lite-youtube>';


    if ($coverImg) {

      $fout .= '<figure class="dzsytb-fullsize dzsytb-cover-img visible" style=";';

      $fout .= ' background-image: url(' . $coverImg . ');';


      if ($argsShortcodePlayer['max_height']) {
        $fout .= ' max-height: ' . $argsShortcodePlayer['max_height'] . 'px';
      }

      $fout .= '"></figure>';
    }

    if ($argsShortcodePlayer['title'] || $argsShortcodePlayer['subtitle']) {

      $fout .= '<div class="dzsytb-title-subtitle-con dzsytb-fullsize" style="';


      if ($argsShortcodePlayer['max_height']) {
        $fout .= ' max-height: ' . $argsShortcodePlayer['max_height'] . 'px';
      }

      $fout .= '">';
      if ($argsShortcodePlayer['title']) {

        $fout .= '<h1 class="dzsytb-title">' . $argsShortcodePlayer['title'] . '</h1>';
        $fout .= '';
      }
      if ($argsShortcodePlayer['subtitle']) {

        $fout .= '<h3 class="dzsytb-subtitle">' . $argsShortcodePlayer['subtitle'] . '</h3>';
        $fout .= '';
      }
      $fout .= '</div>';
    }

    $fout .= '</div>';

    $fout .= '</div>';


    DZSYTBView::enqueueFeScripts();


    return $fout;
  }

  static function enqueueFeScripts(): void {
    wp_enqueue_style('frontend-dzsytb', DZSYTB_BASE_URL . 'libs/frontend-dzsytb/frontend-dzsytb.css');
    wp_enqueue_script('frontend-dzsytb', DZSYTB_BASE_URL . 'libs/frontend-dzsytb/frontend-dzsytb.js', array(), DZSYTB_VERSION,
      array(
        'in_footer' => true,
        'strategy' => 'defer',
      ));

//    wp_enqueue_style('lite-yt-embed', 'https://unpkg.com/lite-youtube-embed@0.3.2/src/lite-yt-embed.css');
//    wp_enqueue_script('lite-yt-embed', 'https://unpkg.com/lite-youtube-embed@0.3.2/src/lite-yt-embed.js', array(), DZSYTB_VERSION,
//      array(
//        'in_footer' => true,
//        'strategy' => 'defer',
//      )
//    );
  }

}
