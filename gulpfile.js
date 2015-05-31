var gulp = require( "gulp" );
var $ = require( "gulp-load-plugins" )();
var browserSync = require( "browser-sync" );

var vendorScripts = [
	"bower_components/jquery/dist/jquery.js",
	"bower_components/bootstrap/dist/js/bootstrap.js"
];
var applicationScripts = [
	"public/scripts/application.js"
];

// CSS
gulp.task( "styles", function() {
	return gulp.src( "public/styles/application.less" )
		.pipe( $.sourcemaps.init() )
		.pipe( $.less() )
		.pipe( $.autoprefixer() )
		.pipe( $.minifyCss() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/styles" ) )
		.pipe( browserSync.stream() );
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
		.pipe( gulp.dest( "public/dist/scripts" ) );
} );

// Lint
gulp.task( "lint", function() {
	return gulp.src( [ "public/scripts/**/*.js", "gulpfile.js" ] )
		.pipe( $.jshint() )
		.pipe( $.jshint.reporter( "jshint-stylish" ) );
} );

// Code style
gulp.task( "jscs", function() {
	return gulp.src( [ "public/scripts/**/*.js", "gulpfile.js" ] )
		.pipe( $.jscs() );
} );

// Clean
gulp.task( "clean", require( "del" ).bind( null, [ "public/dist" ] ) );

// Server
gulp.task( "serve", [ "styles" ], function() {
	browserSync.init( {
		proxy: "atk14skelet.localhost"
	} );

	gulp.watch( [
		"app/**/*.tpl",
		"public/scripts/**/*.js",
		"public/images/**/*",
		".tmp/fonts/**/*"
	] ).on( "change", browserSync.reload );

	gulp.watch( "public/styles/**/*.less", [ "styles" ] );
} );

// Build
gulp.task( "build", [ "lint", "jscs", "styles", "scripts" ], function() {
	return gulp.src( "public/dist/**/*" )
		.pipe( $.size( { title: "build", gzip: true } ) );
} );

// Default
gulp.task( "default", [ "clean" ], function() {
	gulp.start( "build" );
} );