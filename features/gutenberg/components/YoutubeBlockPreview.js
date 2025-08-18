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

  // http://i3.ytimg.com/vi/90_7t-uCLEw/hqdefault.jpg


  useEffect(() => {




    console.log('init BLOCK', props);

    var attributesFromMain = props.props.attributes;
    console.log('props.props.attributes -> ', attributesFromMain);


    setStoreProps(Object.assign({...DEFAULT_VALS}, {...attributesFromMain}));
    Object.keys(attributesFromMain).forEach((prop)=>{
      const val = attributesFromMain[prop];
      console.log('prop - ', prop, val, 'DEFAULT_VALS[prop] - ', DEFAULT_VALS[prop], val!==DEFAULT_VALS[prop] && Object.keys(DEFAULT_VALS).includes(prop));

      if(val!==DEFAULT_VALS[prop] && Object.keys(DEFAULT_VALS).includes(prop)){
        adjustProp(prop, val);
      }
    })
  }, []);

  // const onChangeHandler = useCallback((event) => {
  //
  //   handleChange(event);
  // }, [setAspectRatio]);

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
    console.log($currTarget.name);
    console.log($currTarget.value);


    // setTimeout(()=>{
    //
    //   const newProps = {
    //     youtubeUrl: youtubeUrl,
    //     aspectRatio,
    //     title: title
    //   };
    //   console.log('chaNGE', newProps);
    //
    //
    //   props.updateProps(newProps);
    // },10);
  }

  const getCoverImage = () => {
    var attributesFromMain = props.props.attributes;

    if(attributesFromMain.cover && attributesFromMain.cover!=='default'){
      return `url(${attributesFromMain.cover})`;
    }
    return youtubeUrl ? `url(https://i3.ytimg.com/vi/${getYouTubeId(youtubeUrl)}/hqdefault.jpg)`: '';
  }

  const blockCssName = "dzs-ytb--block-preview"
  return <div className={blockCssName}>
    <div className={`${blockCssName}--height-decider`}>
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