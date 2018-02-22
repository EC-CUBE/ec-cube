const gulp = require("gulp");
const $ = require("gulp-load-plugins")();
const sass = require('gulp-sass');
const rename = require('gulp-rename');
const es = require('event-stream');

const srcPattern = [
  'default',
  'admin'
];
gulp.task('sass', function(){
  let events = srcPattern.map((target) => {
    return gulp.src(`./html/template/${target}/assets/scss/**/*.scss`)
      .pipe($.plumber({
        errorHandler: $.notify.onError('<%= error.message %>')
      }))
      .pipe($.sourcemaps.init())
      .pipe(sass({
        sourceMap: true
      }))
      .pipe($.pleeease({
        autoprefixer: true,
        minifier: true,
        mqpacker: true
      }))
      .pipe($.sourcemaps.write())
      .pipe(rename({
        extname: '.min.css'
      }))
      .pipe(gulp.dest(`./html/template/${target}/assets/css/`));
  });
  return es.concat(events);
});

gulp.task("default",["sass"]);

