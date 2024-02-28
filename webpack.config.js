const path = require("path");
const webpack = require('webpack');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin'); // browsersync
const FaviconsWebpackPlugin = require('favicons-webpack-plugin'); // favicons generation
const autoprefixer = require('autoprefixer'); // autoprefixer
const MiniCssExtractPlugin = require("mini-css-extract-plugin"); // extracts css from js
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin"); // css minimizer
const CopyWebpackPlugin = require('copy-webpack-plugin'); // copy files
const TerserPlugin = require("terser-webpack-plugin"); // js minimizer
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin'); // do not output some unnecessary files
const ESLintPlugin = require('eslint-webpack-plugin'); // linter

// Aplication JS scripts. Vendor scripts referenced inside app JS files.
var application_scripts = [
	"./public/scripts/utils/display_utils.js",
	"./public/scripts/utils/utils.js",
	"./public/scripts/application.js",
];

// Application styles incl. Bootstrap
var application_styles = "./public/styles/application.scss";

// Other vendor styles
var vendorStyles = [
  "./node_modules/@fortawesome/fontawesome-free/css/all.css",
	"./node_modules/swiper/swiper-bundle.css",
	"./node_modules/photoswipe/dist/photoswipe.css"
];

// Files to be ignored
// typically unnecessary almost empty JS files created during styles compilation
var ignoredFiles = [
  "vendor_styles.min.js", "vendor_styles.min.js.map", // unused JS from CSS compile
  "application_styles.min.js", "application_styles.min.js.map", // unused JS from CSS compile
];

var config = {
  entry: {
    application: application_scripts,
    application_es6: "./public/scripts/modules/application_es6.js",
    application_styles: application_styles,
    vendor_styles: vendorStyles,
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
    new FaviconsWebpackPlugin( {
      logo: "./public/favicons/favicon.png",
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
    new IgnoreEmitPlugin( ignoredFiles ),
    require ('autoprefixer'),
    new MiniCssExtractPlugin( {
      filename: "styles/[name].css",
      runtime: false,
    } ),
    new CopyWebpackPlugin({
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
    minimizer: [
      new TerserPlugin(),
      new CssMinimizerPlugin(),
    ],
    minimize: true
  },
  cache: true,
  stats: {
    // SASS compiler enable to show @debug
    loggingDebug: ['sass-loader'],
  },
};

module.exports = (env, args) => {
  if( env.clean_dist ) {
    // clean dist folder if clean_dist
    console.log( "dist directory will be cleaned" );
    config.output.clean = true;
  }
  console.log("mode:", args.mode);
  if( args.mode !== "production" ) {
    // minimize outputs only in production mode
    config.optimization.minimize = false;
  }
  if( !args.watch ){
    console.log( "JS will be linted" );
    config.plugins.push( new ESLintPlugin() );
  }else{
    console.log( "JS will not be linted" );
  }
  return config;
}