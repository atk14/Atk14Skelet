const path = require("path");
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const webpack = require('webpack');
const FaviconsWebpackPlugin = require('favicons-webpack-plugin');
const autoprefixer = require('autoprefixer');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const CopyWebpackPlugin = require('copy-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");

// TODO some scripts from vendorScripts are missing in application.js imports
/*var vendorScripts = [
	"./node_modules/jquery/dist/jquery.js",
	"./node_modules/bootstrap/dist/js/bootstrap.bundle.js", // Bootstrap + Popper
	"./node_modules/atk14js/src/atk14.js",
	"./node_modules/unobfuscatejs/src/jquery.unobfuscate.js",
	"./node_modules/swiper/swiper-bundle.js"
];*/

var application_assets = [
	"./public/scripts/utils/utils.js",
	"./public/scripts/application.js",
  //"./public/styles/application.scss"
];

var application_styles = "./public/styles/application.scss";

var vendorStyles = [
  "./node_modules/@fortawesome/fontawesome-free/css/all.css",
	"./node_modules/swiper/swiper-bundle.css",
	"./node_modules/photoswipe/dist/photoswipe.css"
];

var config = {
  entry: {
    application: application_assets,
    application_es6: "./public/scripts/modules/application_es6.js",
    application_styles: application_styles,
    vendor_styles: vendorStyles,
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
    new FaviconsWebpackPlugin( {
      logo: "./public/favicons/favicon.png",
      //prefix: "favicons/",
      outputPath: 'favicons',
      inject: false,
      favicons: {
        icons : {
          android: { overlayShadow: false, overlayGlow: false },
          appleIcon: { overlayShadow: false, overlayGlow: false },
          appleStartup: false,
          coast: false,
          favicons: { overlayShadow: false, overlayGlow: false },
          firefox: false,
          windows: { overlayShadow: false, overlayGlow: false },
          yandex: false
        }
      }
    } ),
    new MiniCssExtractPlugin(),
    require ('autoprefixer'),
    new MiniCssExtractPlugin( {
      filename: "styles/[name].css"
    } ),
    new CopyWebpackPlugin({
      // TODO copy fontawesome fonts
      patterns: [
        { from: 'public/images', to: 'images' },
        { from: 'public/fonts', to: 'webfonts', noErrorOnMissing: true },
        {from: './node_modules/svg-country-flags/svg/*', to({ context, absoluteFilename }) {
          // rename some flags according to locale codes
          var renameTr = {
            "cz": "cs",
            "gb": "en",
            "rs": "sr", // sr: Srpski
            "si": "sl", // sl: Slovenščina
            "ee": "et", // et: eesti
            "kz": "kk" // kk: Қазақ
          };
          var filename = path.basename( absoluteFilename, ".svg" );
          Object.keys( renameTr ).forEach( function( key ) {
              if (filename === key) {
                filename = renameTr[ key ];
              }
          } );
          return "images/languages/" + filename + "[ext]";
        }},
        {from: "./node_modules/@fortawesome/fontawesome-free/webfonts/*", to({ context, absoluteFilename }) {
          return "webfonts/[name][ext]";
        }},
      ]
    }),
  ],
  module: { 
    "rules": [ 
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
    minimizer: [
      new TerserPlugin(),
      new CssMinimizerPlugin(),
    ],
    minimize: true
  },
  cache: true
};

module.exports = (env, args) => {
  if( env.clean_dist ) {
    // clean dist folder if clean_dist
    config.output.clean = true;
  }
  console.log("mode----", args.mode);
  if( args.mode !== "production" ) {
    // minimize outputs only in production mode
    config.optimization.minimize = false;
  }
  return config;
}