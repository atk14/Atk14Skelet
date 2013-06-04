module.exports = function( grunt ) {

	grunt.initConfig({

		pkg: grunt.file.readJSON( "package.json" ),

		jshint: {
			options: {
				jshintrc: ".jshintrc"
			},

			bootstrap: {
				options: {
					jshintrc: "public/assets/vendor/bootstrap/js/.jshintrc"
				},
				src: "public/assets/vendor/bootstrap/js/*.js"
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
					"public/assets/vendor/bootstrap/js/bootstrap-transition.js",
					"public/assets/vendor/bootstrap/js/bootstrap-alert.js",
					"public/assets/vendor/bootstrap/js/bootstrap-button.js",
					"public/assets/vendor/bootstrap/js/bootstrap-carousel.js",
					"public/assets/vendor/bootstrap/js/bootstrap-collapse.js",
					"public/assets/vendor/bootstrap/js/bootstrap-dropdown.js",
					"public/assets/vendor/bootstrap/js/bootstrap-modal.js",
					"public/assets/vendor/bootstrap/js/bootstrap-tooltip.js",
					"public/assets/vendor/bootstrap/js/bootstrap-popover.js",
					"public/assets/vendor/bootstrap/js/bootstrap-scrollspy.js",
					"public/assets/vendor/bootstrap/js/bootstrap-tab.js",
					"public/assets/vendor/bootstrap/js/bootstrap-typeahead.js",
					"public/assets/vendor/bootstrap/js/bootstrap-affix.js"
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
				dest: "public/javascripts/application.js"
			}
		},

		uglify: {
			options: {
				banner: "/* <%= pkg.name %> <%= grunt.template.today('dd-mm-yyyy') %> */\n"
			},
			app: {
				files: {
					"public/javascripts/application.min.js": [ "<%= concat.app.dest %>" ]
				}
			}
		},

		recess: {
			options: {
				compile: true
			},

			bootstrap: {
				src: "public/assets/vendor/bootstrap/less/bootstrap.less",
				dest: "public/assets/vendor/bootstrap/dist/css/bootstrap.css"
			},
			bootstrap_responsive: {
				src: "public/assets/vendor/bootstrap/less/responsive.less",
				dest: "public/assets/vendor/bootstrap/dist/css/bootstrap-responsive.css"
			},
			concat: {
				src: [
					"<%= recess.bootstrap.dest %>",
					"<%= recess.bootstrap_responsive.dest %>",
					"public/stylesheets/skelet.css"
				],
				dest: "public/stylesheets/application.css"
			},
			minify: {
				options: {
					compress: true
				},
				src: "<%= recess.concat.dest %>",
				dest: "public/stylesheets/application.min.css"
			}
		}
	});

	grunt.loadNpmTasks( "grunt-contrib-jshint" );
	grunt.loadNpmTasks( "grunt-contrib-uglify" );
	grunt.loadNpmTasks( "grunt-contrib-concat" );
	grunt.loadNpmTasks( "grunt-recess" );
	//grunt.loadNpmTasks( "grunt-contrib-qunit" );
	//grunt.loadNpmTasks( "grunt-contrib-watch" );

	grunt.registerTask( "lint", ["jshint"] );
	grunt.registerTask( "default", ["lint"] );
	grunt.registerTask( "build", ["concat", "uglify", "recess"] );
};
