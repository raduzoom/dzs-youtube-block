<?php
if ( ! defined( 'ABSPATH' ) ) exit;


include_once DZSYTB_BASE_PATH.'features/gutenberg-block-factory/DzsCommonGutenbergBlockFactory.php';
use DigitalZoomStudio\Common\Gutenberg\V1\BlockFactory;

#[AllowDynamicProperties]
class DZSYTBGutenberg {

  public $dzsytb;

  /**
   * @param DZSYtBlock $dzsytb
   */
  function __construct(DZSYtBlock $dzsytb) {

    $this->dzsytb = $dzsytb;


    add_action('init', array($this, 'handle_init'), 4);




  }
  function handle_init(): void {



    $gutenbergPlayer = new BlockFactory(array(
      'gutenbergBlockName' => DZSYTB_GUTENBERG_PLAYER_ID,
      'gutenbergBlockNameJs' => DZSYTB_GUTENBERG_PLAYER_ID,
      'blockJsUrl' => DZSYTB_BASE_URL . 'features/gutenberg/gutenberg-player.js',
      'blockShortcode' => DZSYTB_VIEW_SHORTCODE_NAME,
      'actualShortcode' => array($this->dzsytb->classView, 'shortcode_player'),
      'blockOptions' => include(DZSYTB_BASE_PATH . 'configs/config-gutenberg-player.php'),
    ));
    add_action('init', array($this,'dzsap_gutenberg_add_support_block_on_init'), 125);
  }



  function dzsap_gutenberg_add_support_block_on_init() {


  }

}
