module.exports = function (grunt) {
  grunt.initConfig({
    jshint: {
      options: {
        node: true,
        maxerr: 200,
        browser: true,
        devel: true,
        unused: true
      },
      files: {
        src: [
          'Gruntfile.js',
          'html/js/eccube.js',
          'html/user_data/packages/admin/js/breadcrumbs.js',
          'html/user_data/packages/admin/js/eccube.admin.js',
          'html/user_data/packages/admin/js/layout_design.js ',
          'html/user_data/packages/sphone/js/eccube.sphone.js',
        ]
      }
    }
  });
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.registerTask('default', [ 'jshint' ]);
};
