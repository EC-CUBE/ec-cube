const config = require('../config')
const { src, dest } = require('gulp')
const plumber = require('gulp-plumber')
const notify = require('gulp-notify')
const browserSync = require('browser-sync')
const rename = require('gulp-rename')
const sass = require('gulp-sass')(require('sass-embedded'))
const postcss = require('gulp-postcss')
const autoprefixer = require('autoprefixer')
const sortMediaQueries = require('postcss-sort-media-queries')
const cleanCSS = require('gulp-clean-css')

const SILENCE = [
  'legacy-js-api',
  'import',
  'global-builtin',
  'color-functions',
  'if-function',
  'abs-percent',
  'function-units',
  'slash-div',
  'feature-exists',
  'duplicate-var-flags-in-list'
]

module.exports = () => {
  return src([config.paths.source.template + config.paths.assets.scss], {
      sourcemaps: true,
      base: config.paths.source.template
    })
    .pipe(plumber({ errorHandler: notify.onError('Error: <%= error.message %>') }))
    .pipe(sass({ silenceDeprecations: SILENCE }))
    .pipe(postcss([autoprefixer(), sortMediaQueries()]))
    .pipe(rename((p) => { p.dirname = p.dirname.replace(/scss$/, 'css') }))
    .pipe(dest(config.paths.output.template, { sourcemaps: false }))
    .pipe(browserSync.stream())
    .pipe(cleanCSS())
    .pipe(rename((p) => { if (p.extname === '.css') p.extname = '.min.css' }))
    .pipe(dest(config.paths.output.template, { sourcemaps: '.' }))
}
