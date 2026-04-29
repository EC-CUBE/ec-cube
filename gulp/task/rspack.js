const rspack = require('@rspack/core')
const rspackConfig = require('../../rspack.config.js')

module.exports = (done) => {
  rspack(rspackConfig, (err, stats) => {
    if (err) {
      console.error(err)
      done(err)
      return
    }
    if (stats.hasErrors()) {
      console.error(stats.toString({ colors: true, errors: true }))
      done(new Error('rspack build failed'))
      return
    }
    console.log(stats.toString({ colors: true, modules: false, chunks: false, children: false }))
    done()
  })
}
