const path = require("path");
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const webpack = require('webpack');

var vendorScripts = [
	"./node_modules/jquery/dist/jquery.js",
	"./node_modules/bootstrap/dist/js/bootstrap.bundle.js", // Bootstrap + Popper
	"./node_modules/atk14js/src/atk14.js",
	"./node_modules/unobfuscatejs/src/jquery.unobfuscate.js",
	"./node_modules/swiper/swiper-bundle.js"
];

var applicationScripts = [
	"./public/scripts/utils/utils.js",
	//"./public/scripts/utils/swiper.js",
	"./public/scripts/application.js",
  
];

module.exports = {
  entry: {
    application: applicationScripts,
    application_es6: "./public/scripts/modules/application_es6.js",
    //vendor: vendorScripts,
    //test: "./public/test/test.js",
    //vendor: "./public/test/vendor.js"
  },
  output: {
    clean: true,
    path: path.resolve( __dirname, "public", "dist2", "scripts" ),
    filename: "[name].min.js"
  },
  plugins: [
    new BrowserSyncPlugin(
      // BrowserSync options
      {
        // browse to http://localhost:3000/ during development
        host: 'localhost',
        port: 3000,
        // proxy the Webpack Dev Server endpoint
        // (which should be serving on http://localhost:3100/)
        // through BrowserSync
        proxy: 'http://localhost:8000/'
      },
      // plugin options
      {
        // prevent BrowserSync from reloading the page
        // and let Webpack Dev Server take care of this
        reload: false
      }
    ),
    /*new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      bootstrap: 'bootstrap',
    })*/
  ],
  module: { 
    "rules": [ 
      /*{ 
        "test": /\.css$/, 
        "use": [ "style-loader", "css-loader" ] 
      }, */
      { 
        test: /\.js$/, 
        exclude: /node_modules/, 
        use: { 
          loader: "babel-loader", 
          options: { 
            presets: [ "@babel/preset-env", ] 
          } 
        } 
      }, 
    ] 
  },
  devtool: false,
  optimization: {
    splitChunks: {
      chunks: 'all',
      cacheGroups: {
        reactVendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendor',
          chunks: 'all',
        }
      },
    },
  }
};