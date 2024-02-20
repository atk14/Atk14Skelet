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
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin'); // do not output some unnecessary files


// Application styles incl. Bootstrap
var application_styles = "./public/styles/application.scss";


// Files to be ignored
// typically unnecessary almost empty JS files created during styles compilation
var ignoredFiles = [
  "application_styles.min.js", "application_styles.min.js.map", // unused JS from CSS compile
];

var config = {
  entry: {
    application_styles: application_styles,
  },
  output: {
    //clean: true,
    path: path.resolve( __dirname, "public", "dist" ),
    filename: "scripts/[name].min.js"
  },
  plugins: [
    new BrowserSyncPlugin(
      // BrowserSync options
      {
        host: 'localhost',
        port: 3000,
        proxy: 'http://localhost:8000/',
        files: [ "app/**/*.tpl", "public/images/**/*", "public/dist/**/*" ],
        injectChanges: true,
        injectFileTypes: ["css"],
      },
      // plugin options
      {
        reload: false,
        injectCss: true,
      }
    ),
    new IgnoreEmitPlugin( ignoredFiles ),
    require ('autoprefixer'),
    new MiniCssExtractPlugin( {
      filename: "styles/[name].css",
      runtime: false,
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
  devtool: "source-map",
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
  return config;
}