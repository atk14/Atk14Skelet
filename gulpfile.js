var gulp = require( "gulp" );
var del = require( "del" );
var rename = require( "gulp-rename" );
var $ = require( "gulp-load-plugins" )();
var browserSync = require( "browser-sync" ).create();
var favicons = require("favicons").stream;
require( "./gulpfile-admin" );

var vendorStyles = [
	"node_modules/@fortawesome/fontawesome-free/css/all.css",
	"node_modules/swiper/swiper-bundle.css",
	"node_modules/photoswipe/dist/photoswipe.css"
];

var vendorScripts = [
	"node_modules/jquery/dist/jquery.js",
	"node_modules/bootstrap/dist/js/bootstrap.bundle.js", // Bootstrap + Popper
	"node_modules/atk14js/src/atk14.js",
	"node_modules/unobfuscatejs/src/jquery.unobfuscate.js",
	"node_modules/swiper/swiper-bundle.js",
	"node_modules/photoswipe/dist/photoswipe.js",
	"node_modules/photoswipe/dist/photoswipe-ui-default.js"
];

var applicationScripts = [
	"public/scripts/utils/utils.js",
	"public/scripts/utils/photoswipe.js",
	"public/scripts/utils/swiper.js",
	"public/scripts/utils/extended_password_field.js",
	"public/scripts/application.js"
];

// CSS
gulp.task( "styles", function() {
	return gulp.src( "public/styles/application.scss" )
		.pipe( $.sourcemaps.init() )
		.pipe( $.sass( {
			includePaths: [
				"public/styles"
			]
		} ) )
		.pipe( $.autoprefixer( { grid: true } ) )
		.pipe( $.cssnano() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
} );

gulp.task( "styles-vendor", function() {
	return gulp.src( vendorStyles )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concatCss( "vendor.css" ) )
		.pipe( $.autoprefixer() )
		.pipe( $.cssnano( { svgo: false } ) )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
} );

// JS
gulp.task( "scripts", function() {
	gulp.src( vendorScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "vendor.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/scripts" ) );

	gulp.src( applicationScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "application.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/scripts" ) )
		.pipe( browserSync.stream() );
} );

// Favicons
gulp.task( "favicons", function() {
	var execSync = require( "child_process" ).execSync;
	var appName = execSync( "./scripts/dump_settings ATK14_APPLICATION_NAME" ).toString().trim();
	var appDescription = execSync( "./scripts/dump_settings ATK14_APPLICATION_DESCRIPTION" ).toString().trim();
	var appUrl = execSync( "./scripts/dump_settings ATK14_APPLICATION_URL" ).toString().trim();
	var baseHref = execSync( "./scripts/dump_settings ATK14_BASE_HREF" ).toString().trim(); // e.g. "/"

	gulp.src( [ "public/favicons/favicon.png" ] )
	.pipe(
		favicons( {
			appName: appName,
			appShortName: null,
			appDescription: appDescription,
			background: "#ffffff",
			path: baseHref + "public/dist/favicons/",
			url: appUrl,
			display: "standalone",
			orientation: "portrait",
			scope: baseHref,
			start_url: baseHref,
			version: 1.0,
			logging: false,
			html: "index.html",
			pipeHTML: false,
			replace: true,
			icons: {
				android: { overlayShadow: false, overlayGlow: false },
				appleIcon: { overlayShadow: false, overlayGlow: false },
				appleStartup: false,
				coast: false,
				favicons: { overlayShadow: false, overlayGlow: false },
				firefox: false,
				windows: { overlayShadow: false, overlayGlow: false },
				yandex: false
			}
		} )
	)
	.pipe( gulp.dest( "public/dist/favicons" ) );
} );

// Lint & Code style
gulp.task( "lint", function() {
	return gulp.src( [ "public/scripts/**/*.js", "gulpfile.js" ] )
		.pipe( $.eslint() )
		.pipe( $.eslint.format() )
		.pipe( $.eslint.failAfterError() );
} );

// Copy
gulp.task( "copy", function() {
	gulp.src( "node_modules/html5shiv/dist/html5shiv.min.js" )
		.pipe( gulp.dest( "public/dist/scripts" ) );
	gulp.src( "node_modules/respond.js/dest/respond.min.js" )
		.pipe( gulp.dest( "public/dist/scripts" ) );
	gulp.src( "node_modules/@fortawesome/fontawesome-free/webfonts/*" )
		.pipe( gulp.dest( "public/dist/webfonts" ) );
	gulp.src( "public/fonts/*" )
		.pipe( gulp.dest( "public/dist/fonts" ) );
	gulp.src( "public/images/**/*" )
		.pipe( gulp.dest( "public/dist/images" ) );
	gulp.src( "node_modules/photoswipe/dist/default-skin/*" )
		.pipe( gulp.dest( "public/dist/styles/default-skin/" ) );

	// Flags for languages
	gulp.src( "node_modules/svg-country-flags/svg/*" )
		.pipe( gulp.dest( "public/dist/images/languages" ) )
		.on( "end", function() {

			// Some corrections in language flags
			var renameTr = {
				"cz": "cs",
				"gb": "en",
				"rs": "sr", // sr: Srpski
				"si": "sl", // sl: Slovenščina
				"ee": "et", // et: eesti
				"kz": "kk" // kk: Қазақ
			};
			Object.keys( renameTr ).forEach( function( key ) {
				gulp.src( "public/dist/images/languages/" + key + ".svg" )
					.pipe( rename( renameTr[ key ] + ".svg" ) )
					.pipe( gulp.dest( "public/dist/images/languages" ) );
			} );
		} );
} );

// Clean
gulp.task( "clean", function() {
	del.sync( "public/dist" );
} );

// Server
gulp.task( "serve", [ "styles" ], function() {
	browserSync.init( {
		proxy: "localhost:8000"
	} );

	// If these files change = reload browser
	gulp.watch( [
		"app/**/*.tpl",
		"public/images/**/*"
	] ).on( "change", browserSync.reload );

	// If javascript files change = run 'scripts' task, then reload browser
	gulp.watch( "public/scripts/**/*.js", [ "scripts" ] ).on( "change", browserSync.reload );

	// If styles files change = run 'styles' task with style injection
	gulp.watch( "public/styles/**/*.scss", [ "styles" ] );
} );

// Build
var buildTasks = [
	"lint",
	"styles",
	"styles-vendor",
	"scripts",
	"favicons",
	"copy"
];

gulp.task( "build", buildTasks, function() {
	return gulp.src( "public/dist/**/*" )
		.pipe( $.size( { title: "build", gzip: true } ) );
} );

// Default
gulp.task( "default", [ "clean" ], function() {
	gulp.start( "build" );
} );
