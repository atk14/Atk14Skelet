const path = require("path");
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const webpack = require('webpack');
const FaviconsWebpackPlugin = require('favicons-webpack-plugin');
const autoprefixer = require('autoprefixer');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

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
  "./public/styles/application.scss"
];

var vendorStyles = [
  "./node_modules/@fortawesome/fontawesome-free/css/all.css",
	"./node_modules/swiper/swiper-bundle.css",
	"./node_modules/photoswipe/dist/photoswipe.css"
];

module.exports = {
  entry: {
    application: application_assets,
    application_es6: "./public/scripts/modules/application_es6.js",
    vendor_styles: vendorStyles,
  },
  output: {
    clean: true,
    path: path.resolve( __dirname, "public", "dist2" ),
    filename: "[name].min.js"
  },
  plugins: [
    new BrowserSyncPlugin(
      // BrowserSync options
      {
        host: 'localhost',
        port: 3000,
        proxy: 'http://localhost:8000/'
      },
      // plugin options
      {
        // prevent BrowserSync from reloading the page
        // and let Webpack Dev Server take care of this
        reload: false
      }
    ),
    new FaviconsWebpackPlugin( {
      logo: "./public/favicons/favicon.png",
      //prefix: "favicons/",
      outputPath: '../favicons',
      inject: false,
      favicons: {
        icons : {
          yandex: false,
          appleStartup: false
        }
      }
    } ),
    new MiniCssExtractPlugin(),
    require ('autoprefixer'),
    new MiniCssExtractPlugin(),
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
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          {
            loader: "postcss-loader",
          },
          "sass-loader",
        ],
      },
      /*{
        test: /\.(scss)$/,
        //test: /\.s[ac]ss$/i,
        use: [{
          loader: 'style-loader', // inject CSS to page
        }, {
          loader: 'css-loader', // translates CSS into CommonJS modules
        }, {
          loader: 'postcss-loader', // Run post css actions
          options: {
            plugins: function () { // post css plugins, can be exported to postcss.config.js
              return [
                require('precss'),
                require('autoprefixer')
              ];
            }
          }
        }, {
          loader: 'sass-loader' // compiles Sass to CSS
        }]
      },*/
      /*{
        test: /\.scss$/,
        exclude: /node_modules/,
        use: [
            {
                loader: 'file-loader',
                options: { outputPath: 'css/', name: '[name].min.css'}
            },
            'sass-loader'
        ]
      }*/

    ] 
  },
  devtool: false,
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
      // For webpack@5 you can use the `...` syntax to extend existing minimizers (i.e. `terser-webpack-plugin`), uncomment the next line
      // `...`,
      new CssMinimizerPlugin(),
    ],
    minimize: true
  }
};