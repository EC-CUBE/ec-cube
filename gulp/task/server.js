const browserSync = require('browser-sync')
const config = require('../config')

module.exports = (done) => {
  browserSync({
    proxy: config.server
  })
  done()
}
