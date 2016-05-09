var gulp = require( "gulp" );
var del = require( "del" );
var glp = require( "gulp-load-plugins" )();
var browserSync = require( "browser-sync" ).create();
var devel = false;

require( "./gulpfile-admin" );

// Global error handler for gulp tasks. Needs to be attached first tho.
function errorHandler( error ) {
	if ( devel ) {
		console.error( error );
	} else {
		throw new Error( error );
	}
}

var vendorStyles = [
];

var vendorScripts = [
	"bower_components/jquery/dist/jquery.js",
	"bower_components/bootstrap/dist/js/bootstrap.js",
	"bower_components/atk14js/src/atk14.js"
];

var applicationScripts = [
	"public/scripts/application.js"
];

// CSS
gulp.task( "styles", function() {
	return gulp.src( "public/styles/application.less" )
		.pipe( glp.sourcemaps.init() )
		.pipe( glp.less() )
		.pipe( glp.autoprefixer() )
		.pipe( glp.cleanCss() )
		.pipe( glp.rename( { suffix: ".min" } ) )
		.pipe( glp.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/styles" ) )
		.pipe( browserSync.stream() );
} );

gulp.task( "styles-vendor", function() {
	return gulp.src( vendorStyles )
		.pipe( glp.sourcemaps.init() )
		.pipe( glp.concatCss( "vendor.css" ) )
		.pipe( glp.autoprefixer() )
		.pipe( glp.cleanCss() )
		.pipe( glp.rename( { suffix: ".min" } ) )
		.pipe( glp.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/styles" ) )
		.pipe( browserSync.stream() );
} );

// JS
gulp.task( "scripts", function() {
	gulp.src( vendorScripts )
		.pipe( glp.sourcemaps.init() )
		.pipe( glp.concat( "vendor.js" ) )
		.pipe( glp.uglify() )
		.pipe( glp.rename( { suffix: ".min" } ) )
		.pipe( glp.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/scripts" ) );

	gulp.src( applicationScripts )
		.pipe( glp.sourcemaps.init() )
		.pipe( glp.concat( "application.js" ) )
		.pipe( glp.uglify() )
		.on( "error", errorHandler )
		.pipe( glp.rename( { suffix: ".min" } ) )
		.pipe( glp.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/scripts" ) )
		.pipe( browserSync.stream() );
} );

// Lint
gulp.task( "lint", function() {
	return gulp.src( [ "public/scripts/**/*.js", "gulpfile.js" ] )
		.pipe( glp.jshint() )
		.pipe( glp.jshint.reporter( "jshint-stylish" ) );
} );

// Code style
gulp.task( "jscs", function() {
	return gulp.src( [ "public/scripts/**/*.js", "gulpfile.js" ] )
		.pipe( glp.jscs() );
} );

// Copy
gulp.task( "copy", function() {
	gulp.src( "bower_components/html5shiv/dist/html5shiv.min.js" )
		.pipe( gulp.dest( "public/dist/scripts" ) );
	gulp.src( "bower_components/respond/dest/respond.min.js" )
		.pipe( gulp.dest( "public/dist/scripts" ) );
	gulp.src( "bower_components/bootstrap/dist/fonts/*" )
		.pipe( gulp.dest( "public/dist/fonts" ) );
	gulp.src( [
		"public/fonts/**/*.eot",
		"public/fonts/**/*.woff2",
		"public/fonts/**/*.woff",
		"public/fonts/**/*.ttf",
		"public/fonts/**/*.svg"
		] )
		.pipe( gulp.dest( "public/dist/fonts" ) );
	gulp.src( "public/images/*" )
		.pipe( gulp.dest( "public/dist/images" ) );
} );

// Clean
gulp.task( "clean", function() {
	del( "dist" );
} );

// Sets devel environment so that errors are handled properly.
gulp.task( "setDevEnv", function() {
	devel = true;
} );

// Server
gulp.task( "serve", [ "setDevEnv", "styles", "scripts" ], function() {
	browserSync.init( {
		proxy: "atk14skelet.localhost"
	} );

	gulp.watch( [
		"app/**/*.tpl",
		"public/images/**/*"
	] ).on( "change", browserSync.reload );

	gulp.watch( "public/styles/**/*.less", [ "styles" ] );
	gulp.watch( "public/scripts/**/*.js", [ "scripts" ] );
} );

// Build
var buildTasks = [
	"lint",
	"jscs",
	"styles",
	"styles-vendor",
	"scripts",
	"copy"
];

gulp.task( "build", buildTasks, function() {
	return gulp.src( "public/dist/**/*" )
		.pipe( glp.size( { title: "build", gzip: true } ) );
} );

// Default
gulp.task( "default", [ "clean" ], function() {
	gulp.start( "build" );
} );
