module.exports = function( grunt ) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		uglify: {
			main: {
				options: {
					mangle: true,
					compress: {
						sequences: true,
						dead_code: true,
						conditionals: true,
						booleans: true,
						unused: true,
						if_return: true,
						join_vars: true,
						drop_console: true
					}
				},
				files: [{
					expand: true,
					cwd: 'assets/js',
					src: '**/*.js',
					dest: 'assets/min/js'
				}]
			}
		},

		// Get json file from the google-fonts API
		curl: {
			'google-fonts-source': {
				src: 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=AIzaSyCDiOc36EIOmwdwspLG3LYwCg9avqC5YLs',
				dest: 'inc/redux/custom-fields/typography/googlefonts.json'
			}
		},

		// Converts the googlefonts json file to a PHP array.
		json2php: {
			convert: {
				expand: true,
				ext: '-array.php',
				src: ['inc/redux/custom-fields/typography/googlefonts.json']
			}
		},

		// Delete the json array
		clean: [
			'inc/redux/custom-fields/typography/googlefonts.json',
			'languages/Avada.mo',
			'languages/Avada.po'
		],
		watch: {
			js: {
				files: ['assets/js/**/*.js'],
				tasks: ['less:development', 'cssmin']
			}
		},
		less: {
			options: {
				plugins: [ new ( require( 'less-plugin-autoprefix' ) )( { browsers: [ 'last 8 versions' ] } ) ]
			},
			development: {
				files: {
					'inc/redux/assets/style.css': 'inc/redux/assets/style.less'
				}
			}
		},
		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					'assets/fonts/fontawesome/font-awesome.min.css': ['assets/fonts/fontawesome/font-awesome.css']
				}
			}
		}
	});

	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-less' );
	grunt.loadNpmTasks( 'grunt-curl' );
	grunt.loadNpmTasks( 'grunt-json2php' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );

	grunt.registerTask( 'watchCSS', ['watch:js'] );
	grunt.registerTask( 'default', ['uglify:main', 'less:development', 'cssmin', 'curl:google-fonts-source', 'json2php', 'clean'] );

};
