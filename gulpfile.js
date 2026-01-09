
const gulp = require("gulp");
const del = require("del");
const rename = require("gulp-rename");
const babel = require("gulp-babel");
const $ = require("gulp-load-plugins")();
const browserSync = require("browser-sync").create();
const favicons = require("favicons").stream;
const postcss = require("gulp-postcss");
const cssnano = require("cssnano");
const concat = require("gulp-concat");
const autoprefixer = require("autoprefixer");
const eslint = require("gulp-eslint-new");

const vendorStyles = [
	"node_modules/@fortawesome/fontawesome-free/css/all.min.css",
	"node_modules/swiper/swiper-bundle.min.css",
	"node_modules/photoswipe/dist/photoswipe.css"
];

const vendorScripts = [
	"node_modules/jquery/dist/jquery.js",
	"node_modules/bootstrap/dist/js/bootstrap.bundle.js", // Bootstrap + Popper
	"node_modules/atk14js/src/atk14.js",
	"node_modules/unobfuscatejs/src/jquery.unobfuscate.js",
	"node_modules/swiper/swiper-bundle.js",
];

const applicationScripts = [
	"public/scripts/utils/utils.js",
	"public/scripts/utils/swiper.js",
	"public/scripts/application.js"
];

const applicationESModules = [
	"public/scripts/modules/application_es6.js"
]

// CSS
function styles() {
	return gulp.src( "public/styles/application.scss" )
		.pipe( $.sourcemaps.init() )
		.pipe( $.sass( {
			includePaths: [
				"public/styles"
			]
		} ) )
		.pipe( postcss( [ autoprefixer(), cssnano() ] ) )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
}

function stylesVendor() {
	return gulp.src( vendorStyles )
		.pipe( $.sourcemaps.init() )
		.pipe( concat( "vendor.css" ) )
		.pipe( postcss( [ autoprefixer(), cssnano() ] ) )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( ".", { sourceRoot: null } ) )
		.pipe( gulp.dest( "public/dist/styles" ) )
		.pipe( browserSync.stream( { match: "**/*.css" } ) );
}

// JS
function scriptsVendor() {
  return gulp.src( vendorScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "vendor.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/scripts" ) );
}

function scriptsApplication() {
  return gulp.src( applicationScripts )
		.pipe( $.sourcemaps.init() )
		.pipe( $.concat( "application.js" ) )
		.pipe( $.uglify() )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( gulp.dest( "public/dist/scripts" ) )
		.pipe( browserSync.stream() );
}

function scriptsES6() {
  return gulp.src( applicationESModules )
		.pipe( $.sourcemaps.init() )
		.pipe( babel() )
		.pipe( $.uglify() )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( gulp.dest( "public/dist/scripts/modules" ) )
		.pipe( browserSync.stream() );
}

// Combine all scripts tasks
const scripts = gulp.parallel( scriptsVendor, scriptsApplication, scriptsES6 );

/*gulp.task( "scripts", function() {
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

	// ES6 modules need different processing
	gulp.src( applicationESModules )
		.pipe( $.sourcemaps.init() )
		.pipe( babel() )
		.pipe( $.uglify() )
		.pipe( $.sourcemaps.write( "." ) )
		.pipe( $.rename( { suffix: ".min" } ) )
		.pipe( gulp.dest( "public/dist/scripts/modules" ) )
		.pipe( browserSync.stream() );
} );*/

// Favicons
function faviconTask() {
  const execSync = require( "child_process" ).execSync;
	const appName = execSync( "./scripts/dump_settings ATK14_APPLICATION_NAME" ).toString().trim();
	const appDescription = execSync( "./scripts/dump_settings ATK14_APPLICATION_DESCRIPTION" ).toString().trim();
	const appUrl = execSync( "./scripts/dump_settings ATK14_APPLICATION_URL" ).toString().trim();
	const baseHref = execSync( "./scripts/dump_settings ATK14_BASE_HREF" ).toString().trim(); // e.g. "/"

  return gulp.src( [ "public/favicons/favicon.png" ] )
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
}

// Lint & Code style
function lint() {
  return gulp.src( [ "public/scripts/**/*.js", "gulpfile.js" ] )
		.pipe( eslint() )
		.pipe( eslint.format() )
		.pipe( eslint.failAfterError() );
}

// Copy
function copyFiles( done ) {
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
	gulp.src( "node_modules/photoswipe/dist/photoswipe.esm.min.js" )
		.pipe( gulp.dest( "public/dist/scripts/modules" ) );
	gulp.src( "node_modules/photoswipe/dist/photoswipe-lightbox.esm.min.js" )
		.pipe( gulp.dest( "public/dist/scripts/modules" ) );

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
			done();
		} );
}

// Clean
function clean() {
	return del( "public/dist" );
}

// Watch function
function watchFiles() {
  // If these files change = reload browser
  gulp.watch( [
    "app/**/*.tpl",
    "public/images/**/*"
  ] ).on( "change", browserSync.reload );

  // If javascript files change = run 'scripts' task, then reload browser
  gulp.watch( "public/scripts/**/*.js", gulp.series( scripts ) ).on( "change", browserSync.reload );

  // If styles files change = run 'styles' task with style injection
  gulp.watch( "public/styles/**/*.scss", styles );
}

// Server
function serve( done ) {
  browserSync.init( {
    proxy: "localhost:8000"
  } );
  done();
}

// Build size reporting
function buildSize() {
  return gulp.src( "public/dist/**/*" )
    .pipe( $.size( { title: "build", gzip: true } ) );
}

// Build task
const build = gulp.series(
  //lint,
  gulp.parallel( styles, stylesVendor, scripts ),
  faviconTask,
  copyFiles,
  buildSize
);

/*// Build
var buildTasks = [
	"lint",
	"styles",
	"styles-vendor",
	"scripts",
	"favicons",
	"copy"
];*/

// Serve task (with initial styles build)
const serveDev = gulp.series( styles, serve, watchFiles );

// Default task
const defaultTask = gulp.series( clean, build );

// Export tasks
exports.styles = styles;
exports.stylesVendor = stylesVendor;
exports.scripts = scripts;
exports.favicons = faviconTask;
exports.lint = lint;
exports.copy = copyFiles;
exports.clean = clean;
exports.build = build;
exports.serve = serveDev;
exports.default = defaultTask;

// Legacy task names for backward compatibility (optional)
gulp.task("styles", styles);
gulp.task("styles-vendor", stylesVendor);
gulp.task("scripts", scripts);
gulp.task("favicons", faviconTask);
gulp.task("lint", lint);
gulp.task("copy", copyFiles);
gulp.task("clean", clean);
gulp.task("build", build);
gulp.task("serve", serveDev);
gulp.task("default", defaultTask);
