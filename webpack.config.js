const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,

  entry: {
    // Gutenberg block entries (auto-detected by block.json, keep this)
    ...defaultConfig.entry,

    // Add admin React app
    'admin/index': path.resolve(process.cwd(), 'src/admin/index.js'),
  },

  output: {
    ...defaultConfig.output,
    path: path.resolve(process.cwd(), 'build'),
    filename: '[name].js', // keeps admin/index.js separate
  },
};
