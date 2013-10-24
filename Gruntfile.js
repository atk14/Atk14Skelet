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
				src: [ "public/javascripts/skelet.js" ]
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
					"public/javascripts/skelet.js"
				],
				dest: "public/dist/js/app.js"
			}
		},

		uglify: {
			app: {
				files: {
					"public/dist/js/app.min.js": [ "<%= concat.app.dest %>" ]
				}
			}
		},

		recess: {
			compile: {
				options: {
					compile: true
				},
				src: [
					"public/stylesheets/skelet.less"
				],
				dest: "public/dist/css/app.css"
			},
			minify: {
				options: {
					compress: true
				},
				src: "<%= recess.compile.dest %>",
				dest: "public/dist/css/app.min.css"
			}
		},

		copy: {
			bootstrap_fonts: {
				files: [
					{
						src: "public/assets/vendor/bootstrap/fonts/*",
						dest: "public/dist/fonts/",
						flatten: true,
						expand: true
					}
				]
			},
			html5shiv: {
				src: "public/assets/vendor/html5shiv/dist/html5shiv.js",
				dest: "public/dist/js/",
				flatten: true,
				expand: true
			},
			respond: {
				src: "public/assets/vendor/respond/respond.min.js",
				dest: "public/dist/js/",
				flatten: true,
				expand: true
			}
		}
	});

	grunt.loadNpmTasks( "grunt-contrib-clean" );
	grunt.loadNpmTasks( "grunt-contrib-jshint" );
	grunt.loadNpmTasks( "grunt-contrib-uglify" );
	grunt.loadNpmTasks( "grunt-contrib-concat" );
	grunt.loadNpmTasks( "grunt-contrib-copy" );
	grunt.loadNpmTasks( "grunt-recess" );

	grunt.registerTask( "default", ["jshint"] );
	grunt.registerTask( "dist", [ "jshint", "concat", "uglify", "recess", "copy"] );
};
