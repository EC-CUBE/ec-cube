const config = require('../config')
const { src, dest } = require('gulp')
const plumber = require('gulp-plumber')
const notify = require('gulp-notify')
const browserSync = require('browser-sync')
const rename = require('gulp-rename')
const sass = require('gulp-sass')(require('sass'))
const postcss = require('gulp-postcss')
const atImport = require('postcss-import');
const autoprefixer = require('autoprefixer')
const cssmqpacker = require('css-mqpacker')
const sortCSSmq = require('sort-css-media-queries')

module.exports = () => {
  return src([config.paths.source.template + config.paths.assets.scss], {
      sourcemaps: true,
      base: config.paths.source.template
    })
    .pipe(plumber({
      errorHandler: notify.onError('Error: <%= error.message %>')
    }))
    .pipe(sass())
    .pipe(postcss([
      atImport(),
      autoprefixer(),
      cssmqpacker({
        sort: sortCSSmq
      }),
    ]))
    .pipe(rename((path) => {
      path.dirname = path.dirname.replace(/scss$/, 'css')
    }))
    .pipe(dest(config.paths.output.template, {sourcemaps: '.'}))
    .pipe(browserSync.stream())
}
