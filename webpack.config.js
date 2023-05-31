const path = require("path");

var vendorScripts = [
	"./node_modules/jquery/dist/jquery.js",
	"./node_modules/bootstrap/dist/js/bootstrap.bundle.js", // Bootstrap + Popper
	"./node_modules/atk14js/src/atk14.js",
	"./node_modules/unobfuscatejs/src/jquery.unobfuscate.js",
	"./node_modules/swiper/swiper-bundle.js"
];

var applicationScripts = [
	"./public/scripts/utils/utils.js",
	"./public/scripts/utils/swiper.js",
	"./public/scripts/application.js"
];

module.exports = {
  entry: {
    application: applicationScripts,
    vendor: vendorScripts
  },
  output: {
    path: path.resolve( __dirname, "public", "dist2", "scripts" ),
    filename: "[name].js"
  }
};