var gulp = require( "gulp" );
var del = require( "del" );
var $ = require( "gulp-load-plugins" )();
var browserSync = require( "browser-sync" ).create();

var vendorStyles = [
	"node_modules/blueimp-file-upload/css/jquery.fileupload.css",
	"node_modules/bootstrap-markdown/css/bootstrap-markdown.min.css",
	"node_modules/jquery-ui-bundle/jquery-ui.css",
	"node_modules/@fortawesome/fontawesome-free/css/all.css"
];
var vendorScripts = [
	"node_modules/jquery/dist/jquery.js",
	"node_modules/jquery-ui-bundle/jquery-ui.js",
	"node_modules/blueimp-file-upload/js/jquery.fileupload.js",
	"node_modules/markdown/lib/markdown.js",
	"node_modules/bootstrap-markdown/js/bootstrap-markdown.js",
	"node_modules/bootstrap/dist/js/bootstrap.bundle.js", // Bootstrap + Popper
	"node_modules/atk14js/src/atk14.js",
	"node_modules/unobfuscatejs/src/jquery.unobfuscate.js"
];

var applicationScripts = [
	"public/admin/scripts/application.js"
];

// CSS
gulp.task( "styles-admin", function() {
	return gulp.src( "public/admin/styles/application.scss" )
		.pipe( $.sourcemaps.init() )
		.pipe( $.sass( {
			includePaths: [
				"public/admin/styles"
			]
		} ) )
		.pipe( $.autoprefixer() )
		.pipe( $.cssnano() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/admin/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
} );

gulp.task( "styles-vendor-admin", function() {
	return gulp.src( vendorStyles )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concatCss( "vendor.css", { rebaseUrls: false } ) )
		.pipe( $.autoprefixer() )
		.pipe( $.cssnano() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/admin/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
} );

// JS
gulp.task( "scripts-admin", function() {
	gulp.src( vendorScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "vendor.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) );

	gulp.src( applicationScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "application.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) );
} );

// Lint
gulp.task( "lint-admin", function() {
	return gulp.src( [ "public/admin/scripts/**/*.js", "gulpfile-admin.js" ] )
		.pipe( $.jshint() )
		.pipe( $.jshint.reporter( "jshint-stylish" ) );
} );

// Code style
gulp.task( "jscs-admin", function() {
	return gulp.src( [ "public/admin/scripts/**/*.js", "gulpfile-admin.js" ] )
		.pipe( $.jscs() );
} );

// Copy
gulp.task( "copy-admin", function() {
	gulp.src( "node_modules/html5shiv/dist/html5shiv.min.js" )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) );
	gulp.src( "node_modules/respond.js/dest/respond.min.js" )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) );
	gulp.src( "node_modules/@fortawesome/fontawesome-free/webfonts/*" )
		.pipe( gulp.dest( "public/dist/webfonts" ) );
	gulp.src( "node_modules/jquery-ui-bundle/images/*" )
		.pipe( gulp.dest( "public/admin/dist/styles/images" ) );
	gulp.src( "public/admin/fonts/*" )
		.pipe( gulp.dest( "public/admin/dist/fonts" ) );
	gulp.src( "public/admin/images/*" )
		.pipe( gulp.dest( "public/admin/dist/images" ) );
} );

// Clean
gulp.task( "clean-admin", function() {
	del( "admin/dist" );
} );

// Server
gulp.task( "serve-admin", [ "styles-admin", "styles-vendor-admin" ], function() {
	browserSync.init( {
		proxy: "atk14skelet.localhost/admin/"
	} );

	// If these files change = reload browser
	gulp.watch( [
		"app/**/*.tpl",
		"public/admin/images/**/*"
	] ).on( "change", browserSync.reload );

	// If javascript files change = run 'scripts' task, then reload browser
	gulp.watch( "public/admin/scripts/**/*.js", [ "scripts-admin" ] )
		.on( "change", browserSync.reload );

	// If styles files change = run 'styles' task with style injection
	gulp.watch( "public/admin/styles/**/*.scss", [ "styles-admin" ] );
} );

// Build
var buildTasks = [
	"lint-admin",
	"jscs-admin",
	"styles-admin",
	"styles-vendor-admin",
	"scripts-admin",
	"copy-admin"
];

gulp.task( "build-admin", buildTasks,  function() {
	return gulp.src( "public/admin/dist/**/*" )
		.pipe( $.size( { title: "build", gzip: true } ) );
} );

// Default (Admin)
gulp.task( "admin", [ "clean-admin" ], function() {
	gulp.start( "build-admin" );
} );
