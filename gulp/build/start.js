const { series, parallel, watch } = require('gulp')
const config = require('../config')
const server = require('../task/server')
const scss = require('../task/scss')
const scssMin = require('../task/scss-min')

module.exports = series(series(server, parallel(scss, scssMin)), () => {
  watch(config.paths.source.template + config.paths.assets.scss, parallel(scss, scssMin))
})
