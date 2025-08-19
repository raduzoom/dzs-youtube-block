// import { useEffect, useState } from 'react';

import './YoutubeBlockPreview.scss';
const useState = window.wp.element.useState;
const useEffect = window.wp.element.useEffect;

export function getYouTubeId(url){


  if(url){
    if(url.indexOf('.com')===-1 && url.indexOf('.be')===-1){
      return url;
    }

    var rx = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/;

    var r = url.match(rx);
    if(r && r[1]){

      return r[1];
    }

  }
  return '';
}

/**
 * this is what appears on top of the gutenberg player
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export function YoutubeBlockPreview(props) {


  const DEFAULT_VALS = {
    aspectRatio: '0.65',
    youtubeUrl: '',
    title: '',
    subtitle: '',
    cover: 'default',
  }




  const [aspectRatio, setAspectRatio] = useState(DEFAULT_VALS['aspectRatio']);
  const [youtubeUrl, setYoutubeUrl] = useState(DEFAULT_VALS['youtubeUrl']);
  const [title, setTitle] = useState(DEFAULT_VALS['title']);
  const [subtitle, setSubtitle] = useState(DEFAULT_VALS['subtitle']);
  const [cover, setCover] = useState(DEFAULT_VALS['cover']);
  const [storeProps, setStoreProps] = useState(DEFAULT_VALS);



  useEffect(() => {





    var attributesFromMain = props.props.attributes;


    setStoreProps(Object.assign({...DEFAULT_VALS}, {...attributesFromMain}));
    Object.keys(attributesFromMain).forEach((prop)=>{
      const val = attributesFromMain[prop];

      if(val!==DEFAULT_VALS[prop] && Object.keys(DEFAULT_VALS).includes(prop)){
        adjustProp(prop, val);
      }
    })
  }, []);


  function adjustProp(name, value) {

    if(name==='aspectRatio'){
      setAspectRatio(value);

    }
    if(name==='youtubeUrl'){
      setYoutubeUrl(value);
    }
    if(name==='title'){
      setTitle(value);
    }
    if(name==='subtitle'){
      setSubtitle(value);
    }
    if(name==='cover'){
      setCover(value);
    }

    storeProps[name] = value;

    setStoreProps(storeProps);
  }


  useEffect(() => {
    // This code runs when `youtubeUrl`, `aspectRatio`, `title`, `subtitle`, or `cover` changes.
    const newProps = { youtubeUrl, aspectRatio, title, subtitle, cover };
    props.updateProps(newProps);
  }, [youtubeUrl, aspectRatio, title, subtitle, cover]);



  function handleChange(event) {
    const $currTarget = event.currentTarget;

    const name = $currTarget.name;
    const value = $currTarget.value;

    adjustProp(name, value);


  }

  const getCoverImage = () => {
    var attributesFromMain = props.props.attributes;

    if(attributesFromMain.cover && attributesFromMain.cover!=='default'){
      return `url(${attributesFromMain.cover})`;
    }
    return youtubeUrl ? `url(https://i3.ytimg.com/vi/${getYouTubeId(youtubeUrl)}/hqdefault.jpg)`: '';
  }

  const blockCssName = "dzs-ytb--block-preview";

  const styleHeightDecider = {};

  const mainAtts = props.props.attributes;

  if(mainAtts.max_height){
    styleHeightDecider.maxHeight = mainAtts.max_height + 'px';
  }
  return <div className={blockCssName}>
    <div className={`${blockCssName}--height-decider`} style={styleHeightDecider}>
      <div className={`${blockCssName}--bg-placeholder`}></div>
      <div className={`${blockCssName}--bg-with-ratio`} style={{paddingTop: String(aspectRatio * 100) + '%' }}></div>
    </div>
    <div className={`${blockCssName}--bg`} style={{backgroundImage: getCoverImage()}}></div>
    <div className={`${blockCssName}--form`}>
      <div className={`${blockCssName}--form--inner`}>

        <p>
          <label className={`${blockCssName}--att-row`}>
            <span>YouTube URL:</span>
            <input type={'text'} name={'youtubeUrl'} value={youtubeUrl} onChange={handleChange}/>
          </label>
        </p>

        <p>
          <label className={`${blockCssName}--att-row`}>
            <span>Aspect Ratio:</span>
            <input type={'text'} name={'aspectRatio'} value={aspectRatio} onChange={handleChange}/>
          </label>
        </p>

        <p>
          <label className={`${blockCssName}--att-row`}>
            <span>Title:</span>
            <input type={'text'} name={'title'} value={title} onChange={handleChange}/>
          </label>
        </p>

        <p>
          <label className={`${blockCssName}--att-row`}>
            <span>Subtitle:</span>
            <input type={'text'} name={'subtitle'} value={subtitle} onChange={handleChange}/>
          </label>
        </p>

      </div>
    </div>
  </div>;
}
