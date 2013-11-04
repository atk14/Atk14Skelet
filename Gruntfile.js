module.exports = function( grunt ) {

	grunt.initConfig({

		pkg: grunt.file.readJSON( "package.json" ),

		clean: {
			dist: [ "public/dist" ]
		},

		jshint: {
			options: {
				jshintrc: ".jshintrc"
			},

			atk14: {
				src: [ "public/assets/lib/atk14.js" ]
			},
			grunt: {
				src: [ "Gruntfile.js" ]
			},
			app: {
				src: [ "public/javascripts/application.js" ]
			},
			admin: {
				src: [ "public/javascripts/admin/application.js" ]
			}
		},

		concat: {
			bootstrap: {
				src: [
					"public/assets/vendor/bootstrap/js/transition.js",
					"public/assets/vendor/bootstrap/js/alert.js",
					"public/assets/vendor/bootstrap/js/button.js",
					"public/assets/vendor/bootstrap/js/carousel.js",
					"public/assets/vendor/bootstrap/js/collapse.js",
					"public/assets/vendor/bootstrap/js/dropdown.js",
					"public/assets/vendor/bootstrap/js/modal.js",
					"public/assets/vendor/bootstrap/js/tooltip.js",
					"public/assets/vendor/bootstrap/js/popover.js",
					"public/assets/vendor/bootstrap/js/scrollspy.js",
					"public/assets/vendor/bootstrap/js/tab.js",
					"public/assets/vendor/bootstrap/js/affix.js"
				],
				dest: "public/assets/vendor/bootstrap/dist/js/bootstrap.js"
			},
			app: {
				src: [
					"public/assets/vendor/jquery/jquery.js",
					"<%= concat.bootstrap.dest %>",
					"public/assets/lib/atk14.js",
					"public/javascripts/application.js"
				],
				dest: "public/dist/js/app.js"
			},
			admin: {
				src: [
					"public/assets/vendor/jquery/jquery.js",
					"<%= concat.bootstrap.dest %>",
					"public/assets/lib/atk14.js",
					"public/javascripts/admin/application.js"
				],
				dest: "public/dist/admin/js/app.js"
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
					"public/dist/js/admin/app.min.js": [ "<%= concat.admin.dest %>" ]
				}
			},
		},

		recess: {
			compile_app: {
				options: {
					compile: true
				},
				src: [
					"public/stylesheets/application.less"
				],
				dest: "public/dist/css/app.css"
			},
			minify_app: {
				options: {
					compress: true
				},
				src: "<%= recess.compile_app.dest %>",
				dest: "public/dist/css/app.min.css"
			},
			compile_admin: {
				options: {
					compile: true
				},
				src: [
					"public/stylesheets/admin/application.less"
				],
				dest: "public/dist/admin/css/app.css"
			},
			minify_admin: {
				options: {
					compress: true
				},
				src: "<%= recess.compile_admin.dest %>",
				dest: "public/dist/admin/css/app.min.css"
			}
		},

		copy: {
			fonts_app: {
				src: "public/assets/vendor/bootstrap/fonts/*",
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
				src: "public/assets/vendor/bootstrap/fonts/*",
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
				src: "public/assets/vendor/respond/respond.min.js",
				dest: "public/dist/vendor/js/",
				flatten: true,
				expand: true
			}
		},

		watch: {
			css_app: {
				files: "public/stylesheets/application.less",
				tasks: [ "recess:compile_app" ]
			},
			js_app: {
				files: "<%= concat.app.src %>",
				tasks: [ "concat:app" ]
			},

			css_admin: {
				files: "public/stylesheets/admin/application.less",
				tasks: [ "recess:compile_admin" ]
			},
			js_admin: {
				files: "<%= concat.admin.src %>",
				tasks: [ "concat:admin" ]
			}
		},
	});

	grunt.loadNpmTasks( "grunt-contrib-clean" );
	grunt.loadNpmTasks( "grunt-contrib-concat" );
	grunt.loadNpmTasks( "grunt-contrib-copy" );
	grunt.loadNpmTasks( "grunt-contrib-jshint" );
	grunt.loadNpmTasks( "grunt-contrib-uglify" );
	grunt.loadNpmTasks( "grunt-contrib-watch" );
	grunt.loadNpmTasks( "grunt-recess" );

	grunt.registerTask( "default", ["jshint"] );
	grunt.registerTask( "dist", [ "jshint", "concat", "uglify", "recess", "copy"] );
};
