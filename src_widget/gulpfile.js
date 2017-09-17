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
var replace = require('gulp-replace');
var babel = require('gulp-babel');

var env = require('./src/environments/environment.dev');

var config = {
    templates: './src/app/**/*.tpl.html',
    angularAppFiles: ['./build/tmp/templates.js', './src/app/prototypes/**/*.js', './src/app/**/*module.js', './src/app/app.js', './src/app/**/*.js', '!./src/app/**/*spec.js'],
    angularAppFiles_watch: ['./src/app/prototypes/**/*.js', './src/app/**/*module.js', './src/app/app.js', './src/app/**/*.js', '!./src/app/**/*spec.js'],
    integration: ['./src/app/prototypes/**/*.js', './src/app/**/*module.js', './src/app/app.js', './src/app/**/*.js', '!./src/app/**/*spec.js'],
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
    // 'node_modules/angular-material/angular-material.min.css',
    // 'node_modules/font-awesome/css/font-awesome.min.css'
];

var vendorFonts = [
    'node_modules/font-awesome/fonts/*'
];

var appScss = [
    './src/sass/app.scss'
];

var appImages = [
    './src/img/*'
];

var appScssWatch = [
    './src/sass/**/*.scss'
];


// =======================================================================//
// Build Tasks                                                            //
// =======================================================================//

gulp.task('build_dist:dev', function (callback) {
    runSequence('build_dist',
        'init_dev_env',
        callback);
});

gulp.task('build_dist:prod', function (callback) {
    runSequence('build_dist',
        'init_prod_env',
        callback);
});

gulp.task('serve:build_dist:prod', function (callback) {
    runSequence('build_dist',
        'init_prod_env',
        'serve',
        'watch_dist',
        callback);
});

gulp.task('serve:build_debug:local', function (callback) {
    runSequence('build_debug',
        'init_local_env',
        'serve',
        'watch_debug',
        callback);
});

gulp.task('serve:build_debug:prod', function (callback) {
    runSequence('build_debug',
        'init_prod_prod',
        'serve',
        'watch_debug',
        callback);
});

// =======================================================================//

gulp.task('clean', function () {
    return del.sync('./build');
});

gulp.task('init_local_env', function () {
    var env = require('./src/environments/environment.local');
    replaceConfigs(env);
});

gulp.task('init_dev_env', function () {
    var env = require('./src/environments/environment.dev');
    replaceConfigs(env);
});

gulp.task('init_prod_env', function () {
    var env = require('./src/environments/environment.prod');
    replaceConfigs(env);
});

function replaceConfigs(env) {
    gulp.src(['./build/app.js'])
        .pipe(replace('%apiBaseUrl%', env.apiBaseUrl))
        .pipe(replace('%siteId%', env.defaultSiteId))
        .pipe(gulp.dest('./build/'));

    gulp.src(['./build/integration/integration.js'])
        .pipe(replace('%widgetUrl%', env.widgetUrl))
        .pipe(replace('%siteId%', env.defaultSiteId))
        .pipe(gulp.dest('./build/integration/'));
}

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

gulp.task('copy_app_scss_dist', function () {
    gulp.src(appScss)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(cssnano())
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('./build'));
});

gulp.task('copy_app_scss_debug', function () {
    gulp.src(appScss)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('./maps'))
        .pipe(gulp.dest('./build'));
});

gulp.task('copy_app_fonts', function () {
    return gulp.src(vendorFonts)
        .pipe(gulp.dest('./build/fonts'));
});

gulp.task('bundle_integration', function () {
    gulp.src('./src/integration/*.js')
        .pipe(concat('integration.js'))
        .pipe(gulp.dest('./build/integration/'));

    gulp.src('./src/integration/*.scss')
        .pipe(plumber())
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./build/integration/'));
});

gulp.task('bundle_integration_dist', function () {
    gulp.src('./src/integration/*.js')
        .pipe(babel({presets: ['es2015']}))
        .pipe(concat('integration.js'))
        .pipe(uglify({compress: true}).on('error', function (e) {
            console.log(e);
        }))
        .pipe(gulp.dest('./build/integration/'));

    gulp.src('./src/integration/*.scss')
        .pipe(plumber())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(cssnano())
        .pipe(gulp.dest('./build/integration/'));
});

gulp.task('copy_app_images', function () {
    return gulp.src(appImages)
        .pipe(gulp.dest('./build/img'));
});

gulp.task('bundle_app_dist', function () {
    return gulp.src(config.angularAppFiles)
        .pipe(plumber())
        .pipe(concat('app.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./build'))
        .pipe($.size({
            title: 'size of app.js:'
        }));
});

gulp.task('bundle_app_debug', function () {
    return gulp.src(config.angularAppFiles)
        .pipe(plumber())
        .pipe(concat('app.js'))
        .pipe(gulp.dest('./build'))
        .pipe($.size({
            title: 'size of app.js:'
        }));
});

//generate angular templates using html2js
gulp.task('compile_templates', function () {
    return gulp.src(config.templates)
        .pipe(plumber())
        // .pipe(debug('templates.js'))
        .pipe(html2js('templates.js', {
            name: 'templates',
            base: '.',
            adapter: 'angular',
            useStrict: true
        }))
        .pipe(gulp.dest('./build/tmp'))
        .pipe($.size({
            title: 'templates size:'
        }));
});

gulp.task('copy_index', function () {
    return gulp.src('./src/app/index.html')
        .pipe(gulp.dest('./build/'));
});

gulp.task('build_dist', function (callback) {
    runSequence('clean',
        'bundle_vendor_js',
        'bundle_vendor_css',
        'copy_app_fonts',
        'compile_templates',
        'bundle_app_dist',
        'copy_app_scss_dist',
        'copy_app_images',
        'copy_index',
        'bundle_integration_dist',
        callback);
});

gulp.task('build_debug', function (callback) {
    runSequence('clean',
        'bundle_vendor_js',
        'bundle_vendor_css',
        'copy_app_fonts',
        'compile_templates',
        'bundle_app_debug',
        'copy_app_scss_debug',
        'copy_app_images',
        'copy_index',
        'bundle_integration',
        callback);
});

gulp.task('reload_sequence_debug', function (callback) {
    runSequence(
        'compile_templates',
        'bundle_app_debug',
        callback);
});

gulp.task('serve', function () {
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
});

gulp.task('watch_debug', function () {
    gulp.watch(config.templates, ['reload_sequence', reload]);
    gulp.watch(vendorJs, ['bundle_vendor_js', reload]);
    gulp.watch(config.angularAppFiles_watch, ['bundle_app_debug', reload]);
    gulp.watch(appScssWatch, ['copy_app_scss_debug', reload]);
    gulp.watch('./src/integration/*', ['bundle_integration', reload]);
});

gulp.task('watch_dist', function () {
    gulp.watch(config.templates, ['reload_sequence', reload]);
    gulp.watch(vendorJs, ['bundle_vendor_js', reload]);
    gulp.watch(config.angularAppFiles_watch, ['bundle_app_dist', reload]);
    gulp.watch(appScssWatch, ['copy_app_scss_dist', reload]);
    gulp.watch('./src/integration/*', ['bundle_integration', reload]);
});