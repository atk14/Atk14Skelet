module.exports = function( grunt ) {

	grunt.initConfig({

		pkg: grunt.file.readJSON( "package.json" ),

		jshint: {
			options: {
				jshintrc: ".jshintrc"
			},

			vendor: {
				src: [
					"public/assets/vendor/jquery/jquery.js",
					"public/assets/bootstrap/bootstrap/js/bootstrap.js"
				]
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
			js: {
				src: [
					"public/assets/vendor/jquery/jquery.js",
					"public/assets/vendor/bootstrap/bootstrap/js/bootstrap.js",
					"public/assets/lib/atk14.js",
					"public/javascripts/skelet.js"
				],
				dest: "public/javascripts/application.js"
			},
			css: {
				src: [
					"public/assets/vendor/bootstrap/bootstrap/css/bootstrap.css",
					"public/assets/vendor/bootstrap/bootstrap/css/bootstrap-responsive.css",
					"public/stylesheets/skelet.css"
				],
				dest: "public/stylesheets/application.css"
			}
		},

		uglify: {
			options: {
				banner: "/* <%= pkg.name %> <%= grunt.template.today('dd-mm-yyyy') %> */\n"
			},
			app: {
				files: {
					"public/javascripts/application.min.js": [ "<%= concat.js.dest %>" ]
				}
			}
		},

		cssmin: {
			app: {
				src: "<%= concat.css.dest %>",
				dest: "public/stylesheets/application.min.css"
			}
		}
	});

	grunt.loadNpmTasks( "grunt-contrib-jshint" );
	grunt.loadNpmTasks( "grunt-contrib-uglify" );
	grunt.loadNpmTasks( "grunt-contrib-concat" );
	grunt.loadNpmTasks( "grunt-contrib-cssmin" );
	//grunt.loadNpmTasks( "grunt-contrib-qunit" );
	//grunt.loadNpmTasks( "grunt-contrib-watch" );

	grunt.registerTask( "lint", ["jshint"] );
	grunt.registerTask( "default", ["lint"] );
	grunt.registerTask( "build", ["uglify", "cssmin"] );
};
