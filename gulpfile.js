const packagejson = require('./package.json');
const plugins = require('gulp-load-plugins')({
    pattern: ['*'],
    scope: ['devDependencies']
});

// Sass
function sass(pumpCallback) {
    let themePath = process.argv.indexOf('--admin') === -1 ? packagejson.paths.theme : packagejson.paths.theme_admin;

    return plugins.pump([
        plugins.gulp.src(themePath + '/' + packagejson.paths.styles + '/src/**/*.scss'),
        plugins.sass(),
        plugins.postcss([
            plugins.autoprefixer(packagejson.autoprefixer),
            plugins.postcssPxtorem(packagejson.pxtorem)
        ]),
        plugins.rename('theme.css'),
        plugins.gulp.dest(themePath + '/' + packagejson.paths.styles + '/dist/')
    ], pumpCallback);
}

// Watch SASS
function watchsass() {
    let themePath = process.argv.indexOf('--admin') === -1 ? packagejson.paths.theme : packagejson.paths.theme_admin;

    plugins.gulp.watch(themePath + '/' + packagejson.paths.styles + '/src/**/*.scss', plugins.gulp.parallel(sass));
}

// Alias
exports.default = plugins.gulp.series(sass, watchsass);