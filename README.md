=== Video Block Cover for YouTube DZS ===
Contributors: digitalzoomstudio
Tags: youtube, video, gutenberg, block, media
Requires at least: 6.2
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful and feature-rich YouTube block for the WordPress Gutenberg editor with advanced customization options, responsive design, and seamless integration.

== Description ==

**DZS YouTube Block** is a comprehensive WordPress plugin that enhances your content creation experience with a professional YouTube block for the Gutenberg editor. This plugin provides developers and content creators with a robust, customizable solution for embedding YouTube videos with advanced features.

## Key Features

### 🎯 **Gutenberg Integration**
- Native Gutenberg block experience
- Seamless integration with WordPress editor
- Custom block inspector controls for easy customization

### 🎨 **Advanced Customization**
- Multiple player themes and styles
- Customizable player controls and appearance
- Responsive design that works on all devices
- Custom CSS support for advanced styling

### 🚀 **Performance Optimized**
- Lightweight and fast loading
- Optimized video embedding
- Minimal impact on page load times

### 🔧 **Developer Friendly**
- Clean, well-structured code
- Extensible architecture
- Comprehensive documentation
- Elementor widget integration

### 📱 **Responsive Design**
- Mobile-first approach
- Adaptive layouts for all screen sizes
- Touch-friendly controls

## Perfect For

- **Content Creators** who want professional video presentations
- **Developers** building custom WordPress themes
- **Agencies** creating client websites with video content
- **Bloggers** enhancing their posts with embedded videos
- **Businesses** showcasing product demos and company videos

## Why Choose DZS YouTube Block?

Unlike basic YouTube embeds, our plugin provides:
- **Professional appearance** with customizable themes
- **Better user experience** with optimized loading
- **Developer flexibility** with extensive customization options
- **Future-proof** with regular updates and WordPress compatibility

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/dzs-video-block-for-youtube` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Start using the YouTube block in your Gutenberg editor!

## Quick Start

1. **Create a new post or page** using the Gutenberg editor
2. **Add the YouTube Block** from the block inserter
3. **Paste your YouTube URL** or video ID
4. **Customize the appearance** using the block settings
5. **Preview and publish** your content

== Frequently Asked Questions ==

= Is this plugin compatible with my theme? =

Yes! DZS YouTube Block is designed to work with all WordPress themes that support Gutenberg blocks.

= Does this plugin work with page builders? =

Yes, it's compatible with most page builders including Elementor (with our dedicated widget).

= Is the plugin responsive? =

Yes, all videos are fully responsive and will look great on any device.

= Can I use this with YouTube playlists? =

Yes, the plugin supports both single videos and playlists.

= Is there a limit on how many videos I can embed? =

No, you can embed as many videos as you want on any page or post.

### Screenshots

1. **Gutenberg Block Editor** - The YouTube block in action
2. **Block Settings Panel** - Customization options
3. **Frontend Display** - How videos appear on your website
4. **Responsive Design** - Mobile and tablet views
   == Source Code and Build Tools ==

This plugin ships compiled JavaScript for performance, along with the corresponding readable source files and build instructions. All source files used to generate the distributed/minified assets are included in the plugin, so they can be reviewed, studied, and forked.

= Frontend player script =

* Compiled file (distributed): libs/frontend-dzsytb/frontend-dzsytb.js
* Source file (readable): libs/frontend-dzsytb/frontend-dzsytb.source.js
* Build tools used: browserify, envify, babelify, minifyify

To rebuild the compiled file from source (run from the plugin directory):

    npx browserify libs/frontend-dzsytb/frontend-dzsytb.source.js \
      -t [ envify --NODE_ENV production ] \
      -t [ babelify --presets [@babel/preset-env @babel/preset-react] ] \
      -p [ minifyify --map frontend-dzsytb.js.map --output libs/frontend-dzsytb/frontend-dzsytb.js.map ] \
      --debug \
      -o libs/frontend-dzsytb/frontend-dzsytb.js


## Gutenberg block script

* Compiled file (distributed): features/gutenberg/gutenberg-player.js
* Main source modules (readable):
    * features/gutenberg/gutenberg-player.reactpack.js
    * features/gutenberg/components/YoutubeBlockPreview.js
    * configs/config-gutenberg-player.json

These files contain the unminified, human-readable source for the Gutenberg block and can be inspected, modified, and rebuilt using standard Node/Webpack tooling.

## Changelog

= 1.0.0 =
* Initial release
* Gutenberg block integration
* Customizable player themes
* Responsive design
* Elementor widget support
* Advanced customization options

== Upgrade Notice ==

= 1.0.0 =
Initial release of DZS YouTube Block - A powerful and customizable YouTube block for WordPress Gutenberg editor.

== Support ==

For support, feature requests, or bug reports, please visit our website or contact us:

- **Website**: [https://digitalzoomstudio.net/](https://digitalzoomstudio.net/)
- **Documentation**: Available on our website
- **Support**: Email support available for all users

## Contributing

We welcome contributions from the community! If you'd like to contribute to this plugin, please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## Roadmap

- [ ] Additional player themes
- [ ] Advanced analytics integration
- [ ] Custom thumbnail support
- [ ] Lazy loading optimization
- [ ] More customization options

== License ==

This plugin is licensed under the GPL v2 or later.

== Author ==

**Digital Zoom Studio** - Professional WordPress development and design services.

For more information about our services, visit [https://digitalzoomstudio.net/](https://digitalzoomstudio.net/)

---

*DZS YouTube Block - Making video content creation simple and professional.*
