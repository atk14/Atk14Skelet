var gulp = require( "gulp" );
var del = require( "del" );
var $ = require( "gulp-load-plugins" )();
var browserSync = require( "browser-sync" ).create();

var vendorStyles = [
	"node_modules/blueimp-file-upload/css/jquery.fileupload.css",
	"node_modules/bootstrap-markdown-editor-4/dist/css/bootstrap-markdown-editor.min.css",
	"node_modules/jquery-ui-bundle/jquery-ui.css",
	"node_modules/@fortawesome/fontawesome-free/css/all.css",
	"node_modules/animate.css/animate.css",
];
var vendorScripts = [
	"node_modules/jquery/dist/jquery.js",
	"node_modules/jquery-ui-bundle/jquery-ui.js",
	"node_modules/sortablejs/Sortable.js",
	"node_modules/blueimp-file-upload/js/jquery.fileupload.js",
	"node_modules/ace-builds/src/ace.js",
	"node_modules/ace-builds/src/mode-markdown.js",
	"node_modules/ace-builds/src/theme-tomorrow.js",
	"node_modules/bootstrap-markdown-editor-4/dist/js/bootstrap-markdown-editor.min.js",
	"node_modules/bootstrap/dist/js/bootstrap.bundle.js", // Bootstrap + Popper
	"node_modules/atk14js/src/atk14.js",
	"node_modules/unobfuscatejs/src/jquery.unobfuscate.js",
	"node_modules/popper.js/dist/umd/popper.js",
	"node_modules/bootstrap4-notify/bootstrap-notify.js",
	"node_modules/autocompleter/autocomplete.js",
];

var applicationScripts = [
	"public/scripts/utils/utils.js",
	"public/scripts/utils/leaving_unsaved_page_checker.js",
	"public/scripts/utils/suggestions.js",
	"public/scripts/utils/async_file_upload.js",
	"public/admin/scripts/application.js",
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
		.pipe( $.autoprefixer( { grid: true } ) )
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
		.pipe( $.eslint() )
		.pipe( $.eslint.format() )
		.pipe( $.eslint.failAfterError() );
} );

// Copy
gulp.task( "copy-admin", function() {
	gulp.src( "node_modules/html5shiv/dist/html5shiv.min.js" )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) );
	gulp.src( "node_modules/respond.js/dest/respond.min.js" )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) );
	gulp.src( "node_modules/@fortawesome/fontawesome-free/webfonts/*" )
		.pipe( gulp.dest( "public/admin/dist/webfonts" ) );
	gulp.src( "node_modules/jquery-ui-bundle/images/*" )
		.pipe( gulp.dest( "public/admin/dist/styles/images" ) );
	gulp.src( "public/admin/fonts/*" )
		.pipe( gulp.dest( "public/admin/dist/fonts" ) );
	gulp.src( "public/admin/images/*" )
		.pipe( gulp.dest( "public/admin/dist/images" ) );
	gulp.src( "node_modules/ace-builds/src-min/**" )
		.pipe( gulp.dest( "public/admin/dist/scripts/ace" ) );
} );

// Clean
gulp.task( "clean-admin", function() {
	del.sync( "public/admin/dist" );
} );

// Server
gulp.task( "serve-admin", [ "styles-admin", "styles-vendor-admin" ], function() {
	browserSync.init( {
		proxy: "localhost:8000/admin/"
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
