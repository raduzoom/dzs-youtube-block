import {BlockFactory} from '../gutenberg-block-factory/block-factory/blockFactory.jsx';
import configAttributes from '../../configs/config-gutenberg-player.json';
import {YoutubeBlockPreview} from "./components/YoutubeBlockPreview";

// import { MediaUpload, MediaUploadCheck, PlainText } from '@wordpress/block-editor';
// import { TextControl } from '@wordpress/components';
// import { __ } from '@wordpress/i18n';

const {MediaUpload, MediaUploadCheck, PlainText} = window.wp.blockEditor;
const {TextControl} = window.wp.components;


let __ = (arg) => {
  return arg;
};

if (wp.i18n) {
  __ = wp.i18n.__;
}

const BLOCK_NAME = 'dzsytb/youtube-player';
const BLOCK_TITLE = 'YouTube Block';

const DzsytbGutenbergPlayer = new BlockFactory({
  'blockKey': BLOCK_NAME,
  'blockTitle': BLOCK_TITLE,
  'keywords': [
    __('YouTube'),
    __('Background'),
    __('Media'),],
  'adminPreviewComponent': function (props) {


    let configOptionKey = 'cover';
    let argsInputForm = {
      "label": "Thumbnail",
      "value": "",
      "instanceId": "item_thumb",
      "className": " dzs-dependency-field",
      "onChange": null,
      "allowedTypes": [
        "image"
      ]
    }

    function updateProps(newProps){

      Object.keys(newProps).forEach((lab) => {

        const val = newProps[lab];
        props.setAttributes({[lab]: val})
      })
    }
    return (


      <div className={props.className}>

        <YoutubeBlockPreview props={props} name={'ceva'} updateProps={updateProps.bind(this)} aspectRatio={props.attributes['aspectRatio']}></YoutubeBlockPreview>
        <div className="dzsytb-gutenberg-con--player zoomsounds-containers">
          <h6 className="gutenberg-title"><span
            className="dashicons dashicons-format-audio"/> {__('YouTube Block')}</h6>

          <div className="react-setting-container">
            <div className="react-setting-container--label">{__("Cover")}</div>
            <div className="react-setting-container--control">
              <MediaUploadCheck>
                <MediaUpload
                  {...argsInputForm}
                  onSelect={(imageObject) => {
                    props.setAttributes({[configOptionKey]: imageObject.url});
                  }}
                  render={({open}) => (
                    <div className="render-song-selector field-and-button-container">
                      <PlainText
                        className={"editor-rich-text__tinymce"}
                        format="string"
                        formattingControls={[]}
                        placeholder={('Input song uri')}
                        onChange={(val) => props.setAttributes({[configOptionKey]: val})}
                        value={props.attributes[configOptionKey]}
                      />
                      <button className="button-secondary" onClick={open}>{props.uploadButtonLabel}</button>
                    </div>
                  )}
                />
              </MediaUploadCheck>


            </div>
          </div>
          <div className="react-setting-container">
            <div className="react-setting-container--label">{__("Max height")}</div>
            <div className="react-setting-container--control">
              <TextControl
                className={" "}
                format="string"
                formattingControls={[]}
                placeholder={('Max height')}
                rows={1}
                onChange={(val) => props.setAttributes({['max_height']: val})}
                value={props.attributes['max_height']}
              />
            </div>
          </div>
          <small className="react-setting-container--sidenote"><em>Force a height, if set will disregard aspect
            ratio</em></small>


        </div>
      </div>
    )
  },
  'configAttributes': configAttributes,
  'ignoredKeysInOptions': ['artistname', 'the_post_title', "youtubeUrl", "title", 'subtitle'],
  'sidenoteReplaces': {
    dzsapEditConfigLink: (argProps) => {
      return `<a href="${'' + 'admin.php?page=dzsap_configs&find_slider_by_slug=' + argProps.attributes.config}" target="_blank">Edit Here</a>`;
    }
  },
})
