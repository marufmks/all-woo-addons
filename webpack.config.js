const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const glob = require('glob');

function getBlockEntries() {
  const entries = {};
  // Find all block entry points (edit.js or index.js inside each block)
  const files = glob.sync('./src/blocks/*/index.js');

  files.forEach((file) => {
    const blockName = file.split('/').slice(-2, -1)[0]; // folder name
    entries[`blocks/${blockName}/index`] = path.resolve(process.cwd(), file);
  });

  return entries;
}

module.exports = {
  ...defaultConfig,

  entry: {
    // Keep default block.json auto-entries
    ...defaultConfig.entry,

    // Add each block separately
    ...getBlockEntries(),

    // Admin React entry
    'admin/index': path.resolve(process.cwd(), 'src/admin/index.js'),
  },

  output: {
    ...defaultConfig.output,
    path: path.resolve(process.cwd(), 'build'),
    filename: '[name].js', // keeps folder structure like blocks/hello-world/index.js
  },
};
