
const gulp = require("gulp");
const del = require("del");
const $ = require("gulp-load-plugins")();
const postcss = require("gulp-postcss");
const cssnano = require("cssnano");
const concat = require("gulp-concat");
const autoprefixer = require("autoprefixer");
const eslint = require("gulp-eslint-new");
const browserSync = require("browser-sync").create();


const vendorStyles = [
	"node_modules/blueimp-file-upload/css/jquery.fileupload.css",
	"node_modules/bootstrap-markdown-editor-4/dist/css/bootstrap-markdown-editor.min.css",
	"node_modules/jquery-ui-bundle/jquery-ui.css",
	"node_modules/@fortawesome/fontawesome-free/css/all.css",
	"node_modules/animate.css/animate.css",
	"node_modules/swiper/swiper-bundle.min.css"
];

const vendorScripts = [
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
	"node_modules/autocompleter/autocomplete.js",
	"node_modules/swiper/swiper-bundle.js", // needed for md preview
];

const applicationScripts = [
	"public/scripts/utils/utils.js",
	"public/scripts/utils/leaving_unsaved_page_checker.js",
	"public/scripts/utils/suggestions.js",
	"public/admin/scripts/utils/async_image_upload.js",
	"public/scripts/utils/async_file_upload.js",
	"public/admin/scripts/utils/tag_chooser.js",
	"public/scripts/utils/notifications.js",
	"public/admin/scripts/utils/md_editor_resizer.js",
	"public/admin/scripts/utils/collapsible_sidebar.js",
	"public/admin/scripts/utils/enhanced_file_field.js",
	"public/admin/scripts/utils/layout_designer.js",
	"public/scripts/utils/swiper.js",
	"public/admin/scripts/utils/preview_mode_toggle.js",
	"public/admin/scripts/application.js",
];

// CSS
function stylesAdmin() {
	return gulp.src( "public/admin/styles/application.scss" )
		.pipe( $.sourcemaps.init() )
		.pipe( $.sass( {
			includePaths: [
				"public/admin/styles"
			]
		} ) )
		.pipe( postcss( [ autoprefixer(), cssnano() ] ) )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/admin/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
}

function stylesVendorAdmin() {
	return gulp.src( vendorStyles )
		.pipe( $.sourcemaps.init() )
		.pipe( concat( "vendor.css" ) )
		.pipe( postcss( [ autoprefixer(), cssnano() ] ) )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/admin/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
}

// JS
function scriptsVendorAdmin() {
  return gulp.src( vendorScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "vendor.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) );
}

function scriptsApplicationAdmin() {
  return gulp.src( applicationScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "application.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/admin/dist/scripts" ) )
		.pipe( browserSync.stream() );
}

// Combine all scripts tasks
const scriptsAdmin = gulp.parallel( scriptsVendorAdmin, scriptsApplicationAdmin );

// Lint
function lintAdmin() {
	return gulp.src( [ "public/admin/scripts/**/*.js", "gulpfile-admin.js" ] )
		.pipe( eslint() )
		.pipe( eslint.format() )
		.pipe( eslint.failAfterError() );
}

// Copy
function copyFilesAdmin( done ) {
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
	done();
}

// Clean
function cleanAdmin() {
	return del.sync( "public/admin/dist" );
}

// Watch function
function watchFilesAdmin() {
  // If these files change = reload browser
  gulp.watch( [
    "app/**/*.tpl",
    "public/admin/images/**/*"
  ] ).on( "change", browserSync.reload );

  // If javascript files change = run 'scripts' task, then reload browser
  gulp.watch( "public/admin/scripts/**/*.js", gulp.series( scriptsAdmin ) ).on( "change", browserSync.reload );

  // If styles files change = run 'styles' task with style injection
  gulp.watch( "public/admin/styles/**/*.scss", stylesAdmin );
}

// Server
function serveAdmin( done ) {
  browserSync.init( {
    proxy: "localhost:8000/admin/"
  } );
  done();
}

// Build size reporting
function buildSizeAdmin() {
  return gulp.src( "public/admin/dist/**/*" )
    .pipe( $.size( { title: "build", gzip: true } ) );
}

// Build task
const buildAdmin = gulp.series(
  lintAdmin,
  gulp.parallel( stylesAdmin, stylesVendorAdmin, scriptsAdmin ),
  copyFilesAdmin,
  buildSizeAdmin
);

// Serve task (with initial styles build)
const serveDevAdmin = gulp.series( stylesAdmin, serveAdmin, watchFilesAdmin );

// Default task
const defaultTaskAdmin = gulp.series( cleanAdmin, buildAdmin );

// Export tasks
exports.stylesAdmin = stylesAdmin;
exports.stylesVendorAdmin = stylesVendorAdmin;
exports.scriptsAdmin = scriptsAdmin;
exports.lintAdmin = lintAdmin;
exports.copyAdmin = copyFilesAdmin;
exports.cleanAdmin = cleanAdmin;
exports.buildAdmin = buildAdmin;
exports.serveAdmin = serveDevAdmin;
exports.defaultAdmin = defaultTaskAdmin;
