const url_extension = "?pluginSetPreview=JDJ5JDEwJEhpeUJmeEwyMUc5SXBvSElWTUxWN3VnQXVKd0hMa0hyMVBZaDBEVzVTc3F5dHBVcEhUTU0u";
const TW_SRC = "./resources/views/";
const JS_SRC = "./resources/js/src/";
const JS_DIST = "./resources/js/dist/";
const SCSS_SRC = "./resources/scss/";
const SCSS_DIST = "./resources/css/";
const OUTPUT_PREFIX = "main";

// import gulp
var gulp = require("gulp");
var gutil = require("gulp-util");
var sourcemaps = require("gulp-sourcemaps");
var concat = require("gulp-concat");
var minify = require('gulp-minify');
var rename = require("gulp-rename");
var browserify = require("browserify");
var glob = require("glob");
var source = require("vinyl-source-stream");
var buffer = require("vinyl-buffer");
var minifyCSS = require("gulp-minify-css");
var sass = require("gulp-sass");
var autoprefixer = require("gulp-autoprefixer");
var cache = require('gulp-cache');   
var browserSync = require('browser-sync').create();

gulp.task("default", ["build"]);

gulp.task("build", [
    "build:bundle",
    "build:sass-min"
]);

// Bundle everything
gulp.task("build:bundle", function()
{
    var libraries = require(JS_SRC + "vendor.json");
    var _src = libraries.concat([JS_SRC + "main.js"]);
    //var _components = _src.concat([JS_SRC + "components/*.js"]);

    return gulp.src(_src)
        .pipe(concat(OUTPUT_PREFIX + ".js"))
        //.pipe(minify())
        .pipe(gulp.dest(JS_DIST));
});

// SASS
gulp.task("build:sass-min", ["build:sass"], function()
{
    return buildSass(OUTPUT_PREFIX + ".min.css", "compressed");
});

gulp.task("build:sass", function()
{
    return buildSass(OUTPUT_PREFIX + ".css", "expanded");
});

gulp.task('clearCache', function() {

  // Or, just call this for everything
  cache.clearAll();

});

function buildSass(outputFile, outputStyle)
{
    var config = {
        scssOptions  : {
            errLogToConsole: true,
            outputStyle    : outputStyle
        },
        prefixOptions: {
            browsers: [
                "last 2 versions",
                "> 5%",
                "Firefox ESR"
            ]
        }
    };

    return gulp
        .src(SCSS_SRC + "Main.scss")
        .pipe(sourcemaps.init())
        .pipe(sass(config.scssOptions).on("error", sass.logError))
        .pipe(rename(outputFile))
        .pipe(autoprefixer(config.prefixOptions))
        .pipe(minifyCSS())
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(SCSS_DIST));
}

// Compile sass into CSS & auto-inject into browsers
gulp.task('sass', function() {

    var outputFile = OUTPUT_PREFIX + ".css";
    var outputStyle = "expanded"
    var config = {
        scssOptions  : {
            errLogToConsole: true,
            outputStyle    : outputStyle
        },
        prefixOptions: {
            browsers: [
                "last 2 versions",
                "> 5%",
                "Firefox ESR"
            ]
        }
    };

    return gulp
        .src(SCSS_SRC + "Main.scss")
        .pipe(sourcemaps.init())
        .pipe(sass(config.scssOptions).on("error", sass.logError))
        .pipe(rename(outputFile))
        .pipe(autoprefixer(config.prefixOptions))
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(SCSS_DIST))
        .pipe(browserSync.stream());
});

// Watchers
gulp.task("watch:js", function()
{
    return gulp.watch(JS_DIST + "**/*.js", ["build:app"]);
});

gulp.task("watch:sass", function()
{
    return gulp.watch(SCSS_SRC + "**/*.scss", ["build:sass"]);
});

// Static Server + watching scss/html files
gulp.task('serve', ['sass'], function() {

    browserSync.init({
        //Enter the remote URL of the plugin on your plentymarkets system
        proxy: {
            target: "https://www.reiseschein.de/",
            proxyReq: [
                function(proxyReq) {
                    proxyReq.setHeader('Access-Control-Allow-Origin', '*');
                }
            ],
            middleware: function (req, res, next) {
                req.url = req.url + url_extension;
                next();
            }
        },
        port:8181,

        // open: "external",
        cors: true,
        //logLevel: "debug",
        //logPrefix: "reiseschein",
        //tunnel : "https://s3-eu-central-1.amazonaws.com/plentymarkets-public-92/yb15payhzij0/plugin/8/reiseschein/",
        //reloadDelay: 2000,
        //Directories to watch for changes
        //The browser refreshes whenever a file in this directory is changed
        files: [
            'resources/js/**',
            'resources/css/**',
        ],
     
        //Add rewrite rules for CSS and JS
        //This will make it look for CSS and JS files in the plugin directory
        rewriteRules: [
            {
                match: new RegExp('http.*\\/reiseschein\\/js\\/(.*.js)'),
                replace: '/resources/js/$1'
            },
            {
                // https://s3-eu-central-1.amazonaws.com/plentymarkets-public-92/yb15payhzij0/plugin/8/reiseschein/css/main.css
                match: new RegExp('<link rel=\"stylesheet.*https.*\\/reiseschein\\/css\\/(.*.css)'),
                replace: '<link rel="stylesheet" href="/resources/css/$1'
            },
        ],
     
        //Instruct Browsersync to provide static resources for JS and CSS files
        //This way, your browser will load the local resources instead of remote ones
        serveStatic: [
            {
                route: ['/resources'],
                dir: 'resources'
            }
        ]
    });

    gulp.watch(SCSS_SRC + "**/*.scss", ["clearCache", "sass"]);
    //gulp.watch(TW_SRC   + "**/*.twig").on('change', browserSync.reload);
    gulp.watch(JS_SRC  + "**/*.js", ["build:bundle"]).on('change', browserSync.reload);
});
