const path = require('path');
const { argv } = require('yargs');
const { merge } = require('webpack-merge');
const resolveRequire = require('./helpers/resolve-require');

const userConfig = resolveRequire(`${__dirname}/../config`);

const isProduction = !!(argv.env && argv.env === 'production');
const rootPath =
  'paths' in userConfig && userConfig.paths.root
    ? userConfig.paths.root
    : process.cwd();
const themeName =
  'themeName' in userConfig ? userConfig.themeName : 'foundation_xy';

const config = merge(
  {
    open: true,
    entry: {
      main: ['./scripts/main.js', './styles/main.scss'],
    },
    copy: 'images/**/*',
    proxyUrl: 'http://localhost:3000',
    publicPath: `/wp-content/themes/${themeName}`,
    cacheBusting: '[name]_[contenthash]',
    paths: {
      root: rootPath,
      assets: path.join(rootPath, 'assets'),
      dist: path.join(rootPath, 'dist'),
    },
    enabled: {
      sourceMaps: true,
      optimize: isProduction,
      cacheBusting: isProduction,
      watcher: argv.$0.endsWith('watch.js'),
    },
    bsWatchFiles: ['**/*.php', 'assets/**/*.js'],
  },
  userConfig
);

module.exports = merge(config, {
  env: Object.assign(
    { production: isProduction, development: !isProduction },
    argv.env
  ),
  publicPath: `${config.publicPath}/${path.basename(config.paths.dist)}/`,
  manifest: {},
});

if (process.env.NODE_ENV === undefined) {
  process.env.NODE_ENV = isProduction ? 'production' : 'none';
}
