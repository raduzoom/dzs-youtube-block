import {decode_json, embedScript, embedStyle} from "../../features/gutenberg-block-factory/block-factory/js_common/_helpers";


class DzsYtbPlayer {
  constructor($ytb) {

    this.$ytb = $ytb;
    this.init();
  }


  async getPlayer($t) {


    const $player_ = $t.find('lite-youtube').get(0);

    const player = await $player_.getYTPlayer();

    return player;
  }


  initYoutubeBlock($ytb) {

    const self = this;
    let playerArgs = {
      autoplay: 'pm',
    };


    try{
      playerArgs = Object.assign(playerArgs, JSON.parse($ytb.attr('data-player_args')))
    }catch (e) {

      console.log('[err] e - ', e);
    }


    let player$ = this.getPlayer($ytb);
    player$.then(function(playerHere){

      if(playerArgs.autoplay==='on'){

        playerHere.mute();
        playerHere.playVideo();
      }


      setTimeout(()=>{
        $ytb.find('.dzsytb-cover-img').removeClass('visible');
      },3500);


      window.addEventListener("resize", self.handleResize.bind(self));

      self.handleResize(null);

      playerHere.addEventListener("onReady", function(){
      });

    })

  }

  init(){

    this.initYoutubeBlock(this.$ytb);

  }

  /**
   *
   * @param {Event} event
   */
  handleResize(event)  {

    const $ytb = this.$ytb;

    let tw = $ytb.outerWidth();
    let th = $ytb.outerHeight();

    const RATIO_W = 16;
    const RATIO_H = 8;

    let excessW = 0;
    let excessH = 0;
    let targetWidth = tw;
    let targetHeight = RATIO_H/RATIO_W * tw;




    if(th > targetHeight){
      targetWidth = RATIO_W/RATIO_H * th;
      targetHeight = th;
    }

    if(targetWidth > tw){
      excessW = targetWidth - tw;
    }
    if(targetHeight > th){
      excessH = targetHeight - th;
    }

    /** @type {jQuery} */
    const $ytl =$ytb.find('lite-youtube');

    if(excessW){
      $ytl.css({
        'width': `calc(${tw + excessW}px)`,
        'left': `-${excessW/2}px`,
        'top': `0`,
        'height': `${th}px`,
        'max-height': `none`,
      })
    }
    if(excessH){
      $ytl.css({
        'height': `calc(${th + excessH}px)`,
        'top': `-${excessH/2}px`,
        'left': `0`,
        'width': `${tw}px`,
        'max-height': `none`,
      })
    }

  }
}

jQuery(document).ready(function($) {




  setTimeout(()=>{
    embedStyle('https://unpkg.com/lite-youtube-embed@0.3.2/src/lite-yt-embed.css')
    embedScript('https://unpkg.com/lite-youtube-embed@0.3.2/src/lite-yt-embed.js', () => {
      $('.dzsytb-con').each(function () {
        /** @type {jQuery} */
        var $t = $(this);

        var $ytb = new DzsYtbPlayer($t);
      })

    })
  },300);



});
