const { series } = require('gulp')
const config = require('../config')
const scss = require('../task/scss')
const scssMin = require('../task/scss-min')

module.exports = series(scss, scssMin)
