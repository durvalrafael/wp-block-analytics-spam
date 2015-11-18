/*global module:false, require:false*/

module.exports = function(grunt) {

	require('load-grunt-tasks')(grunt);

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		dirs: {
			js      : '../assets/js',
			css     : '../assets/css',
			sass    : '../assets/sass'
		},

		svn: {
			path: '../../../../../../_wp_plugins/<%= pkg.name %>',
			tag: '<%= svn.path %>/tags/<%= pkg.version %>',
			trunk: '<%= svn.path %>/trunk',
			exclude: [
				'.editorconfig',
				'.git/',
				'.gitignore',
				'bin/',
				'assets/sass/',
				'README.md'
			]
		},

    	// Watch for changes
		watch: {
			compass: {
				files: [
					'<%= compass.dist.options.sassDir %>/**'
				],
				tasks: ['compass']
			},
			js: {
				files: [
					'<%= jshint.all %>'
				],
				tasks: ['jshint', 'uglify']
			}
		},

		// Javascript linting with jshint
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/*.js',
				'!<%= dirs.js %>/*.min.js'
			]
		},

		// Uglify to concat and minify
		uglify: {
			options: {
				force: true,
				mangle: false
			},
			dist: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			}
		},

		// Compile scss/sass files to CSS
		compass: {
			dist: {
				options: {
					force: true,

					basePath:'./',
					sassDir: '<%= dirs.styles %>/src/',
					cssDir: '<%= dirs.styles %>/dist/',
					javascriptsDir: '<%= dirs.scripts %>/dist/',
					imagesDir: '<%= dirs.images %>',
					fontsDir: '<%= dirs.fonts %>',

					outputStyle: 'compressed',
					relativeAssets: true,
					noLineComments: true
				}
			}
		},

		// Image optimization
		imagemin: {
			dist: {
				options: {
					optimizationLevel: 7,
					progressive: true
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.images %>/',
					src: ['**/*.{png,jpg,gif}'],
					dest: '<%= dirs.images %>/'
				}]
			}
		},

	    // rsync commands used to take the files to svn repository
	    rsync: {
			options: {
				//args: ['--verbose'],
				exclude: '<%= svn.exclude %>',
				syncDest: true,
				recursive: true
			},
			tag: {
				options: {
					src: '.././',
					dest: '<%= svn.tag %>'
				}
			},
			trunk: {
				options: {
					src: '.././',
					dest: '<%= svn.trunk %>'
				}
			}
		},

	    // shell command to commit the new version of the plugin
	    shell: {
			svn_remove: {
				command: 'svn st | grep \'^!\' | awk \'{print $2}\'',
				options: {
					stdout: true,
					stderr: true,
					execOptions: {
						cwd: '<%= svn.path %>'
					}
				}
			},

			svn_add: {
				command: 'svn add --force * --auto-props --parents --depth infinity -q',
				options: {
					stdout: true,
					stderr: true,
					execOptions: {
						cwd: '<%= svn.path %>'
					}
				}
			},

			svn_commit: {
				command: 'svn commit -m "updated the plugin version to <%= pkg.version %>"',
				options: {
					stdout: true,
					stderr: true,
					execOptions: {
						cwd: '<%= svn.path %>'
					}
				}
			}
		},

	    // Clean directories and files
	    clean: {
			options: {
				force: true
			},
			build: [
				'<%= dirs.deploy %>'
			]
		}

	});

	grunt.registerTask( 'default', [ 'compass', 'jshint', 'uglify' ]);
	grunt.registerTask( 'script', [ 'jshint', 'uglify' ]);
	grunt.registerTask( 'style', [ 'compass' ]);
	grunt.registerTask( 'image', [ 'imagemin' ]);
	grunt.registerTask( 'deploy', ['default', 'rsync:tag', 'rsync:trunk', 'shell:svn_remove', 'shell:svn_add', 'shell:svn_commit']);
};
