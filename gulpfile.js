var gulp = require('gulp');
var concat = require('gulp-concat');
var minify = require("gulp-minify");
var cleanCSS = require('gulp-clean-css');
var rename = require("gulp-rename");
var autoprefixer = require('gulp-autoprefixer');


gulp.task('combine_js', function() {    
  return gulp.src([ './js/script.js', './js/video.js', './js/facbook.js', './js/twitter.js', './js/googleplus.js', './js/whatsapp.js'   ])
    .pipe(concat('scripts.js'))
    .pipe(gulp.dest('./js'));
});

gulp.task('minify_css', function(){    
    return gulp.src('./style.css') 
    .pipe(autoprefixer({
            browsers: ['last 3 versions'],
            cascade: false
        }))
    .pipe(rename('style.min.css'))
    .pipe(cleanCSS())
    .pipe(gulp.dest('.'));
});

gulp.task('minify-js', function () {
    return gulp.src('./js/scripts.js') // path to your files
    .pipe(rename('scripts.min.js'))
    .pipe(minify({
        ext:{
            src:'-debug.js',
            min:'.js'
        },
        exclude: ['tasks']}))
    .pipe(gulp.dest('js/'));
});

gulp.task('default', function() {
    gulp.watch([ './js/script.js', './js/whatsapp.js' ], ['combine_js' ]);   
    gulp.watch('./js/scripts.js', [ 'minify-js']);
    gulp.watch('./style.css',  ['minify_css']);
});