const { series } = require('gulp')
const config = require('../config')
const scss = require('../task/scss')
const scssMin = require('../task/scss-min')
const webpack = require('../task/webpack')

module.exports = series(scss, scssMin, webpack)
