/*
  Webpack config file for fast CSS compiling
  For use with npm run servenojs command
  ! WARNING !
  This config processes only application styles and reloads on .tpl templates change.
  It does NOT process Javascript app files. To process JS , use standard webpack.config.js
  Also, no vendor styles are processed (except Bootstrap) and no other files are copied to dist folder.
*/

const path = require("path");
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const webpack = require('webpack');
const autoprefixer = require('autoprefixer');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
var application_styles = "./public/styles/application.scss";

var vendorStyles = [
  "./node_modules/@fortawesome/fontawesome-free/css/all.css",
	"./node_modules/swiper/swiper-bundle.css",
	"./node_modules/photoswipe/dist/photoswipe.css"
];

var config = {
  entry: {
    application_styles: application_styles,
    //vendor_styles: vendorStyles,
  },
  output: {
    //clean: true,
    path: path.resolve( __dirname, "public", "dist" ),
    filename: "[name].min.js"
  },
  plugins: [
    new BrowserSyncPlugin(
      // BrowserSync options
      {
        host: 'localhost',
        port: 3000,
        proxy: 'http://localhost:8000/',
        files: [ "app/**/*.tpl", "public/images/**/*", "public/dist/**/*" ],
        //files: [ "app/**/*.tpl", "public/images/**/*" ],
        injectChanges: true,
        injectFileTypes: ["css"],
      },
      // plugin options
      {
        // prevent BrowserSync from reloading the page
        // and let Webpack Dev Server take care of this
        reload: false,
        injectCss: true,
      }
    ),
    new MiniCssExtractPlugin(),
    require ('autoprefixer'),
    new MiniCssExtractPlugin( {
      filename: "styles/[name].css"
    } ),
  ],
  module: { 
    "rules": [ 
      /*{ 
        test: /\.js$/, 
        exclude: /node_modules/, 
        use: { 
          loader: "babel-loader", 
          options: { 
            presets: [ "@babel/preset-env", ] 
          } 
        } 
      },*/
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              url: false
            }
          },
          {
            loader: "postcss-loader",
          },
          "sass-loader",
        ],
      },
    ] 
  },
  //devtool: false,
  optimization: {
    splitChunks: {
      chunks: 'all',
      cacheGroups: {
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendor',
          chunks: 'all',
        }
      },
    },
    minimize: false,
  },
  cache: true
};

module.exports = (env, args) => {
  if( env.clean_dist ) {
    config.output.clean = true;
  }
  console.log("mode----", args.mode);
  if( args.mode !== "production" ) {
    config.optimization.minimize = false;
  }
  return config;
}