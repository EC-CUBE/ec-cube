const { series, parallel, watch } = require('gulp')
const config = require('../config')
const server = require('../task/server')
const scssAll = require('../task/scss-all')
const rspack = require('../task/rspack')

module.exports = series(series(server, parallel(scssAll, rspack)), () => {
  watch(config.paths.source.template + config.paths.assets.scss, scssAll)
  watch(config.paths.source.template + config.paths.assets.js, rspack)
})
