'use strict';

var browserSync = require('browser-sync').create();

var gulp = require('gulp');
var $ = require('gulp-load-plugins')();
var plumber = require('gulp-plumber');
var gulpCopy = require('gulp-copy');
var html2js = require('gulp-html2js');
var changed = require('gulp-changed');
var util = require('gulp-util');
var runSequence = require('run-sequence');
var debug = require('gulp-debug');
var sass = require('gulp-sass');
var useref = require('gulp-useref');
var uglify = require('gulp-uglify');
var cssnano = require('gulp-cssnano');
var inject = require('gulp-inject');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var del = require('del');
var clean = require('gulp-clean');

var config = {
    templates: './src/app/**/*.tpl.html',
    angularAppFiles: ['tmp/templates.js', './src/app/prototypes/**/*.js', './src/app/**/*module.js', './src/app/app.js', './src/app/**/*.js', '!./src/app/**/*spec.js']
};

var vendorJs = [
    'node_modules/angular/angular.min.js',
    'node_modules/angular-animate/angular-animate.min.js',
    'node_modules/angular-aria/angular-aria.min.js',
    'node_modules/angular-messages/angular-messages.min.js',
    'node_modules/angular-route/angular-route.min.js',
    'node_modules/angular-material/angular-material.min.js'
];

var vendorCss = [
    'src/sass/vendor/angular-material.css',
    'src/sass/vendor/bootstrap.min.css',
    'src/sass/vendor/roboto-fonts.css'
];

var appScss = [
    './src/sass/app.scss'
];

var appScssWatch = [
    './src/sass/*.scss'
];

gulp.task('clean', function () {
    return del.sync('./build/*');
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
        .pipe(cssnano())
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

gulp.task('bundle_app', function () {
    gulp.src(config.angularAppFiles)
        .pipe(plumber())
        // .pipe(debug('da-inventory-plugin'))
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
            title: 'da-inventory-plugin app.js'
        }));
});

//generate angular templates using html2js
gulp.task('templates', function () {
    return gulp.src(config.templates)
    // .pipe(changed(config.tmp))
        .pipe(plumber())
        .pipe(debug())
        .pipe(html2js('templates.js', {
            name: 'templates',
            base: '.',
            adapter: 'angular',
            useStrict: true
        }))
        // .pipe($.concat('templates.js'))
        .pipe(gulp.dest('./tmp'))
        .pipe($.size({
            title: 'templates'
        }))
        .pipe($.size({
            title: 'app.js'
        }));
});

gulp.task('copy_index', function () {
    gulp.src('./src/app/index.html')
        .pipe(gulp.dest('./build/'));
});

gulp.task('build', function (callback) {
    runSequence('clean',
        'bundle_vendor_js',
        'bundle_vendor_css',
        'templates',
        'bundle_app',
        'copy_app_scss',
        'copy_index',
        callback);
});

gulp.task('build_debug', function (callback) {
    runSequence('clean',
        'bundle_vendor_js',
        'bundle_vendor_css',
        'templates',
        'bundle_app_debug',
        'copy_app_scss',
        'copy_index',
        callback);
});


var reload = browserSync.reload;

gulp.task('test_reload', function () {
    reload()
});


gulp.task('serve', ['build_debug'], function () {
    browserSync.init({
        port: 8080,
        ui: {
            port: config.uiPort
        },
        notify: false,
        logPrefix: 'serve',
        server: {
            baseDir: ['./build', '.'],
            middleware: []
        }
    });

    gulp.watch(config.templates, ['templates', 'reload']);
    gulp.watch(vendorJs, ['bundle_vendor_js', 'reload']);
    gulp.watch(config.angularAppFiles, ['bundle_app_debug', 'reload']);
    gulp.watch(appScssWatch, ['copy_app_scss', 'test_reload']);
});