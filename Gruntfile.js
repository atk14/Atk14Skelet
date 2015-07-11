module.exports = function( grunt ) {
	"use strict";

	// Load grunt tasks automatically
	require( "load-grunt-tasks" )( grunt );
	// Time how long tasks take. Can help when optimizing build times
	require( "time-grunt" )( grunt );

	var bsRoot = "public/assets/vendor/bootstrap/";

	grunt.initConfig({

		pkg: grunt.file.readJSON( "package.json" ),

		clean: {
			dist: [ "public/dist" ]
		},

		jshint: {
			options: {
				jshintrc: ".jshintrc",
				reporter: require( "jshint-stylish" )
			},
			atk14: {
				src: "public/assets/lib/atk14.js"
			},
			grunt: {
				src: "Gruntfile.js"
			},
			app: {
				src: "public/javascripts/application.js"
			},
			admin: {
				src: "public/javascripts/admin/application.js"
			}
		},

		jscs: {
			options: {
				config: ".jscsrc"
			},
			atk14: {
				src: "<%= jshint.atk14.src %>"
			},
			grunt: {
				src: "<%= jshint.grunt.src %>"
			},
			app: {
				src: "<%= jshint.app.src %>"
			},
			admin: {
				src: "<%= jshint.admin.src %>"
			}
		},

		concat: {
			bootstrap: {
				src: [
					bsRoot + "js/transition.js",
					bsRoot + "js/alert.js",
					bsRoot + "js/button.js",
					bsRoot + "js/carousel.js",
					bsRoot + "js/collapse.js",
					bsRoot + "js/dropdown.js",
					bsRoot + "js/modal.js",
					bsRoot + "js/tooltip.js",
					bsRoot + "js/popover.js",
					bsRoot + "js/scrollspy.js",
					bsRoot + "js/tab.js",
					bsRoot + "js/affix.js"
				],
				dest: bsRoot + "dist/js/bootstrap.js",
				nonull: true
			},
			app: {
				src: [
					"public/assets/vendor/jquery/dist/jquery.js",
					"<%= concat.bootstrap.dest %>",
					"public/assets/lib/atk14.js",
					"public/javascripts/application.js"
				],
				dest: "public/dist/js/app.js",
				nonull: true
			},
			admin: {
				src: [
					"public/assets/vendor/jquery/dist/jquery.js",
					"public/assets/vendor/jquery-ui/ui/core.js",
					"public/assets/vendor/jquery-ui/ui/widget.js",
					"public/assets/vendor/jquery-ui/ui/position.js",
					"public/assets/vendor/jquery-ui/ui/menu.js",
					"public/assets/vendor/jquery-ui/ui/autocomplete.js",
					"<%= concat.bootstrap.dest %>",
					"public/assets/lib/atk14.js",
					"public/javascripts/admin/application.js"
				],
				dest: "public/dist/admin/js/app.js",
				nonull: true
			}
		},

		uglify: {
			app: {
				files: {
					"public/dist/js/app.min.js": [ "<%= concat.app.dest %>" ]
				}
			},
			admin: {
				files: {
					"public/dist/admin/js/app.min.js": [ "<%= concat.admin.dest %>" ]
				}
			}
		},

		less: {
			development: {
				src: "public/stylesheets/application.less",
				dest: "public/dist/css/app.css",
				nonull: true
			},
			production: {
				options: {
					cleancss: true
				},
				src: "public/stylesheets/application.less",
				dest: "public/dist/css/app.min.css"
			},
			admin_development: {
				src: "public/stylesheets/admin/application.less",
				dest: "public/dist/admin/css/app.css",
				nonull: true
			},
			admin_production: {
				options: {
					cleancss: true
				},
				src: "public/stylesheets/admin/application.less",
				dest: "public/dist/admin/css/app.min.css"
			}
		},

		copy: {
			fonts_app: {
				src: "public/assets/vendor/bootstrap/dist/fonts/*",
				dest: "public/dist/fonts/",
				flatten: true,
				expand: true
			},
			images_app: {
				src: "public/images/*",
				dest: "public/dist/images/",
				flatten: true,
				expand: true
			},
			fonts_admin: {
				src: "public/assets/vendor/bootstrap/dist/fonts/*",
				dest: "public/dist/admin/fonts/",
				flatten: true,
				expand: true
			},
			images_admin: {
				src: "public/images/admin/*",
				dest: "public/dist/admin/images/",
				flatten: true,
				expand: true
			},
			html5shiv: {
				src: "public/assets/vendor/html5shiv/dist/html5shiv.js",
				dest: "public/dist/vendor/js/",
				flatten: true,
				expand: true
			},
			respond: {
				src: "public/assets/vendor/respond/dest/respond.min.js",
				dest: "public/dist/vendor/js/",
				flatten: true,
				expand: true
			}
		},

		watch: {
			options: {
				livereload: true
			},
			gruntfile: {
				files: "Gruntfile.js",
				tasks: [ "jshint:grunt", "jscs:grunt", "concat" ]
			},
			css_app: {
				files: "public/stylesheets/application.less",
				tasks: [ "less:development" ]
			},
			js_app: {
				files: "<%= concat.app.src %>",
				tasks: [ "concat:app" ]
			},

			css_admin: {
				files: "public/stylesheets/admin/application.less",
				tasks: [ "less:admin_development" ]
			},
			js_admin: {
				files: "<%= concat.admin.src %>",
				tasks: [ "concat:admin" ]
			}
		}
	});

	grunt.registerTask( "default", [ "jshint", "jscs" ] );
	grunt.registerTask( "dist", [ "default", "concat", "uglify", "less", "copy" ] );
};
