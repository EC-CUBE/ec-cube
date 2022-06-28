const gulp = require('gulp')
const webpackStream = require('webpack-stream')
const webpack = require('webpack')
const webpackConfig = require('../../webpack.config.js')

module.exports = () => {
  return webpackStream(webpackConfig, webpack).on('error', (e) => this.emit('end'))
    .pipe(gulp.dest('html/bundle'))
};
