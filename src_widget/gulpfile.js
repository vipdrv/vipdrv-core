'use strict';

var browserSync = require('browser-sync').create();
var reload = browserSync.reload;

var gulp = require('gulp');
var $ = require('gulp-load-plugins')();
var plumber = require('gulp-plumber');
var html2js = require('gulp-html2js');
var runSequence = require('run-sequence');
var debug = require('gulp-debug');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var cssnano = require('gulp-cssnano');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var del = require('del');
var clean = require('gulp-clean');
var urlAdjuster = require('gulp-css-url-adjuster');

var config = {
    templates: './src/app/**/*.tpl.html',
    angularAppFiles: ['./build/tmp/templates.js', './src/app/prototypes/**/*.js', './src/app/**/*module.js', './src/app/app.js', './src/app/**/*.js', '!./src/app/**/*spec.js'],
    angularAppFiles_watch: ['./src/app/prototypes/**/*.js', './src/app/**/*module.js', './src/app/app.js', './src/app/**/*.js', '!./src/app/**/*spec.js'],
};

var vendorJs = [
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/popper.js/dist/umd/popper.min.js',

    'node_modules/angular/angular.min.js',
    'node_modules/angular-animate/angular-animate.min.js',
    'node_modules/angular-aria/angular-aria.min.js',
    'node_modules/angular-messages/angular-messages.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/ngcomponentrouter/angular_1_router.js'
];

var vendorCss = [
    'node_modules/bootstrap/dist/css/bootstrap.min.css',
    'node_modules/angular-material/angular-material.min.css',
    'node_modules/font-awesome/css/font-awesome.min.css'
];

var vendorFonts = [
    'node_modules/font-awesome/fonts/*'
];

var appScss = [
    './src/sass/app.scss'
];

var appScssWatch = [
    './src/sass/**/*.scss'
];

gulp.task('clean', function () {
    return del.sync('./build');
});

gulp.task('clean_tmp', function () {
    del.sync('./build/maps');
    del.sync('./build/tmp');
});

gulp.task('bundle_vendor_js', function () {
    gulp.src(vendorJs)
        .pipe(plumber())
        // .pipe(debug('da-inventory-plugin bundle_vendors'))
        .pipe(concat('vendor.js'))
        .pipe(uglify({mangle: false}))
        .pipe(gulp.dest('./build'))
        .pipe($.size({
            title: 'test-drive vendor.js'
        }));
});

gulp.task('bundle_vendor_css', function () {
    gulp.src(vendorCss)
        .pipe(concat('vendor.css'))
        .pipe(urlAdjuster({
            replace: ['../fonts', './fonts'],
        }))
        .pipe(gulp.dest('./build'));
});

gulp.task('copy_app_scss', function () {
    gulp.src(appScss)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass())
        // .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        /*.pipe(urlAdjuster({
         replace: ['../../img', '../img'],
         }))*/
        // .pipe(concat('site.css'))
        // .pipe(cssnano())
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('./build'));
});

gulp.task('copy_app_fonts', function () {
    gulp.src(vendorFonts)
        .pipe(gulp.dest('./build/fonts'));
});


gulp.task('bundle_app', function () {
    gulp.src(config.angularAppFiles)
        .pipe(plumber())
        .pipe(concat('app.js'))
        .pipe(uglify({mangle: false}))
        .pipe(gulp.dest('./build'))
        .pipe($.size({
            title: 'da-inventory-plugin app.js'
        }));
});

gulp.task('bundle_app_debug', function () {
    gulp.src(config.angularAppFiles)
        .pipe(plumber())
        // .pipe(debug('da-inventory-plugin'))
        .pipe(concat('app.js'))
        // .pipe(uglify({mangle: false}))
        .pipe(gulp.dest('./build'))
        .pipe($.size({
            title: 'size of app.js: '
        }));
});

//generate angular templates using html2js
gulp.task('compile_templates', function () {
    return gulp.src(config.templates)
        .pipe(plumber())
        .pipe(debug('tigr'))
        .pipe(html2js('templates.js', {
            name: 'templates',
            base: '.',
            adapter: 'angular',
            useStrict: true
        }))
        // .pipe($.concat('templates.js'))
        .pipe(gulp.dest('./build/tmp'));
    // .pipe($.size({
    //     title: 'templates'
    // }))
    // .pipe($.size({
    //     title: 'app.js'
    // }));
});

gulp.task('copy_index', function () {
    gulp.src('./src/app/index.html')
        .pipe(gulp.dest('./build/'));
});

gulp.task('build_dist', function (callback) {
    runSequence('clean',
        'bundle_vendor_js',
        'bundle_vendor_css',
        'copy_app_fonts',
        'compile_templates',
        'bundle_app',
        'copy_app_scss',
        'copy_index',
        // 'clean_tmp',
        callback);
});

gulp.task('build_debug', function (callback) {
    runSequence('clean',
        'bundle_vendor_js',
        'bundle_vendor_css',
        'copy_app_fonts',
        'compile_templates',
        'bundle_app_debug',
        'copy_app_scss',
        'copy_index',
        // 'clean_tmp',
        callback);
});

gulp.task('reload_sequence', function (callback) {
    runSequence(
        'compile_templates',
        'bundle_app_debug',
        callback);
});


gulp.task('serve', ['build_debug'], function () {
    browserSync.init({
        port: 8081,
        ui: {
            port: 8083
        },
        notify: false,
        logPrefix: 'serve',
        server: {
            baseDir: ['./build', '.'],
            middleware: []
        }
    });

    gulp.watch(config.templates, ['reload_sequence', reload]);
    gulp.watch(vendorJs, ['bundle_vendor_js', reload]);
    gulp.watch(config.angularAppFiles_watch, ['bundle_app_debug', reload]);
    gulp.watch(appScssWatch, ['copy_app_scss', reload]);
});