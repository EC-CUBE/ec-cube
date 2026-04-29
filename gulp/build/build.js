const { parallel } = require('gulp')
const scssAll = require('../task/scss-all')
const rspack = require('../task/rspack')

module.exports = parallel(scssAll, rspack)
